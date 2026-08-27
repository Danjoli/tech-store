<?php

namespace Tests\Feature\Store;

use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    public function test_an_authenticated_user_can_add_update_and_remove_a_cart_item(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        $variant = ProductVariant::factory()->default()->create([
            'product_id' => $product->id,
            'stock' => 5,
        ]);

        $this->actingAs($user)->post('/carrinho/itens', [
            'product_variant_id' => $variant->id,
            'quantity' => 2,
        ])->assertRedirect();

        $item = CartItem::firstOrFail();
        $this->assertSame(2, $item->quantity);

        $this->actingAs($user)->put("/carrinho/itens/{$item->id}", [
            'quantity' => 4,
        ])->assertRedirect();

        $this->assertDatabaseHas('cart_items', ['id' => $item->id, 'quantity' => 4]);

        $this->actingAs($user)->delete("/carrinho/itens/{$item->id}")
            ->assertRedirect();

        $this->assertDatabaseMissing('cart_items', ['id' => $item->id]);
    }

    public function test_cart_quantity_is_limited_to_available_stock(): void
    {
        $user = User::factory()->create();
        $variant = ProductVariant::factory()->default()->create(['stock' => 2]);

        $this->actingAs($user)->post('/carrinho/itens', [
            'product_variant_id' => $variant->id,
            'quantity' => 2,
        ])->assertRedirect();

        $this->actingAs($user)->post('/carrinho/itens', [
            'product_variant_id' => $variant->id,
            'quantity' => 2,
        ])->assertRedirect();

        $this->assertDatabaseHas('cart_items', ['quantity' => 2]);
    }

    public function test_users_cannot_update_another_users_cart_item(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $variant = ProductVariant::factory()->default()->create();

        $this->actingAs($owner)->post('/carrinho/itens', [
            'product_variant_id' => $variant->id,
            'quantity' => 1,
        ]);

        $item = CartItem::firstOrFail();

        $this->actingAs($otherUser)
            ->put("/carrinho/itens/{$item->id}", ['quantity' => 2])
            ->assertNotFound();
    }
}
