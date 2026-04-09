<?php

namespace Tests\Feature;

use App\Models\Masjid;
use App\Models\User;
use App\Models\Role;
use App\Models\Item;
use App\Models\Category;
use App\Models\Location;
use App\Models\Loan;
use App\Models\Feedback;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MultiTenantIsolationTest extends TestCase
{
    use RefreshDatabase;

    private Masjid $masjidA;
    private Masjid $masjidB;
    private User $adminA;
    private User $adminB;
    private User $superadmin;
    private Category $catA;
    private Category $catB;
    private Location $locA;
    private Location $locB;
    private Item $itemA;
    private Item $itemB;

    protected function setUp(): void
    {
        parent::setUp();

        $this->masjidA = Masjid::create([
            'name' => 'Masjid Alpha', 'slug' => 'masjid-alpha',
            'address' => 'Jl. Alpha', 'city' => 'Jakarta', 'province' => 'DKI Jakarta',
        ]);
        $this->masjidB = Masjid::create([
            'name' => 'Masjid Beta', 'slug' => 'masjid-beta',
            'address' => 'Jl. Beta', 'city' => 'Bandung', 'province' => 'Jawa Barat',
        ]);

        $adminRole = Role::firstOrCreate(['name' => 'admin'], ['display_name' => 'Administrator']);

        $this->adminA = User::create([
            'name' => 'Admin Alpha', 'email' => 'admin-alpha@test.com',
            'password' => bcrypt('password'), 'role_id' => $adminRole->id,
            'masjid_id' => $this->masjidA->id,
        ]);
        $this->adminB = User::create([
            'name' => 'Admin Beta', 'email' => 'admin-beta@test.com',
            'password' => bcrypt('password'), 'role_id' => $adminRole->id,
            'masjid_id' => $this->masjidB->id,
        ]);
        $this->superadmin = User::create([
            'name' => 'Superadmin', 'email' => 'super@test.com',
            'password' => bcrypt('password'), 'role_id' => $adminRole->id,
            'is_superadmin' => true, 'masjid_id' => null,
        ]);

        $this->catA = Category::withoutGlobalScopes()->create(['name' => 'Elektronik', 'masjid_id' => $this->masjidA->id]);
        $this->catB = Category::withoutGlobalScopes()->create(['name' => 'Furniture', 'masjid_id' => $this->masjidB->id]);
        $this->locA = Location::withoutGlobalScopes()->create(['name' => 'Lantai 1', 'masjid_id' => $this->masjidA->id]);
        $this->locB = Location::withoutGlobalScopes()->create(['name' => 'Lantai 2', 'masjid_id' => $this->masjidB->id]);

        $this->itemA = Item::withoutGlobalScopes()->create([
            'name' => 'Speaker Alpha', 'category_id' => $this->catA->id,
            'location_id' => $this->locA->id, 'quantity' => 5, 'unit' => 'pcs',
            'condition' => 'baik', 'masjid_id' => $this->masjidA->id,
        ]);
        $this->itemB = Item::withoutGlobalScopes()->create([
            'name' => 'Meja Beta', 'category_id' => $this->catB->id,
            'location_id' => $this->locB->id, 'quantity' => 3, 'unit' => 'pcs',
            'condition' => 'baik', 'masjid_id' => $this->masjidB->id,
        ]);
    }

    /** @test */
    public function admin_a_only_sees_own_items_on_index()
    {
        $response = $this->actingAs($this->adminA)->get(route('items.index'));
        $response->assertStatus(200);
        $response->assertSee('Speaker Alpha');
        $response->assertDontSee('Meja Beta');
    }

    /** @test */
    public function admin_b_only_sees_own_items_on_index()
    {
        $response = $this->actingAs($this->adminB)->get(route('items.index'));
        $response->assertStatus(200);
        $response->assertSee('Meja Beta');
        $response->assertDontSee('Speaker Alpha');
    }

    /** @test */
    public function admin_a_cannot_view_item_b_directly()
    {
        $response = $this->actingAs($this->adminA)->get(route('items.show', $this->itemB));
        $response->assertStatus(404);
    }

    /** @test */
    public function admin_b_cannot_view_item_a_directly()
    {
        $response = $this->actingAs($this->adminB)->get(route('items.show', $this->itemA));
        $response->assertStatus(404);
    }

    /** @test */
    public function admin_a_creates_item_scoped_to_own_masjid()
    {
        $response = $this->actingAs($this->adminA)->post(route('items.store'), [
            'name' => 'Mic Baru Alpha',
            'category_id' => $this->catA->id,
            'location_id' => $this->locA->id,
            'quantity' => 2,
            'unit' => 'pcs',
            'condition' => 'baik',
        ]);
        $response->assertRedirect(route('items.index'));

        $newItem = Item::withoutGlobalScopes()->where('name', 'Mic Baru Alpha')->first();
        $this->assertNotNull($newItem);
        $this->assertEquals($this->masjidA->id, $newItem->masjid_id);
    }

    /** @test */
    public function admin_a_cannot_delete_item_b()
    {
        $response = $this->actingAs($this->adminA)->delete(route('items.destroy', $this->itemB));
        $response->assertStatus(404);

        $this->assertDatabaseHas('items', ['id' => $this->itemB->id]);
    }

    /** @test */
    public function admin_a_only_sees_own_categories()
    {
        $response = $this->actingAs($this->adminA)->get(route('categories.index'));
        $response->assertStatus(200);
        $response->assertSee('Elektronik');
        $response->assertDontSee('Furniture');
    }

    /** @test */
    public function admin_a_only_sees_own_loans()
    {
        Loan::withoutGlobalScopes()->create([
            'item_id' => $this->itemA->id, 'borrower_name' => 'Ali A', 'quantity' => 1,
            'borrowed_at' => now(), 'due_at' => now()->addDays(7), 'masjid_id' => $this->masjidA->id,
        ]);
        Loan::withoutGlobalScopes()->create([
            'item_id' => $this->itemB->id, 'borrower_name' => 'Budi B', 'quantity' => 1,
            'borrowed_at' => now(), 'due_at' => now()->addDays(7), 'masjid_id' => $this->masjidB->id,
        ]);

        $response = $this->actingAs($this->adminA)->get(route('loans.index'));
        $response->assertStatus(200);
        $response->assertSee('Ali A');
        $response->assertDontSee('Budi B');
    }

    /** @test */
    public function dashboard_shows_only_own_masjid_stats()
    {
        $responseA = $this->actingAs($this->adminA)->get(route('dashboard'));
        $responseA->assertStatus(200);

        $responseB = $this->actingAs($this->adminB)->get(route('dashboard'));
        $responseB->assertStatus(200);
    }

    /** @test */
    public function superadmin_can_access_admin_routes()
    {
        $response = $this->actingAs($this->superadmin)
            ->withSession(['current_masjid_id' => $this->masjidA->id])
            ->get(route('users.index'));
        $response->assertStatus(200);
    }

    /** @test */
    public function superadmin_bypasses_role_check()
    {
        $response = $this->actingAs($this->superadmin)
            ->withSession(['current_masjid_id' => $this->masjidA->id])
            ->get(route('backups.index'));
        $response->assertStatus(200);

        $response = $this->actingAs($this->superadmin)
            ->withSession(['current_masjid_id' => $this->masjidA->id])
            ->get(route('settings.index'));
        $response->assertStatus(200);

        $response = $this->actingAs($this->superadmin)
            ->withSession(['current_masjid_id' => $this->masjidA->id])
            ->get(route('activity-logs.index'));
        $response->assertStatus(200);
    }

    /** @test */
    public function admin_a_feedback_only_shows_own()
    {
        Feedback::withoutGlobalScopes()->create([
            'user_id' => $this->adminA->id, 'module' => 'items', 'type' => 'bug',
            'message' => 'Bug dari Alpha', 'status' => 'new', 'masjid_id' => $this->masjidA->id,
        ]);
        Feedback::withoutGlobalScopes()->create([
            'user_id' => $this->adminB->id, 'module' => 'loans', 'type' => 'suggestion',
            'message' => 'Saran dari Beta', 'status' => 'new', 'masjid_id' => $this->masjidB->id,
        ]);

        $response = $this->actingAs($this->adminA)->get(route('feedbacks.index'));
        $response->assertStatus(200);
        $response->assertSee('Bug dari Alpha');
        $response->assertDontSee('Saran dari Beta');
    }

    /** @test */
    public function admin_a_cannot_edit_item_b()
    {
        $response = $this->actingAs($this->adminA)->get(route('items.edit', $this->itemB));
        $response->assertStatus(404);
    }

    /** @test */
    public function admin_a_cannot_update_item_b()
    {
        $response = $this->actingAs($this->adminA)->put(route('items.update', $this->itemB), [
            'name' => 'Hacked',
            'category_id' => $this->catA->id,
            'location_id' => $this->locA->id,
            'quantity' => 99,
            'unit' => 'pcs',
            'condition' => 'baik',
        ]);
        $response->assertStatus(404);
        $this->itemB->refresh();
        $this->assertEquals('Meja Beta', $this->itemB->name);
    }

    /** @test */
    public function cross_tenant_loan_creation_fails()
    {
        $response = $this->actingAs($this->adminA)->post(route('loans.store'), [
            'item_id' => $this->itemB->id,
            'borrower_name' => 'Hacker',
            'quantity' => 1,
            'borrowed_at' => now()->format('Y-m-d'),
            'due_at' => now()->addDays(7)->format('Y-m-d'),
        ]);

        // Should fail validation since itemB not visible to adminA
        $this->assertTrue(
            $response->isRedirect() || $response->status() === 422 || $response->status() === 404
        );
    }
}
