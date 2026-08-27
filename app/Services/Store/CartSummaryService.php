<?php

namespace App\Services\Store;

use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Support\Facades\Storage;

class CartSummaryService
{
    /** @return array<string, array<int, array<string, int|string|null>>|int|float> */
    public function present(?Cart $cart): array
    {
        $items = $cart?->items->map(function (CartItem $item): array {
            $variant = $item->variant;
            $product = $variant->product;
            $image = $product->primaryImage;
            $unitPrice = (float) $variant->currentPrice();

            return [
                'id' => $item->id,
                'quantity' => $item->quantity,
                'product_name' => $product->name,
                'product_slug' => $product->slug,
                'variant_name' => $variant->name,
                'available_stock' => $variant->availableStock(),
                'image_url' => $image ? Storage::url($image->path) : null,
                'unit_price' => $unitPrice,
                'subtotal' => $unitPrice * $item->quantity,
            ];
        })->values() ?? collect();

        return [
            'items' => $items->all(),
            'total_items' => (int) $items->sum('quantity'),
            'total_amount' => (float) $items->sum('subtotal'),
        ];
    }
}
