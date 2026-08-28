<?php

namespace Tests\Feature\Store;

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ProfileEmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_changing_an_email_requires_the_customer_to_verify_it_again(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'email' => 'cliente@techstore.test',
            'email_verified_at' => now(),
        ]);

        $this->actingAs($user)
            ->put(route('store.profile.update'), [
                'name' => 'Cliente Atualizado',
                'email' => 'novo-cliente@techstore.test',
                'phone' => '(11) 99999-9999',
            ])
            ->assertRedirect();

        $user->refresh();

        $this->assertSame('novo-cliente@techstore.test', $user->email);
        $this->assertNull($user->email_verified_at);
        Notification::assertSentTo($user, VerifyEmail::class);
    }
}
