<?php

namespace Tests\Feature\Admin;

use App\Models\Brand;
use App\Models\Product;
use App\Models\User;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class BrandManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
    }

    public function test_admin_can_view_brands_list(): void
    {
        Brand::factory()->count(3)->create();

        $response = $this
            ->actingAs($this->admin)
            ->get(route('admin.brands.index'));

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/Brands/Index')
                ->has('brands.data', 3)
                ->has('filters')
            );
    }

    public function test_customer_cannot_access_brand_management(): void
    {
        $customer = User::factory()->create();

        $response = $this
            ->actingAs($customer)
            ->get(route('admin.brands.index'));

        $response->assertForbidden();
    }

    public function test_admin_can_create_brand_with_logo(): void
    {
        Storage::fake('public');

        $logo = UploadedFile::fake()->image('logitech.png');

        $response = $this
            ->actingAs($this->admin)
            ->post(route('admin.brands.store'), [
                'name' => 'Logitech',
                'description' => 'Periféricos e acessórios.',
                'website_url' => 'https://www.logitech.com',
                'logo' => $logo,
                'is_active' => true,
            ]);

        $response->assertRedirect(route('admin.brands.index'));

        $brand = Brand::query()
            ->where('name', 'Logitech')
            ->firstOrFail();

        $this->assertSame('logitech', $brand->slug);
        $this->assertTrue($brand->is_active);
        $this->assertNotNull($brand->logo_path);

        /** @var FilesystemAdapter $disk */
        $disk = Storage::disk('public');

        $disk->assertExists($brand->logo_path);
    }

    public function test_brand_name_must_be_unique(): void
    {
        Brand::factory()->create([
            'name' => 'Intel',
            'slug' => 'intel',
        ]);

        $response = $this
            ->actingAs($this->admin)
            ->post(route('admin.brands.store'), [
                'name' => 'Intel',
                'description' => null,
                'website_url' => null,
                'is_active' => true,
            ]);

        $response->assertSessionHasErrors('name');

        $this->assertDatabaseCount('brands', 1);
    }

    public function test_admin_can_update_brand(): void
    {
        $brand = Brand::factory()->create([
            'name' => 'Marca antiga',
            'slug' => 'marca-antiga',
            'is_active' => true,
        ]);

        $response = $this
            ->actingAs($this->admin)
            ->put(route('admin.brands.update', $brand), [
                'name' => 'Marca atualizada',
                'description' => 'Nova descrição.',
                'website_url' => 'https://example.com',
                'is_active' => false,
            ]);

        $response->assertRedirect(route('admin.brands.index'));

        $this->assertDatabaseHas('brands', [
            'id' => $brand->id,
            'name' => 'Marca atualizada',
            'slug' => 'marca-atualizada',
            'description' => 'Nova descrição.',
            'is_active' => false,
        ]);
    }

    public function test_admin_can_delete_brand_without_products(): void
    {
        $brand = Brand::factory()->create();

        $response = $this
            ->actingAs($this->admin)
            ->delete(route('admin.brands.destroy', $brand));

        $response->assertRedirect(route('admin.brands.index'));

        $this->assertSoftDeleted('brands', [
            'id' => $brand->id,
        ]);
    }

    public function test_brand_with_products_cannot_be_deleted(): void
    {
        $brand = Brand::factory()->create();

        Product::factory()
            ->for($brand)
            ->create();

        $response = $this
            ->actingAs($this->admin)
            ->delete(route('admin.brands.destroy', $brand));

        $response
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertDatabaseHas('brands', [
            'id' => $brand->id,
            'deleted_at' => null,
        ]);
    }
}
