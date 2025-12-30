<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Item;
use App\Models\Category;
use App\Models\Location;
use App\Models\StockMovement;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StockMovementTest extends TestCase
{
    use RefreshDatabase;

    protected Item $item;

    protected function setUp(): void
    {
        parent::setUp();
        
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

    public function test_stock_movement_type_label(): void
    {
        $movementIn = StockMovement::create([
            'item_id' => $this->item->id,
            'type' => 'in',
            'quantity' => 5,
            'reason' => 'Pembelian',
            'moved_at' => now(),
        ]);

        $movementOut = StockMovement::create([
            'item_id' => $this->item->id,
            'type' => 'out',
            'quantity' => 2,
            'reason' => 'Rusak',
            'moved_at' => now(),
        ]);

        $this->assertEquals('Masuk', $movementIn->type_label);
        $this->assertEquals('Keluar', $movementOut->type_label);
    }

    public function test_stock_movement_type_color(): void
    {
        $movementIn = StockMovement::create([
            'item_id' => $this->item->id,
            'type' => 'in',
            'quantity' => 5,
            'reason' => 'Pembelian',
            'moved_at' => now(),
        ]);

        $movementOut = StockMovement::create([
            'item_id' => $this->item->id,
            'type' => 'out',
            'quantity' => 2,
            'reason' => 'Rusak',
            'moved_at' => now(),
        ]);

        $this->assertEquals('green', $movementIn->type_color);
        $this->assertEquals('red', $movementOut->type_color);
    }
}
