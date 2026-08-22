<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCategoryRequest;
use App\Http\Requests\Admin\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class CategoryController extends Controller
{
    public function index(Request $request): Response
    {
        $search = trim($request->string('search')->toString());
        $status = $request->string('status')->toString();
        $type = $request->string('type')->toString();

        $categories = Category::query()
            ->with('parent:id,name')
            ->withCount(['products', 'children'])
            ->when($search, function ($query) use ($search): void {
                $query->where('name', 'like', "%{$search}%");
            })
            ->when($status === 'active', function ($query): void {
                $query->where('is_active', true);
            })
            ->when($status === 'inactive', function ($query): void {
                $query->where('is_active', false);
            })
            ->when($type === 'main', function ($query): void {
                $query->whereNull('parent_id');
            })
            ->when($type === 'subcategory', function ($query): void {
                $query->whereNotNull('parent_id');
            })
            ->orderBy('parent_id')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString()
            ->through(fn (Category $category): array => [
                'id' => $category->id,
                'parent' => $category->parent
                    ? [
                        'id' => $category->parent->id,
                        'name' => $category->parent->name,
                    ]
                    : null,
                'name' => $category->name,
                'slug' => $category->slug,
                'image_url' => $category->image_path
                    ? Storage::url($category->image_path)
                    : null,
                'sort_order' => $category->sort_order,
                'is_active' => $category->is_active,
                'products_count' => $category->products_count,
                'children_count' => $category->children_count,
            ]);

        return Inertia::render('Admin/Categories/Index', [
            'categories' => $categories,
            'filters' => [
                'search' => $search,
                'status' => $status,
                'type' => $type,
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Categories/Create', [
            'parentCategories' => $this->parentCategories(),
        ]);
    }

    public function store(
        StoreCategoryRequest $request,
    ): RedirectResponse {
        $data = $request->safe()->except(['image']);
        $data['slug'] = $this->generateUniqueSlug($data['name']);

        if ($request->hasFile('image')) {
            $data['image_path'] = $request
                ->file('image')
                ->store('categories', 'public');
        }

        Category::create($data);

        return to_route('admin.categories.index')
            ->with('success', 'Categoria cadastrada com sucesso.');
    }

    public function show(Category $category): RedirectResponse
    {
        return to_route('admin.categories.edit', $category);
    }

    public function edit(Category $category): Response
    {
        return Inertia::render('Admin/Categories/Edit', [
            'category' => [
                'id' => $category->id,
                'parent_id' => $category->parent_id,
                'name' => $category->name,
                'description' => $category->description,
                'image_url' => $category->image_path
                    ? Storage::url($category->image_path)
                    : null,
                'sort_order' => $category->sort_order,
                'is_active' => $category->is_active,
                'has_children' => $category->children()->exists(),
            ],
            'parentCategories' => $this->parentCategories($category),
        ]);
    }

    public function update(
        UpdateCategoryRequest $request,
        Category $category,
    ): RedirectResponse {
        if (
            $request->filled('parent_id')
            && $category->children()->exists()
        ) {
            return back()->withErrors([
                'parent_id' => 'Uma categoria com subcategorias não pode se tornar uma subcategoria.',
            ]);
        }

        $data = $request->safe()->except([
            'image',
            'remove_image',
        ]);

        if ($data['name'] !== $category->name) {
            $data['slug'] = $this->generateUniqueSlug(
                $data['name'],
                $category,
            );
        }

        if ($request->hasFile('image')) {
            if ($category->image_path) {
                Storage::disk('public')
                    ->delete($category->image_path);
            }

            $data['image_path'] = $request
                ->file('image')
                ->store('categories', 'public');
        } elseif ($request->boolean('remove_image')) {
            if ($category->image_path) {
                Storage::disk('public')
                    ->delete($category->image_path);
            }

            $data['image_path'] = null;
        }

        $category->update($data);

        return to_route('admin.categories.index')
            ->with('success', 'Categoria atualizada com sucesso.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->products()->exists()) {
            return back()->with(
                'error',
                'Não é possível excluir uma categoria que possui produtos.',
            );
        }

        if ($category->children()->exists()) {
            return back()->with(
                'error',
                'Exclua ou mova as subcategorias antes de excluir esta categoria.',
            );
        }

        $category->delete();

        return to_route('admin.categories.index')
            ->with('success', 'Categoria excluída com sucesso.');
    }

    /**
     * @return array<int, array{id: int, name: string}>
     */
    private function parentCategories(
        ?Category $ignoredCategory = null,
    ): array {
        return Category::query()
            ->whereNull('parent_id')
            ->when(
                $ignoredCategory,
                fn ($query) => $query->whereKeyNot(
                    $ignoredCategory->id,
                ),
            )
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn (Category $category): array => [
                'id' => $category->id,
                'name' => $category->name,
            ])
            ->all();
    }

    private function generateUniqueSlug(
        string $name,
        ?Category $ignoredCategory = null,
    ): string {
        $baseSlug = Str::slug($name) ?: 'categoria';
        $slug = $baseSlug;
        $suffix = 2;

        while (
            Category::withTrashed()
                ->where('slug', $slug)
                ->when(
                    $ignoredCategory,
                    fn ($query) => $query->whereKeyNot(
                        $ignoredCategory->id,
                    ),
                )
                ->exists()
        ) {
            $slug = "{$baseSlug}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }
}
