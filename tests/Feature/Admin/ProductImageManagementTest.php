<?php

namespace Tests\Feature\Admin;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ProductImageManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private Product $product;

    private ProductVariant $variant;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->admin = User::factory()->admin()->create();
        $this->product = Product::factory()->create();

        $this->variant = ProductVariant::factory()
            ->for($this->product)
            ->create([
                'is_default' => true,
            ]);
    }

    public function test_admin_can_view_product_images(): void
    {
        ProductImage::factory()
            ->for($this->product)
            ->count(3)
            ->create();

        $response = $this
            ->actingAs($this->admin)
            ->get(route(
                'admin.products.images.index',
                $this->product,
            ));

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/Products/Images/Index')
                ->where('product.id', $this->product->id)
                ->has('images', 3)
                ->has('variants', 1)
            );
    }

    public function test_admin_can_upload_multiple_images(): void
    {
        $response = $this
            ->actingAs($this->admin)
            ->post(
                route(
                    'admin.products.images.store',
                    $this->product,
                ),
                [
                    'images' => [
                        UploadedFile::fake()->image('front.png'),
                        UploadedFile::fake()->image('back.png'),
                    ],
                ],
            );

        $response->assertRedirect(route(
            'admin.products.images.index',
            $this->product,
        ));

        $images = $this->product->images()
            ->orderBy('sort_order')
            ->get();

        $this->assertCount(2, $images);
        $this->assertTrue($images->first()->is_primary);
        $this->assertFalse($images->last()->is_primary);

        foreach ($images as $image) {
            Storage::disk('public')->assertExists($image->path);
        }
    }

    public function test_admin_can_update_image_metadata(): void
    {
        $image = ProductImage::factory()
            ->for($this->product)
            ->create([
                'product_variant_id' => null,
                'is_primary' => true,
            ]);

        $response = $this
            ->actingAs($this->admin)
            ->put(
                route('admin.products.images.update', [
                    $this->product,
                    $image,
                ]),
                [
                    'product_variant_id' => $this->variant->id,
                    'alt_text' => 'Produto visto de frente',
                    'sort_order' => 4,
                    'is_primary' => true,
                ],
            );

        $response->assertRedirect(route(
            'admin.products.images.index',
            $this->product,
        ));

        $this->assertDatabaseHas('product_images', [
            'id' => $image->id,
            'product_variant_id' => $this->variant->id,
            'alt_text' => 'Produto visto de frente',
            'sort_order' => 4,
            'is_primary' => true,
        ]);
    }

    public function test_variant_from_another_product_cannot_be_assigned(): void
    {
        $image = ProductImage::factory()
            ->for($this->product)
            ->create([
                'is_primary' => true,
            ]);

        $otherVariant = ProductVariant::factory()->create();

        $response = $this
            ->actingAs($this->admin)
            ->put(
                route('admin.products.images.update', [
                    $this->product,
                    $image,
                ]),
                [
                    'product_variant_id' => $otherVariant->id,
                    'alt_text' => null,
                    'sort_order' => 0,
                    'is_primary' => true,
                ],
            );

        $response->assertSessionHasErrors('product_variant_id');

        $this->assertNull(
            $image->fresh()->product_variant_id,
        );
    }

    public function test_selecting_new_primary_image_unsets_previous_one(): void
    {
        $oldPrimary = ProductImage::factory()
            ->for($this->product)
            ->create([
                'sort_order' => 0,
                'is_primary' => true,
            ]);

        $newPrimary = ProductImage::factory()
            ->for($this->product)
            ->create([
                'sort_order' => 1,
                'is_primary' => false,
            ]);

        $this
            ->actingAs($this->admin)
            ->put(
                route('admin.products.images.update', [
                    $this->product,
                    $newPrimary,
                ]),
                [
                    'product_variant_id' => null,
                    'alt_text' => 'Nova principal',
                    'sort_order' => 1,
                    'is_primary' => true,
                ],
            )
            ->assertRedirect();

        $this->assertFalse($oldPrimary->fresh()->is_primary);
        $this->assertTrue($newPrimary->fresh()->is_primary);
    }

    public function test_primary_image_cannot_be_unmarked_directly(): void
    {
        $image = ProductImage::factory()
            ->for($this->product)
            ->create([
                'is_primary' => true,
            ]);

        $response = $this
            ->actingAs($this->admin)
            ->put(
                route('admin.products.images.update', [
                    $this->product,
                    $image,
                ]),
                [
                    'product_variant_id' => null,
                    'alt_text' => null,
                    'sort_order' => 0,
                    'is_primary' => false,
                ],
            );

        $response->assertSessionHasErrors('is_primary');

        $this->assertTrue($image->fresh()->is_primary);
    }

    public function test_deleting_image_removes_database_record_and_file(): void
    {
        $path = "products/{$this->product->id}/image.png";

        Storage::disk('public')->put($path, 'image-content');

        $image = ProductImage::factory()
            ->for($this->product)
            ->create([
                'path' => $path,
                'is_primary' => false,
            ]);

        $response = $this
            ->actingAs($this->admin)
            ->delete(route('admin.products.images.destroy', [
                $this->product,
                $image,
            ]));

        $response->assertRedirect(route(
            'admin.products.images.index',
            $this->product,
        ));

        $this->assertDatabaseMissing('product_images', [
            'id' => $image->id,
        ]);

        Storage::disk('public')->assertMissing($path);
    }

    public function test_deleting_primary_image_promotes_next_image(): void
    {
        $primary = ProductImage::factory()
            ->for($this->product)
            ->create([
                'sort_order' => 0,
                'is_primary' => true,
            ]);

        $nextImage = ProductImage::factory()
            ->for($this->product)
            ->create([
                'sort_order' => 1,
                'is_primary' => false,
            ]);

        $this
            ->actingAs($this->admin)
            ->delete(route('admin.products.images.destroy', [
                $this->product,
                $primary,
            ]))
            ->assertRedirect();

        $this->assertTrue($nextImage->fresh()->is_primary);
    }

    public function test_image_from_another_product_returns_not_found(): void
    {
        $otherProduct = Product::factory()->create();

        $otherImage = ProductImage::factory()
            ->for($otherProduct)
            ->create();

        $response = $this
            ->actingAs($this->admin)
            ->delete(route('admin.products.images.destroy', [
                $this->product,
                $otherImage,
            ]));

        $response->assertNotFound();

        $this->assertDatabaseHas('product_images', [
            'id' => $otherImage->id,
        ]);
    }
}
