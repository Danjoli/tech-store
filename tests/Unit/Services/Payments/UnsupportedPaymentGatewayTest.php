<?php

namespace Tests\Unit\Services\Payments;

use App\Enums\PaymentMethod;
use App\Models\Order;
use App\Services\Payments\UnsupportedPaymentGateway;
use LogicException;
use PHPUnit\Framework\TestCase;

class UnsupportedPaymentGatewayTest extends TestCase
{
    public function test_an_unimplemented_gateway_fails_explicitly(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('ainda não possui uma implementação');

        (new UnsupportedPaymentGateway)->create(new Order, PaymentMethod::PIX);
    }
}
