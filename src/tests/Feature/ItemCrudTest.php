<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Item;
use App\Models\Category;
use App\Models\Location;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ItemCrudTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $viewer;
    protected Category $category;
    protected Location $location;

    protected function setUp(): void
    {
        parent::setUp();

        $adminRole = Role::where('name', 'admin')->first();
        $viewerRole = Role::firstOrCreate(
            ['name' => 'viewer'],
            ['display_name' => 'Viewer']
        );

        $this->admin = User::where('role_id', $adminRole->id)->first();
        $this->viewer = User::create([
            'name' => 'Viewer User',
            'email' => 'viewer@test.com',
            'password' => bcrypt('password'),
            'role_id' => $viewerRole->id,
        ]);

        $this->category = Category::create(['name' => 'Peralatan Ibadah']);
        $this->location = Location::create(['name' => 'Ruang Utama']);
    }

    protected function createTestItem(array $attributes = []): Item
    {
        return Item::create(array_merge([
            'name' => 'Test Item',
            'category_id' => $this->category->id,
            'location_id' => $this->location->id,
            'quantity' => 10,
            'unit' => 'pcs',
            'condition' => 'baik',
        ], $attributes));
    }

    public function test_items_index_page_loads(): void
    {
        $response = $this->actingAs($this->admin)->get(route('items.index'));

        $response->assertStatus(200);
        $response->assertSee('Inventaris');
    }

    public function test_admin_can_access_create_item_page(): void
    {
        $response = $this->actingAs($this->admin)->get(route('items.create'));
        $response->assertStatus(200);
    }

    public function test_viewer_cannot_access_create_item_page(): void
    {
        $response = $this->actingAs($this->viewer)->get(route('items.create'));
        $response->assertStatus(403);
    }

    public function test_admin_can_create_item(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('items.store'), [
                'name' => 'Al-Quran Baru',
                'category_id' => $this->category->id,
                'location_id' => $this->location->id,
                'quantity' => 25,
                'unit' => 'eksemplar',
                'condition' => 'baik',
                'note' => 'Donasi jamaah',
            ]);

        $response->assertRedirect(route('items.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('items', [
            'name' => 'Al-Quran Baru',
            'quantity' => 25,
        ]);
    }

    public function test_create_item_validates_required_fields(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('items.store'), []);

        $response->assertSessionHasErrors(['name', 'category_id', 'location_id', 'quantity', 'unit', 'condition']);
    }

    public function test_admin_can_update_item(): void
    {
        $item = $this->createTestItem(['name' => 'Original Name']);

        $response = $this->actingAs($this->admin)
            ->put(route('items.update', $item), [
                'name' => 'Updated Item Name',
                'category_id' => $item->category_id,
                'location_id' => $item->location_id,
                'quantity' => $item->quantity,
                'unit' => $item->unit,
                'condition' => 'perlu_perbaikan',
            ]);

        $response->assertRedirect(route('items.index'));
        $item->refresh();
        $this->assertEquals('Updated Item Name', $item->name);
        $this->assertEquals('perlu_perbaikan', $item->condition);
    }

    public function test_admin_can_delete_item(): void
    {
        $item = $this->createTestItem(['name' => 'To Be Deleted', 'condition' => 'rusak']);

        $response = $this->actingAs($this->admin)
            ->delete(route('items.destroy', $item));

        $response->assertRedirect(route('items.index'));
        $this->assertSoftDeleted('items', ['id' => $item->id]);
    }

    public function test_viewer_cannot_delete_item(): void
    {
        $item = $this->createTestItem();

        $response = $this->actingAs($this->viewer)
            ->delete(route('items.destroy', $item));

        $response->assertStatus(403);
    }

    public function test_item_show_page_loads(): void
    {
        $item = $this->createTestItem(['name' => 'Shown Item']);

        $response = $this->actingAs($this->admin)
            ->get(route('items.show', $item));

        $response->assertStatus(200);
        $response->assertSee($item->name);
    }

    public function test_items_search_filter_works(): void
    {
        $item = $this->createTestItem(['name' => 'Sajadah Unik']);

        $response = $this->actingAs($this->admin)
            ->get(route('items.index', ['search' => $item->name]));

        $response->assertStatus(200);
        $response->assertSee($item->name);
    }

    public function test_items_category_filter_works(): void
    {
        $this->createTestItem();

        $response = $this->actingAs($this->admin)
            ->get(route('items.index', ['category_id' => $this->category->id]));

        $response->assertStatus(200);
    }

    public function test_save_and_add_new_redirects_to_create(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('items.store'), [
                'name' => 'Quick Add Item',
                'category_id' => $this->category->id,
                'location_id' => $this->location->id,
                'quantity' => 5,
                'unit' => 'pcs',
                'condition' => 'baik',
                'action' => 'save_and_new',
            ]);

        $response->assertRedirect(route('items.create'));
        $response->assertSessionHas('success');
    }
}
