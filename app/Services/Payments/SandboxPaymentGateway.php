<?php

namespace App\Services\Payments;

use App\Contracts\Payments\PaymentGateway;
use App\Data\PaymentAttemptData;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Order;
use Illuminate\Support\Str;

class SandboxPaymentGateway implements PaymentGateway
{
    public function create(Order $order, PaymentMethod $method): PaymentAttemptData
    {
        $status = $method === PaymentMethod::CARD
            ? PaymentStatus::APPROVED
            : PaymentStatus::PENDING;

        return new PaymentAttemptData(
            status: $status,
            externalId: 'sandbox_'.Str::uuid(),
            metadata: [
                'mode' => 'sandbox',
                'instruction' => match ($method) {
                    PaymentMethod::PIX => 'Pagamento Pix em modo sandbox.',
                    PaymentMethod::BOLETO => 'Boleto em modo sandbox.',
                    PaymentMethod::CARD => 'Cartão aprovado em modo sandbox.',
                },
            ],
        );
    }
}
