<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $notebooks = $this->createCategory(
            name: 'Notebooks',
            slug: 'notebooks',
            description: 'Notebooks para trabalho, estudo e jogos.',
            sortOrder: 1,
        );

        $computers = $this->createCategory(
            name: 'Computadores',
            slug: 'computadores',
            description: 'Computadores completos e workstations.',
            sortOrder: 2,
        );

        $monitors = $this->createCategory(
            name: 'Monitores',
            slug: 'monitores',
            description: 'Monitores para jogos e produtividade.',
            sortOrder: 3,
        );

        $peripherals = $this->createCategory(
            name: 'Periféricos',
            slug: 'perifericos',
            description: 'Periféricos para completar seu setup.',
            sortOrder: 4,
        );

        $audio = $this->createCategory(
            name: 'Áudio',
            slug: 'audio',
            description: 'Equipamentos de áudio e comunicação.',
            sortOrder: 5,
        );

        $components = $this->createCategory(
            name: 'Componentes',
            slug: 'componentes',
            description: 'Peças para montar ou atualizar seu computador.',
            sortOrder: 6,
        );

        $this->createCategory(
            name: 'Notebooks gamers',
            slug: 'notebooks-gamers',
            description: 'Notebooks com alto desempenho gráfico.',
            sortOrder: 1,
            parentId: $notebooks->id,
        );

        $this->createCategory(
            name: 'Workstations',
            slug: 'workstations',
            description: 'Computadores para criação e trabalho profissional.',
            sortOrder: 1,
            parentId: $computers->id,
        );

        $this->createCategory(
            name: 'Teclados',
            slug: 'teclados',
            description: 'Teclados mecânicos e convencionais.',
            sortOrder: 1,
            parentId: $peripherals->id,
        );

        $this->createCategory(
            name: 'Mouses',
            slug: 'mouses',
            description: 'Mouses para produtividade e jogos.',
            sortOrder: 2,
            parentId: $peripherals->id,
        );

        $this->createCategory(
            name: 'Headsets',
            slug: 'headsets',
            description: 'Headsets para jogos e comunicação.',
            sortOrder: 1,
            parentId: $audio->id,
        );

        $this->createCategory(
            name: 'Processadores',
            slug: 'processadores',
            description: 'Processadores para computadores.',
            sortOrder: 1,
            parentId: $components->id,
        );

        $this->createCategory(
            name: 'Placas de vídeo',
            slug: 'placas-de-video',
            description: 'Placas gráficas para jogos e criação.',
            sortOrder: 2,
            parentId: $components->id,
        );

        $this->createCategory(
            name: 'Memórias',
            slug: 'memorias',
            description: 'Memórias RAM para computadores.',
            sortOrder: 3,
            parentId: $components->id,
        );

        unset($monitors, $audio);
    }

    private function createCategory(
        string $name,
        string $slug,
        string $description,
        int $sortOrder,
        ?int $parentId = null,
    ): Category {
        return Category::updateOrCreate(
            ['slug' => $slug],
            [
                'parent_id' => $parentId,
                'name' => $name,
                'description' => $description,
                'image_path' => null,
                'sort_order' => $sortOrder,
                'is_active' => true,
            ]
        );
    }
}
