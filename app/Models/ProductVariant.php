<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductVariant extends Model
{
    /** @use HasFactory<\Database\Factories\ProductVariantFactory> */
    use HasFactory;

    protected $fillable = [
        'product_id',
        'name',
        'sku',
        'barcode',
        'price',
        'sale_price',
        'cost_price',
        'stock',
        'reserved_stock',
        'low_stock_threshold',
        'attributes',
        'is_default',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'sale_price' => 'decimal:2',
            'cost_price' => 'decimal:2',
            'stock' => 'integer',
            'reserved_stock' => 'integer',
            'low_stock_threshold' => 'integer',
            'attributes' => 'array',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(
            ProductImage::class,
            'product_variant_id'
        )->orderBy('sort_order');
    }

    public function availableStock(): int
    {
        return max(0, $this->stock - $this->reserved_stock);
    }

    public function isLowStock(): bool
    {
        return $this->availableStock() <= $this->low_stock_threshold;
    }

    public function isOnSale(): bool
    {
        return $this->sale_price !== null
            && (float) $this->sale_price < (float) $this->price;
    }

    public function currentPrice(): string
    {
        return $this->isOnSale()
            ? $this->sale_price
            : $this->price;
    }
}
