<?php

namespace App\Services\Payments;

use App\Contracts\Payments\PaymentGateway;
use App\Enums\PaymentMethod;
use App\Models\Order;
use App\Models\Payment;

class PaymentService
{
    public function __construct(private PaymentGateway $gateway) {}

    public function createFor(Order $order, PaymentMethod $method): Payment
    {
        $attempt = $this->gateway->create($order, $method);

        return $order->payment()->create([
            'provider' => config('payments.driver'),
            'method' => $method,
            'status' => $attempt->status,
            'amount' => $order->total_amount,
            'external_id' => $attempt->externalId,
            'metadata' => $attempt->metadata,
            'paid_at' => $attempt->status->value === 'approved' ? now() : null,
        ]);
    }
}
