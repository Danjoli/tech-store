<?php

namespace Database\Seeders\Development;

use App\Enums\ProductStatus;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class RemoteCatalogSeeder extends Seeder
{
    public function run(): void
    {
        $catalog = $this->catalog();

        foreach ($catalog as $position => $item) {
            $brand = Brand::query()->updateOrCreate(
                ['slug' => $item['brand']['slug']],
                [
                    'name' => $item['brand']['name'],
                    'description' => $item['brand']['description'],
                    'logo_path' => null,
                    'website_url' => null,
                    'is_active' => true,
                ],
            );

            $category = Category::query()->updateOrCreate(
                ['slug' => $item['category']['slug']],
                [
                    'parent_id' => null,
                    'name' => $item['category']['name'],
                    'description' => $item['category']['description'],
                    'image_path' => null,
                    'sort_order' => $item['category']['sort_order'],
                    'is_active' => true,
                ],
            );

            $product = Product::query()->updateOrCreate(
                ['slug' => $item['product']['slug']],
                [
                    'brand_id' => $brand->id,
                    'category_id' => $category->id,
                    'name' => $item['product']['name'],
                    'short_description' => $item['product']['short_description'],
                    'description' => $item['product']['description'],
                    'status' => ProductStatus::ACTIVE,
                    'is_featured' => true,
                    'warranty_months' => $item['product']['warranty_months'],
                    'weight' => $item['product']['weight'],
                    'height' => $item['product']['height'],
                    'width' => $item['product']['width'],
                    'length' => $item['product']['length'],
                    'seo_title' => $item['product']['seo_title'],
                    'seo_description' => $item['product']['seo_description'],
                    'published_at' => now(),
                ],
            );

            ProductVariant::query()->updateOrCreate(
                ['sku' => $item['variant']['sku']],
                [
                    'product_id' => $product->id,
                    'name' => $item['variant']['name'],
                    'barcode' => null,
                    'price' => $item['variant']['price'],
                    'sale_price' => $item['variant']['sale_price'],
                    'cost_price' => $item['variant']['cost_price'],
                    'stock' => $item['variant']['stock'],
                    'reserved_stock' => 0,
                    'low_stock_threshold' => 5,
                    'attributes' => $item['variant']['attributes'],
                    'is_default' => true,
                    'is_active' => true,
                ],
            );

            $this->storeSpecifications(
                $product,
                $item['specifications'],
            );

            $this->storeImage(
                product: $product,
                imageUrl: $item['image_url'],
                altText: $item['product']['name'],
                position: $position,
            );
        }
    }

    /**
     * @param  array<int, array<string, mixed>>  $specifications
     */
    private function storeSpecifications(
        Product $product,
        array $specifications,
    ): void {
        foreach ($specifications as $specification) {
            DB::table('product_specifications')->updateOrInsert(
                [
                    'product_id' => $product->id,
                    'group_name' => $specification['group_name'],
                    'name' => $specification['name'],
                ],
                [
                    'value' => $specification['value'],
                    'unit' => $specification['unit'],
                    'sort_order' => $specification['sort_order'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            );
        }
    }

    private function storeImage(
        Product $product,
        string $imageUrl,
        string $altText,
        int $position,
    ): void {
        $path = "products/{$product->id}/catalog-main.jpg";

        if (! Storage::disk('public')->exists($path)) {
            $response = Http::retry(
                times: 3,
                sleepMilliseconds: 500,
            )
                ->timeout(30)
                ->get($imageUrl);

            if (! $response->successful()) {
                throw new RuntimeException(
                    "Não foi possível baixar a imagem de {$product->name}.",
                );
            }

            Storage::disk('public')->put(
                $path,
                $response->body(),
            );
        }

        $product->images()
            ->where('path', '!=', $path)
            ->update(['is_primary' => false]);

        ProductImage::query()->updateOrCreate(
            [
                'product_id' => $product->id,
                'path' => $path,
            ],
            [
                'product_variant_id' => null,
                'alt_text' => $altText,
                'sort_order' => $position,
                'is_primary' => true,
            ],
        );
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function catalog(): array
    {
        return [
            [
                'brand' => [
                    'name' => 'Apex',
                    'slug' => 'apex',
                    'description' => 'Notebooks de alta performance.',
                ],
                'category' => [
                    'name' => 'Notebooks',
                    'slug' => 'notebooks',
                    'description' => 'Notebooks para trabalho, criação e jogos.',
                    'sort_order' => 1,
                ],
                'product' => [
                    'name' => 'Notebook Apex Pro 15',
                    'slug' => 'notebook-apex-pro-15',
                    'short_description' => 'Notebook de alto desempenho com tela QHD e gráficos RTX.',
                    'description' => 'O Apex Pro 15 combina desempenho para jogos, criação de conteúdo e produtividade. Possui processador Intel Core i7, gráficos RTX, memória DDR5 e armazenamento NVMe de alta velocidade.',
                    'warranty_months' => 12,
                    'weight' => 2.200,
                    'height' => 2.20,
                    'width' => 35.80,
                    'length' => 25.90,
                    'seo_title' => 'Notebook Apex Pro 15 | Tech Store',
                    'seo_description' => 'Notebook Apex Pro 15 com Intel Core i7, RTX 4060, 16 GB DDR5 e SSD NVMe de 1 TB.',
                ],
                'variant' => [
                    'name' => '16 GB / 1 TB',
                    'sku' => 'APEX-PRO15-16-1TB',
                    'price' => 8299.90,
                    'sale_price' => 7499.90,
                    'cost_price' => 5900.00,
                    'stock' => 12,
                    'attributes' => [
                        'Memória' => '16 GB DDR5',
                        'Armazenamento' => '1 TB NVMe',
                        'Cor' => 'Preto',
                    ],
                ],
                'specifications' => [
                    $this->spec(
                        'Desempenho',
                        'Processador',
                        'Intel Core i7',
                        1,
                    ),
                    $this->spec(
                        'Desempenho',
                        'Placa de vídeo',
                        'NVIDIA GeForce RTX 4060',
                        2,
                    ),
                    $this->spec(
                        'Memória',
                        'Memória RAM',
                        '16',
                        3,
                        'GB DDR5',
                    ),
                    $this->spec(
                        'Armazenamento',
                        'SSD',
                        '1',
                        4,
                        'TB NVMe',
                    ),
                    $this->spec(
                        'Tela',
                        'Tela',
                        '15,6 polegadas QHD 165 Hz',
                        5,
                    ),
                ],
                'image_url' => 'https://images.unsplash.com/photo-1593642632823-8f785ba67e45?auto=format&fit=crop&w=1400&q=90',
            ],
            [
                'brand' => [
                    'name' => 'UltraView',
                    'slug' => 'ultraview',
                    'description' => 'Monitores para produtividade e jogos.',
                ],
                'category' => [
                    'name' => 'Monitores',
                    'slug' => 'monitores',
                    'description' => 'Monitores de alta resolução e frequência.',
                    'sort_order' => 2,
                ],
                'product' => [
                    'name' => 'Monitor UltraView 27” QHD',
                    'slug' => 'monitor-ultraview-27-qhd',
                    'short_description' => 'Monitor QHD de 27 polegadas com painel IPS e 165 Hz.',
                    'description' => 'O UltraView 27 oferece alta definição, cores precisas e movimento fluido. É ideal para jogos, edição, produtividade e setups profissionais.',
                    'warranty_months' => 24,
                    'weight' => 5.800,
                    'height' => 45.00,
                    'width' => 61.50,
                    'length' => 21.00,
                    'seo_title' => 'Monitor UltraView 27 QHD | Tech Store',
                    'seo_description' => 'Monitor UltraView de 27 polegadas, resolução QHD, painel IPS e frequência de 165 Hz.',
                ],
                'variant' => [
                    'name' => '27” QHD',
                    'sku' => 'ULTRAVIEW-27-QHD',
                    'price' => 1899.90,
                    'sale_price' => null,
                    'cost_price' => 1180.00,
                    'stock' => 18,
                    'attributes' => [
                        'Tamanho' => '27 polegadas',
                        'Resolução' => 'QHD',
                        'Cor' => 'Preto',
                    ],
                ],
                'specifications' => [
                    $this->spec(
                        'Tela',
                        'Tamanho',
                        '27',
                        1,
                        'polegadas',
                    ),
                    $this->spec(
                        'Tela',
                        'Resolução',
                        '2560 × 1440',
                        2,
                        'QHD',
                    ),
                    $this->spec(
                        'Tela',
                        'Painel',
                        'IPS',
                        3,
                    ),
                    $this->spec(
                        'Desempenho',
                        'Frequência',
                        '165',
                        4,
                        'Hz',
                    ),
                    $this->spec(
                        'Desempenho',
                        'Tempo de resposta',
                        '1',
                        5,
                        'ms',
                    ),
                ],
                'image_url' => 'https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?auto=format&fit=crop&w=1400&q=90',
            ],
            [
                'brand' => [
                    'name' => 'Pulse',
                    'slug' => 'pulse',
                    'description' => 'Periféricos para precisão e desempenho.',
                ],
                'category' => [
                    'name' => 'Periféricos',
                    'slug' => 'perifericos',
                    'description' => 'Teclados, mouses e acessórios.',
                    'sort_order' => 3,
                ],
                'product' => [
                    'name' => 'Teclado Mecânico Pulse 75',
                    'slug' => 'teclado-mecanico-pulse-75',
                    'short_description' => 'Teclado mecânico compacto com iluminação RGB.',
                    'description' => 'O Pulse 75 oferece construção compacta, resposta rápida e conforto para longas sessões. Seu formato preserva as principais teclas sem ocupar espaço excessivo no setup.',
                    'warranty_months' => 12,
                    'weight' => 0.850,
                    'height' => 4.00,
                    'width' => 33.00,
                    'length' => 14.00,
                    'seo_title' => 'Teclado Mecânico Pulse 75 | Tech Store',
                    'seo_description' => 'Teclado mecânico Pulse 75 compacto, padrão ABNT2 e iluminação RGB.',
                ],
                'variant' => [
                    'name' => 'Switch Brown',
                    'sku' => 'PULSE-75-BROWN',
                    'price' => 529.90,
                    'sale_price' => 449.90,
                    'cost_price' => 270.00,
                    'stock' => 30,
                    'attributes' => [
                        'Switch' => 'Brown',
                        'Layout' => 'ABNT2',
                        'Cor' => 'Preto',
                    ],
                ],
                'specifications' => [
                    $this->spec(
                        'Teclado',
                        'Formato',
                        '75%',
                        1,
                    ),
                    $this->spec(
                        'Teclado',
                        'Switch',
                        'Mecânico Brown',
                        2,
                    ),
                    $this->spec(
                        'Teclado',
                        'Layout',
                        'ABNT2',
                        3,
                    ),
                    $this->spec(
                        'Iluminação',
                        'Iluminação',
                        'RGB configurável',
                        4,
                    ),
                    $this->spec(
                        'Conectividade',
                        'Conexão',
                        'USB-C',
                        5,
                    ),
                ],
                'image_url' => 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?auto=format&fit=crop&w=1400&q=90',
            ],
            [
                'brand' => [
                    'name' => 'Core',
                    'slug' => 'core',
                    'description' => 'Computadores de alta performance.',
                ],
                'category' => [
                    'name' => 'Computadores',
                    'slug' => 'computadores',
                    'description' => 'Computadores montados e estações de trabalho.',
                    'sort_order' => 4,
                ],
                'product' => [
                    'name' => 'Setup Core RTX Creator',
                    'slug' => 'setup-core-rtx-creator',
                    'short_description' => 'Computador completo para criação, produtividade e jogos.',
                    'description' => 'O Core RTX Creator foi projetado para edição, renderização, desenvolvimento e jogos. Combina processador Ryzen 7, RTX 4070, 32 GB de memória e SSD NVMe.',
                    'warranty_months' => 24,
                    'weight' => 12.500,
                    'height' => 47.00,
                    'width' => 22.00,
                    'length' => 45.00,
                    'seo_title' => 'Setup Core RTX Creator | Tech Store',
                    'seo_description' => 'Computador Core RTX Creator com Ryzen 7, RTX 4070, 32 GB de memória e SSD de 1 TB.',
                ],
                'variant' => [
                    'name' => 'RTX 4070 / 32 GB',
                    'sku' => 'CORE-RTX4070-32GB',
                    'price' => 9999.90,
                    'sale_price' => null,
                    'cost_price' => 7200.00,
                    'stock' => 8,
                    'attributes' => [
                        'Placa de vídeo' => 'RTX 4070',
                        'Memória' => '32 GB DDR5',
                        'Armazenamento' => '1 TB NVMe',
                    ],
                ],
                'specifications' => [
                    $this->spec(
                        'Desempenho',
                        'Processador',
                        'AMD Ryzen 7',
                        1,
                    ),
                    $this->spec(
                        'Desempenho',
                        'Placa de vídeo',
                        'NVIDIA GeForce RTX 4070',
                        2,
                    ),
                    $this->spec(
                        'Memória',
                        'Memória RAM',
                        '32',
                        3,
                        'GB DDR5',
                    ),
                    $this->spec(
                        'Armazenamento',
                        'SSD',
                        '1',
                        4,
                        'TB NVMe',
                    ),
                    $this->spec(
                        'Sistema',
                        'Sistema operacional',
                        'Windows 11',
                        5,
                    ),
                ],
                'image_url' => 'https://images.unsplash.com/photo-1593640408182-31c70c8268f5?auto=format&fit=crop&w=1400&q=90',
            ],
            [
                'brand' => [
                    'name' => 'Vector',
                    'slug' => 'vector',
                    'description' => 'Periféricos leves e precisos.',
                ],
                'category' => [
                    'name' => 'Periféricos',
                    'slug' => 'perifericos',
                    'description' => 'Teclados, mouses e acessórios.',
                    'sort_order' => 3,
                ],
                'product' => [
                    'name' => 'Mouse Vector Pro Wireless',
                    'slug' => 'mouse-vector-pro-wireless',
                    'short_description' => 'Mouse sem fio leve e preciso para jogos.',
                    'description' => 'O Vector Pro Wireless combina baixo peso, sensor de alta precisão e conexão estável para trabalho competitivo e jogos.',
                    'warranty_months' => 12,
                    'weight' => 0.069,
                    'height' => 4.00,
                    'width' => 6.50,
                    'length' => 12.50,
                    'seo_title' => 'Mouse Vector Pro Wireless | Tech Store',
                    'seo_description' => 'Mouse Vector Pro Wireless com sensor de 26.000 DPI, conexão sem fio e peso de 69 gramas.',
                ],
                'variant' => [
                    'name' => 'Preto',
                    'sku' => 'VECTOR-PRO-WL-BLK',
                    'price' => 369.90,
                    'sale_price' => null,
                    'cost_price' => 185.00,
                    'stock' => 42,
                    'attributes' => [
                        'Cor' => 'Preto',
                        'Conexão' => 'Sem fio',
                        'Sensor' => '26.000 DPI',
                    ],
                ],
                'specifications' => [
                    $this->spec(
                        'Desempenho',
                        'Sensor',
                        '26.000',
                        1,
                        'DPI',
                    ),
                    $this->spec(
                        'Estrutura',
                        'Peso',
                        '69',
                        2,
                        'g',
                    ),
                    $this->spec(
                        'Conectividade',
                        'Conexão',
                        '2,4 GHz sem fio',
                        3,
                    ),
                    $this->spec(
                        'Energia',
                        'Autonomia',
                        '70',
                        4,
                        'horas',
                    ),
                ],
                'image_url' => 'https://images.unsplash.com/photo-1527814050087-3793815479db?auto=format&fit=crop&w=1400&q=90',
            ],
            [
                'brand' => [
                    'name' => 'Orbit',
                    'slug' => 'orbit',
                    'description' => 'Áudio imersivo para jogos e comunicação.',
                ],
                'category' => [
                    'name' => 'Áudio',
                    'slug' => 'audio',
                    'description' => 'Headsets, fones e equipamentos de áudio.',
                    'sort_order' => 5,
                ],
                'product' => [
                    'name' => 'Headset Orbit 7.1',
                    'slug' => 'headset-orbit-7-1',
                    'short_description' => 'Headset com áudio surround 7.1 e microfone.',
                    'description' => 'O Orbit 7.1 oferece áudio imersivo, comunicação clara e conforto para jogos, reuniões e consumo de conteúdo.',
                    'warranty_months' => 12,
                    'weight' => 0.320,
                    'height' => 20.00,
                    'width' => 18.00,
                    'length' => 9.00,
                    'seo_title' => 'Headset Orbit 7.1 | Tech Store',
                    'seo_description' => 'Headset Orbit com áudio surround 7.1, microfone integrado e conexão USB.',
                ],
                'variant' => [
                    'name' => 'Preto',
                    'sku' => 'ORBIT-71-BLK',
                    'price' => 699.90,
                    'sale_price' => 599.90,
                    'cost_price' => 340.00,
                    'stock' => 25,
                    'attributes' => [
                        'Cor' => 'Preto',
                        'Conexão' => 'USB',
                        'Áudio' => 'Surround 7.1',
                    ],
                ],
                'specifications' => [
                    $this->spec(
                        'Áudio',
                        'Sistema',
                        'Surround virtual 7.1',
                        1,
                    ),
                    $this->spec(
                        'Áudio',
                        'Drivers',
                        '50',
                        2,
                        'mm',
                    ),
                    $this->spec(
                        'Microfone',
                        'Microfone',
                        'Removível com redução de ruído',
                        3,
                    ),
                    $this->spec(
                        'Conectividade',
                        'Conexão',
                        'USB',
                        4,
                    ),
                ],
                'image_url' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=1400&q=90',
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function spec(
        string $group,
        string $name,
        string $value,
        int $sortOrder,
        ?string $unit = null,
    ): array {
        return [
            'group_name' => $group,
            'name' => $name,
            'value' => $value,
            'unit' => $unit,
            'sort_order' => $sortOrder,
        ];
    }
}
