<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        $brands = [
            [
                'name' => 'Apex',
                'slug' => 'apex',
                'description' => 'Notebooks de alta performance.',
                'website_url' => null,
                'logo_path' => null,
                'is_active' => true,
            ],
            [
                'name' => 'CoreForge',
                'slug' => 'coreforge',
                'description' => 'Computadores para jogos e criação profissional.',
                'website_url' => null,
                'logo_path' => null,
                'is_active' => true,
            ],
            [
                'name' => 'UltraView',
                'slug' => 'ultraview',
                'description' => 'Monitores para produtividade e entretenimento.',
                'website_url' => null,
                'logo_path' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Pulse',
                'slug' => 'pulse',
                'description' => 'Periféricos de precisão para jogadores.',
                'website_url' => null,
                'logo_path' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Vector',
                'slug' => 'vector',
                'description' => 'Acessórios e periféricos sem fio.',
                'website_url' => null,
                'logo_path' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Orbit',
                'slug' => 'orbit',
                'description' => 'Equipamentos de áudio de alta qualidade.',
                'website_url' => null,
                'logo_path' => null,
                'is_active' => true,
            ],
        ];

        foreach ($brands as $brand) {
            Brand::updateOrCreate(
                ['slug' => $brand['slug']],
                $brand
            );
        }
    }
}
