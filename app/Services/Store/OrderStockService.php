<?php

namespace App\Services\Store;

use App\Models\ProductVariant;
use Illuminate\Validation\ValidationException;

class OrderStockService
{
    public function reserve(ProductVariant $variant, int $quantity): void
    {
        if ($variant->availableStock() < $quantity) {
            throw ValidationException::withMessages([
                'cart' => "O item {$variant->product->name} não possui estoque suficiente.",
            ]);
        }

        $variant->increment('reserved_stock', $quantity);
        $variant->refresh();
    }

    public function commitSale(ProductVariant $variant, int $quantity): void
    {
        if ($variant->stock < $quantity) {
            throw ValidationException::withMessages([
                'cart' => "O item {$variant->product->name} não possui estoque suficiente.",
            ]);
        }

        $variant->decrement('stock', $quantity);
        $variant->refresh();
    }

    public function releaseReservation(ProductVariant $variant, int $quantity): void
    {
        $variant->decrement('reserved_stock', min($quantity, $variant->reserved_stock));
        $variant->refresh();
    }
}
