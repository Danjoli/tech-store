<?php

namespace Tests\Feature\Store;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FavoritesTest extends TestCase
{
    use RefreshDatabase;

    public function test_an_authenticated_user_can_add_and_remove_a_favorite(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        ProductVariant::factory()->default()->create(['product_id' => $product->id]);

        $this->actingAs($user)
            ->post("/favoritos/{$product->slug}/alternar")
            ->assertRedirect();

        $this->assertDatabaseHas('product_favorites', [
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);

        $this->actingAs($user)
            ->post("/favoritos/{$product->slug}/alternar")
            ->assertRedirect();

        $this->assertDatabaseMissing('product_favorites', [
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);
    }

    public function test_guests_are_redirected_when_opening_favorites(): void
    {
        $this->get('/favoritos')->assertRedirect('/login');
    }
}
