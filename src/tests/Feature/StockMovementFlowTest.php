<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Item;
use App\Models\Category;
use App\Models\Location;
use App\Models\StockMovement;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StockMovementFlowTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Item $item;

    protected function setUp(): void
    {
        parent::setUp();
        
        $adminRole = Role::firstOrCreate(
            ['name' => 'admin'],
            ['display_name' => 'Administrator']
        );
        
        $this->admin = User::firstOrCreate(
            ['email' => 'admin@test.com'],
            [
                'name' => 'Admin',
                'password' => bcrypt('password'),
                'role_id' => $adminRole->id,
            ]
        );
        
        $category = Category::create(['name' => 'Test Category']);
        $location = Location::create(['name' => 'Test Location']);
        
        $this->item = Item::create([
            'name' => 'Test Item',
            'category_id' => $category->id,
            'location_id' => $location->id,
            'quantity' => 10,
            'unit' => 'pcs',
            'condition' => 'baik',
        ]);
    }

    public function test_stock_in_increases_quantity(): void
    {
        $initialQuantity = $this->item->quantity;

        $response = $this->actingAs($this->admin)
            ->post(route('stock-movements.store'), [
                'item_id' => $this->item->id,
                'type' => 'in',
                'quantity' => 5,
                'reason' => 'Pembelian',
                'moved_at' => now()->format('Y-m-d'),
            ]);

        $response->assertRedirect(route('stock-movements.index'));

        $this->item->refresh();
        $this->assertEquals($initialQuantity + 5, $this->item->quantity);
    }

    public function test_stock_out_decreases_quantity(): void
    {
        $initialQuantity = $this->item->quantity;

        $response = $this->actingAs($this->admin)
            ->post(route('stock-movements.store'), [
                'item_id' => $this->item->id,
                'type' => 'out',
                'quantity' => 3,
                'reason' => 'Rusak',
                'moved_at' => now()->format('Y-m-d'),
            ]);

        $response->assertRedirect(route('stock-movements.index'));

        $this->item->refresh();
        $this->assertEquals($initialQuantity - 3, $this->item->quantity);
    }

    public function test_cannot_stock_out_more_than_available(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('stock-movements.store'), [
                'item_id' => $this->item->id,
                'type' => 'out',
                'quantity' => 15, // More than available (10)
                'reason' => 'Rusak',
                'moved_at' => now()->format('Y-m-d'),
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_stock_movement_creates_history(): void
    {
        $this->actingAs($this->admin)
            ->post(route('stock-movements.store'), [
                'item_id' => $this->item->id,
                'type' => 'in',
                'quantity' => 5,
                'reason' => 'Pembelian',
                'moved_at' => now()->format('Y-m-d'),
                'notes' => 'Test notes',
            ]);

        $this->assertDatabaseHas('stock_movements', [
            'item_id' => $this->item->id,
            'type' => 'in',
            'quantity' => 5,
            'reason' => 'Pembelian',
            'notes' => 'Test notes',
        ]);
    }
}
