<?php

namespace App\Http\Controllers\Store;

use App\Actions\Store\Cart\AddCartItemAction;
use App\Actions\Store\Cart\UpdateCartItemQuantityAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Store\StoreCartItemRequest;
use App\Http\Requests\Store\UpdateCartItemRequest;
use App\Models\CartItem;
use App\Models\ProductVariant;
use App\Services\Store\CartSummaryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CartController extends Controller
{
    public function index(Request $request, CartSummaryService $summary): Response
    {
        $cart = $request->user()->cart()
            ->with(['items.variant.product.primaryImage'])
            ->first();

        return Inertia::render('Store/Cart/Index', $summary->present($cart));
    }

    public function store(
        StoreCartItemRequest $request,
        AddCartItemAction $addItem,
    ): RedirectResponse {
        $validated = $request->validated();
        $variant = ProductVariant::findOrFail($validated['product_variant_id']);

        $addItem->handle($request->user(), $variant, $validated['quantity']);

        return back()->with('success', 'Produto adicionado ao carrinho.');
    }

    public function update(
        UpdateCartItemRequest $request,
        CartItem $cartItem,
        UpdateCartItemQuantityAction $updateQuantity,
    ): RedirectResponse {
        $this->ensureOwnership($request, $cartItem);

        $updateQuantity->handle($cartItem, $request->integer('quantity'));

        return back()->with('success', 'Quantidade atualizada.');
    }

    public function destroy(Request $request, CartItem $cartItem): RedirectResponse
    {
        $this->ensureOwnership($request, $cartItem);
        $cartItem->delete();

        return back()->with('success', 'Produto removido do carrinho.');
    }

    private function ensureOwnership(Request $request, CartItem $cartItem): void
    {
        abort_unless($cartItem->cart()->where('user_id', $request->user()->id)->exists(), 404);
    }
}
