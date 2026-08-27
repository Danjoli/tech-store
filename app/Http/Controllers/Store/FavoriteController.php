<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\Store\ProductCardPresenter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FavoriteController extends Controller
{
    public function index(Request $request, ProductCardPresenter $presenter): Response
    {
        $products = $request->user()
            ->favoriteProducts()
            ->active()
            ->whereHas('variants', fn (Builder $query) => $query->where('is_active', true))
            ->with([
                'brand:id,name,slug',
                'category:id,name,slug',
                'images' => fn ($query) => $query->orderByDesc('is_primary')->orderBy('sort_order'),
                'variants' => fn ($query) => $query->where('is_active', true)->orderByDesc('is_default')->orderBy('price'),
            ])
            ->latest('product_favorites.created_at')
            ->get()
            ->map(fn (Product $product): array => $presenter->present($product, true));

        return Inertia::render('Store/Favorites/Index', [
            'products' => $products,
        ]);
    }

    public function toggle(Request $request, Product $product): RedirectResponse
    {
        abort_unless(
            Product::query()->active()->whereKey($product)->exists(),
            404,
        );

        $favorites = $request->user()->favoriteProducts();

        if ($favorites->whereKey($product)->exists()) {
            $favorites->detach($product);

            return back()->with('success', 'Produto removido dos favoritos.');
        }

        $favorites->attach($product);

        return back()->with('success', 'Produto adicionado aos favoritos.');
    }
}
