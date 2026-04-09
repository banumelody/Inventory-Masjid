<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Item;
use App\Models\Category;
use App\Models\Location;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DashboardAndNavigationTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $adminRole = Role::where('name', 'admin')->first();
        $this->admin = User::where('role_id', $adminRole->id)->first();
    }

    public function test_dashboard_loads_with_stats(): void
    {
        $response = $this->actingAs($this->admin)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Dashboard');
    }

    public function test_categories_page_loads(): void
    {
        $response = $this->actingAs($this->admin)->get(route('categories.index'));
        $response->assertStatus(200);
    }

    public function test_locations_page_loads(): void
    {
        $response = $this->actingAs($this->admin)->get(route('locations.index'));
        $response->assertStatus(200);
    }

    public function test_loans_page_loads(): void
    {
        $response = $this->actingAs($this->admin)->get(route('loans.index'));
        $response->assertStatus(200);
    }

    public function test_stock_movements_page_loads(): void
    {
        $response = $this->actingAs($this->admin)->get(route('stock-movements.index'));
        $response->assertStatus(200);
    }

    public function test_reports_page_loads(): void
    {
        $response = $this->actingAs($this->admin)->get(route('reports.index'));
        $response->assertStatus(200);
    }

    public function test_export_page_loads(): void
    {
        $response = $this->actingAs($this->admin)->get(route('export.index'));
        $response->assertStatus(200);
    }

    public function test_users_page_loads_for_admin(): void
    {
        $response = $this->actingAs($this->admin)->get(route('users.index'));
        $response->assertStatus(200);
    }

    public function test_backups_page_loads_for_admin(): void
    {
        $response = $this->actingAs($this->admin)->get(route('backups.index'));
        $response->assertStatus(200);
    }

    public function test_activity_logs_page_loads(): void
    {
        $response = $this->actingAs($this->admin)->get(route('activity-logs.index'));
        $response->assertStatus(200);
    }

    public function test_settings_page_loads(): void
    {
        $response = $this->actingAs($this->admin)->get(route('settings.index'));
        $response->assertStatus(200);
    }

    public function test_scan_page_loads(): void
    {
        $response = $this->actingAs($this->admin)->get(route('qrcode.scan'));
        $response->assertStatus(200);
    }

    public function test_help_faq_is_public(): void
    {
        $response = $this->get(route('help.faq'));
        $response->assertStatus(200);
    }

    public function test_help_guide_is_public(): void
    {
        $response = $this->get(route('help.guide'));
        $response->assertStatus(200);
    }
}
