<?php

namespace Database\Factories;

use App\Enums\ProductStatus;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $name = ucfirst(
            fake()->unique()->words(4, true)
        );

        return [
            'brand_id' => Brand::factory(),
            'category_id' => Category::factory(),

            'name' => $name,
            'slug' => Str::slug($name).'-'.fake()->unique()->numerify('####'),

            'short_description' => fake()->sentence(12),
            'description' => fake()->paragraphs(4, true),

            'status' => ProductStatus::ACTIVE,
            'is_featured' => fake()->boolean(25),

            'warranty_months' => fake()->randomElement([
                3,
                6,
                12,
                24,
                36,
            ]),

            'weight' => fake()->randomFloat(3, 0.100, 15),
            'height' => fake()->randomFloat(2, 1, 60),
            'width' => fake()->randomFloat(2, 1, 80),
            'length' => fake()->randomFloat(2, 1, 80),

            'seo_title' => $name,
            'seo_description' => fake()->sentence(18),

            'published_at' => now(),
        ];
    }

    public function draft(): static
    {
        return $this->state(fn (): array => [
            'status' => ProductStatus::DRAFT,
            'published_at' => null,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (): array => [
            'status' => ProductStatus::INACTIVE,
        ]);
    }

    public function featured(): static
    {
        return $this->state(fn (): array => [
            'is_featured' => true,
        ]);
    }
}
