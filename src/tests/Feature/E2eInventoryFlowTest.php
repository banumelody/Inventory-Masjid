<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Item;
use App\Models\Loan;
use App\Models\Category;
use App\Models\Location;
use App\Models\StockMovement;
use Illuminate\Foundation\Testing\RefreshDatabase;

class E2eInventoryFlowTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $adminRole = Role::where('name', 'admin')->first();
        $this->admin = User::where('role_id', $adminRole->id)->first();
    }

    /**
     * Full lifecycle: login → create category/location → create item
     * → borrow → return → stock movement → verify dashboard
     */
    public function test_full_inventory_lifecycle(): void
    {
        // Step 1: Login
        $response = $this->post('/login', [
            'email' => 'admin@masjid.local',
            'password' => 'password',
        ]);
        $response->assertRedirect(route('items.index'));

        // Step 2: Create a new category
        $response = $this->actingAs($this->admin)
            ->post(route('categories.store'), ['name' => 'Perlengkapan Sholat']);
        $response->assertRedirect(route('categories.index'));
        $this->assertDatabaseHas('categories', ['name' => 'Perlengkapan Sholat']);
        $category = Category::where('name', 'Perlengkapan Sholat')->first();

        // Step 3: Create a new location
        $response = $this->actingAs($this->admin)
            ->post(route('locations.store'), ['name' => 'Lantai 2 Masjid']);
        $response->assertRedirect(route('locations.index'));
        $this->assertDatabaseHas('locations', ['name' => 'Lantai 2 Masjid']);
        $location = Location::where('name', 'Lantai 2 Masjid')->first();

        // Step 4: Create an item
        $response = $this->actingAs($this->admin)
            ->post(route('items.store'), [
                'name' => 'Sajadah Turki Premium',
                'category_id' => $category->id,
                'location_id' => $location->id,
                'quantity' => 50,
                'unit' => 'lembar',
                'condition' => 'baik',
                'note' => 'Donasi H. Ahmad',
            ]);
        $response->assertRedirect(route('items.index'));
        $item = Item::where('name', 'Sajadah Turki Premium')->first();
        $this->assertNotNull($item);
        $this->assertEquals(50, $item->quantity);
        $this->assertEquals(50, $item->available_quantity);

        // Step 5: Borrow some items
        $response = $this->actingAs($this->admin)
            ->post(route('loans.store'), [
                'item_id' => $item->id,
                'borrower_name' => 'Pak RT 05',
                'borrower_phone' => '081234567890',
                'quantity' => 10,
                'borrowed_at' => now()->format('Y-m-d'),
                'due_at' => now()->addDays(7)->format('Y-m-d'),
            ]);
        $response->assertRedirect(route('loans.index'));
        $item->refresh();
        $this->assertEquals(10, $item->borrowed_quantity);
        $this->assertEquals(40, $item->available_quantity);

        // Step 6: Return the loan
        $loan = Loan::where('borrower_name', 'Pak RT 05')->first();
        $this->assertNotNull($loan);
        $this->assertEquals('Dipinjam', $loan->status);

        $response = $this->actingAs($this->admin)
            ->post(route('loans.return.store', $loan), [
                'returned_at' => now()->format('Y-m-d'),
                'returned_condition' => 'baik',
            ]);
        $response->assertRedirect(route('loans.index'));
        $loan->refresh();
        $this->assertEquals('Sudah Kembali', $loan->status);

        $item->refresh();
        $this->assertEquals(0, $item->borrowed_quantity);
        $this->assertEquals(50, $item->available_quantity);

        // Step 7: Record stock movement (in)
        $response = $this->actingAs($this->admin)
            ->post(route('stock-movements.store'), [
                'item_id' => $item->id,
                'type' => 'in',
                'quantity' => 20,
                'reason' => 'Pembelian tambahan',
                'moved_at' => now()->format('Y-m-d'),
                'notes' => 'Pembelian bulan April',
            ]);
        $response->assertRedirect(route('stock-movements.index'));
        $item->refresh();
        $this->assertEquals(70, $item->quantity);

        // Step 8: Record stock movement (out)
        $response = $this->actingAs($this->admin)
            ->post(route('stock-movements.store'), [
                'item_id' => $item->id,
                'type' => 'out',
                'quantity' => 5,
                'reason' => 'Rusak',
                'moved_at' => now()->format('Y-m-d'),
            ]);
        $response->assertRedirect(route('stock-movements.index'));
        $item->refresh();
        $this->assertEquals(65, $item->quantity);

        // Step 9: Verify dashboard shows correct data
        $response = $this->actingAs($this->admin)->get(route('dashboard'));
        $response->assertStatus(200);

        // Step 10: Verify reports page works
        $response = $this->actingAs($this->admin)->get(route('reports.index'));
        $response->assertStatus(200);
    }

    public function test_qr_code_generation_and_scan_flow(): void
    {
        // Create test data
        $category = Category::create(['name' => 'QR Test Category']);
        $location = Location::create(['name' => 'QR Test Location']);
        $item = Item::create([
            'name' => 'QR Test Item',
            'category_id' => $category->id,
            'location_id' => $location->id,
            'quantity' => 5,
            'unit' => 'pcs',
            'condition' => 'baik',
        ]);
        $this->assertNotNull($item);

        // Generate QR code
        $response = $this->actingAs($this->admin)
            ->post(route('qrcode.generate', $item));
        $response->assertRedirect();

        $item->refresh();
        $this->assertTrue($item->hasQrCode());
        $this->assertNotEmpty($item->qr_code_key);

        // Scan QR code (public route) - redirects to item detail
        $response = $this->get("/i/{$item->qr_code_key}");
        $response->assertRedirect(route('items.show', $item));
    }

    public function test_user_management_flow(): void
    {
        $operatorRole = Role::where('name', 'operator')->first();

        // Create a new operator user
        $response = $this->actingAs($this->admin)
            ->post(route('users.store'), [
                'name' => 'Budi Operator',
                'email' => 'budi@masjid.local',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'role_id' => $operatorRole->id,
            ]);
        $response->assertRedirect(route('users.index'));
        $this->assertDatabaseHas('users', ['email' => 'budi@masjid.local']);

        // New operator can login
        $response = $this->post('/login', [
            'email' => 'budi@masjid.local',
            'password' => 'password123',
        ]);
        $response->assertRedirect(route('items.index'));

        // New operator can create items
        $operator = User::where('email', 'budi@masjid.local')->first();
        $response = $this->actingAs($operator)->get(route('items.create'));
        $response->assertStatus(200);

        // New operator cannot manage users
        $response = $this->actingAs($operator)->get(route('users.index'));
        $response->assertStatus(403);
    }

    public function test_category_and_location_management(): void
    {
        // Create category
        $response = $this->actingAs($this->admin)
            ->post(route('categories.store'), ['name' => 'Alat Kebersihan']);
        $response->assertRedirect(route('categories.index'));
        $category = Category::where('name', 'Alat Kebersihan')->first();

        // Update category
        $response = $this->actingAs($this->admin)
            ->put(route('categories.update', $category), ['name' => 'Peralatan Kebersihan']);
        $response->assertRedirect(route('categories.index'));
        $category->refresh();
        $this->assertEquals('Peralatan Kebersihan', $category->name);

        // Create location
        $response = $this->actingAs($this->admin)
            ->post(route('locations.store'), ['name' => 'Mushola Wanita']);
        $response->assertRedirect(route('locations.index'));

        // Delete location (no items linked)
        $location = Location::where('name', 'Mushola Wanita')->first();
        $response = $this->actingAs($this->admin)
            ->delete(route('locations.destroy', $location));
        $response->assertRedirect(route('locations.index'));
        $this->assertDatabaseMissing('locations', ['name' => 'Mushola Wanita']);
    }

    public function test_overdue_loan_detection(): void
    {
        // Create test data
        $category = Category::create(['name' => 'Overdue Test Category']);
        $location = Location::create(['name' => 'Overdue Test Location']);
        $item = Item::create([
            'name' => 'Overdue Test Item',
            'category_id' => $category->id,
            'location_id' => $location->id,
            'quantity' => 10,
            'unit' => 'pcs',
            'condition' => 'baik',
        ]);

        // Create a loan that is already overdue
        $response = $this->actingAs($this->admin)
            ->post(route('loans.store'), [
                'item_id' => $item->id,
                'borrower_name' => 'Test Borrower',
                'quantity' => 1,
                'borrowed_at' => now()->subDays(14)->format('Y-m-d'),
                'due_at' => now()->subDays(7)->format('Y-m-d'),
            ]);
        $response->assertRedirect(route('loans.index'));

        $loan = Loan::where('borrower_name', 'Test Borrower')->first();
        $this->assertNotNull($loan);
        $this->assertTrue($loan->isOverdue());
        $this->assertEquals('Terlambat', $loan->status);
    }

    public function test_feedback_submission_flow(): void
    {
        // Submit feedback
        $response = $this->actingAs($this->admin)
            ->post(route('feedbacks.store'), [
                'module' => 'items',
                'type' => 'suggestion',
                'message' => 'Tambahkan fitur barcode scanner',
            ]);
        $response->assertRedirect();

        $this->assertDatabaseHas('feedbacks', [
            'module' => 'items',
            'type' => 'suggestion',
            'message' => 'Tambahkan fitur barcode scanner',
        ]);
    }

    public function test_export_csv_works(): void
    {
        $response = $this->actingAs($this->admin)->get(route('export.excel'));
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }
}
