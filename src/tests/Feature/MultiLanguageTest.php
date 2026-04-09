<?php

namespace Tests\Feature;

use App\Models\Masjid;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MultiLanguageTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Masjid $masjid;

    protected function setUp(): void
    {
        parent::setUp();

        $this->masjid = Masjid::create([
            'name' => 'Masjid Lang Test',
            'slug' => 'masjid-lang-test',
            'address' => 'Jl. Test',
            'city' => 'Jakarta',
            'province' => 'DKI Jakarta',
        ]);

        $adminRole = Role::where('name', 'admin')->first();

        $this->admin = User::create([
            'name' => 'Admin Lang',
            'email' => 'admin@lang.test',
            'password' => bcrypt('password'),
            'role_id' => $adminRole->id,
            'masjid_id' => $this->masjid->id,
        ]);
    }

    public function test_default_locale_is_indonesian(): void
    {
        $this->assertEquals('id', config('app.locale'));
    }

    public function test_can_switch_to_english(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('language.switch', 'en'));

        $response->assertRedirect();

        // After switch, sidebar should show English labels
        $response = $this->actingAs($this->admin)
            ->withSession(['current_masjid_id' => $this->masjid->id, 'locale' => 'en'])
            ->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Inventory'); // English for "Inventaris"
    }

    public function test_can_switch_back_to_indonesian(): void
    {
        $response = $this->actingAs($this->admin)
            ->withSession(['locale' => 'en'])
            ->post(route('language.switch', 'id'));

        $response->assertRedirect();

        $response = $this->actingAs($this->admin)
            ->withSession(['current_masjid_id' => $this->masjid->id, 'locale' => 'id'])
            ->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Inventaris');
    }

    public function test_invalid_locale_ignored(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('language.switch', 'xx'));

        $response->assertRedirect();
    }

    public function test_translation_files_exist(): void
    {
        $this->assertFileExists(lang_path('id/ui.php'));
        $this->assertFileExists(lang_path('en/ui.php'));

        $id = require lang_path('id/ui.php');
        $en = require lang_path('en/ui.php');

        // Both files should have the same keys
        $this->assertEquals(array_keys($id), array_keys($en));
    }

    public function test_language_switcher_visible_in_sidebar(): void
    {
        $response = $this->actingAs($this->admin)
            ->withSession(['current_masjid_id' => $this->masjid->id])
            ->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('🌐');
    }
}
