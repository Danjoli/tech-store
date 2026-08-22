<?php

namespace Tests\Feature\Catalog;

use Database\Seeders\BrandSeeder;
use Database\Seeders\CategorySeeder;
use Database\Seeders\ProductSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_catalog_seeders_create_demo_catalog(): void
    {
        $this->seed([
            BrandSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
        ]);

        $this->assertDatabaseCount('brands', 6);
        $this->assertDatabaseCount('categories', 14);
        $this->assertDatabaseCount('products', 6);
        $this->assertDatabaseCount('product_variants', 10);
        $this->assertDatabaseCount('product_images', 6);
        $this->assertDatabaseCount(
            'product_specifications',
            14
        );
    }
}
