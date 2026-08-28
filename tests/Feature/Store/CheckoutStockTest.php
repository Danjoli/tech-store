<?php

namespace Tests\Feature\Store;

use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutStockTest extends TestCase
{
    use RefreshDatabase;

    public function test_pending_payment_reserves_the_variant_stock(): void
    {
        $user = User::factory()->create();
        $variant = ProductVariant::factory()->default()->create(['stock' => 5]);

        $this->actingAs($user)->post('/carrinho/itens', [
            'product_variant_id' => $variant->id,
            'quantity' => 2,
        ]);

        $this->actingAs($user)->post('/checkout', $this->checkoutData('pix'))
            ->assertRedirect();

        $this->assertDatabaseHas('product_variants', [
            'id' => $variant->id,
            'stock' => 5,
            'reserved_stock' => 2,
        ]);
    }

    public function test_approved_sandbox_card_payment_decrements_the_variant_stock(): void
    {
        $user = User::factory()->create();
        $variant = ProductVariant::factory()->default()->create(['stock' => 5]);

        $this->actingAs($user)->post('/carrinho/itens', [
            'product_variant_id' => $variant->id,
            'quantity' => 2,
        ]);

        $this->actingAs($user)->post('/checkout', $this->checkoutData('card'))
            ->assertRedirect();

        $this->assertDatabaseHas('product_variants', [
            'id' => $variant->id,
            'stock' => 3,
            'reserved_stock' => 0,
        ]);
    }

    /** @return array<string, string|null> */
    private function checkoutData(string $paymentMethod): array
    {
        return [
            'recipient_name' => 'Cliente Tech',
            'phone' => '(11) 99999-9999',
            'zip' => '01001-000',
            'street' => 'Praça da Sé',
            'number' => '1',
            'complement' => null,
            'district' => 'Sé',
            'city' => 'São Paulo',
            'state' => 'SP',
            'payment_method' => $paymentMethod,
        ];
    }
}
