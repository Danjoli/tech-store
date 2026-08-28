<?php

namespace App\Actions\Store\Checkout;

use App\Data\CheckoutData;
use App\Enums\OrderStatus;
use App\Enums\ShipmentStatus;
use App\Models\Address;
use App\Models\Cart;
use App\Models\Order;
use App\Models\ProductVariant;
use App\Models\User;
use App\Services\Payments\PaymentService;
use App\Services\Store\OrderStockService;
use App\Services\Store\ShippingQuoteService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CreateOrderFromCartAction
{
    public function __construct(
        private PaymentService $payments,
        private OrderStockService $stock,
        private ShippingQuoteService $shipping,
    ) {}

    public function handle(User $user, CheckoutData $checkout): Order
    {
        return DB::transaction(function () use ($user, $checkout): Order {
            $cart = Cart::query()->with(['items.variant.product'])->where('user_id', $user->id)->lockForUpdate()->first();

            if (! $cart || $cart->items->isEmpty()) {
                throw ValidationException::withMessages(['cart' => 'Seu carrinho está vazio.']);
            }

            $items = $cart->items;
            $variants = $this->lockVariants($items->pluck('product_variant_id')->all());
            $subtotal = 0.0;

            foreach ($items as $item) {
                $variant = $variants->get($item->product_variant_id);

                if (! $variant || ! $variant->is_active || $variant->availableStock() < $item->quantity) {
                    throw ValidationException::withMessages(['cart' => "O item {$item->variant->product->name} não possui estoque suficiente."]);
                }

                $item->setRelation('variant', $variant);
                $subtotal += (float) $variant->currentPrice() * $item->quantity;
            }

            $address = $this->storeAddress($user, $checkout);
            $shippingAmount = $this->shipping->quote($cart, $checkout);
            $order = Order::create([
                'user_id' => $user->id,
                'number' => 'TS-'.now()->format('ymd').'-'.Str::upper(Str::random(6)),
                'status' => OrderStatus::PENDING_PAYMENT,
                'subtotal' => $subtotal,
                'shipping_amount' => $shippingAmount,
                'total_amount' => $subtotal + $shippingAmount,
                'shipping_address' => $address->only(['recipient_name', 'phone', 'zip', 'street', 'number', 'complement', 'district', 'city', 'state']),
            ]);

            foreach ($items as $item) {
                $variant = $item->variant;
                $unitPrice = (float) $variant->currentPrice();
                $order->items()->create([
                    'product_id' => $variant->product_id,
                    'product_variant_id' => $variant->id,
                    'product_name' => $variant->product->name,
                    'variant_name' => $variant->name,
                    'sku' => $variant->sku,
                    'unit_price' => $unitPrice,
                    'quantity' => $item->quantity,
                    'subtotal' => $unitPrice * $item->quantity,
                ]);
            }

            $payment = $this->payments->createFor($order, $checkout->paymentMethod());

            if ($payment->status->value === 'approved') {
                foreach ($items as $item) {
                    $this->stock->commitSale($item->variant, $item->quantity);
                }

                $order->update(['status' => OrderStatus::PROCESSING]);
            } else {
                foreach ($items as $item) {
                    $this->stock->reserve($item->variant, $item->quantity);
                }
            }

            $order->shipment()->create(['status' => ShipmentStatus::PENDING]);
            $cart->items()->delete();

            return $order;
        });
    }

    /** @return Collection<int, ProductVariant> */
    private function lockVariants(array $variantIds): Collection
    {
        return ProductVariant::query()
            ->with('product')
            ->whereKey($variantIds)
            ->lockForUpdate()
            ->get()
            ->keyBy('id');
    }

    private function storeAddress(User $user, CheckoutData $checkout): Address
    {
        $data = Arr::only($checkout->data, ['recipient_name', 'phone', 'zip', 'street', 'number', 'complement', 'district', 'city', 'state']);
        $user->addresses()->update(['is_default' => false]);

        return $user->addresses()->create([...$data, 'is_default' => true]);
    }
}
