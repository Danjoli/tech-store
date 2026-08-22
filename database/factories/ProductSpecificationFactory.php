<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductSpecification;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProductSpecification>
 */
class ProductSpecificationFactory extends Factory
{
    protected $model = ProductSpecification::class;

    public function definition(): array
    {
        $specification = fake()->randomElement([
            [
                'group_name' => 'Processador',
                'name' => 'Modelo',
                'value' => 'Ryzen 7',
                'unit' => null,
            ],
            [
                'group_name' => 'Memória',
                'name' => 'Capacidade',
                'value' => '16',
                'unit' => 'GB',
            ],
            [
                'group_name' => 'Armazenamento',
                'name' => 'Capacidade',
                'value' => '1',
                'unit' => 'TB',
            ],
            [
                'group_name' => 'Tela',
                'name' => 'Tamanho',
                'value' => '27',
                'unit' => 'polegadas',
            ],
            [
                'group_name' => 'Conectividade',
                'name' => 'Interface',
                'value' => 'USB-C',
                'unit' => null,
            ],
        ]);

        return [
            'product_id' => Product::factory(),

            ...$specification,

            'sort_order' => fake()->numberBetween(0, 20),
        ];
    }
}
