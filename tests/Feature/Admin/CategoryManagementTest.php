<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class CategoryManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
    }

    public function test_admin_can_view_categories_list(): void
    {
        Category::factory()->count(3)->create([
            'parent_id' => null,
        ]);

        $response = $this
            ->actingAs($this->admin)
            ->get(route('admin.categories.index'));

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/Categories/Index')
                ->has('categories.data', 3)
                ->has('filters')
            );
    }

    public function test_admin_can_create_main_category_with_image(): void
    {
        Storage::fake('public');

        $image = UploadedFile::fake()->image('hardware.png');

        $response = $this
            ->actingAs($this->admin)
            ->post(route('admin.categories.store'), [
                'parent_id' => null,
                'name' => 'Hardware',
                'description' => 'Componentes para computadores.',
                'image' => $image,
                'sort_order' => 1,
                'is_active' => true,
            ]);

        $response->assertRedirect(route('admin.categories.index'));

        $category = Category::query()
            ->where('name', 'Hardware')
            ->firstOrFail();

        $this->assertNull($category->parent_id);
        $this->assertSame('hardware', $category->slug);
        $this->assertNotNull($category->image_path);

        Storage::disk('public')->assertExists(
            $category->image_path,
        );
    }

    public function test_admin_can_create_subcategory(): void
    {
        $parent = Category::factory()->create([
            'parent_id' => null,
            'name' => 'Componentes',
            'slug' => 'componentes',
        ]);

        $response = $this
            ->actingAs($this->admin)
            ->post(route('admin.categories.store'), [
                'parent_id' => $parent->id,
                'name' => 'Processadores',
                'description' => null,
                'sort_order' => 2,
                'is_active' => true,
            ]);

        $response->assertRedirect(route('admin.categories.index'));

        $this->assertDatabaseHas('categories', [
            'parent_id' => $parent->id,
            'name' => 'Processadores',
            'slug' => 'processadores',
        ]);
    }

    public function test_subcategory_cannot_be_parent_of_another_category(): void
    {
        $parent = Category::factory()->create([
            'parent_id' => null,
        ]);

        $subcategory = Category::factory()->create([
            'parent_id' => $parent->id,
        ]);

        $response = $this
            ->actingAs($this->admin)
            ->post(route('admin.categories.store'), [
                'parent_id' => $subcategory->id,
                'name' => 'Terceiro nível',
                'description' => null,
                'sort_order' => 0,
                'is_active' => true,
            ]);

        $response->assertSessionHasErrors('parent_id');

        $this->assertDatabaseMissing('categories', [
            'name' => 'Terceiro nível',
        ]);
    }

    public function test_admin_can_update_category(): void
    {
        $category = Category::factory()->create([
            'parent_id' => null,
            'name' => 'Nome antigo',
            'slug' => 'nome-antigo',
            'is_active' => true,
        ]);

        $response = $this
            ->actingAs($this->admin)
            ->put(route('admin.categories.update', $category), [
                'parent_id' => null,
                'name' => 'Nome atualizado',
                'description' => 'Descrição atualizada.',
                'sort_order' => 5,
                'is_active' => false,
            ]);

        $response->assertRedirect(route('admin.categories.index'));

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Nome atualizado',
            'slug' => 'nome-atualizado',
            'description' => 'Descrição atualizada.',
            'sort_order' => 5,
            'is_active' => false,
        ]);
    }

    public function test_category_with_children_cannot_become_subcategory(): void
    {
        $category = Category::factory()->create([
            'parent_id' => null,
        ]);

        Category::factory()->create([
            'parent_id' => $category->id,
        ]);

        $anotherParent = Category::factory()->create([
            'parent_id' => null,
        ]);

        $response = $this
            ->actingAs($this->admin)
            ->put(route('admin.categories.update', $category), [
                'parent_id' => $anotherParent->id,
                'name' => $category->name,
                'description' => $category->description,
                'sort_order' => $category->sort_order,
                'is_active' => $category->is_active,
            ]);

        $response->assertSessionHasErrors('parent_id');

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'parent_id' => null,
        ]);
    }

    public function test_category_with_children_cannot_be_deleted(): void
    {
        $parent = Category::factory()->create([
            'parent_id' => null,
        ]);

        Category::factory()->create([
            'parent_id' => $parent->id,
        ]);

        $response = $this
            ->actingAs($this->admin)
            ->delete(route('admin.categories.destroy', $parent));

        $response
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertDatabaseHas('categories', [
            'id' => $parent->id,
            'deleted_at' => null,
        ]);
    }

    public function test_empty_category_can_be_deleted(): void
    {
        $category = Category::factory()->create([
            'parent_id' => null,
        ]);

        $response = $this
            ->actingAs($this->admin)
            ->delete(route('admin.categories.destroy', $category));

        $response->assertRedirect(route('admin.categories.index'));

        $this->assertSoftDeleted('categories', [
            'id' => $category->id,
        ]);
    }
}
