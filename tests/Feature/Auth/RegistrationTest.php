<?php

namespace Tests\Feature\Auth;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response
            ->assertOk()
            ->assertInertia(
                fn (AssertableInertia $page) => $page
                    ->component('Auth/Register')
            );
    }

    public function test_customer_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Danilo Teste',
            'email' => 'danilo@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();

        $this->assertDatabaseHas('users', [
            'name' => 'Danilo Teste',
            'email' => 'danilo@example.com',
            'role' => UserRole::CUSTOMER->value,
            'is_active' => true,
        ]);

        $response->assertRedirect('/');
    }

    public function test_registration_requires_unique_email(): void
    {
        User::factory()->create([
            'email' => 'danilo@example.com',
        ]);

        $response = $this->post('/register', [
            'name' => 'Outro usuário',
            'email' => 'danilo@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_registration_requires_password_confirmation(): void
    {
        $response = $this->post('/register', [
            'name' => 'Danilo Teste',
            'email' => 'danilo@example.com',
            'password' => 'password',
            'password_confirmation' => 'different-password',
        ]);

        $response->assertSessionHasErrors('password');

        $this->assertGuest();
    }
}
