<?php

namespace Tests\Feature\Admin;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_an_administrator_can_view_catalog_metrics(): void
    {
        $administrator = User::factory()->admin()->create();
        $product = Product::factory()->create();

        ProductVariant::factory()->default()->create([
            'product_id' => $product->id,
            'stock' => 2,
            'low_stock_threshold' => 5,
        ]);

        $this->actingAs($administrator)
            ->get('/admin')
            ->assertOk();
    }

    public function test_a_customer_cannot_access_the_administrative_area(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/admin')
            ->assertForbidden();
    }
}
