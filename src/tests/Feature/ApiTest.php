<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Item;
use App\Models\Loan;
use App\Models\Location;
use App\Models\Masjid;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiTest extends TestCase
{
    use RefreshDatabase;

    private Masjid $masjidA;
    private Masjid $masjidB;
    private User $adminA;
    private User $adminB;
    private User $superadmin;
    private Role $adminRole;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminRole = Role::where('name', 'admin')->first()
            ?? Role::create(['name' => 'admin', 'display_name' => 'Admin']);

        $this->masjidA = Masjid::create([
            'name' => 'Masjid Alpha',
            'slug' => 'masjid-alpha',
            'address' => 'Jl. Alpha',
            'city' => 'Jakarta',
            'province' => 'DKI Jakarta',
            'status' => 'active',
        ]);

        $this->masjidB = Masjid::create([
            'name' => 'Masjid Beta',
            'slug' => 'masjid-beta',
            'address' => 'Jl. Beta',
            'city' => 'Bandung',
            'province' => 'Jawa Barat',
            'status' => 'active',
        ]);

        $this->adminA = User::create([
            'name' => 'Admin A',
            'email' => 'admina@test.com',
            'password' => bcrypt('password'),
            'role_id' => $this->adminRole->id,
            'masjid_id' => $this->masjidA->id,
        ]);

        $this->adminB = User::create([
            'name' => 'Admin B',
            'email' => 'adminb@test.com',
            'password' => bcrypt('password'),
            'role_id' => $this->adminRole->id,
            'masjid_id' => $this->masjidB->id,
        ]);

        $this->superadmin = User::create([
            'name' => 'Super Admin',
            'email' => 'super@test.com',
            'password' => bcrypt('password'),
            'role_id' => $this->adminRole->id,
            'is_superadmin' => true,
            'masjid_id' => null,
        ]);
    }

    public function test_login_returns_token(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'admina@test.com',
            'password' => 'password',
            'device_name' => 'test-device',
        ]);

        $response->assertOk()
            ->assertJsonStructure(['user' => ['id', 'name', 'email', 'role', 'masjid_id'], 'token']);
    }

    public function test_login_fails_with_wrong_password(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'admina@test.com',
            'password' => 'wrong',
            'device_name' => 'test-device',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors('email');
    }

    public function test_unauthenticated_request_returns_401(): void
    {
        $response = $this->getJson('/api/items');

        $response->assertUnauthorized();
    }

    public function test_authenticated_user_can_get_me(): void
    {
        $token = $this->adminA->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/auth/me');

        $response->assertOk()
            ->assertJson([
                'id' => $this->adminA->id,
                'name' => 'Admin A',
                'email' => 'admina@test.com',
            ]);
    }

    public function test_logout_revokes_token(): void
    {
        $token = $this->adminA->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/auth/logout');

        $response->assertOk()
            ->assertJson(['message' => 'Berhasil logout.']);

        // Verify token was deleted from DB
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_items_scoped_to_tenant(): void
    {
        app()->instance('current_masjid_id', $this->masjidA->id);
        $catA = Category::create(['name' => 'Cat A', 'masjid_id' => $this->masjidA->id]);
        $locA = Location::create(['name' => 'Loc A', 'masjid_id' => $this->masjidA->id]);
        $itemA = Item::create([
            'name' => 'Sajadah A',
            'quantity' => 10,
            'unit' => 'pcs',
            'condition' => 'baik',
            'category_id' => $catA->id,
            'location_id' => $locA->id,
            'masjid_id' => $this->masjidA->id,
        ]);

        app()->instance('current_masjid_id', $this->masjidB->id);
        $catB = Category::create(['name' => 'Cat B', 'masjid_id' => $this->masjidB->id]);
        $locB = Location::create(['name' => 'Loc B', 'masjid_id' => $this->masjidB->id]);
        Item::create([
            'name' => 'Sajadah B',
            'quantity' => 5,
            'unit' => 'pcs',
            'condition' => 'baik',
            'category_id' => $catB->id,
            'location_id' => $locB->id,
            'masjid_id' => $this->masjidB->id,
        ]);

        // Admin A should only see masjid A items
        $token = $this->adminA->createToken('test')->plainTextToken;
        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/items');

        $response->assertOk();
        $names = collect($response->json('data'))->pluck('name')->toArray();
        $this->assertContains('Sajadah A', $names);
        $this->assertNotContains('Sajadah B', $names);
    }

    public function test_superadmin_with_header_sees_specific_masjid(): void
    {
        app()->instance('current_masjid_id', $this->masjidA->id);
        $catA = Category::create(['name' => 'Cat A', 'masjid_id' => $this->masjidA->id]);
        $locA = Location::create(['name' => 'Loc A', 'masjid_id' => $this->masjidA->id]);
        Item::create([
            'name' => 'Item Alpha',
            'quantity' => 3,
            'unit' => 'pcs',
            'condition' => 'baik',
            'category_id' => $catA->id,
            'location_id' => $locA->id,
            'masjid_id' => $this->masjidA->id,
        ]);

        app()->instance('current_masjid_id', $this->masjidB->id);
        $catB = Category::create(['name' => 'Cat B', 'masjid_id' => $this->masjidB->id]);
        $locB = Location::create(['name' => 'Loc B', 'masjid_id' => $this->masjidB->id]);
        Item::create([
            'name' => 'Item Beta',
            'quantity' => 7,
            'unit' => 'pcs',
            'condition' => 'baik',
            'category_id' => $catB->id,
            'location_id' => $locB->id,
            'masjid_id' => $this->masjidB->id,
        ]);

        $token = $this->superadmin->createToken('test')->plainTextToken;

        // With X-Masjid-Id header pointing to masjid A
        $response = $this->withHeaders([
            'Authorization' => "Bearer {$token}",
            'X-Masjid-Id' => (string) $this->masjidA->id,
        ])->getJson('/api/items');

        $response->assertOk();
        $names = collect($response->json('data'))->pluck('name')->toArray();
        $this->assertContains('Item Alpha', $names);
        $this->assertNotContains('Item Beta', $names);
    }

    public function test_stats_endpoint(): void
    {
        app()->instance('current_masjid_id', $this->masjidA->id);
        $cat = Category::create(['name' => 'Furniture', 'masjid_id' => $this->masjidA->id]);
        $loc = Location::create(['name' => 'Gudang', 'masjid_id' => $this->masjidA->id]);
        Item::create([
            'name' => 'Meja',
            'quantity' => 5,
            'unit' => 'pcs',
            'condition' => 'baik',
            'category_id' => $cat->id,
            'location_id' => $loc->id,
            'masjid_id' => $this->masjidA->id,
        ]);

        $token = $this->adminA->createToken('test')->plainTextToken;
        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/stats');

        $response->assertOk()
            ->assertJsonStructure([
                'total_items',
                'total_quantity',
                'items_good',
                'active_loans',
                'overdue_loans',
                'total_categories',
                'total_locations',
            ]);
    }

    public function test_categories_endpoint(): void
    {
        app()->instance('current_masjid_id', $this->masjidA->id);
        Category::create(['name' => 'Elektronik', 'masjid_id' => $this->masjidA->id]);

        $token = $this->adminA->createToken('test')->plainTextToken;
        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/categories');

        $response->assertOk();
        $this->assertNotEmpty($response->json());
    }

    public function test_loans_endpoint_with_status_filter(): void
    {
        app()->instance('current_masjid_id', $this->masjidA->id);
        $cat = Category::create(['name' => 'Cat', 'masjid_id' => $this->masjidA->id]);
        $loc = Location::create(['name' => 'Ruang Utama', 'masjid_id' => $this->masjidA->id]);
        $item = Item::create([
            'name' => 'Proyektor',
            'quantity' => 2,
            'unit' => 'pcs',
            'condition' => 'baik',
            'category_id' => $cat->id,
            'location_id' => $loc->id,
            'masjid_id' => $this->masjidA->id,
        ]);

        Loan::create([
            'item_id' => $item->id,
            'borrower_name' => 'Pak Ahmad',
            'quantity' => 1,
            'borrowed_at' => now(),
            'due_at' => now()->addDays(7),
            'masjid_id' => $this->masjidA->id,
        ]);

        $token = $this->adminA->createToken('test')->plainTextToken;

        // Get active loans
        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/loans?status=active');

        $response->assertOk();
        $this->assertEquals(1, count($response->json('data')));
    }
}
