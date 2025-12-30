<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Location;
use App\Models\Item;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Default categories
        $categories = [
            'Peralatan Ibadah',
            'Peralatan Kebersihan',
            'Furniture',
            'Elektronik',
            'Perlengkapan Acara',
            'Lain-lain',
        ];

        foreach ($categories as $name) {
            Category::firstOrCreate(['name' => $name]);
        }

        // Default locations
        $locations = [
            'Ruang Utama',
            'Ruang Wudhu',
            'Gudang',
            'Kantor Sekretariat',
            'Teras Depan',
            'Lantai 2',
        ];

        foreach ($locations as $name) {
            Location::firstOrCreate(['name' => $name]);
        }

        // Sample items
        $sampleItems = [
            ['name' => 'Sajadah Merah', 'category' => 'Peralatan Ibadah', 'location' => 'Ruang Utama', 'quantity' => 50, 'unit' => 'pcs', 'condition' => 'baik', 'note' => 'Warna merah maroon'],
            ['name' => 'Al-Quran', 'category' => 'Peralatan Ibadah', 'location' => 'Ruang Utama', 'quantity' => 30, 'unit' => 'pcs', 'condition' => 'baik', 'note' => null],
            ['name' => 'Mukena Putih', 'category' => 'Peralatan Ibadah', 'location' => 'Ruang Utama', 'quantity' => 20, 'unit' => 'set', 'condition' => 'baik', 'note' => null],
            ['name' => 'Sapu Lantai', 'category' => 'Peralatan Kebersihan', 'location' => 'Gudang', 'quantity' => 5, 'unit' => 'pcs', 'condition' => 'baik', 'note' => null],
            ['name' => 'Vacuum Cleaner', 'category' => 'Peralatan Kebersihan', 'location' => 'Gudang', 'quantity' => 2, 'unit' => 'unit', 'condition' => 'perlu_perbaikan', 'note' => 'Perlu ganti filter'],
            ['name' => 'Meja Lipat', 'category' => 'Furniture', 'location' => 'Gudang', 'quantity' => 10, 'unit' => 'unit', 'condition' => 'baik', 'note' => null],
            ['name' => 'Kursi Plastik', 'category' => 'Furniture', 'location' => 'Gudang', 'quantity' => 100, 'unit' => 'pcs', 'condition' => 'baik', 'note' => 'Warna hijau'],
            ['name' => 'AC Split 1 PK', 'category' => 'Elektronik', 'location' => 'Ruang Utama', 'quantity' => 4, 'unit' => 'unit', 'condition' => 'baik', 'note' => 'Merk Daikin'],
            ['name' => 'Sound System', 'category' => 'Elektronik', 'location' => 'Kantor Sekretariat', 'quantity' => 1, 'unit' => 'set', 'condition' => 'baik', 'note' => null],
            ['name' => 'Kipas Angin Berdiri', 'category' => 'Elektronik', 'location' => 'Teras Depan', 'quantity' => 3, 'unit' => 'unit', 'condition' => 'rusak', 'note' => 'Perlu diganti motor'],
        ];

        foreach ($sampleItems as $itemData) {
            $category = Category::where('name', $itemData['category'])->first();
            $location = Location::where('name', $itemData['location'])->first();
            
            if ($category && $location) {
                Item::firstOrCreate(
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
            }
        }
    }
}
