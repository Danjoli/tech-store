<?php

namespace Tests\Unit\Models;

use App\Models\ProductVariant;
use PHPUnit\Framework\TestCase;

class ProductVariantTest extends TestCase
{
    public function test_it_calculates_available_stock_and_current_price_without_database_access(): void
    {
        $variant = new ProductVariant([
            'stock' => 12,
            'reserved_stock' => 5,
            'low_stock_threshold' => 3,
            'price' => '199.90',
            'sale_price' => '149.90',
        ]);

        $this->assertSame(7, $variant->availableStock());
        $this->assertFalse($variant->isLowStock());
        $this->assertTrue($variant->isOnSale());
        $this->assertSame('149.90', $variant->currentPrice());
    }

    public function test_it_never_reports_negative_available_stock(): void
    {
        $variant = new ProductVariant([
            'stock' => 2,
            'reserved_stock' => 8,
            'low_stock_threshold' => 1,
        ]);

        $this->assertSame(0, $variant->availableStock());
        $this->assertTrue($variant->isLowStock());
    }
}
