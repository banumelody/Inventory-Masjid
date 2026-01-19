<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure roles exist
        $this->call(RoleSeeder::class);

        $adminRole = Role::where('name', 'admin')->first();

        if (!$adminRole) {
            $this->command->error('Admin role not found. Run RoleSeeder first.');
            return;
        }

        // Create default admin if not exists
        $admin = User::firstOrCreate(
            ['email' => 'admin@masjid.local'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('admin123'),
                'role_id' => $adminRole->id,
            ]
        );

        if ($admin->wasRecentlyCreated) {
            $this->command->info('Default admin created:');
            $this->command->info('  Email: admin@masjid.local');
            $this->command->info('  Password: admin123');
            $this->command->warn('  ⚠️  PENTING: Segera ganti password setelah login pertama!');
        } else {
            $this->command->info('Admin user already exists.');
        }
    }
}
