<?php

namespace Tests\Feature\Auth;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'no_whatsapp' => '0812-3456-7890',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));

        $user = User::where('email', 'test@example.com')->first();
        $this->assertSame(Role::Penyewa, $user->role);
        $this->assertSame('6281234567890', $user->no_whatsapp);
        $this->assertNotNull($user->penyewa);
    }

    public function test_role_field_in_request_cannot_make_admin(): void
    {
        $this->post('/register', [
            'name' => 'Penyusup',
            'email' => 'x@example.com',
            'no_whatsapp' => '081299998888',
            'role' => 'admin',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertSame(Role::Penyewa, User::where('email', 'x@example.com')->first()->role);
    }

    public function test_duplicate_whatsapp_number_is_rejected(): void
    {
        User::factory()->create(['no_whatsapp' => '081234567890']);

        $this->post('/register', [
            'name' => 'Dua',
            'email' => 'dua@example.com',
            'no_whatsapp' => '+62 812 3456 7890',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertSessionHasErrors('no_whatsapp');

        $this->assertGuest();
    }
}
