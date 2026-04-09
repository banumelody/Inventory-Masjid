<?php

namespace Tests\Unit;

use App\Models\Masjid;
use App\Models\Item;
use App\Models\Category;
use App\Models\Location;
use App\Models\Loan;
use App\Models\StockMovement;
use App\Models\Maintenance;
use App\Models\Feedback;
use App\Models\ActivityLog;
use App\Models\ScanLog;
use App\Models\Backup;
use App\Models\ImportLog;
use App\Models\Setting;
use App\Models\User;
use App\Models\Role;
use App\Scopes\MasjidScope;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MultiTenantScopeTest extends TestCase
{
    use RefreshDatabase;

    private Masjid $masjidA;
    private Masjid $masjidB;
    private Category $catA;
    private Category $catB;
    private Location $locA;
    private Location $locB;

    protected function setUp(): void
    {
        parent::setUp();

        $this->masjidA = Masjid::create([
            'name' => 'Masjid Alpha',
            'slug' => 'masjid-alpha',
            'address' => 'Jl. Alpha',
            'city' => 'Jakarta',
            'province' => 'DKI Jakarta',
        ]);
        $this->masjidB = Masjid::create([
            'name' => 'Masjid Beta',
            'slug' => 'masjid-beta',
            'address' => 'Jl. Beta',
            'city' => 'Bandung',
            'province' => 'Jawa Barat',
        ]);

        // Create categories/locations without scope (use withoutGlobalScopes)
        $this->catA = Category::withoutGlobalScopes()->create(['name' => 'Cat Alpha', 'masjid_id' => $this->masjidA->id]);
        $this->catB = Category::withoutGlobalScopes()->create(['name' => 'Cat Beta', 'masjid_id' => $this->masjidB->id]);
        $this->locA = Location::withoutGlobalScopes()->create(['name' => 'Loc Alpha', 'masjid_id' => $this->masjidA->id]);
        $this->locB = Location::withoutGlobalScopes()->create(['name' => 'Loc Beta', 'masjid_id' => $this->masjidB->id]);
    }

    private function setMasjidContext(?int $masjidId): void
    {
        if ($masjidId) {
            app()->instance('current_masjid_id', $masjidId);
        } else {
            app()->forgetInstance('current_masjid_id');
        }
    }

    /** @test */
    public function items_are_scoped_to_masjid()
    {
        Item::withoutGlobalScopes()->create([
            'name' => 'Item A', 'category_id' => $this->catA->id, 'location_id' => $this->locA->id,
            'quantity' => 5, 'unit' => 'pcs', 'condition' => 'baik', 'masjid_id' => $this->masjidA->id,
        ]);
        Item::withoutGlobalScopes()->create([
            'name' => 'Item B', 'category_id' => $this->catB->id, 'location_id' => $this->locB->id,
            'quantity' => 3, 'unit' => 'pcs', 'condition' => 'baik', 'masjid_id' => $this->masjidB->id,
        ]);

        $this->setMasjidContext($this->masjidA->id);
        $items = Item::all();
        $this->assertCount(1, $items);
        $this->assertEquals('Item A', $items->first()->name);

        $this->setMasjidContext($this->masjidB->id);
        $items = Item::all();
        $this->assertCount(1, $items);
        $this->assertEquals('Item B', $items->first()->name);
    }

    /** @test */
    public function categories_are_scoped_to_masjid()
    {
        $this->setMasjidContext($this->masjidA->id);
        $this->assertCount(1, Category::all());
        $this->assertEquals('Cat Alpha', Category::first()->name);

        $this->setMasjidContext($this->masjidB->id);
        $this->assertCount(1, Category::all());
        $this->assertEquals('Cat Beta', Category::first()->name);
    }

    /** @test */
    public function locations_are_scoped_to_masjid()
    {
        $this->setMasjidContext($this->masjidA->id);
        $this->assertCount(1, Location::all());
        $this->assertEquals('Loc Alpha', Location::first()->name);

        $this->setMasjidContext($this->masjidB->id);
        $this->assertCount(1, Location::all());
        $this->assertEquals('Loc Beta', Location::first()->name);
    }

    /** @test */
    public function loans_are_scoped_to_masjid()
    {
        $itemA = Item::withoutGlobalScopes()->create([
            'name' => 'Loan Item A', 'category_id' => $this->catA->id, 'location_id' => $this->locA->id,
            'quantity' => 10, 'unit' => 'pcs', 'condition' => 'baik', 'masjid_id' => $this->masjidA->id,
        ]);
        $itemB = Item::withoutGlobalScopes()->create([
            'name' => 'Loan Item B', 'category_id' => $this->catB->id, 'location_id' => $this->locB->id,
            'quantity' => 10, 'unit' => 'pcs', 'condition' => 'baik', 'masjid_id' => $this->masjidB->id,
        ]);

        Loan::withoutGlobalScopes()->create([
            'item_id' => $itemA->id, 'borrower_name' => 'Ali', 'quantity' => 1,
            'borrowed_at' => now(), 'due_at' => now()->addDays(7), 'masjid_id' => $this->masjidA->id,
        ]);
        Loan::withoutGlobalScopes()->create([
            'item_id' => $itemB->id, 'borrower_name' => 'Budi', 'quantity' => 1,
            'borrowed_at' => now(), 'due_at' => now()->addDays(7), 'masjid_id' => $this->masjidB->id,
        ]);

        $this->setMasjidContext($this->masjidA->id);
        $this->assertCount(1, Loan::all());
        $this->assertEquals('Ali', Loan::first()->borrower_name);

        $this->setMasjidContext($this->masjidB->id);
        $this->assertCount(1, Loan::all());
        $this->assertEquals('Budi', Loan::first()->borrower_name);
    }

    /** @test */
    public function auto_assigns_masjid_id_on_create()
    {
        $this->setMasjidContext($this->masjidA->id);

        $cat = Category::create(['name' => 'Auto Cat']);
        $this->assertEquals($this->masjidA->id, $cat->masjid_id);

        $loc = Location::create(['name' => 'Auto Loc']);
        $this->assertEquals($this->masjidA->id, $loc->masjid_id);
    }

    /** @test */
    public function no_scope_when_no_masjid_context()
    {
        $this->setMasjidContext(null);
        $allCategories = Category::all();
        $this->assertCount(2, $allCategories);
    }

    /** @test */
    public function settings_are_scoped_per_masjid()
    {
        Setting::withoutGlobalScopes()->create([
            'key' => 'app_name', 'value' => 'Inventaris Al-Ikhlas',
            'label' => 'Nama Aplikasi', 'type' => 'text', 'group' => 'general',
            'masjid_id' => $this->masjidA->id,
        ]);
        Setting::withoutGlobalScopes()->create([
            'key' => 'app_name', 'value' => 'Inventaris Ar-Rahman',
            'label' => 'Nama Aplikasi', 'type' => 'text', 'group' => 'general',
            'masjid_id' => $this->masjidB->id,
        ]);

        $this->setMasjidContext($this->masjidA->id);
        // Clear cache to ensure fresh query
        cache()->flush();
        $this->assertEquals('Inventaris Al-Ikhlas', Setting::get('app_name'));

        $this->setMasjidContext($this->masjidB->id);
        cache()->flush();
        $this->assertEquals('Inventaris Ar-Rahman', Setting::get('app_name'));
    }

    /** @test */
    public function without_global_scope_returns_all_records()
    {
        Item::withoutGlobalScopes()->create([
            'name' => 'Item A', 'category_id' => $this->catA->id, 'location_id' => $this->locA->id,
            'quantity' => 5, 'unit' => 'pcs', 'condition' => 'baik', 'masjid_id' => $this->masjidA->id,
        ]);
        Item::withoutGlobalScopes()->create([
            'name' => 'Item B', 'category_id' => $this->catB->id, 'location_id' => $this->locB->id,
            'quantity' => 3, 'unit' => 'pcs', 'condition' => 'baik', 'masjid_id' => $this->masjidB->id,
        ]);

        $this->setMasjidContext($this->masjidA->id);
        $allItems = Item::withoutGlobalScope(MasjidScope::class)->get();
        $this->assertCount(2, $allItems);
    }

    /** @test */
    public function stock_movements_are_scoped_to_masjid()
    {
        $itemA = Item::withoutGlobalScopes()->create([
            'name' => 'SM Item A', 'category_id' => $this->catA->id, 'location_id' => $this->locA->id,
            'quantity' => 10, 'unit' => 'pcs', 'condition' => 'baik', 'masjid_id' => $this->masjidA->id,
        ]);

        StockMovement::withoutGlobalScopes()->create([
            'item_id' => $itemA->id, 'type' => 'in', 'quantity' => 5,
            'reason' => 'Pengadaan', 'moved_at' => now(), 'masjid_id' => $this->masjidA->id,
        ]);
        StockMovement::withoutGlobalScopes()->create([
            'item_id' => $itemA->id, 'type' => 'in', 'quantity' => 3,
            'reason' => 'Pengadaan', 'moved_at' => now(), 'masjid_id' => $this->masjidB->id,
        ]);

        $this->setMasjidContext($this->masjidA->id);
        $this->assertCount(1, StockMovement::all());

        $this->setMasjidContext($this->masjidB->id);
        $this->assertCount(1, StockMovement::all());
    }

    /** @test */
    public function activity_logs_are_scoped_to_masjid()
    {
        ActivityLog::withoutGlobalScopes()->create([
            'action' => 'create', 'description' => 'Test A', 'masjid_id' => $this->masjidA->id,
        ]);
        ActivityLog::withoutGlobalScopes()->create([
            'action' => 'create', 'description' => 'Test B', 'masjid_id' => $this->masjidB->id,
        ]);

        $this->setMasjidContext($this->masjidA->id);
        $this->assertCount(1, ActivityLog::all());
        $this->assertEquals('Test A', ActivityLog::first()->description);
    }

    /** @test */
    public function feedbacks_are_scoped_to_masjid()
    {
        $role = Role::where('name', 'admin')->first();
        $userA = User::create([
            'name' => 'User A', 'email' => 'ua@test.com', 'password' => bcrypt('password'),
            'role_id' => $role->id, 'masjid_id' => $this->masjidA->id,
        ]);

        Feedback::withoutGlobalScopes()->create([
            'user_id' => $userA->id, 'module' => 'items', 'type' => 'bug',
            'message' => 'Feedback A', 'status' => 'new', 'masjid_id' => $this->masjidA->id,
        ]);
        Feedback::withoutGlobalScopes()->create([
            'user_id' => $userA->id, 'module' => 'items', 'type' => 'bug',
            'message' => 'Feedback B', 'status' => 'new', 'masjid_id' => $this->masjidB->id,
        ]);

        $this->setMasjidContext($this->masjidA->id);
        $this->assertCount(1, Feedback::all());
        $this->assertEquals('Feedback A', Feedback::first()->message);
    }

    /** @test */
    public function maintenance_is_scoped_to_masjid()
    {
        $role = Role::where('name', 'admin')->first();
        $user = User::create([
            'name' => 'Maint User', 'email' => 'mu@test.com', 'password' => bcrypt('password'),
            'role_id' => $role->id, 'masjid_id' => $this->masjidA->id,
        ]);
        $itemA = Item::withoutGlobalScopes()->create([
            'name' => 'Maint Item', 'category_id' => $this->catA->id, 'location_id' => $this->locA->id,
            'quantity' => 5, 'unit' => 'pcs', 'condition' => 'baik', 'masjid_id' => $this->masjidA->id,
        ]);

        Maintenance::withoutGlobalScopes()->create([
            'item_id' => $itemA->id, 'user_id' => $user->id, 'type' => 'perbaikan',
            'status' => 'pending', 'description' => 'Maint A', 'masjid_id' => $this->masjidA->id,
        ]);
        Maintenance::withoutGlobalScopes()->create([
            'item_id' => $itemA->id, 'user_id' => $user->id, 'type' => 'perbaikan',
            'status' => 'pending', 'description' => 'Maint B', 'masjid_id' => $this->masjidB->id,
        ]);

        $this->setMasjidContext($this->masjidA->id);
        $this->assertCount(1, Maintenance::all());
        $this->assertEquals('Maint A', Maintenance::first()->description);
    }
}
