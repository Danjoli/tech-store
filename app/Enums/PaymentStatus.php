<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case DECLINED = 'declined';
    case REFUNDED = 'refunded';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pendente',
            self::APPROVED => 'Aprovado',
            self::DECLINED => 'Recusado',
            self::REFUNDED => 'Estornado',
        };
    }
}
