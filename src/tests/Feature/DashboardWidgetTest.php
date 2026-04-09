<?php

namespace Tests\Feature;

use App\Models\Masjid;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardWidgetTest extends TestCase
{
    use RefreshDatabase;

    private Masjid $masjid;
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->masjid = Masjid::create([
            'name' => 'Masjid Widget Test',
            'slug' => 'masjid-widget-test',
            'address' => 'Jl. Test',
            'city' => 'Jakarta',
            'province' => 'DKI Jakarta',
        ]);

        $adminRole = Role::where('name', 'admin')->first();

        $this->admin = User::create([
            'name' => 'Admin Widget',
            'email' => 'admin@widget.test',
            'password' => bcrypt('password'),
            'role_id' => $adminRole->id,
            'masjid_id' => $this->masjid->id,
        ]);
    }

    public function test_dashboard_shows_widget_settings_button(): void
    {
        $response = $this->actingAs($this->admin)
            ->withSession(['current_masjid_id' => $this->masjid->id])
            ->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Atur Widget');
    }

    public function test_can_save_widget_preferences(): void
    {
        $response = $this->actingAs($this->admin)
            ->withSession(['current_masjid_id' => $this->masjid->id])
            ->postJson(route('dashboard.updateWidgets'), [
                'widgets' => [
                    'stats_overview' => true,
                    'charts' => false,
                    'most_borrowed' => false,
                    'condition_summary' => true,
                    'quick_actions' => true,
                ],
            ]);

        $response->assertOk();
        $response->assertJson(['success' => true]);

        $this->admin->refresh();
        $this->assertFalse($this->admin->dashboard_widgets['charts']);
        $this->assertTrue($this->admin->dashboard_widgets['stats_overview']);
    }

    public function test_default_widget_preferences(): void
    {
        $prefs = $this->admin->getWidgetPreferences();

        $this->assertTrue($prefs['stats_overview']);
        $this->assertTrue($prefs['charts']);
        $this->assertTrue($prefs['quick_actions']);
    }

    public function test_widget_enabled_check(): void
    {
        $this->admin->update(['dashboard_widgets' => ['charts' => false]]);

        $this->assertFalse($this->admin->isWidgetEnabled('charts'));
        $this->assertTrue($this->admin->isWidgetEnabled('stats_overview'));
    }

    public function test_dashboard_respects_widget_preferences(): void
    {
        $this->admin->update(['dashboard_widgets' => ['quick_actions' => false]]);

        $response = $this->actingAs($this->admin)
            ->withSession(['current_masjid_id' => $this->masjid->id])
            ->get(route('dashboard'));

        $response->assertStatus(200);
        // The section heading with emoji should not appear when widget is disabled
        $response->assertDontSee('⚡ Aksi Cepat');
    }
}
