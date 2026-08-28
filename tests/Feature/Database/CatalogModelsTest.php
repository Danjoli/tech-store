<?php

namespace Tests\Feature\Database;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductSpecification;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogModelsTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_has_catalog_relationships(): void
    {
        $product = Product::factory()->create();

        $variant = ProductVariant::factory()
            ->for($product)
            ->default()
            ->create();

        $image = ProductImage::factory()
            ->for($product)
            ->primary()
            ->create();

        $specification = ProductSpecification::factory()
            ->for($product)
            ->create();

        $product->refresh();

        $this->assertNotNull($product->brand);
        $this->assertNotNull($product->category);

        $this->assertTrue(
            $product->variants->contains($variant)
        );

        $this->assertTrue(
            $product->images->contains($image)
        );

        $this->assertTrue(
            $product->specifications->contains($specification)
        );

        $this->assertTrue(
            $product->defaultVariant->is($variant)
        );

        $this->assertTrue(
            $product->primaryImage->is($image)
        );
    }

    public function test_category_can_have_parent_and_children(): void
    {
        $parent = Category::factory()->create();

        $child = Category::factory()
            ->childOf($parent)
            ->create();

        $this->assertTrue(
            $child->parent->is($parent)
        );

        $this->assertTrue(
            $parent->children->contains($child)
        );
    }

    public function test_variant_calculates_available_stock(): void
    {
        $variant = ProductVariant::factory()->create([
            'stock' => 20,
            'reserved_stock' => 6,
            'low_stock_threshold' => 5,
        ]);

        $this->assertSame(14, $variant->availableStock());
        $this->assertFalse($variant->isLowStock());
    }

    public function test_variant_detects_low_stock(): void
    {
        $variant = ProductVariant::factory()->create([
            'stock' => 10,
            'reserved_stock' => 7,
            'low_stock_threshold' => 5,
        ]);

        $this->assertSame(3, $variant->availableStock());
        $this->assertTrue($variant->isLowStock());
    }

    public function test_active_scope_returns_only_published_active_products(): void
    {
        $activeProduct = Product::factory()->create();

        $draftProduct = Product::factory()
            ->draft()
            ->create();

        $unpublishedProduct = Product::factory()->create([
            'published_at' => null,
        ]);

        $products = Product::active()->get();

        $this->assertTrue(
            $products->contains($activeProduct)
        );

        $this->assertFalse(
            $products->contains($draftProduct)
        );

        $this->assertFalse(
            $products->contains($unpublishedProduct)
        );
    }
}
