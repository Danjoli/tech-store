<?php

namespace Tests\Feature\Database;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_default_database_seeder_creates_the_local_catalog_without_the_remote_importer(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseCount('brands', 6);
        $this->assertDatabaseCount('categories', 14);
        $this->assertDatabaseCount('products', 6);
        $this->assertDatabaseCount('product_variants', 10);
        $this->assertDatabaseHas('users', [
            'email' => 'admin@techstore.test',
        ]);
        $this->assertDatabaseMissing('product_variants', [
            'sku' => 'APEX-PRO15-16-1TB',
        ]);
    }
}
