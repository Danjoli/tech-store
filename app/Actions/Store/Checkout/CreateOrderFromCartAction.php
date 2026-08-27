<?php

namespace App\Actions\Store\Checkout;

use App\Data\CheckoutData;
use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\ShipmentStatus;
use App\Models\Address;
use App\Models\Cart;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CreateOrderFromCartAction
{
    public function handle(User $user, CheckoutData $checkout): Order
    {
        return DB::transaction(function () use ($user, $checkout): Order {
            $cart = Cart::query()->with(['items.variant.product'])->where('user_id', $user->id)->lockForUpdate()->first();

            if (! $cart || $cart->items->isEmpty()) {
                throw ValidationException::withMessages(['cart' => 'Seu carrinho está vazio.']);
            }

            $items = $cart->items;
            $subtotal = 0.0;

            foreach ($items as $item) {
                if (! $item->variant->is_active || $item->variant->availableStock() < $item->quantity) {
                    throw ValidationException::withMessages(['cart' => "O item {$item->variant->product->name} não possui estoque suficiente."]);
                }

                $subtotal += (float) $item->variant->currentPrice() * $item->quantity;
            }

            $address = $this->storeAddress($user, $checkout);
            $order = Order::create([
                'user_id' => $user->id,
                'number' => 'TS-'.now()->format('ymd').'-'.Str::upper(Str::random(6)),
                'status' => OrderStatus::PENDING_PAYMENT,
                'subtotal' => $subtotal,
                'shipping_amount' => 0,
                'total_amount' => $subtotal,
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

            $method = $checkout->paymentMethod();
            $status = $method === PaymentMethod::CARD ? PaymentStatus::APPROVED : PaymentStatus::PENDING;
            $order->payment()->create([
                'provider' => config('payments.driver'),
                'method' => $method,
                'status' => $status,
                'amount' => $subtotal,
                'external_id' => 'sandbox_'.Str::uuid(),
                'metadata' => $this->sandboxMetadata($method),
                'paid_at' => $status === PaymentStatus::APPROVED ? now() : null,
            ]);

            if ($status === PaymentStatus::APPROVED) {
                $order->update(['status' => OrderStatus::PROCESSING]);
            }

            $order->shipment()->create(['status' => ShipmentStatus::PENDING]);
            $cart->items()->delete();

            return $order;
        });
    }

    private function storeAddress(User $user, CheckoutData $checkout): Address
    {
        $data = Arr::only($checkout->data, ['recipient_name', 'phone', 'zip', 'street', 'number', 'complement', 'district', 'city', 'state']);
        $user->addresses()->update(['is_default' => false]);

        return $user->addresses()->create([...$data, 'is_default' => true]);
    }

    /** @return array<string, string> */
    private function sandboxMetadata(PaymentMethod $method): array
    {
        return match ($method) {
            PaymentMethod::PIX => ['instruction' => 'Pagamento Pix em modo sandbox.'],
            PaymentMethod::BOLETO => ['instruction' => 'Boleto em modo sandbox.'],
            PaymentMethod::CARD => ['instruction' => 'Cartão aprovado em modo sandbox.'],
        };
    }
}
