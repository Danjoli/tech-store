<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case PIX = 'pix';
    case BOLETO = 'boleto';
    case CARD = 'card';

    public function label(): string
    {
        return match ($this) {
            self::PIX => 'Pix',
            self::BOLETO => 'Boleto',
            self::CARD => 'Cartão',
        };
    }
}
