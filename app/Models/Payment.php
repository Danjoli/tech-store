<?php

namespace App\Models;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = ['order_id', 'provider', 'method', 'status', 'amount', 'external_id', 'metadata', 'paid_at'];

    protected function casts(): array
    {
        return ['method' => PaymentMethod::class, 'status' => PaymentStatus::class, 'amount' => 'decimal:2', 'metadata' => 'array', 'paid_at' => 'datetime'];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
