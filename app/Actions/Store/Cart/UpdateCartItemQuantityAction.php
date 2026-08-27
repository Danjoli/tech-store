<?php

namespace App\Actions\Store\Cart;

use App\Models\CartItem;
use Illuminate\Validation\ValidationException;

class UpdateCartItemQuantityAction
{
    public function handle(CartItem $item, int $quantity): void
    {
        $item->loadMissing('variant');

        if (! $item->variant->is_active || $item->variant->availableStock() < 1) {
            throw ValidationException::withMessages([
                'quantity' => 'Este produto não está mais disponível.',
            ]);
        }

        $item->update([
            'quantity' => min($quantity, $item->variant->availableStock()),
        ]);
    }
}
