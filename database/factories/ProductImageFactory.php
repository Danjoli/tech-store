<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProductImage>
 */
class ProductImageFactory extends Factory
{
    protected $model = ProductImage::class;

    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'product_variant_id' => null,

            'path' => 'products/placeholder.webp',
            'alt_text' => fake()->sentence(5),

            'sort_order' => fake()->numberBetween(0, 10),
            'is_primary' => false,
        ];
    }

    public function primary(): static
    {
        return $this->state(fn (): array => [
            'sort_order' => 0,
            'is_primary' => true,
        ]);
    }

    public function forVariant(ProductVariant $variant): static
    {
        return $this->state(fn (): array => [
            'product_id' => $variant->product_id,
            'product_variant_id' => $variant->id,
        ]);
    }
}
