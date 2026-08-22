<?php

namespace Tests\Feature\Admin;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ProductVariantManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Product $product;
    private ProductVariant $defaultVariant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();

        $brand = Brand::factory()->create();

        $category = Category::factory()->create([
            'parent_id' => null,
        ]);

        $this->product = Product::factory()
            ->for($brand)
            ->for($category)
            ->create();

        $this->defaultVariant = ProductVariant::factory()
            ->for($this->product)
            ->create([
                'name' => 'Padrão',
                'sku' => 'DEFAULT-001',
                'is_default' => true,
                'is_active' => true,
            ]);
    }

    public function test_admin_can_view_product_variants(): void
    {
        ProductVariant::factory()
            ->for($this->product)
            ->count(2)
            ->create([
                'is_default' => false,
            ]);

        $response = $this
            ->actingAs($this->admin)
            ->get(route(
                'admin.products.variants.index',
                $this->product,
            ));

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/Products/Variants/Index')
                ->where('product.id', $this->product->id)
                ->has('variants', 3)
            );
    }

    public function test_admin_can_create_variant_with_attributes(): void
    {
        $response = $this
            ->actingAs($this->admin)
            ->post(
                route(
                    'admin.products.variants.store',
                    $this->product,
                ),
                $this->validPayload(),
            );

        $response->assertRedirect(route(
            'admin.products.variants.index',
            $this->product,
        ));

        $variant = ProductVariant::query()
            ->where('sku', 'MOUSE-PRETO-001')
            ->firstOrFail();

        $this->assertSame($this->product->id, $variant->product_id);
        $this->assertFalse($variant->is_default);
        $this->assertSame([
            'Cor' => 'Preto',
            'Conexão' => 'Sem fio',
        ], $variant->attributes);
    }

    public function test_creating_new_default_variant_removes_old_default(): void
    {
        $payload = $this->validPayload([
            'sku' => 'NEW-DEFAULT-001',
            'is_default' => true,
        ]);

        $this
            ->actingAs($this->admin)
            ->post(
                route(
                    'admin.products.variants.store',
                    $this->product,
                ),
                $payload,
            )
            ->assertRedirect();

        $this->assertFalse(
            $this->defaultVariant->fresh()->is_default,
        );

        $this->assertDatabaseHas('product_variants', [
            'product_id' => $this->product->id,
            'sku' => 'NEW-DEFAULT-001',
            'is_default' => true,
        ]);
    }

    public function test_admin_can_update_variant(): void
    {
        $variant = ProductVariant::factory()
            ->for($this->product)
            ->create([
                'sku' => 'UPDATE-001',
                'is_default' => false,
            ]);

        $response = $this
            ->actingAs($this->admin)
            ->put(
                route('admin.products.variants.update', [
                    $this->product,
                    $variant,
                ]),
                $this->validPayload([
                    'name' => 'Branco',
                    'sku' => 'UPDATE-002',
                    'price' => 749.90,
                    'stock' => 30,
                ]),
            );

        $response->assertRedirect(route(
            'admin.products.variants.index',
            $this->product,
        ));

        $this->assertDatabaseHas('product_variants', [
            'id' => $variant->id,
            'name' => 'Branco',
            'sku' => 'UPDATE-002',
            'price' => '749.90',
            'stock' => 30,
        ]);
    }

    public function test_default_variant_cannot_be_unmarked_directly(): void
    {
        $response = $this
            ->actingAs($this->admin)
            ->put(
                route('admin.products.variants.update', [
                    $this->product,
                    $this->defaultVariant,
                ]),
                $this->validPayload([
                    'name' => $this->defaultVariant->name,
                    'sku' => $this->defaultVariant->sku,
                    'is_default' => false,
                ]),
            );

        $response->assertSessionHasErrors('is_default');

        $this->assertTrue(
            $this->defaultVariant->fresh()->is_default,
        );
    }

    public function test_default_variant_cannot_be_deleted(): void
    {
        ProductVariant::factory()
            ->for($this->product)
            ->create([
                'is_default' => false,
            ]);

        $response = $this
            ->actingAs($this->admin)
            ->delete(route('admin.products.variants.destroy', [
                $this->product,
                $this->defaultVariant,
            ]));

        $response->assertSessionHas('error');

        $this->assertDatabaseHas('product_variants', [
            'id' => $this->defaultVariant->id,
        ]);
    }

    public function test_non_default_variant_can_be_deleted(): void
    {
        $variant = ProductVariant::factory()
            ->for($this->product)
            ->create([
                'is_default' => false,
                'reserved_stock' => 0,
            ]);

        $response = $this
            ->actingAs($this->admin)
            ->delete(route('admin.products.variants.destroy', [
                $this->product,
                $variant,
            ]));

        $response->assertRedirect(route(
            'admin.products.variants.index',
            $this->product,
        ));

        $this->assertDatabaseMissing('product_variants', [
            'id' => $variant->id,
        ]);
    }

    public function test_variant_with_reserved_stock_cannot_be_deleted(): void
    {
        $variant = ProductVariant::factory()
            ->for($this->product)
            ->create([
                'is_default' => false,
                'reserved_stock' => 2,
            ]);

        $response = $this
            ->actingAs($this->admin)
            ->delete(route('admin.products.variants.destroy', [
                $this->product,
                $variant,
            ]));

        $response->assertSessionHas('error');

        $this->assertDatabaseHas('product_variants', [
            'id' => $variant->id,
        ]);
    }

    public function test_variant_from_another_product_returns_not_found(): void
    {
        $otherProduct = Product::factory()->create();

        $otherVariant = ProductVariant::factory()
            ->for($otherProduct)
            ->create();

        $response = $this
            ->actingAs($this->admin)
            ->get(route('admin.products.variants.edit', [
                $this->product,
                $otherVariant,
            ]));

        $response->assertNotFound();
    }

    /**
     * @param array<string, mixed> $overrides
     * @return array<string, mixed>
     */
    private function validPayload(array $overrides = []): array
    {
        return array_replace([
            'name' => 'Preto',
            'sku' => 'MOUSE-PRETO-001',
            'barcode' => '7891234567001',
            'price' => 699.90,
            'sale_price' => 649.90,
            'cost_price' => 400,
            'stock' => 15,
            'low_stock_threshold' => 3,
            'attributes' => [
                'Cor' => 'Preto',
                'Conexão' => 'Sem fio',
            ],
            'is_default' => false,
            'is_active' => true,
        ], $overrides);
    }
}
