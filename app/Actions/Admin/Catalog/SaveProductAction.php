<?php

namespace App\Actions\Admin\Catalog;

use App\Enums\ProductStatus;
use App\Models\Product;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SaveProductAction
{
    /**
     * Creates or updates the product and its primary/default variant atomically.
     *
     * @param  array<string, mixed>  $validated
     */
    public function handle(array $validated, ?Product $product = null): Product
    {
        return DB::transaction(function () use ($validated, $product): Product {
            $data = Arr::only($validated, [
                'brand_id', 'category_id', 'name', 'short_description', 'description',
                'status', 'is_featured', 'warranty_months', 'weight', 'height', 'width',
                'length', 'seo_title', 'seo_description',
            ]);

            if (! $product || $data['name'] !== $product->name) {
                $data['slug'] = $this->uniqueSlug($data['name'], $product);
            }

            $willBeActive = $data['status'] === ProductStatus::ACTIVE->value;
            $data['published_at'] = $willBeActive
                ? ($product?->published_at ?? now())
                : null;

            $product = $product
                ? tap($product)->update($data)
                : Product::create($data);

            $variantData = [
                'name' => $validated['variant_name'],
                'sku' => $validated['sku'],
                'barcode' => $validated['barcode'] ?? null,
                'price' => $validated['price'],
                'sale_price' => $validated['sale_price'] ?? null,
                'cost_price' => $validated['cost_price'] ?? null,
                'stock' => $validated['stock'],
                'reserved_stock' => 0,
                'low_stock_threshold' => $validated['low_stock_threshold'],
                'attributes' => null,
                'is_default' => true,
                'is_active' => $validated['variant_is_active'],
            ];

            $product->defaultVariant
                ? $product->defaultVariant->update($variantData)
                : $product->variants()->create($variantData);

            return $product;
        });
    }

    private function uniqueSlug(string $name, ?Product $ignoredProduct = null): string
    {
        $baseSlug = Str::slug($name) ?: 'produto';
        $slug = $baseSlug;
        $suffix = 2;

        while (Product::withTrashed()->where('slug', $slug)
            ->when($ignoredProduct, fn ($query) => $query->whereKeyNot($ignoredProduct->id))
            ->exists()) {
            $slug = "{$baseSlug}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }
}
