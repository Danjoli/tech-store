<?php

namespace App\Services\Payments;

use App\Contracts\Payments\PaymentGateway;
use App\Data\PaymentAttemptData;
use App\Enums\PaymentMethod;
use App\Models\Order;
use LogicException;

class UnsupportedPaymentGateway implements PaymentGateway
{
    public function create(Order $order, PaymentMethod $method): PaymentAttemptData
    {
        throw new LogicException('O gateway de pagamentos configurado ainda não possui uma implementação.');
    }
}
