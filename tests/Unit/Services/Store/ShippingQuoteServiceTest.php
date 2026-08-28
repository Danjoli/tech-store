<?php

namespace Tests\Unit\Services\Store;

use App\Data\CheckoutData;
use App\Models\Cart;
use App\Services\Store\ShippingQuoteService;
use Tests\TestCase;

class ShippingQuoteServiceTest extends TestCase
{
    public function test_sandbox_shipping_returns_the_configured_flat_rate(): void
    {
        $service = new ShippingQuoteService;

        $this->assertSame(0.0, $service->quote(
            new Cart,
            new CheckoutData(['payment_method' => 'pix']),
        ));
    }
}
