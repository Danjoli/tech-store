<?php

namespace Tests\Feature\Http;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class RouteRegistrationTest extends TestCase
{
    public function test_store_and_admin_route_modules_keep_the_expected_named_routes(): void
    {
        foreach ([
            'store.home',
            'store.products.index',
            'store.products.show',
            'store.cart.index',
            'store.checkout.create',
            'store.orders.show',
            'admin.dashboard',
            'admin.products.index',
            'admin.products.variants.index',
            'admin.products.images.index',
            'admin.shipments.update',
        ] as $routeName) {
            $this->assertTrue(Route::has($routeName), "A rota {$routeName} deve estar registrada.");
        }
    }
}
