<?php

namespace App\Services\Store;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Storage;

class ProductCardPresenter
{
    /**
     * Produces the stable, minimal data contract used by product cards.
     *
     * @return array<string, int|string|null>
     */
    public function present(Product $product, bool $isFavorited = false): array
    {
        /** @var ProductVariant|null $variant */
        $variant = $product->variants->first();

        /** @var ProductImage|null $image */
        $image = $product->images->first();

        return [
            'id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'brand' => $product->brand?->name,
            'category' => $product->category?->name,
            'image_url' => $image ? Storage::url($image->path) : null,
            'price' => $variant?->price,
            'sale_price' => $variant?->sale_price,
            'available_stock' => $variant?->availableStock() ?? 0,
            'is_favorited' => $isFavorited,
        ];
    }
}
