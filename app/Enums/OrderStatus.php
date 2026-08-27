<?php

namespace App\Enums;

enum OrderStatus: string
{
    case PENDING_PAYMENT = 'pending_payment';
    case PROCESSING = 'processing';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::PENDING_PAYMENT => 'Aguardando pagamento',
            self::PROCESSING => 'Em preparação',
            self::COMPLETED => 'Concluído',
            self::CANCELLED => 'Cancelado',
        };
    }
}
