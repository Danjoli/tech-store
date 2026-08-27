<?php

namespace Tests\Feature\Store;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_creates_an_order_payment_and_shipment_from_the_cart(): void
    {
        $user = User::factory()->create();
        $variant = ProductVariant::factory()->default()->create(['stock' => 5]);

        $this->actingAs($user)->post('/carrinho/itens', [
            'product_variant_id' => $variant->id,
            'quantity' => 2,
        ]);

        $this->actingAs($user)->post('/checkout', [
            'recipient_name' => 'Cliente Tech',
            'phone' => '(11) 99999-9999',
            'zip' => '01001-000',
            'street' => 'Praça da Sé',
            'number' => '1',
            'complement' => null,
            'district' => 'Sé',
            'city' => 'São Paulo',
            'state' => 'SP',
            'payment_method' => 'card',
        ])->assertRedirect();

        $order = Order::firstOrFail();
        $this->assertSame(OrderStatus::PROCESSING, $order->status);
        $this->assertSame(PaymentStatus::APPROVED, $order->payment->status);
        $this->assertDatabaseCount('order_items', 1);
        $this->assertDatabaseCount('shipments', 1);
        $this->assertDatabaseCount('cart_items', 0);
    }

    public function test_a_user_cannot_view_another_users_order(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $owner->id]);

        $this->actingAs($otherUser)->get("/pedidos/{$order->id}")
            ->assertNotFound();
    }
}
