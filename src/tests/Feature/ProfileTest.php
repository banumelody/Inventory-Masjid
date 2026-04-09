<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $role = Role::where('name', 'admin')->first();
        $this->user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'role_id' => $role->id,
            'masjid_id' => null,
        ]);
    }

    public function test_profile_page_loads()
    {
        $response = $this->actingAs($this->user)->get('/profile');
        $response->assertStatus(200);
        $response->assertSee('Profil Saya');
        $response->assertSee('Test User');
    }

    public function test_can_update_profile()
    {
        $response = $this->actingAs($this->user)->put('/profile', [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
        ]);
        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHas('success');

        $this->user->refresh();
        $this->assertEquals('Updated Name', $this->user->name);
        $this->assertEquals('updated@example.com', $this->user->email);
    }

    public function test_can_change_password()
    {
        $response = $this->actingAs($this->user)->put('/profile/password', [
            'current_password' => 'password',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);
        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHas('success');

        $this->user->refresh();
        $this->assertTrue(Hash::check('newpassword123', $this->user->password));
    }

    public function test_wrong_current_password_rejected()
    {
        $response = $this->actingAs($this->user)->put('/profile/password', [
            'current_password' => 'wrongpassword',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);
        $response->assertSessionHasErrors('current_password');
    }

    public function test_password_must_be_confirmed()
    {
        $response = $this->actingAs($this->user)->put('/profile/password', [
            'current_password' => 'password',
            'password' => 'newpassword123',
            'password_confirmation' => 'differentpassword',
        ]);
        $response->assertSessionHasErrors('password');
    }

    public function test_guest_cannot_access_profile()
    {
        $response = $this->get('/profile');
        $response->assertRedirect('/login');
    }
}
