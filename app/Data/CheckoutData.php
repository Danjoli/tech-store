<?php

namespace App\Data;

use App\Enums\PaymentMethod;

readonly class CheckoutData
{
    /** @param array<string, mixed> $data */
    public function __construct(public array $data) {}

    public function paymentMethod(): PaymentMethod
    {
        return PaymentMethod::from($this->data['payment_method']);
    }
}
