<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Location;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        // Default categories for mosque inventory
        $categories = [
            'Peralatan Ibadah',
            'Peralatan Kebersihan',
            'Furniture',
            'Elektronik',
            'Perlengkapan Acara',
            'Perlengkapan Kantor',
            'Lain-lain',
        ];

        foreach ($categories as $name) {
            Category::firstOrCreate(['name' => $name]);
        }

        $this->command->info('Created ' . count($categories) . ' default categories.');

        // Default locations for mosque
        $locations = [
            'Ruang Utama (Sholat)',
            'Ruang Wudhu Pria',
            'Ruang Wudhu Wanita',
            'Gudang',
            'Kantor Sekretariat',
            'Teras/Serambi',
            'Lantai 2',
            'Parkiran',
        ];

        foreach ($locations as $name) {
            Location::firstOrCreate(['name' => $name]);
        }

        $this->command->info('Created ' . count($locations) . ' default locations.');
    }
}
