<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function getAdminUser(): User
    {
        $role = Role::where('name', 'admin')->first();
        return User::where('role_id', $role->id)->first();
    }

    public function test_login_page_loads(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Login');
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_admin_can_login(): void
    {
        $response = $this->post('/login', [
            'email' => 'admin@masjid.local',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('items.index'));
        $this->assertAuthenticated();
    }

    public function test_login_fails_with_wrong_password(): void
    {
        $response = $this->post('/login', [
            'email' => 'admin@masjid.local',
            'password' => 'wrongpassword',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_authenticated_user_can_logout(): void
    {
        $admin = $this->getAdminUser();

        $response = $this->actingAs($admin)->post('/logout');

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_authenticated_user_redirected_from_login(): void
    {
        $admin = $this->getAdminUser();

        $response = $this->actingAs($admin)->get('/login');
        $response->assertRedirect();
    }
}
