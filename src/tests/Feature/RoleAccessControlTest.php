<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RoleAccessControlTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $operator;
    protected User $viewer;

    protected function setUp(): void
    {
        parent::setUp();

        $adminRole = Role::where('name', 'admin')->first();
        $operatorRole = Role::firstOrCreate(
            ['name' => 'operator'],
            ['display_name' => 'Operator']
        );
        $viewerRole = Role::firstOrCreate(
            ['name' => 'viewer'],
            ['display_name' => 'Viewer']
        );

        $this->admin = User::where('role_id', $adminRole->id)->first();

        $this->operator = User::create([
            'name' => 'Operator',
            'email' => 'operator@test.com',
            'password' => bcrypt('password'),
            'role_id' => $operatorRole->id,
        ]);

        $this->viewer = User::create([
            'name' => 'Viewer',
            'email' => 'viewer@test.com',
            'password' => bcrypt('password'),
            'role_id' => $viewerRole->id,
        ]);
    }

    // Admin-only routes
    public function test_viewer_cannot_access_users_page(): void
    {
        $response = $this->actingAs($this->viewer)->get(route('users.index'));
        $response->assertStatus(403);
    }

    public function test_operator_cannot_access_users_page(): void
    {
        $response = $this->actingAs($this->operator)->get(route('users.index'));
        $response->assertStatus(403);
    }

    public function test_viewer_cannot_access_backups(): void
    {
        $response = $this->actingAs($this->viewer)->get(route('backups.index'));
        $response->assertStatus(403);
    }

    public function test_viewer_cannot_access_settings(): void
    {
        $response = $this->actingAs($this->viewer)->get(route('settings.index'));
        $response->assertStatus(403);
    }

    public function test_viewer_cannot_access_activity_logs(): void
    {
        $response = $this->actingAs($this->viewer)->get(route('activity-logs.index'));
        $response->assertStatus(403);
    }

    // Operator can access item CRUD
    public function test_operator_can_access_item_create(): void
    {
        $response = $this->actingAs($this->operator)->get(route('items.create'));
        $response->assertStatus(200);
    }

    public function test_operator_can_access_loan_create(): void
    {
        $response = $this->actingAs($this->operator)->get(route('loans.create'));
        $response->assertStatus(200);
    }

    // All roles can view items and reports
    public function test_viewer_can_view_items(): void
    {
        $response = $this->actingAs($this->viewer)->get(route('items.index'));
        $response->assertStatus(200);
    }

    public function test_viewer_can_view_reports(): void
    {
        $response = $this->actingAs($this->viewer)->get(route('reports.index'));
        $response->assertStatus(200);
    }

    public function test_viewer_can_access_export(): void
    {
        $response = $this->actingAs($this->viewer)->get(route('export.index'));
        $response->assertStatus(200);
    }

    public function test_viewer_can_submit_feedback(): void
    {
        $response = $this->actingAs($this->viewer)->get(route('feedbacks.create'));
        $response->assertStatus(200);
    }

    public function test_viewer_cannot_manage_feedbacks(): void
    {
        $response = $this->actingAs($this->viewer)->get(route('feedbacks.index'));
        $response->assertStatus(403);
    }
}
