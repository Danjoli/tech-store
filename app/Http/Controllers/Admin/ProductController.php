<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ProductStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProductRequest;
use App\Http\Requests\Admin\UpdateProductRequest;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    public function index(Request $request): Response
    {
        $search = trim($request->string('search')->toString());
        $status = $request->string('status')->toString();

        $brandId = $request->filled('brand_id')
            ? $request->integer('brand_id')
            : null;

        $categoryId = $request->filled('category_id')
            ? $request->integer('category_id')
            : null;

        $products = Product::query()
            ->with([
                'brand:id,name',
                'category:id,name',
                'defaultVariant',
                'primaryImage',
            ])
            ->withCount('variants')
            ->withSum('variants', 'stock')
            ->when($search, function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query
                        ->where('name', 'like', "%{$search}%")
                        ->orWhereHas(
                            'variants',
                            fn ($query) => $query
                                ->where('sku', 'like', "%{$search}%"),
                        );
                });
            })
            ->when($status, function ($query) use ($status): void {
                $query->where('status', $status);
            })
            ->when($brandId, function ($query) use ($brandId): void {
                $query->where('brand_id', $brandId);
            })
            ->when(
                $categoryId,
                function ($query) use ($categoryId): void {
                    $query->where('category_id', $categoryId);
                },
            )
            ->latest()
            ->paginate(15)
            ->withQueryString()
            ->through(fn (Product $product): array => [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'brand' => $product->brand?->name,
                'category' => $product->category->name,
                'status' => $product->status->value,
                'is_featured' => $product->is_featured,
                'price' => $product->defaultVariant?->price,
                'sale_price' => $product->defaultVariant?->sale_price,
                'sku' => $product->defaultVariant?->sku,
                'stock' => (int) ($product->variants_sum_stock ?? 0),
                'variants_count' => $product->variants_count,
                'image_url' => $product->primaryImage
                    ? Storage::url($product->primaryImage->path)
                    : null,
            ]);

        return Inertia::render('Admin/Products/Index', [
            'products' => $products,
            'filters' => [
                'search' => $search,
                'status' => $status,
                'brand_id' => $brandId,
                'category_id' => $categoryId,
            ],
            'brands' => $this->brandOptions(),
            'categories' => $this->categoryOptions(),
            'statuses' => $this->statusOptions(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Products/Create', [
            'brands' => $this->brandOptions(),
            'categories' => $this->categoryOptions(),
            'statuses' => $this->statusOptions(),
        ]);
    }

    public function store(
        StoreProductRequest $request,
    ): RedirectResponse {
        $validated = $request->validated();

        DB::transaction(function () use ($validated): void {
            $productData = $this->productData($validated);
            $productData['slug'] = $this->generateUniqueSlug(
                $productData['name'],
            );
            $productData['published_at'] =
                $productData['status'] === ProductStatus::ACTIVE->value
                    ? now()
                    : null;

            $product = Product::create($productData);

            $product->variants()->create(
                $this->variantData($validated),
            );
        });

        return to_route('admin.products.index')
            ->with('success', 'Produto cadastrado com sucesso.');
    }

    public function show(Product $product): RedirectResponse
    {
        return to_route('admin.products.edit', $product);
    }

    public function edit(Product $product): Response
    {
        $product->load('defaultVariant');

        $variant = $product->defaultVariant;

        return Inertia::render('Admin/Products/Edit', [
            'product' => [
                'id' => $product->id,
                'brand_id' => $product->brand_id,
                'category_id' => $product->category_id,
                'name' => $product->name,
                'short_description' => $product->short_description,
                'description' => $product->description,
                'status' => $product->status->value,
                'is_featured' => $product->is_featured,
                'warranty_months' => $product->warranty_months,
                'weight' => $product->weight,
                'height' => $product->height,
                'width' => $product->width,
                'length' => $product->length,
                'seo_title' => $product->seo_title,
                'seo_description' => $product->seo_description,

                'variant_name' => $variant?->name ?? 'Padrão',
                'sku' => $variant?->sku ?? '',
                'barcode' => $variant?->barcode,
                'price' => $variant?->price ?? '',
                'sale_price' => $variant?->sale_price,
                'cost_price' => $variant?->cost_price,
                'stock' => $variant?->stock ?? 0,
                'low_stock_threshold' =>
                    $variant?->low_stock_threshold ?? 5,
                'variant_is_active' =>
                    $variant?->is_active ?? true,
            ],
            'brands' => $this->brandOptions(),
            'categories' => $this->categoryOptions(),
            'statuses' => $this->statusOptions(),
        ]);
    }

    public function update(
        UpdateProductRequest $request,
        Product $product,
    ): RedirectResponse {
        $validated = $request->validated();

        DB::transaction(
            function () use ($validated, $product): void {
                $productData = $this->productData($validated);

                if ($productData['name'] !== $product->name) {
                    $productData['slug'] = $this->generateUniqueSlug(
                        $productData['name'],
                        $product,
                    );
                }

                $wasActive =
                    $product->status === ProductStatus::ACTIVE;

                $willBeActive =
                    $productData['status']
                    === ProductStatus::ACTIVE->value;

                if ($willBeActive && ! $wasActive) {
                    $productData['published_at'] = now();
                } elseif (! $willBeActive) {
                    $productData['published_at'] = null;
                }

                $product->update($productData);

                $variantData = $this->variantData($validated);
                $variant = $product->defaultVariant;

                if ($variant) {
                    $variant->update($variantData);
                } else {
                    $product->variants()->create($variantData);
                }
            },
        );

        return to_route('admin.products.index')
            ->with('success', 'Produto atualizado com sucesso.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return to_route('admin.products.index')
            ->with('success', 'Produto excluído com sucesso.');
    }

    /**
     * @param array<string, mixed> $validated
     * @return array<string, mixed>
     */
    private function productData(array $validated): array
    {
        return Arr::only($validated, [
            'brand_id',
            'category_id',
            'name',
            'short_description',
            'description',
            'status',
            'is_featured',
            'warranty_months',
            'weight',
            'height',
            'width',
            'length',
            'seo_title',
            'seo_description',
        ]);
    }

    /**
     * @param array<string, mixed> $validated
     * @return array<string, mixed>
     */
    private function variantData(array $validated): array
    {
        return [
            'name' => $validated['variant_name'],
            'sku' => $validated['sku'],
            'barcode' => $validated['barcode'] ?? null,
            'price' => $validated['price'],
            'sale_price' => $validated['sale_price'] ?? null,
            'cost_price' => $validated['cost_price'] ?? null,
            'stock' => $validated['stock'],
            'reserved_stock' => 0,
            'low_stock_threshold' =>
                $validated['low_stock_threshold'],
            'attributes' => null,
            'is_default' => true,
            'is_active' => $validated['variant_is_active'],
        ];
    }

    /**
     * @return array<int, array{id: int, name: string}>
     */
    private function brandOptions(): array
    {
        return Brand::query()
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn (Brand $brand): array => [
                'id' => $brand->id,
                'name' => $brand->name,
            ])
            ->all();
    }

    /**
     * @return array<int, array{id: int, name: string}>
     */
    private function categoryOptions(): array
    {
        return Category::query()
            ->with('parent:id,name')
            ->orderBy('parent_id')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'parent_id', 'name'])
            ->map(fn (Category $category): array => [
                'id' => $category->id,
                'name' => $category->parent
                    ? "{$category->parent->name} → {$category->name}"
                    : $category->name,
            ])
            ->all();
    }

    /**
     * @return array<int, array{value: string, label: string}>
     */
    private function statusOptions(): array
    {
        return [
            [
                'value' => ProductStatus::DRAFT->value,
                'label' => 'Rascunho',
            ],
            [
                'value' => ProductStatus::ACTIVE->value,
                'label' => 'Ativo',
            ],
            [
                'value' => ProductStatus::INACTIVE->value,
                'label' => 'Inativo',
            ],
        ];
    }

    private function generateUniqueSlug(
        string $name,
        ?Product $ignoredProduct = null,
    ): string {
        $baseSlug = Str::slug($name) ?: 'produto';
        $slug = $baseSlug;
        $suffix = 2;

        while (
            Product::withTrashed()
                ->where('slug', $slug)
                ->when(
                    $ignoredProduct,
                    fn ($query) => $query->whereKeyNot(
                        $ignoredProduct->id,
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
