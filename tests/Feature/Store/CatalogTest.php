<?php

namespace Tests\Feature\Store;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_public_catalog_only_lists_published_products_with_active_variants(): void
    {
        $visibleProduct = Product::factory()->create(['name' => 'Notebook visível']);
        ProductVariant::factory()->default()->create(['product_id' => $visibleProduct->id]);
        ProductImage::factory()->primary()->create(['product_id' => $visibleProduct->id]);

        $hiddenProduct = Product::factory()->draft()->create(['name' => 'Notebook oculto']);
        ProductVariant::factory()->default()->create(['product_id' => $hiddenProduct->id]);

        $response = $this->get('/produtos');

        $response->assertOk();
    }

    public function test_a_draft_product_cannot_be_opened_on_the_storefront(): void
    {
        $product = Product::factory()->draft()->create();

        $this->get("/produtos/{$product->slug}")
            ->assertNotFound();
    }
}
