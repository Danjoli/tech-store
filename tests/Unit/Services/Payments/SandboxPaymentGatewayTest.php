<?php

namespace Tests\Unit\Services\Payments;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Services\Payments\SandboxPaymentGateway;
use PHPUnit\Framework\TestCase;

class SandboxPaymentGatewayTest extends TestCase
{
    public function test_card_is_approved_only_in_the_sandbox_contract(): void
    {
        $attempt = (new SandboxPaymentGateway)->create(new Order, PaymentMethod::CARD);

        $this->assertSame(PaymentStatus::APPROVED, $attempt->status);
        $this->assertSame('sandbox', $attempt->metadata['mode']);
        $this->assertStringStartsWith('sandbox_', $attempt->externalId);
    }

    public function test_pix_and_boleto_remain_pending_in_the_sandbox_contract(): void
    {
        $gateway = new SandboxPaymentGateway;

        $this->assertSame(PaymentStatus::PENDING, $gateway->create(new Order, PaymentMethod::PIX)->status);
        $this->assertSame(PaymentStatus::PENDING, $gateway->create(new Order, PaymentMethod::BOLETO)->status);
    }
}
