<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProductImagesRequest;
use App\Http\Requests\Admin\UpdateProductImageRequest;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class ProductImageController extends Controller
{
    public function index(Product $product): Response
    {
        $images = $product->images()
            ->with('variant:id,name')
            ->get()
            ->map(fn (ProductImage $image): array => [
                'id' => $image->id,
                'url' => Storage::url($image->path),
                'alt_text' => $image->alt_text,
                'sort_order' => $image->sort_order,
                'is_primary' => $image->is_primary,
                'variant' => $image->variant
                    ? [
                        'id' => $image->variant->id,
                        'name' => $image->variant->name,
                    ]
                    : null,
            ]);

        $variants = $product->variants()
            ->orderByDesc('is_default')
            ->orderBy('name')
            ->get(['id', 'name', 'sku'])
            ->map(fn (ProductVariant $variant): array => [
                'id' => $variant->id,
                'name' => "{$variant->name} ({$variant->sku})",
            ]);

        return Inertia::render('Admin/Products/Images/Index', [
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
            ],
            'images' => $images,
            'variants' => $variants,
        ]);
    }

    public function store(
        StoreProductImagesRequest $request,
        Product $product,
    ): RedirectResponse {
        $storedPaths = [];

        try {
            DB::transaction(
                function () use (
                    $request,
                    $product,
                    &$storedPaths,
                ): void {
                    $nextOrder = (
                        (int) $product->images()->max('sort_order')
                    ) + 1;

                    $hasPrimaryImage = $product
                        ->images()
                        ->where('is_primary', true)
                        ->exists();

                    foreach (
                        $request->file('images', []) as $position => $uploadedImage
                    ) {
                        $path = $uploadedImage->store(
                            "products/{$product->id}",
                            'public',
                        );

                        $storedPaths[] = $path;

                        $product->images()->create([
                            'product_variant_id' => null,
                            'path' => $path,
                            'alt_text' => $product->name,
                            'sort_order' => $nextOrder + $position,
                            'is_primary' => ! $hasPrimaryImage
                                && $position === 0,
                        ]);
                    }
                },
            );
        } catch (Throwable $exception) {
            Storage::disk('public')->delete($storedPaths);

            throw $exception;
        }

        return to_route('admin.products.images.index', $product)
            ->with('success', 'Imagens enviadas com sucesso.');
    }

    public function update(
        UpdateProductImageRequest $request,
        Product $product,
        ProductImage $image,
    ): RedirectResponse {
        $data = $request->validated();

        if (
            isset($data['product_variant_id'])
            && ! $product->variants()
                ->whereKey($data['product_variant_id'])
                ->exists()
        ) {
            return back()->withErrors([
                'product_variant_id' => 'A variante selecionada não pertence a este produto.',
            ]);
        }

        if ($image->is_primary && ! $data['is_primary']) {
            return back()->withErrors([
                'is_primary' => 'Defina outra imagem como principal antes de remover esta opção.',
            ]);
        }

        DB::transaction(
            function () use ($product, $image, $data): void {
                if ($data['is_primary']) {
                    $product->images()
                        ->whereKeyNot($image->id)
                        ->update(['is_primary' => false]);
                }

                $image->update($data);
            },
        );

        return to_route('admin.products.images.index', $product)
            ->with('success', 'Imagem atualizada com sucesso.');
    }

    public function destroy(
        Product $product,
        ProductImage $image,
    ): RedirectResponse {
        $path = $image->path;
        $wasPrimary = $image->is_primary;

        DB::transaction(
            function () use ($product, $image, $wasPrimary): void {
                $image->delete();

                if ($wasPrimary) {
                    $product->images()
                        ->orderBy('sort_order')
                        ->orderBy('id')
                        ->first()
                        ?->update(['is_primary' => true]);
                }
            },
        );

        Storage::disk('public')->delete($path);

        return to_route('admin.products.images.index', $product)
            ->with('success', 'Imagem excluída com sucesso.');
    }
}
