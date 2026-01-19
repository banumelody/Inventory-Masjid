<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Location;
use App\Models\Item;

class DatabaseSeeder extends Seeder
{
    /**
     * Default seeder - creates everything including sample data.
     * For production, use: php artisan db:seed --class=ProductionSeeder
     */
    public function run(): void
    {
        // Run production seeder first (roles, admin, master data)
        $this->call(ProductionSeeder::class);

        // Add sample items for development/demo
        $this->command->info('4. Creating sample items (development only)...');
        $this->createSampleItems();
        $this->command->newLine();

        $this->command->info('✅ Database seeder completed with sample data!');
    }

    private function createSampleItems(): void
    {
        $sampleItems = [
            ['name' => 'Sajadah Merah', 'category' => 'Peralatan Ibadah', 'location' => 'Ruang Utama (Sholat)', 'quantity' => 50, 'unit' => 'pcs', 'condition' => 'baik', 'note' => 'Warna merah maroon'],
            ['name' => 'Al-Quran', 'category' => 'Peralatan Ibadah', 'location' => 'Ruang Utama (Sholat)', 'quantity' => 30, 'unit' => 'pcs', 'condition' => 'baik', 'note' => null],
            ['name' => 'Mukena Putih', 'category' => 'Peralatan Ibadah', 'location' => 'Ruang Utama (Sholat)', 'quantity' => 20, 'unit' => 'set', 'condition' => 'baik', 'note' => null],
            ['name' => 'Sapu Lantai', 'category' => 'Peralatan Kebersihan', 'location' => 'Gudang', 'quantity' => 5, 'unit' => 'pcs', 'condition' => 'baik', 'note' => null],
            ['name' => 'Vacuum Cleaner', 'category' => 'Peralatan Kebersihan', 'location' => 'Gudang', 'quantity' => 2, 'unit' => 'unit', 'condition' => 'perlu_perbaikan', 'note' => 'Perlu ganti filter'],
            ['name' => 'Meja Lipat', 'category' => 'Furniture', 'location' => 'Gudang', 'quantity' => 10, 'unit' => 'unit', 'condition' => 'baik', 'note' => null],
            ['name' => 'Kursi Plastik', 'category' => 'Furniture', 'location' => 'Gudang', 'quantity' => 100, 'unit' => 'pcs', 'condition' => 'baik', 'note' => 'Warna hijau'],
            ['name' => 'AC Split 1 PK', 'category' => 'Elektronik', 'location' => 'Ruang Utama (Sholat)', 'quantity' => 4, 'unit' => 'unit', 'condition' => 'baik', 'note' => 'Merk Daikin'],
            ['name' => 'Sound System', 'category' => 'Elektronik', 'location' => 'Kantor Sekretariat', 'quantity' => 1, 'unit' => 'set', 'condition' => 'baik', 'note' => null],
            ['name' => 'Kipas Angin Berdiri', 'category' => 'Elektronik', 'location' => 'Teras/Serambi', 'quantity' => 3, 'unit' => 'unit', 'condition' => 'rusak', 'note' => 'Perlu diganti motor'],
        ];

        $created = 0;
        foreach ($sampleItems as $itemData) {
            $category = Category::where('name', $itemData['category'])->first();
            $location = Location::where('name', $itemData['location'])->first();
            
            if ($category && $location) {
                $item = Item::firstOrCreate(
                    ['name' => $itemData['name']],
                    [
                        'category_id' => $category->id,
                        'location_id' => $location->id,
                        'quantity' => $itemData['quantity'],
                        'unit' => $itemData['unit'],
                        'condition' => $itemData['condition'],
                        'note' => $itemData['note'],
                    ]
                );
                if ($item->wasRecentlyCreated) {
                    $created++;
                }
            }
        }

        $this->command->info("Created {$created} sample items.");
    }
}
