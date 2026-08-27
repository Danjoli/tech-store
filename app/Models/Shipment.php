<?php

namespace App\Models;

use App\Enums\ShipmentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Shipment extends Model
{
    protected $fillable = ['order_id', 'status', 'carrier', 'tracking_code', 'shipped_at', 'delivered_at'];

    protected function casts(): array
    {
        return ['status' => ShipmentStatus::class, 'shipped_at' => 'datetime', 'delivered_at' => 'datetime'];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
