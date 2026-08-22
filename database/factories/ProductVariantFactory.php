<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProductVariant>
 */
class ProductVariantFactory extends Factory
{
    protected $model = ProductVariant::class;

    public function definition(): array
    {
        $price = fake()->randomFloat(2, 150, 15000);

        $hasDiscount = fake()->boolean(30);

        return [
            'product_id' => Product::factory(),

            'name' => fake()->randomElement([
                'Padrão',
                'Preto',
                'Branco',
                '16 GB / 512 GB',
                '32 GB / 1 TB',
            ]),

            'sku' => strtoupper(
                fake()->unique()->bothify('TS-???-#####')
            ),

            'barcode' => fake()->optional()->ean13(),

            'price' => $price,

            'sale_price' => $hasDiscount
                ? round($price * fake()->randomFloat(2, 0.75, 0.95), 2)
                : null,

            'cost_price' => round(
                $price * fake()->randomFloat(2, 0.45, 0.70),
                2
            ),

            'stock' => fake()->numberBetween(0, 100),
            'reserved_stock' => 0,
            'low_stock_threshold' => 5,

            'attributes' => [
                'color' => fake()->randomElement([
                    'Preto',
                    'Branco',
                    'Cinza',
                ]),
            ],

            'is_default' => false,
            'is_active' => true,
        ];
    }

    public function default(): static
    {
        return $this->state(fn (): array => [
            'is_default' => true,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (): array => [
            'is_active' => false,
        ]);
    }

    public function outOfStock(): static
    {
        return $this->state(fn (): array => [
            'stock' => 0,
            'reserved_stock' => 0,
        ]);
    }
}
