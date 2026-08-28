<?php

namespace Tests\Feature\Admin;

use App\Enums\ProductStatus;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ProductManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private Brand $brand;

    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();

        $this->brand = Brand::factory()->create([
            'name' => 'Logitech',
            'slug' => 'logitech',
        ]);

        $this->category = Category::factory()->create([
            'parent_id' => null,
            'name' => 'Periféricos',
            'slug' => 'perifericos',
        ]);
    }

    public function test_admin_can_view_products_list(): void
    {
        Product::factory()
            ->for($this->brand)
            ->for($this->category)
            ->count(3)
            ->create();

        $response = $this
            ->actingAs($this->admin)
            ->get(route('admin.products.index'));

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/Products/Index')
                ->has('products.data', 3)
                ->has('brands')
                ->has('categories')
                ->has('statuses')
            );
    }

    public function test_admin_can_create_product_and_default_variant(): void
    {
        $response = $this
            ->actingAs($this->admin)
            ->post(
                route('admin.products.store'),
                $this->validPayload(),
            );

        $response->assertRedirect(route('admin.products.index'));

        $product = Product::query()
            ->where('name', 'Mouse Gamer G Pro')
            ->firstOrFail();

        $this->assertSame('mouse-gamer-g-pro', $product->slug);
        $this->assertSame(ProductStatus::DRAFT, $product->status);
        $this->assertNull($product->published_at);

        $this->assertDatabaseHas('product_variants', [
            'product_id' => $product->id,
            'name' => 'Padrão',
            'sku' => 'LOG-GPRO-001',
            'price' => '599.90',
            'stock' => 20,
            'is_default' => true,
            'is_active' => true,
        ]);
    }

    public function test_active_product_receives_publication_date(): void
    {
        $payload = $this->validPayload([
            'status' => ProductStatus::ACTIVE->value,
        ]);

        $this
            ->actingAs($this->admin)
            ->post(route('admin.products.store'), $payload)
            ->assertRedirect(route('admin.products.index'));

        $product = Product::query()
            ->where('name', 'Mouse Gamer G Pro')
            ->firstOrFail();

        $this->assertNotNull($product->published_at);
    }

    public function test_product_is_not_created_with_duplicate_sku(): void
    {
        ProductVariant::factory()->create([
            'sku' => 'LOG-GPRO-001',
        ]);

        $response = $this
            ->actingAs($this->admin)
            ->post(
                route('admin.products.store'),
                $this->validPayload(),
            );

        $response->assertSessionHasErrors('sku');

        $this->assertDatabaseMissing('products', [
            'name' => 'Mouse Gamer G Pro',
        ]);
    }

    public function test_sale_price_must_be_lower_than_regular_price(): void
    {
        $response = $this
            ->actingAs($this->admin)
            ->post(
                route('admin.products.store'),
                $this->validPayload([
                    'price' => 500,
                    'sale_price' => 600,
                ]),
            );

        $response->assertSessionHasErrors('sale_price');

        $this->assertDatabaseMissing('products', [
            'name' => 'Mouse Gamer G Pro',
        ]);
    }

    public function test_admin_can_update_product_and_default_variant(): void
    {
        $product = Product::factory()
            ->for($this->brand)
            ->for($this->category)
            ->create([
                'name' => 'Mouse antigo',
                'slug' => 'mouse-antigo',
                'status' => ProductStatus::DRAFT,
                'published_at' => null,
            ]);

        $variant = ProductVariant::factory()
            ->for($product)
            ->create([
                'name' => 'Padrão',
                'sku' => 'SKU-ANTIGO',
                'is_default' => true,
            ]);

        $response = $this
            ->actingAs($this->admin)
            ->put(
                route('admin.products.update', $product),
                $this->validPayload([
                    'name' => 'Mouse atualizado',
                    'sku' => 'SKU-ATUALIZADO',
                    'price' => 699.90,
                    'stock' => 35,
                    'status' => ProductStatus::ACTIVE->value,
                ]),
            );

        $response->assertRedirect(route('admin.products.index'));

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Mouse atualizado',
            'slug' => 'mouse-atualizado',
            'status' => ProductStatus::ACTIVE->value,
        ]);

        $this->assertDatabaseHas('product_variants', [
            'id' => $variant->id,
            'product_id' => $product->id,
            'sku' => 'SKU-ATUALIZADO',
            'price' => '699.90',
            'stock' => 35,
            'is_default' => true,
        ]);

        $this->assertNotNull($product->fresh()->published_at);
    }

    public function test_deactivating_product_removes_publication_date(): void
    {
        $product = Product::factory()
            ->for($this->brand)
            ->for($this->category)
            ->create([
                'status' => ProductStatus::ACTIVE,
                'published_at' => now(),
            ]);

        ProductVariant::factory()
            ->for($product)
            ->create([
                'sku' => 'ACTIVE-001',
                'is_default' => true,
            ]);

        $response = $this
            ->actingAs($this->admin)
            ->put(
                route('admin.products.update', $product),
                $this->validPayload([
                    'name' => $product->name,
                    'sku' => 'ACTIVE-001',
                    'status' => ProductStatus::INACTIVE->value,
                ]),
            );

        $response->assertRedirect(route('admin.products.index'));

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'status' => ProductStatus::INACTIVE->value,
            'published_at' => null,
        ]);
    }

    public function test_admin_can_soft_delete_product(): void
    {
        $product = Product::factory()
            ->for($this->brand)
            ->for($this->category)
            ->create();

        $response = $this
            ->actingAs($this->admin)
            ->delete(route('admin.products.destroy', $product));

        $response->assertRedirect(route('admin.products.index'));

        $this->assertSoftDeleted('products', [
            'id' => $product->id,
        ]);
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function validPayload(array $overrides = []): array
    {
        return array_replace([
            'brand_id' => $this->brand->id,
            'category_id' => $this->category->id,
            'name' => 'Mouse Gamer G Pro',
            'short_description' => 'Mouse gamer sem fio.',
            'description' => 'Mouse gamer para alto desempenho.',
            'status' => ProductStatus::DRAFT->value,
            'is_featured' => false,
            'warranty_months' => 12,
            'weight' => 0.080,
            'height' => 4.00,
            'width' => 6.30,
            'length' => 12.50,
            'seo_title' => 'Mouse Gamer G Pro',
            'seo_description' => 'Mouse gamer Logitech.',

            'variant_name' => 'Padrão',
            'sku' => 'LOG-GPRO-001',
            'barcode' => '7891234567890',
            'price' => 599.90,
            'sale_price' => 549.90,
            'cost_price' => 350.00,
            'stock' => 20,
            'low_stock_threshold' => 5,
            'variant_is_active' => true,
        ], $overrides);
    }
}
