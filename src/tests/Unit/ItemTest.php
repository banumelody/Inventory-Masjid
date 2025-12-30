<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Item;
use App\Models\Category;
use App\Models\Location;
use App\Models\Loan;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ItemTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create default category and location
        Category::create(['name' => 'Test Category']);
        Location::create(['name' => 'Test Location']);
    }

    public function test_item_quantity_cannot_be_negative(): void
    {
        $category = Category::first();
        $location = Location::first();

        $item = Item::create([
            'name' => 'Test Item',
            'category_id' => $category->id,
            'location_id' => $location->id,
            'quantity' => 10,
            'unit' => 'pcs',
            'condition' => 'baik',
        ]);

        $this->assertEquals(10, $item->quantity);
        $this->assertGreaterThanOrEqual(0, $item->quantity);
    }

    public function test_item_available_quantity_calculation(): void
    {
        $category = Category::first();
        $location = Location::first();

        $item = Item::create([
            'name' => 'Test Item',
            'category_id' => $category->id,
            'location_id' => $location->id,
            'quantity' => 10,
            'unit' => 'pcs',
            'condition' => 'baik',
        ]);

        // Create a loan
        Loan::create([
            'item_id' => $item->id,
            'borrower_name' => 'John Doe',
            'quantity' => 3,
            'borrowed_at' => now(),
        ]);

        $item->refresh();
        
        $this->assertEquals(3, $item->borrowed_quantity);
        $this->assertEquals(7, $item->available_quantity);
    }

    public function test_item_condition_label(): void
    {
        $category = Category::first();
        $location = Location::first();

        $item = Item::create([
            'name' => 'Test Item',
            'category_id' => $category->id,
            'location_id' => $location->id,
            'quantity' => 10,
            'unit' => 'pcs',
            'condition' => 'perlu_perbaikan',
        ]);

        $this->assertEquals('Perlu Perbaikan', $item->condition_label);
    }
}
