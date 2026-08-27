<?php

namespace Tests\Feature\Store;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_an_authenticated_user_can_view_and_update_their_profile(): void
    {
        $user = User::factory()->create(['phone' => null]);

        $this->actingAs($user)->get('/perfil')->assertOk();

        $this->actingAs($user)
            ->put('/perfil', [
                'name' => 'Cliente Tech',
                'email' => $user->email,
                'phone' => '(11) 99999-9999',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Cliente Tech',
            'phone' => '(11) 99999-9999',
        ]);
    }

    public function test_guests_are_redirected_from_the_profile(): void
    {
        $this->get('/perfil')->assertRedirect('/login');
    }
}
