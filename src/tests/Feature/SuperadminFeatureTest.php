<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Role;
use App\Models\Masjid;
use App\Models\Item;
use App\Models\Category;
use App\Models\Location;
use App\Models\Loan;

class SuperadminFeatureTest extends TestCase
{
    use RefreshDatabase;

    private User $superadmin;
    private User $adminA;
    private User $adminB;
    private Masjid $masjidA;
    private Masjid $masjidB;

    protected function setUp(): void
    {
        parent::setUp();

        $adminRole = Role::firstOrCreate(['name' => 'admin'], ['display_name' => 'Administrator']);
        Role::firstOrCreate(['name' => 'operator'], ['display_name' => 'Operator']);
        Role::firstOrCreate(['name' => 'viewer'], ['display_name' => 'Viewer']);

        $this->masjidA = Masjid::create([
            'name' => 'Masjid Alpha',
            'slug' => 'masjid-alpha',
            'address' => 'Jl. Alpha No.1',
            'city' => 'Jakarta',
            'province' => 'DKI Jakarta',
            'status' => 'active',
            'verified_at' => now(),
        ]);

        $this->masjidB = Masjid::create([
            'name' => 'Masjid Beta',
            'slug' => 'masjid-beta',
            'address' => 'Jl. Beta No.2',
            'city' => 'Bandung',
            'province' => 'Jawa Barat',
            'status' => 'active',
            'verified_at' => now(),
        ]);

        $this->superadmin = User::create([
            'name' => 'Super Admin',
            'email' => 'super@test.com',
            'password' => bcrypt('password'),
            'role_id' => $adminRole->id,
            'is_superadmin' => true,
            'masjid_id' => null,
        ]);

        $this->adminA = User::create([
            'name' => 'Admin Alpha',
            'email' => 'admin-alpha@test.com',
            'password' => bcrypt('password'),
            'role_id' => $adminRole->id,
            'is_superadmin' => false,
            'masjid_id' => $this->masjidA->id,
        ]);

        $this->adminB = User::create([
            'name' => 'Admin Beta',
            'email' => 'admin-beta@test.com',
            'password' => bcrypt('password'),
            'role_id' => $adminRole->id,
            'is_superadmin' => false,
            'masjid_id' => $this->masjidB->id,
        ]);
    }

    // --- Superadmin Dashboard ---

    public function test_superadmin_sees_platform_dashboard()
    {
        $response = $this->actingAs($this->superadmin)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Dashboard Superadmin');
        $response->assertSee('Total Masjid');
    }

    public function test_superadmin_dashboard_shows_correct_stats()
    {
        // Add items to both masjids
        $catA = Category::create(['name' => 'Cat A', 'masjid_id' => $this->masjidA->id]);
        $locA = Location::create(['name' => 'Loc A', 'masjid_id' => $this->masjidA->id]);
        Item::create(['name' => 'Item A1', 'quantity' => 5, 'unit' => 'pcs', 'condition' => 'baik', 'category_id' => $catA->id, 'location_id' => $locA->id, 'masjid_id' => $this->masjidA->id]);

        $catB = Category::create(['name' => 'Cat B', 'masjid_id' => $this->masjidB->id]);
        $locB = Location::create(['name' => 'Loc B', 'masjid_id' => $this->masjidB->id]);
        Item::create(['name' => 'Item B1', 'quantity' => 3, 'unit' => 'pcs', 'condition' => 'baik', 'category_id' => $catB->id, 'location_id' => $locB->id, 'masjid_id' => $this->masjidB->id]);

        $response = $this->actingAs($this->superadmin)->get('/dashboard');

        $response->assertStatus(200);
        // Should show 2 masjids and 2 items total
        $response->assertSee('Masjid Alpha');
        $response->assertSee('Masjid Beta');
    }

    public function test_regular_admin_sees_tenant_dashboard()
    {
        $response = $this->actingAs($this->adminA)->get('/dashboard');

        $response->assertStatus(200);
        // Should NOT see platform overview
        $response->assertDontSee('Dashboard Superadmin');
    }

    // --- Masjid Index ---

    public function test_superadmin_can_view_masjid_list()
    {
        $response = $this->actingAs($this->superadmin)->get('/masjids');

        $response->assertStatus(200);
        $response->assertSee('Kelola Masjid');
        $response->assertSee('Masjid Alpha');
        $response->assertSee('Masjid Beta');
    }

    public function test_regular_admin_can_view_masjid_list()
    {
        // Regular admin should be BLOCKED from masjid management
        $response = $this->actingAs($this->adminA)->get('/masjids');
        $response->assertStatus(403);
    }

    public function test_regular_admin_blocked_from_masjid_crud()
    {
        $this->actingAs($this->adminA)->get('/masjids/create')->assertStatus(403);
        $this->actingAs($this->adminA)->post('/masjids', ['name' => 'Test'])->assertStatus(403);
        $this->actingAs($this->adminA)->get("/masjids/{$this->masjidA->id}")->assertStatus(403);
        $this->actingAs($this->adminA)->get("/masjids/{$this->masjidA->id}/edit")->assertStatus(403);
        $this->actingAs($this->adminA)->put("/masjids/{$this->masjidA->id}", ['name' => 'X'])->assertStatus(403);
        $this->actingAs($this->adminA)->post('/masjids/switch', ['masjid_id' => '1'])->assertStatus(403);
    }

    public function test_masjid_list_shows_stats()
    {
        $catA = Category::create(['name' => 'Cat A', 'masjid_id' => $this->masjidA->id]);
        $locA = Location::create(['name' => 'Loc A', 'masjid_id' => $this->masjidA->id]);
        Item::create(['name' => 'Item A1', 'quantity' => 5, 'unit' => 'pcs', 'condition' => 'baik', 'category_id' => $catA->id, 'location_id' => $locA->id, 'masjid_id' => $this->masjidA->id]);

        $response = $this->actingAs($this->superadmin)->get('/masjids');

        $response->assertStatus(200);
        $response->assertSee('Total Masjid');
    }

    // --- Masjid Show ---

    public function test_superadmin_can_view_masjid_detail()
    {
        $response = $this->actingAs($this->superadmin)->get("/masjids/{$this->masjidA->id}");

        $response->assertStatus(200);
        $response->assertSee('Masjid Alpha');
        $response->assertSee('Jakarta');
        $response->assertSee('Admin Alpha');
    }

    public function test_masjid_detail_shows_items_and_loans()
    {
        $catA = Category::create(['name' => 'Elektronik', 'masjid_id' => $this->masjidA->id]);
        $locA = Location::create(['name' => 'Lantai 1', 'masjid_id' => $this->masjidA->id]);
        $item = Item::create(['name' => 'Proyektor', 'quantity' => 2, 'unit' => 'pcs', 'condition' => 'baik', 'category_id' => $catA->id, 'location_id' => $locA->id, 'masjid_id' => $this->masjidA->id]);

        Loan::create([
            'item_id' => $item->id,
            'borrower_name' => 'Pak Ahmad',
            'quantity' => 1,
            'borrowed_at' => now(),
            'due_at' => now()->addDays(7),
            'masjid_id' => $this->masjidA->id,
        ]);

        $response = $this->actingAs($this->superadmin)->get("/masjids/{$this->masjidA->id}");

        $response->assertStatus(200);
        $response->assertSee('Proyektor');
        $response->assertSee('Pak Ahmad');
    }

    // --- Masjid Create ---

    public function test_superadmin_can_create_masjid()
    {
        $response = $this->actingAs($this->superadmin)->get('/masjids/create');
        $response->assertStatus(200);
        $response->assertSee('Tambah Masjid');

        $response = $this->actingAs($this->superadmin)->post('/masjids', [
            'name' => 'Masjid Gamma',
            'slug' => 'masjid-gamma',
            'address' => 'Jl. Gamma No.3',
            'city' => 'Surabaya',
            'province' => 'Jawa Timur',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('masjids', ['name' => 'Masjid Gamma', 'status' => 'active']);
    }

    public function test_masjid_create_validates_required_fields()
    {
        $response = $this->actingAs($this->superadmin)->post('/masjids', []);

        $response->assertSessionHasErrors(['name', 'slug', 'address', 'city', 'province']);
    }

    public function test_masjid_create_validates_unique_slug()
    {
        $response = $this->actingAs($this->superadmin)->post('/masjids', [
            'name' => 'Masjid Duplicate',
            'slug' => 'masjid-alpha', // already exists
            'address' => 'Jl. Test',
            'city' => 'Test',
            'province' => 'Test',
        ]);

        $response->assertSessionHasErrors(['slug']);
    }

    // --- Masjid Edit ---

    public function test_superadmin_can_edit_masjid()
    {
        $response = $this->actingAs($this->superadmin)->get("/masjids/{$this->masjidA->id}/edit");
        $response->assertStatus(200);
        $response->assertSee('Edit Masjid');
        $response->assertSee('Masjid Alpha');

        $response = $this->actingAs($this->superadmin)->put("/masjids/{$this->masjidA->id}", [
            'name' => 'Masjid Alpha Updated',
            'slug' => 'masjid-alpha',
            'address' => 'Jl. Alpha No.1 Updated',
            'city' => 'Jakarta Selatan',
            'province' => 'DKI Jakarta',
            'status' => 'active',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('masjids', ['name' => 'Masjid Alpha Updated', 'city' => 'Jakarta Selatan']);
    }

    public function test_superadmin_can_suspend_masjid()
    {
        $response = $this->actingAs($this->superadmin)->put("/masjids/{$this->masjidB->id}", [
            'name' => 'Masjid Beta',
            'slug' => 'masjid-beta',
            'address' => 'Jl. Beta No.2',
            'city' => 'Bandung',
            'province' => 'Jawa Barat',
            'status' => 'suspended',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('masjids', ['id' => $this->masjidB->id, 'status' => 'suspended']);
    }

    // --- Tenant Switching ---

    public function test_superadmin_can_switch_to_masjid_context()
    {
        $response = $this->actingAs($this->superadmin)->post('/masjids/switch', [
            'masjid_id' => $this->masjidA->id,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $response->assertSessionHas('current_masjid_id', $this->masjidA->id);
    }

    public function test_superadmin_can_switch_back_to_all()
    {
        // First switch to a masjid
        $this->actingAs($this->superadmin)
            ->withSession(['current_masjid_id' => $this->masjidA->id])
            ->post('/masjids/switch', ['masjid_id' => 'all']);

        // Session should not have current_masjid_id anymore
        $response = $this->actingAs($this->superadmin)->post('/masjids/switch', [
            'masjid_id' => 'all',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $response->assertSessionMissing('current_masjid_id');
    }

    public function test_superadmin_with_context_sees_tenant_dashboard()
    {
        $response = $this->actingAs($this->superadmin)
            ->withSession(['current_masjid_id' => $this->masjidA->id])
            ->get('/dashboard');

        $response->assertStatus(200);
        // Should see tenant dashboard, not platform overview
        $response->assertDontSee('Dashboard Superadmin');
    }

    public function test_superadmin_with_context_sees_scoped_items()
    {
        $catA = Category::create(['name' => 'Cat A', 'masjid_id' => $this->masjidA->id]);
        $locA = Location::create(['name' => 'Loc A', 'masjid_id' => $this->masjidA->id]);
        Item::create(['name' => 'Item Alpha Only', 'quantity' => 1, 'unit' => 'pcs', 'condition' => 'baik', 'category_id' => $catA->id, 'location_id' => $locA->id, 'masjid_id' => $this->masjidA->id]);

        $catB = Category::create(['name' => 'Cat B', 'masjid_id' => $this->masjidB->id]);
        $locB = Location::create(['name' => 'Loc B', 'masjid_id' => $this->masjidB->id]);
        Item::create(['name' => 'Item Beta Only', 'quantity' => 1, 'unit' => 'pcs', 'condition' => 'baik', 'category_id' => $catB->id, 'location_id' => $locB->id, 'masjid_id' => $this->masjidB->id]);

        // Superadmin viewing with masjid A context
        $response = $this->actingAs($this->superadmin)
            ->withSession(['current_masjid_id' => $this->masjidA->id])
            ->get('/items');

        $response->assertStatus(200);
        $response->assertSee('Item Alpha Only');
        $response->assertDontSee('Item Beta Only');
    }

    // --- Sidebar / Navigation ---

    public function test_superadmin_sidebar_shows_masjid_management()
    {
        $response = $this->actingAs($this->superadmin)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Kelola Masjid');
        $response->assertSee('Superadmin');
        $response->assertSee('Konteks Masjid');
    }

    public function test_regular_admin_sidebar_hides_superadmin_section()
    {
        $response = $this->actingAs($this->adminA)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertDontSee('Superadmin');
        $response->assertDontSee('Konteks Masjid');
    }

    // --- Activity Log ---

    public function test_masjid_create_logs_activity()
    {
        $this->actingAs($this->superadmin)->post('/masjids', [
            'name' => 'Masjid Logged',
            'slug' => 'masjid-logged',
            'address' => 'Jl. Log No.1',
            'city' => 'Semarang',
            'province' => 'Jawa Tengah',
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'action' => 'create',
            'model_type' => \App\Models\Masjid::class,
            'user_id' => $this->superadmin->id,
        ]);
    }

    public function test_masjid_update_logs_activity()
    {
        $this->actingAs($this->superadmin)->put("/masjids/{$this->masjidA->id}", [
            'name' => 'Masjid Alpha V2',
            'slug' => 'masjid-alpha',
            'address' => 'Jl. Alpha No.1',
            'city' => 'Jakarta',
            'province' => 'DKI Jakarta',
            'status' => 'active',
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'action' => 'update',
            'model_type' => \App\Models\Masjid::class,
            'model_id' => $this->masjidA->id,
        ]);
    }

    // --- E2E Full Flow ---

    public function test_full_superadmin_workflow()
    {
        // 1. Login and see platform dashboard
        $response = $this->actingAs($this->superadmin)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Dashboard Superadmin');

        // 2. Go to masjid list
        $response = $this->actingAs($this->superadmin)->get('/masjids');
        $response->assertStatus(200);
        $response->assertSee('Masjid Alpha');
        $response->assertSee('Masjid Beta');

        // 3. Create a new masjid
        $response = $this->actingAs($this->superadmin)->post('/masjids', [
            'name' => 'Masjid Delta',
            'slug' => 'masjid-delta',
            'address' => 'Jl. Delta No.4',
            'city' => 'Medan',
            'province' => 'Sumatera Utara',
        ]);
        $response->assertRedirect();
        $masjidDelta = Masjid::where('slug', 'masjid-delta')->first();
        $this->assertNotNull($masjidDelta);

        // 4. View the new masjid detail
        $response = $this->actingAs($this->superadmin)->get("/masjids/{$masjidDelta->id}");
        $response->assertStatus(200);
        $response->assertSee('Masjid Delta');
        $response->assertSee('Medan');

        // 5. Switch to masjid A context
        $response = $this->actingAs($this->superadmin)
            ->post('/masjids/switch', ['masjid_id' => $this->masjidA->id]);
        $response->assertRedirect();

        // 6. Dashboard now shows tenant view
        $response = $this->actingAs($this->superadmin)
            ->withSession(['current_masjid_id' => $this->masjidA->id])
            ->get('/dashboard');
        $response->assertStatus(200);
        $response->assertDontSee('Dashboard Superadmin');

        // 7. Switch back to all
        $response = $this->actingAs($this->superadmin)
            ->withSession(['current_masjid_id' => $this->masjidA->id])
            ->post('/masjids/switch', ['masjid_id' => 'all']);
        $response->assertRedirect();

        // 8. Dashboard shows platform overview again
        $response = $this->actingAs($this->superadmin)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Dashboard Superadmin');
    }

    // --- P0-2: Orphan Record Guard ---

    public function test_superadmin_without_context_redirected_from_create_routes()
    {
        $createRoutes = [
            '/items/create',
            '/categories/create',
            '/locations/create',
            '/loans/create',
            '/stock-movements/create',
            '/maintenances/create',
            '/imports',
            '/users',
            '/settings',
            '/backups',
            '/feedbacks/create',
        ];

        foreach ($createRoutes as $route) {
            $response = $this->actingAs($this->superadmin)->get($route);
            $response->assertRedirect(route('dashboard'), "Route $route should redirect superadmin without context");
        }
    }

    public function test_superadmin_with_context_can_access_create_routes()
    {
        $catA = Category::create(['name' => 'Cat A', 'masjid_id' => $this->masjidA->id]);
        $locA = Location::create(['name' => 'Loc A', 'masjid_id' => $this->masjidA->id]);

        $routesThatShouldWork = [
            '/items/create',
            '/categories/create',
            '/locations/create',
            '/maintenances/create',
        ];

        foreach ($routesThatShouldWork as $route) {
            $response = $this->actingAs($this->superadmin)
                ->withSession(['current_masjid_id' => $this->masjidA->id])
                ->get($route);
            $response->assertStatus(200, "Route $route should work for superadmin with context");
        }
    }

    public function test_regular_admin_not_affected_by_context_middleware()
    {
        $catA = Category::create(['name' => 'Cat A', 'masjid_id' => $this->masjidA->id]);
        $locA = Location::create(['name' => 'Loc A', 'masjid_id' => $this->masjidA->id]);

        $response = $this->actingAs($this->adminA)->get('/items/create');
        $response->assertStatus(200);

        $response = $this->actingAs($this->adminA)->get('/categories/create');
        $response->assertStatus(200);
    }

    public function test_belongs_to_masjid_trait_sets_context_automatically()
    {
        app()->instance('current_masjid_id', $this->masjidA->id);
        $category = Category::create(['name' => 'Auto Context Category']);
        $this->assertEquals($this->masjidA->id, $category->masjid_id);
    }

    // --- P2-10: Delete Masjid ---

    public function test_superadmin_can_delete_masjid()
    {
        $catA = Category::create(['name' => 'Cat A', 'masjid_id' => $this->masjidA->id]);
        $locA = Location::create(['name' => 'Loc A', 'masjid_id' => $this->masjidA->id]);
        Item::create([
            'name' => 'Item A', 'quantity' => 1, 'unit' => 'pcs', 'condition' => 'baik',
            'category_id' => $catA->id, 'location_id' => $locA->id, 'masjid_id' => $this->masjidA->id,
        ]);

        $response = $this->actingAs($this->superadmin)
            ->delete("/masjids/{$this->masjidA->id}");

        $response->assertRedirect(route('masjids.index'));
        $this->assertDatabaseMissing('masjids', ['id' => $this->masjidA->id]);
        $this->assertDatabaseMissing('categories', ['masjid_id' => $this->masjidA->id]);
        $this->assertDatabaseMissing('items', ['masjid_id' => $this->masjidA->id]);
        $this->assertDatabaseMissing('users', ['masjid_id' => $this->masjidA->id]);
    }
}
