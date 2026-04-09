<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Masjid;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_page_loads()
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
        $response->assertSee('Daftarkan Masjid Anda');
    }

    public function test_can_register_new_masjid()
    {
        $response = $this->post('/register', [
            'masjid_name' => 'Masjid Baru Test',
            'address' => 'Jl. Test No. 1',
            'city' => 'Depok',
            'province' => 'Jawa Barat',
            'admin_name' => 'Admin Baru',
            'admin_email' => 'admin@baru.test',
            'admin_password' => 'password123',
            'admin_password_confirmation' => 'password123',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('masjids', ['name' => 'Masjid Baru Test', 'city' => 'Depok']);
        $this->assertDatabaseHas('users', ['email' => 'admin@baru.test']);

        $masjid = Masjid::where('name', 'Masjid Baru Test')->first();
        $this->assertNotNull($masjid);
        $this->assertEquals('active', $masjid->status);

        $admin = User::where('email', 'admin@baru.test')->first();
        $this->assertEquals($masjid->id, $admin->masjid_id);
        $this->assertAuthenticatedAs($admin);
    }

    public function test_registration_validates_required_fields()
    {
        $response = $this->post('/register', []);
        $response->assertSessionHasErrors(['masjid_name', 'address', 'city', 'province', 'admin_name', 'admin_email', 'admin_password']);
    }

    public function test_registration_requires_unique_email()
    {
        $role = \App\Models\Role::where('name', 'admin')->first();
        User::create([
            'name' => 'Existing',
            'email' => 'existing@test.com',
            'password' => bcrypt('password'),
            'role_id' => $role->id,
        ]);

        $response = $this->post('/register', [
            'masjid_name' => 'Masjid Test',
            'address' => 'Jl. Test',
            'city' => 'Jakarta',
            'province' => 'DKI Jakarta',
            'admin_name' => 'Admin',
            'admin_email' => 'existing@test.com',
            'admin_password' => 'password123',
            'admin_password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('admin_email');
    }

    public function test_authenticated_user_cannot_access_registration()
    {
        $role = \App\Models\Role::where('name', 'admin')->first();
        $user = User::create([
            'name' => 'Auth User',
            'email' => 'auth@test.com',
            'password' => bcrypt('password'),
            'role_id' => $role->id,
        ]);

        $response = $this->actingAs($user)->get('/register');
        $response->assertRedirect();
    }
}
