<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProductVariantRequest;
use App\Http\Requests\Admin\UpdateProductVariantRequest;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class ProductVariantController extends Controller
{
    public function index(Product $product): Response
    {
        $variants = $product->variants()
            ->orderByDesc('is_default')
            ->orderBy('name')
            ->get()
            ->map(fn (ProductVariant $variant): array => [
                'id' => $variant->id,
                'name' => $variant->name,
                'sku' => $variant->sku,
                'barcode' => $variant->barcode,
                'price' => $variant->price,
                'sale_price' => $variant->sale_price,
                'stock' => $variant->stock,
                'reserved_stock' => $variant->reserved_stock,
                'available_stock' => $variant->availableStock(),
                'low_stock_threshold' => $variant->low_stock_threshold,
                'is_low_stock' => $variant->isLowStock(),
                'attributes' => $variant->attributes ?? [],
                'is_default' => $variant->is_default,
                'is_active' => $variant->is_active,
            ]);

        return Inertia::render('Admin/Products/Variants/Index', [
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
            ],
            'variants' => $variants,
        ]);
    }

    public function create(Product $product): Response
    {
        return Inertia::render('Admin/Products/Variants/Create', [
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
            ],
        ]);
    }

    public function store(
        StoreProductVariantRequest $request,
        Product $product,
    ): RedirectResponse {
        $data = $this->variantData($request->validated());

        DB::transaction(function () use ($product, $data): void {
            if ($data['is_default']) {
                $product->variants()
                    ->update(['is_default' => false]);
            }

            if (! $product->variants()->exists()) {
                $data['is_default'] = true;
            }

            $product->variants()->create($data);
        });

        return to_route('admin.products.variants.index', $product)
            ->with('success', 'Variante cadastrada com sucesso.');
    }

    public function edit(
        Product $product,
        ProductVariant $variant,
    ): Response {
        return Inertia::render('Admin/Products/Variants/Edit', [
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
            ],
            'variant' => [
                'id' => $variant->id,
                'name' => $variant->name,
                'sku' => $variant->sku,
                'barcode' => $variant->barcode,
                'price' => $variant->price,
                'sale_price' => $variant->sale_price,
                'cost_price' => $variant->cost_price,
                'stock' => $variant->stock,
                'reserved_stock' => $variant->reserved_stock,
                'low_stock_threshold' => $variant->low_stock_threshold,
                'attributes' => $variant->attributes ?? [],
                'is_default' => $variant->is_default,
                'is_active' => $variant->is_active,
            ],
        ]);
    }

    public function update(
        UpdateProductVariantRequest $request,
        Product $product,
        ProductVariant $variant,
    ): RedirectResponse {
        $data = $this->variantData($request->validated());

        if ($variant->is_default && ! $data['is_default']) {
            return back()->withErrors([
                'is_default' => 'Defina outra variante como padrão antes de remover esta opção.',
            ]);
        }

        DB::transaction(
            function () use ($product, $variant, $data): void {
                if ($data['is_default']) {
                    $product->variants()
                        ->whereKeyNot($variant->id)
                        ->update(['is_default' => false]);
                }

                $variant->update($data);
            },
        );

        return to_route('admin.products.variants.index', $product)
            ->with('success', 'Variante atualizada com sucesso.');
    }

    public function destroy(
        Product $product,
        ProductVariant $variant,
    ): RedirectResponse {
        if ($product->variants()->count() <= 1) {
            return back()->with(
                'error',
                'O produto precisa possuir pelo menos uma variante.',
            );
        }

        if ($variant->is_default) {
            return back()->with(
                'error',
                'Defina outra variante como padrão antes de excluir esta variante.',
            );
        }

        if ($variant->reserved_stock > 0) {
            return back()->with(
                'error',
                'Esta variante possui estoque reservado e não pode ser excluída.',
            );
        }

        $variant->delete();

        return to_route('admin.products.variants.index', $product)
            ->with('success', 'Variante excluída com sucesso.');
    }

    /**
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    private function variantData(array $validated): array
    {
        $attributes = collect($validated['attributes'] ?? [])
            ->map(fn ($value) => is_string($value)
                ? trim($value)
                : $value)
            ->filter(fn ($value, $key): bool => is_string($key)
                && trim($key) !== ''
                && $value !== null
                && $value !== '')
            ->all();

        return [
            'name' => $validated['name'],
            'sku' => $validated['sku'],
            'barcode' => $validated['barcode'] ?? null,
            'price' => $validated['price'],
            'sale_price' => $validated['sale_price'] ?? null,
            'cost_price' => $validated['cost_price'] ?? null,
            'stock' => $validated['stock'],
            'low_stock_threshold' => $validated['low_stock_threshold'],
            'attributes' => $attributes ?: null,
            'is_default' => $validated['is_default'],
            'is_active' => $validated['is_active'],
        ];
    }
}
