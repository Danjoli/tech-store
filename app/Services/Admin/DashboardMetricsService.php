<?php

namespace App\Services\Admin;

use App\Enums\ProductStatus;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;

class DashboardMetricsService
{
    /**
     * @return array<string, int|array<int, array<string, int|string|null>>>
     */
    public function summarize(): array
    {
        $lowStockVariants = ProductVariant::query()
            ->with('product:id,name')
            ->where('is_active', true)
            ->whereColumn('stock', '<=', 'low_stock_threshold')
            ->orderBy('stock')
            ->limit(5)
            ->get();

        return [
            'products' => Product::count(),
            'active_products' => Product::query()
                ->where('status', ProductStatus::ACTIVE->value)
                ->count(),
            'draft_products' => Product::query()
                ->where('status', ProductStatus::DRAFT->value)
                ->count(),
            'categories' => Category::count(),
            'brands' => Brand::count(),
            'low_stock_variants' => $lowStockVariants
                ->map(fn (ProductVariant $variant): array => [
                    'id' => $variant->id,
                    'name' => $variant->product?->name,
                    'sku' => $variant->sku,
                    'available_stock' => $variant->availableStock(),
                ])
                ->all(),
        ];
    }
}
