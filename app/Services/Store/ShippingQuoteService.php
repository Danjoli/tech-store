<?php

namespace App\Services\Store;

use App\Data\CheckoutData;
use App\Models\Cart;

class ShippingQuoteService
{
    public function quote(Cart $cart, CheckoutData $checkout): float
    {
        // O modo sandbox mantém o frete configurável sem simular uma transportadora real.
        return (float) config('shipping.sandbox_flat_rate', 0);
    }
}
