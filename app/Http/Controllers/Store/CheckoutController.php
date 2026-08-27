<?php

namespace App\Http\Controllers\Store;

use App\Actions\Store\Checkout\CreateOrderFromCartAction;
use App\Data\CheckoutData;
use App\Enums\PaymentMethod;
use App\Http\Controllers\Controller;
use App\Http\Requests\Store\CheckoutRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CheckoutController extends Controller
{
    public function create(Request $request): Response|RedirectResponse
    {
        $cart = $request->user()->cart()->with('items.variant')->first();

        if (! $cart || $cart->items->isEmpty()) {
            return to_route('store.cart.index')->with('error', 'Adicione produtos ao carrinho antes de continuar.');
        }

        $address = $request->user()->addresses()->where('is_default', true)->first();

        return Inertia::render('Store/Checkout/Create', [
            'address' => $address,
            'paymentMethods' => array_map(fn (PaymentMethod $method): array => ['value' => $method->value, 'label' => $method->label()], PaymentMethod::cases()),
            'totalAmount' => $cart->items->sum(fn ($item) => (float) $item->variant->currentPrice() * $item->quantity),
            'sandbox' => config('payments.mode') === 'sandbox',
        ]);
    }

    public function store(CheckoutRequest $request, CreateOrderFromCartAction $createOrder): RedirectResponse
    {
        $order = $createOrder->handle($request->user(), new CheckoutData($request->validated()));

        return to_route('store.orders.show', $order)->with('success', 'Pedido criado com sucesso.');
    }
}
