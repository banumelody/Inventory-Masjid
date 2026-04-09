<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Item;
use App\Models\Category;
use App\Models\Location;
use App\Models\Loan;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoanFlowTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Item $item;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Get or create admin role (migration seeds default roles)
        $adminRole = Role::firstOrCreate(
            ['name' => 'admin'],
            ['display_name' => 'Administrator']
        );
        
        // Create admin user
        $this->admin = User::firstOrCreate(
            ['email' => 'admin@test.com'],
            [
                'name' => 'Admin',
                'password' => bcrypt('password'),
                'role_id' => $adminRole->id,
            ]
        );
        
        // Create item
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

    public function test_admin_can_create_loan(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('loans.store'), [
                'item_id' => $this->item->id,
                'borrower_name' => 'John Doe',
                'borrower_phone' => '08123456789',
                'quantity' => 3,
                'borrowed_at' => now()->format('Y-m-d'),
                'due_at' => now()->addDays(7)->format('Y-m-d'),
            ]);

        $response->assertRedirect(route('loans.index'));
        
        $this->assertDatabaseHas('loans', [
            'item_id' => $this->item->id,
            'borrower_name' => 'John Doe',
            'quantity' => 3,
        ]);
    }

    public function test_cannot_borrow_more_than_available(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('loans.store'), [
                'item_id' => $this->item->id,
                'borrower_name' => 'John Doe',
                'quantity' => 15, // More than available (10)
                'borrowed_at' => now()->format('Y-m-d'),
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_admin_can_return_loan(): void
    {
        // Create a loan
        $loan = Loan::create([
            'item_id' => $this->item->id,
            'borrower_name' => 'John Doe',
            'quantity' => 3,
            'borrowed_at' => now()->subDays(5),
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('loans.return.store', $loan), [
                'returned_at' => now()->format('Y-m-d'),
                'returned_condition' => 'baik',
            ]);

        $response->assertRedirect(route('loans.index'));
        
        $loan->refresh();
        $this->assertNotNull($loan->returned_at);
        $this->assertEquals('baik', $loan->returned_condition);
    }

    public function test_loan_flow_complete(): void
    {
        // Step 1: Create loan
        $this->actingAs($this->admin)
            ->post(route('loans.store'), [
                'item_id' => $this->item->id,
                'borrower_name' => 'Jane Doe',
                'quantity' => 2,
                'borrowed_at' => now()->format('Y-m-d'),
            ]);

        $loan = Loan::where('borrower_name', 'Jane Doe')->first();
        $this->assertNotNull($loan);
        $this->assertNull($loan->returned_at);

        // Step 2: Check available quantity reduced
        $this->item->refresh();
        $this->assertEquals(2, $this->item->borrowed_quantity);
        $this->assertEquals(8, $this->item->available_quantity);

        // Step 3: Return loan
        $this->actingAs($this->admin)
            ->post(route('loans.return.store', $loan), [
                'returned_at' => now()->format('Y-m-d'),
                'returned_condition' => 'baik',
            ]);

        // Step 4: Check loan is returned
        $loan->refresh();
        $this->assertTrue($loan->isReturned());

        // Step 5: Check available quantity restored
        $this->item->refresh();
        $this->assertEquals(0, $this->item->borrowed_quantity);
        $this->assertEquals(10, $this->item->available_quantity);
    }
}
