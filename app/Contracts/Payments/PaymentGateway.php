<?php

namespace App\Contracts\Payments;

use App\Data\PaymentAttemptData;
use App\Enums\PaymentMethod;
use App\Models\Order;

interface PaymentGateway
{
    public function create(Order $order, PaymentMethod $method): PaymentAttemptData;
}
