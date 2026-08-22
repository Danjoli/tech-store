<?php

namespace Database\Seeders;

use App\Enums\ProductStatus;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use RuntimeException;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->products() as $data) {
            $brand = Brand::where('slug', $data['brand'])->first();

            $category = Category::where(
                'slug',
                $data['category']
            )->first();

            if (! $brand || ! $category) {
                throw new RuntimeException(
                    "Marca ou categoria não encontrada para {$data['name']}."
                );
            }

            $product = Product::updateOrCreate(
                [
                    'slug' => $data['slug'],
                ],
                [
                    'brand_id' => $brand->id,
                    'category_id' => $category->id,
                    'name' => $data['name'],
                    'short_description' => $data['short_description'],
                    'description' => $data['description'],
                    'status' => ProductStatus::ACTIVE,
                    'is_featured' => $data['is_featured'],
                    'warranty_months' => $data['warranty_months'],
                    'weight' => $data['weight'],
                    'height' => $data['height'],
                    'width' => $data['width'],
                    'length' => $data['length'],
                    'seo_title' => $data['name'],
                    'seo_description' => $data['short_description'],
                    'published_at' => now(),
                ]
            );

            /*
             * As imagens devem ser apagadas antes das variantes,
             * pois podem estar associadas a uma variante.
             */
            $product->images()->delete();
            $product->specifications()->delete();
            $product->variants()->delete();

            foreach ($data['variants'] as $variant) {
                $product->variants()->create($variant);
            }

            foreach ($data['images'] as $image) {
                $product->images()->create($image);
            }

            foreach ($data['specifications'] as $specification) {
                $product->specifications()->create($specification);
            }
        }
    }

    private function products(): array
    {
        return [
            [
                'brand' => 'apex',
                'category' => 'notebooks-gamers',
                'name' => 'Notebook Apex Pro 15',
                'slug' => 'notebook-apex-pro-15',
                'short_description' =>
                    'Notebook gamer com alto desempenho para jogos e criação.',
                'description' =>
                    'Equipado com processador de alta performance, placa de vídeo dedicada e tela de alta frequência.',
                'is_featured' => true,
                'warranty_months' => 12,
                'weight' => 2.300,
                'height' => 2.40,
                'width' => 35.90,
                'length' => 25.90,

                'variants' => [
                    [
                        'name' => '16 GB / 512 GB',
                        'sku' => 'APEX-PRO15-16-512',
                        'barcode' => null,
                        'price' => 8299.90,
                        'sale_price' => 7499.90,
                        'cost_price' => 5700.00,
                        'stock' => 15,
                        'reserved_stock' => 0,
                        'low_stock_threshold' => 5,
                        'attributes' => [
                            'ram' => '16 GB',
                            'storage' => '512 GB SSD',
                            'color' => 'Preto',
                        ],
                        'is_default' => true,
                        'is_active' => true,
                    ],
                    [
                        'name' => '32 GB / 1 TB',
                        'sku' => 'APEX-PRO15-32-1TB',
                        'barcode' => null,
                        'price' => 9499.90,
                        'sale_price' => null,
                        'cost_price' => 6600.00,
                        'stock' => 8,
                        'reserved_stock' => 0,
                        'low_stock_threshold' => 3,
                        'attributes' => [
                            'ram' => '32 GB',
                            'storage' => '1 TB SSD',
                            'color' => 'Preto',
                        ],
                        'is_default' => false,
                        'is_active' => true,
                    ],
                ],

                'images' => [
                    [
                        'path' => 'products/notebook-apex-pro-15.webp',
                        'alt_text' => 'Notebook Apex Pro 15',
                        'sort_order' => 0,
                        'is_primary' => true,
                    ],
                ],

                'specifications' => [
                    [
                        'group_name' => 'Processador',
                        'name' => 'Modelo',
                        'value' => 'Ryzen 7',
                        'unit' => null,
                        'sort_order' => 1,
                    ],
                    [
                        'group_name' => 'Tela',
                        'name' => 'Tamanho',
                        'value' => '15.6',
                        'unit' => 'polegadas',
                        'sort_order' => 2,
                    ],
                    [
                        'group_name' => 'Tela',
                        'name' => 'Frequência',
                        'value' => '165',
                        'unit' => 'Hz',
                        'sort_order' => 3,
                    ],
                ],
            ],

            [
                'brand' => 'ultraview',
                'category' => 'monitores',
                'name' => 'Monitor UltraView 27 QHD',
                'slug' => 'monitor-ultraview-27-qhd',
                'short_description' =>
                    'Monitor QHD de 27 polegadas para jogos e produtividade.',
                'description' =>
                    'Painel de alta definição com cores precisas e alta taxa de atualização.',
                'is_featured' => true,
                'warranty_months' => 24,
                'weight' => 5.200,
                'height' => 46.00,
                'width' => 61.00,
                'length' => 21.00,

                'variants' => [
                    [
                        'name' => 'Padrão',
                        'sku' => 'ULTRAVIEW-27-QHD',
                        'barcode' => null,
                        'price' => 2199.90,
                        'sale_price' => 1899.90,
                        'cost_price' => 1250.00,
                        'stock' => 30,
                        'reserved_stock' => 0,
                        'low_stock_threshold' => 5,
                        'attributes' => [
                            'color' => 'Preto',
                        ],
                        'is_default' => true,
                        'is_active' => true,
                    ],
                ],

                'images' => [
                    [
                        'path' => 'products/monitor-ultraview-27-qhd.webp',
                        'alt_text' => 'Monitor UltraView 27 QHD',
                        'sort_order' => 0,
                        'is_primary' => true,
                    ],
                ],

                'specifications' => [
                    [
                        'group_name' => 'Tela',
                        'name' => 'Resolução',
                        'value' => '2560 x 1440',
                        'unit' => null,
                        'sort_order' => 1,
                    ],
                    [
                        'group_name' => 'Tela',
                        'name' => 'Frequência',
                        'value' => '180',
                        'unit' => 'Hz',
                        'sort_order' => 2,
                    ],
                    [
                        'group_name' => 'Conectividade',
                        'name' => 'Entradas',
                        'value' => 'HDMI e DisplayPort',
                        'unit' => null,
                        'sort_order' => 3,
                    ],
                ],
            ],

            [
                'brand' => 'pulse',
                'category' => 'teclados',
                'name' => 'Teclado Mecânico Pulse 75',
                'slug' => 'teclado-mecanico-pulse-75',
                'short_description' =>
                    'Teclado mecânico compacto com iluminação RGB.',
                'description' =>
                    'Formato 75%, switches mecânicos e conexão USB-C removível.',
                'is_featured' => true,
                'warranty_months' => 12,
                'weight' => 0.850,
                'height' => 4.00,
                'width' => 33.00,
                'length' => 14.00,

                'variants' => [
                    [
                        'name' => 'Preto',
                        'sku' => 'PULSE-75-BLACK',
                        'barcode' => null,
                        'price' => 529.90,
                        'sale_price' => 449.90,
                        'cost_price' => 270.00,
                        'stock' => 50,
                        'reserved_stock' => 0,
                        'low_stock_threshold' => 8,
                        'attributes' => [
                            'color' => 'Preto',
                            'switch' => 'Linear',
                        ],
                        'is_default' => true,
                        'is_active' => true,
                    ],
                    [
                        'name' => 'Branco',
                        'sku' => 'PULSE-75-WHITE',
                        'barcode' => null,
                        'price' => 549.90,
                        'sale_price' => null,
                        'cost_price' => 280.00,
                        'stock' => 25,
                        'reserved_stock' => 0,
                        'low_stock_threshold' => 5,
                        'attributes' => [
                            'color' => 'Branco',
                            'switch' => 'Linear',
                        ],
                        'is_default' => false,
                        'is_active' => true,
                    ],
                ],

                'images' => [
                    [
                        'path' => 'products/teclado-pulse-75.webp',
                        'alt_text' => 'Teclado Mecânico Pulse 75',
                        'sort_order' => 0,
                        'is_primary' => true,
                    ],
                ],

                'specifications' => [
                    [
                        'group_name' => 'Características',
                        'name' => 'Formato',
                        'value' => '75%',
                        'unit' => null,
                        'sort_order' => 1,
                    ],
                    [
                        'group_name' => 'Conectividade',
                        'name' => 'Interface',
                        'value' => 'USB-C',
                        'unit' => null,
                        'sort_order' => 2,
                    ],
                ],
            ],

            [
                'brand' => 'coreforge',
                'category' => 'workstations',
                'name' => 'Setup Core RTX Creator',
                'slug' => 'setup-core-rtx-creator',
                'short_description' =>
                    'Computador de alta performance para criação profissional.',
                'description' =>
                    'Workstation desenvolvida para edição, renderização, programação e jogos.',
                'is_featured' => true,
                'warranty_months' => 24,
                'weight' => 12.500,
                'height' => 48.00,
                'width' => 22.00,
                'length' => 46.00,

                'variants' => [
                    [
                        'name' => '32 GB / 1 TB',
                        'sku' => 'CORE-RTX-32-1TB',
                        'barcode' => null,
                        'price' => 10999.90,
                        'sale_price' => 9999.90,
                        'cost_price' => 7600.00,
                        'stock' => 10,
                        'reserved_stock' => 0,
                        'low_stock_threshold' => 3,
                        'attributes' => [
                            'ram' => '32 GB',
                            'storage' => '1 TB SSD',
                            'color' => 'Preto',
                        ],
                        'is_default' => true,
                        'is_active' => true,
                    ],
                    [
                        'name' => '64 GB / 2 TB',
                        'sku' => 'CORE-RTX-64-2TB',
                        'barcode' => null,
                        'price' => 13499.90,
                        'sale_price' => null,
                        'cost_price' => 9200.00,
                        'stock' => 5,
                        'reserved_stock' => 0,
                        'low_stock_threshold' => 2,
                        'attributes' => [
                            'ram' => '64 GB',
                            'storage' => '2 TB SSD',
                            'color' => 'Preto',
                        ],
                        'is_default' => false,
                        'is_active' => true,
                    ],
                ],

                'images' => [
                    [
                        'path' => 'products/setup-core-rtx-creator.webp',
                        'alt_text' => 'Setup Core RTX Creator',
                        'sort_order' => 0,
                        'is_primary' => true,
                    ],
                ],

                'specifications' => [
                    [
                        'group_name' => 'Processador',
                        'name' => 'Modelo',
                        'value' => 'Ryzen 9',
                        'unit' => null,
                        'sort_order' => 1,
                    ],
                    [
                        'group_name' => 'Vídeo',
                        'name' => 'Placa de vídeo',
                        'value' => 'RTX Series',
                        'unit' => null,
                        'sort_order' => 2,
                    ],
                ],
            ],

            [
                'brand' => 'vector',
                'category' => 'mouses',
                'name' => 'Mouse Vector Pro Wireless',
                'slug' => 'mouse-vector-pro-wireless',
                'short_description' =>
                    'Mouse sem fio leve e preciso para jogos.',
                'description' =>
                    'Sensor de alta precisão, baixa latência e bateria de longa duração.',
                'is_featured' => false,
                'warranty_months' => 12,
                'weight' => 0.065,
                'height' => 4.00,
                'width' => 6.30,
                'length' => 12.00,

                'variants' => [
                    [
                        'name' => 'Preto',
                        'sku' => 'VECTOR-PRO-BLACK',
                        'barcode' => null,
                        'price' => 369.90,
                        'sale_price' => null,
                        'cost_price' => 190.00,
                        'stock' => 70,
                        'reserved_stock' => 0,
                        'low_stock_threshold' => 10,
                        'attributes' => [
                            'color' => 'Preto',
                        ],
                        'is_default' => true,
                        'is_active' => true,
                    ],
                    [
                        'name' => 'Branco',
                        'sku' => 'VECTOR-PRO-WHITE',
                        'barcode' => null,
                        'price' => 389.90,
                        'sale_price' => null,
                        'cost_price' => 200.00,
                        'stock' => 35,
                        'reserved_stock' => 0,
                        'low_stock_threshold' => 8,
                        'attributes' => [
                            'color' => 'Branco',
                        ],
                        'is_default' => false,
                        'is_active' => true,
                    ],
                ],

                'images' => [
                    [
                        'path' => 'products/mouse-vector-pro.webp',
                        'alt_text' => 'Mouse Vector Pro Wireless',
                        'sort_order' => 0,
                        'is_primary' => true,
                    ],
                ],

                'specifications' => [
                    [
                        'group_name' => 'Sensor',
                        'name' => 'Resolução máxima',
                        'value' => '26000',
                        'unit' => 'DPI',
                        'sort_order' => 1,
                    ],
                    [
                        'group_name' => 'Bateria',
                        'name' => 'Autonomia',
                        'value' => '80',
                        'unit' => 'horas',
                        'sort_order' => 2,
                    ],
                ],
            ],

            [
                'brand' => 'orbit',
                'category' => 'headsets',
                'name' => 'Headset Orbit 7.1',
                'slug' => 'headset-orbit-7-1',
                'short_description' =>
                    'Headset com áudio virtual 7.1 e microfone removível.',
                'description' =>
                    'Áudio imersivo, construção confortável e comunicação clara.',
                'is_featured' => false,
                'warranty_months' => 12,
                'weight' => 0.320,
                'height' => 20.00,
                'width' => 18.00,
                'length' => 9.00,

                'variants' => [
                    [
                        'name' => 'Preto',
                        'sku' => 'ORBIT-71-BLACK',
                        'barcode' => null,
                        'price' => 699.90,
                        'sale_price' => 599.90,
                        'cost_price' => 350.00,
                        'stock' => 40,
                        'reserved_stock' => 0,
                        'low_stock_threshold' => 6,
                        'attributes' => [
                            'color' => 'Preto',
                        ],
                        'is_default' => true,
                        'is_active' => true,
                    ],
                ],

                'images' => [
                    [
                        'path' => 'products/headset-orbit-7-1.webp',
                        'alt_text' => 'Headset Orbit 7.1',
                        'sort_order' => 0,
                        'is_primary' => true,
                    ],
                ],

                'specifications' => [
                    [
                        'group_name' => 'Áudio',
                        'name' => 'Sistema',
                        'value' => 'Virtual 7.1',
                        'unit' => null,
                        'sort_order' => 1,
                    ],
                    [
                        'group_name' => 'Conectividade',
                        'name' => 'Interface',
                        'value' => 'USB',
                        'unit' => null,
                        'sort_order' => 2,
                    ],
                ],
            ],
        ];
    }
}
