<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Services\Store\ProductCardPresenter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function __invoke(Request $request, ProductCardPresenter $presenter): Response
    {
        $favoriteIds = $request->user()
            ? $request->user()->favoriteProducts()->pluck('products.id')->all()
            : [];
        $categories = Category::query()
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->withCount([
                'products' => fn (Builder $query) => $query->active(),
            ])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->limit(8)
            ->get([
                'id',
                'name',
                'slug',
            ])
            ->map(
                fn (Category $category): array => [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'products_count' => $category->products_count,
                ],
            );

        $featuredProducts = Product::query()
            ->active()
            ->featured()
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
            ->latest('published_at')
            ->latest('id')
            ->limit(12)
            ->get()
            ->map(fn (Product $product): array => $presenter->present(
                $product,
                in_array($product->id, $favoriteIds, true),
            ));

        return Inertia::render('Store/Home', [
            'categories' => $categories,
            'featuredProducts' => $featuredProducts,
        ]);
    }
}
