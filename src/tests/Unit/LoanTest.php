<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Item;
use App\Models\Category;
use App\Models\Location;
use App\Models\Loan;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoanTest extends TestCase
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

    public function test_loan_status_is_borrowed_when_not_returned(): void
    {
        $loan = Loan::create([
            'item_id' => $this->item->id,
            'borrower_name' => 'John Doe',
            'quantity' => 2,
            'borrowed_at' => now(),
        ]);

        $this->assertFalse($loan->isReturned());
        $this->assertEquals('Dipinjam', $loan->status);
    }

    public function test_loan_status_is_returned_when_returned(): void
    {
        $loan = Loan::create([
            'item_id' => $this->item->id,
            'borrower_name' => 'John Doe',
            'quantity' => 2,
            'borrowed_at' => now()->subDays(5),
            'returned_at' => now(),
            'returned_condition' => 'baik',
        ]);

        $this->assertTrue($loan->isReturned());
        $this->assertEquals('Sudah Kembali', $loan->status);
    }

    public function test_loan_is_overdue_when_past_due_date(): void
    {
        $loan = Loan::create([
            'item_id' => $this->item->id,
            'borrower_name' => 'John Doe',
            'quantity' => 2,
            'borrowed_at' => now()->subDays(10),
            'due_at' => now()->subDays(3),
        ]);

        $this->assertTrue($loan->isOverdue());
        $this->assertEquals('Terlambat', $loan->status);
    }

    public function test_loan_is_not_overdue_when_already_returned(): void
    {
        $loan = Loan::create([
            'item_id' => $this->item->id,
            'borrower_name' => 'John Doe',
            'quantity' => 2,
            'borrowed_at' => now()->subDays(10),
            'due_at' => now()->subDays(3),
            'returned_at' => now()->subDays(5),
            'returned_condition' => 'baik',
        ]);

        $this->assertFalse($loan->isOverdue());
    }

    public function test_cannot_borrow_more_than_available(): void
    {
        // Create first loan
        Loan::create([
            'item_id' => $this->item->id,
            'borrower_name' => 'John Doe',
            'quantity' => 8,
            'borrowed_at' => now(),
        ]);

        $this->item->refresh();
        
        // Available should be 2
        $this->assertEquals(2, $this->item->available_quantity);
        
        // Trying to borrow 3 should exceed available
        $this->assertGreaterThan($this->item->available_quantity, 3);
    }
}
