<?php

namespace App\Actions\Store\Cart;

use App\Enums\ProductStatus;
use App\Models\Cart;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AddCartItemAction
{
    public function handle(User $user, ProductVariant $variant, int $quantity): void
    {
        DB::transaction(function () use ($user, $variant, $quantity): void {
            $variant->loadMissing('product');

            if (! $variant->is_active || $variant->product->status !== ProductStatus::ACTIVE) {
                throw ValidationException::withMessages([
                    'product_variant_id' => 'Este produto não está disponível no momento.',
                ]);
            }

            $availableStock = $variant->availableStock();

            if ($availableStock < 1) {
                throw ValidationException::withMessages([
                    'product_variant_id' => 'Este produto está sem estoque.',
                ]);
            }

            $cart = Cart::firstOrCreate(['user_id' => $user->id]);
            $item = $cart->items()
                ->where('product_variant_id', $variant->id)
                ->lockForUpdate()
                ->first();

            $newQuantity = min($availableStock, ($item?->quantity ?? 0) + $quantity);

            if ($item) {
                $item->update(['quantity' => $newQuantity]);

                return;
            }

            $cart->items()->create([
                'product_variant_id' => $variant->id,
                'quantity' => $newQuantity,
            ]);
        });
    }
}
