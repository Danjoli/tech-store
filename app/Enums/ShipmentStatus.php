<?php

namespace App\Enums;

enum ShipmentStatus: string
{
    case PENDING = 'pending';
    case PREPARING = 'preparing';
    case SHIPPED = 'shipped';
    case DELIVERED = 'delivered';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Aguardando envio',
            self::PREPARING => 'Em preparação',
            self::SHIPPED => 'Enviado',
            self::DELIVERED => 'Entregue',
            self::CANCELLED => 'Cancelado',
        };
    }
}
