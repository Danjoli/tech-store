<?php

namespace Tests\Feature\Http;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RateLimitingTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_is_limited_per_authenticated_user(): void
    {
        $user = User::factory()->create();

        for ($attempt = 0; $attempt < 10; $attempt++) {
            $this->actingAs($user)
                ->get(route('store.checkout.create'))
                ->assertRedirect(route('store.cart.index'));
        }

        $this->actingAs($user)
            ->get(route('store.checkout.create'))
            ->assertTooManyRequests();
    }
}
