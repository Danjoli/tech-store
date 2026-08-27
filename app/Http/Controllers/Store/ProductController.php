<?php

namespace App\Http\Controllers\Store;

use App\Enums\ProductStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Store\FilterProductsRequest;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Services\Store\ProductCardPresenter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    public function index(
        FilterProductsRequest $request,
        ProductCardPresenter $presenter,
    ): Response {
        $filters = $request->validated();
        $favoriteIds = $request->user()
            ? $request->user()->favoriteProducts()->pluck('products.id')->all()
            : [];

        $products = Product::query()
            ->active()
            ->whereHas(
                'variants',
                fn (Builder $query) => $query
                    ->where('is_active', true),
            )
            ->with([
                'brand:id,name,slug',
                'category:id,name,slug',

                'images' => fn ($query) => $query
                    ->orderByDesc('is_primary')
                    ->orderBy('sort_order'),

                'variants' => fn ($query) => $query
                    ->where('is_active', true)
                    ->orderByDesc('is_default')
                    ->orderBy('price'),
            ]);

        if (! empty($filters['search'])) {
            $search = trim($filters['search']);

            $products->where(
                function (Builder $query) use ($search): void {
                    $query
                        ->where('products.name', 'like', "%{$search}%")
                        ->orWhere(
                            'products.description',
                            'like',
                            "%{$search}%",
                        )
                        ->orWhereHas(
                            'brand',
                            fn (Builder $brandQuery) => $brandQuery
                                ->where(
                                    'brands.name',
                                    'like',
                                    "%{$search}%",
                                ),
                        )
                        ->orWhereHas(
                            'category',
                            fn (Builder $categoryQuery) => $categoryQuery
                                ->where(
                                    'categories.name',
                                    'like',
                                    "%{$search}%",
                                ),
                        )
                        ->orWhereHas(
                            'variants',
                            fn (Builder $variantQuery) => $variantQuery
                                ->where(
                                    'product_variants.sku',
                                    'like',
                                    "%{$search}%",
                                )
                                ->orWhere(
                                    'product_variants.name',
                                    'like',
                                    "%{$search}%",
                                ),
                        );
                },
            );
        }

        if (! empty($filters['category'])) {
            $products->whereHas(
                'category',
                fn (Builder $query) => $query
                    ->where(
                        'categories.slug',
                        $filters['category'],
                    ),
            );
        }

        if (! empty($filters['brand'])) {
            $products->whereHas(
                'brand',
                fn (Builder $query) => $query
                    ->where(
                        'brands.slug',
                        $filters['brand'],
                    ),
            );
        }

        if (isset($filters['min_price'])) {
            $products->whereHas(
                'variants',
                fn (Builder $query) => $query
                    ->where('product_variants.is_active', true)
                    ->whereRaw(
                        'COALESCE(
                            product_variants.sale_price,
                            product_variants.price
                        ) >= ?',
                        [$filters['min_price']],
                    ),
            );
        }

        if (isset($filters['max_price'])) {
            $products->whereHas(
                'variants',
                fn (Builder $query) => $query
                    ->where('product_variants.is_active', true)
                    ->whereRaw(
                        'COALESCE(
                            product_variants.sale_price,
                            product_variants.price
                        ) <= ?',
                        [$filters['max_price']],
                    ),
            );
        }

        if ($request->boolean('featured')) {
            $products->featured();
        }

        if ($request->boolean('on_sale')) {
            $products->whereHas(
                'variants',
                fn (Builder $query) => $query
                    ->where('product_variants.is_active', true)
                    ->whereNotNull('product_variants.sale_price')
                    ->where('product_variants.sale_price', '>', 0)
                    ->whereColumn(
                        'product_variants.sale_price',
                        '<',
                        'product_variants.price',
                    ),
            );
        }

        if ($request->boolean('in_stock')) {
            $products->whereHas(
                'variants',
                fn (Builder $query) => $query
                    ->where('product_variants.is_active', true)
                    ->whereColumn(
                        'product_variants.stock',
                        '>',
                        'product_variants.reserved_stock',
                    ),
            );
        }

        $priceSubquery = ProductVariant::query()
            ->selectRaw(
                'COALESCE(
                    product_variants.sale_price,
                    product_variants.price
                )',
            )
            ->whereColumn(
                'product_variants.product_id',
                'products.id',
            )
            ->where('product_variants.is_active', true)
            ->orderByDesc('product_variants.is_default')
            ->orderBy('product_variants.price')
            ->limit(1);

        match ($filters['sort'] ?? 'newest') {
            'price_asc' => $products
                ->orderBy($priceSubquery)
                ->orderBy('products.id'),

            'price_desc' => $products
                ->orderByDesc($priceSubquery)
                ->orderByDesc('products.id'),

            'name_asc' => $products
                ->orderBy('products.name')
                ->orderBy('products.id'),

            'name_desc' => $products
                ->orderByDesc('products.name')
                ->orderByDesc('products.id'),

            default => $products
                ->latest('products.created_at')
                ->orderByDesc('products.id'),
        };

        $paginatedProducts = $products
            ->paginate(12)
            ->withQueryString()
            ->through(fn (Product $product): array => $presenter->present(
                $product,
                in_array($product->id, $favoriteIds, true),
            ));

        $categories = Category::query()
            ->where('is_active', true)
            ->whereHas(
                'products',
                fn (Builder $query) => $query->active(),
            )
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get([
                'id',
                'name',
                'slug',
            ]);

        $brands = Brand::query()
            ->where('is_active', true)
            ->whereHas(
                'products',
                fn (Builder $query) => $query->active(),
            )
            ->orderBy('name')
            ->get([
                'id',
                'name',
                'slug',
            ]);

        return Inertia::render('Store/Products/Index', [
            'products' => $paginatedProducts,
            'categories' => $categories,
            'brands' => $brands,

            'filters' => [
                'search' => $filters['search'] ?? '',
                'category' => $filters['category'] ?? '',
                'brand' => $filters['brand'] ?? '',
                'min_price' => $filters['min_price'] ?? '',
                'max_price' => $filters['max_price'] ?? '',
                'featured' => $request->boolean('featured'),
                'on_sale' => $request->boolean('on_sale'),
                'in_stock' => $request->boolean('in_stock'),
                'sort' => $filters['sort'] ?? 'newest',
            ],
        ]);
    }

    public function show(
        Product $product,
        ProductCardPresenter $presenter,
        Request $request,
    ): Response {
        abort_unless(
            $product->status === ProductStatus::ACTIVE,
            404,
        );

        $product->load([
            'brand:id,name,slug',
            'category:id,name,slug',

            'images' => fn ($query) => $query
                ->orderByDesc('is_primary')
                ->orderBy('sort_order')
                ->orderBy('id'),

            'variants' => fn ($query) => $query
                ->where('is_active', true)
                ->orderByDesc('is_default')
                ->orderBy('name'),
        ]);

        abort_if($product->variants->isEmpty(), 404);

        /** @var ProductVariant $defaultVariant */
        $defaultVariant = $product->variants
            ->firstWhere('is_default', true)
            ?? $product->variants->first();

        $favoriteIds = $request->user()
            ? $request->user()->favoriteProducts()->pluck('products.id')->all()
            : [];

        $relatedProducts = Product::query()
            ->active()
            ->whereKeyNot($product->id)
            ->when(
                $product->category_id,
                fn (Builder $query) => $query
                    ->where(
                        'category_id',
                        $product->category_id,
                    ),
            )
            ->whereHas(
                'variants',
                fn (Builder $query) => $query
                    ->where('is_active', true),
            )
            ->with([
                'brand:id,name,slug',
                'category:id,name,slug',

                'images' => fn ($query) => $query
                    ->orderByDesc('is_primary')
                    ->orderBy('sort_order'),

                'variants' => fn ($query) => $query
                    ->where('is_active', true)
                    ->orderByDesc('is_default')
                    ->orderBy('price'),
            ])
            ->latest()
            ->limit(3)
            ->get()
            ->map(fn (Product $relatedProduct): array => $presenter->present(
                $relatedProduct,
                in_array($relatedProduct->id, $favoriteIds, true),
            ));

        return Inertia::render('Store/Products/Show', [
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'description' => $product->description,

                'brand' => $product->brand
                    ? [
                        'id' => $product->brand->id,
                        'name' => $product->brand->name,
                        'slug' => $product->brand->slug,
                    ]
                    : null,

                'category' => $product->category
                    ? [
                        'id' => $product->category->id,
                        'name' => $product->category->name,
                        'slug' => $product->category->slug,
                    ]
                    : null,

                'images' => $product->images
                    ->map(
                        fn (ProductImage $image): array => [
                            'id' => $image->id,
                            'url' => Storage::url($image->path),
                            'alt_text' => $image->alt_text,
                            'product_variant_id' => $image->product_variant_id,
                            'is_primary' => $image->is_primary,
                            'sort_order' => $image->sort_order,
                        ],
                    )
                    ->values(),

                'variants' => $product->variants
                    ->map(
                        fn (ProductVariant $variant): array => [
                            'id' => $variant->id,
                            'name' => $variant->name,
                            'sku' => $variant->sku,
                            'price' => $variant->price,
                            'sale_price' => $variant->sale_price,

                            'available_stock' => max(
                                0,
                                $variant->stock
                                    - $variant->reserved_stock,
                            ),

                            'attributes' => $variant->attributes ?? [],
                            'is_default' => $variant->is_default,
                        ],
                    )
                    ->values(),

                'default_variant_id' => $defaultVariant->id,
                'is_favorited' => in_array($product->id, $favoriteIds, true),
            ],

            'relatedProducts' => $relatedProducts,
        ]);
    }
}
