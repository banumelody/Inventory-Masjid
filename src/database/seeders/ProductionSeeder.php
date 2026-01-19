<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Production Seeder
 * 
 * Hanya membuat data yang diperlukan untuk production:
 * - Roles (admin, operator, viewer)
 * - Default admin user
 * - Default categories & locations
 * 
 * TIDAK membuat sample items.
 * 
 * Usage: php artisan db:seed --class=ProductionSeeder
 */
class ProductionSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🚀 Running Production Seeder...');
        $this->command->newLine();

        // 1. Create roles
        $this->command->info('1. Creating roles...');
        $this->call(RoleSeeder::class);
        $this->command->newLine();

        // 2. Create admin user
        $this->command->info('2. Creating admin user...');
        $this->call(AdminUserSeeder::class);
        $this->command->newLine();

        // 3. Create master data (categories & locations)
        $this->command->info('3. Creating master data...');
        $this->call(MasterDataSeeder::class);
        $this->command->newLine();

        $this->command->info('✅ Production seeder completed!');
        $this->command->newLine();
        $this->command->warn('📋 Next steps:');
        $this->command->line('   1. Login dengan admin@masjid.local / admin123');
        $this->command->line('   2. Segera ganti password admin');
        $this->command->line('   3. Buat user operator/viewer sesuai kebutuhan');
        $this->command->line('   4. Mulai input data inventaris');
    }
}
