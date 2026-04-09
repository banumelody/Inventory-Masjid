<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Feedback;
use App\Models\Item;
use App\Models\Loan;
use App\Models\Location;
use App\Models\Maintenance;
use App\Models\Masjid;
use App\Models\Role;
use App\Models\Setting;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * DummySeeder — Data dummy multi-masjid untuk testing.
 *
 * Membuat:
 * - 1 Superadmin (tidak terikat masjid)
 * - 3 Masjid (aktif, aktif belum verifikasi, suspended)
 * - Setiap masjid: admin + operator + viewer
 * - Setiap masjid: kategori, lokasi, item, peminjaman, stok, maintenance, feedback
 *
 * Usage:
 *   docker compose exec app php artisan db:seed --class=DummySeeder
 */
class DummySeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🧪 Running DummySeeder (multi-masjid test data)...');
        $this->command->newLine();

        $this->ensureRoles();
        $superadmin = $this->createSuperAdmin();
        $masjids = $this->createMasjids();

        foreach ($masjids as $masjid) {
            $this->command->info("📍 Seeding data for: {$masjid->name}");
            $users = $this->createUsersForMasjid($masjid);
            $categories = $this->createCategoriesForMasjid($masjid);
            $locations = $this->createLocationsForMasjid($masjid);
            $items = $this->createItemsForMasjid($masjid, $categories, $locations);
            $this->createLoansForMasjid($masjid, $items, $users['admin']);
            $this->createStockMovementsForMasjid($masjid, $items);
            $this->createMaintenancesForMasjid($masjid, $items, $users['operator']);
            $this->createFeedbacksForMasjid($masjid, $users);
            $this->createSettingsForMasjid($masjid);
        }

        // Assign existing default data to first masjid
        $this->assignExistingDataToMasjid($masjids[0]);

        $this->command->newLine();
        $this->command->info('✅ DummySeeder selesai!');
        $this->command->newLine();
        $this->printCredentials($superadmin, $masjids);
    }

    private function ensureRoles(): void
    {
        Role::firstOrCreate(['name' => 'admin'], ['display_name' => 'Administrator']);
        Role::firstOrCreate(['name' => 'operator'], ['display_name' => 'Operator']);
        Role::firstOrCreate(['name' => 'viewer'], ['display_name' => 'Viewer']);
    }

    private function createSuperAdmin(): User
    {
        $adminRole = Role::where('name', 'admin')->first();

        $superadmin = User::updateOrCreate(
            ['email' => 'superadmin@inventaris.id'],
            [
                'name' => 'Super Administrator',
                'password' => Hash::make('superadmin123'),
                'role_id' => $adminRole->id,
                'is_superadmin' => true,
                'masjid_id' => null,
            ]
        );

        $this->command->info('👑 Superadmin: superadmin@inventaris.id / superadmin123');
        return $superadmin;
    }

    private function createMasjids(): array
    {
        $masjidData = [
            [
                'name' => 'Masjid Al-Ikhlas Jakarta',
                'slug' => 'masjid-al-ikhlas-jakarta',
                'address' => 'Jl. Kebon Sirih No. 10',
                'city' => 'Jakarta Pusat',
                'province' => 'DKI Jakarta',
                'phone' => '021-3456789',
                'email' => 'info@alikhlas-jkt.id',
                'status' => 'active',
                'verified_at' => now(),
            ],
            [
                'name' => 'Masjid Ar-Rahman Bandung',
                'slug' => 'masjid-ar-rahman-bandung',
                'address' => 'Jl. Braga No. 25',
                'city' => 'Bandung',
                'province' => 'Jawa Barat',
                'phone' => '022-1234567',
                'email' => 'info@arrahman-bdg.id',
                'status' => 'active',
                'verified_at' => null,
            ],
            [
                'name' => 'Masjid At-Taqwa Surabaya',
                'slug' => 'masjid-at-taqwa-surabaya',
                'address' => 'Jl. Tunjungan No. 50',
                'city' => 'Surabaya',
                'province' => 'Jawa Timur',
                'phone' => '031-9876543',
                'email' => 'info@attaqwa-sby.id',
                'status' => 'suspended',
                'verified_at' => now()->subMonths(3),
            ],
        ];

        $masjids = [];
        foreach ($masjidData as $data) {
            $masjids[] = Masjid::firstOrCreate(
                ['slug' => $data['slug']],
                $data
            );
        }

        $this->command->info("🕌 Created " . count($masjids) . " masjids");
        return $masjids;
    }

    private function createUsersForMasjid(Masjid $masjid): array
    {
        $slug = Str::before($masjid->slug, '-' . Str::afterLast($masjid->slug, '-'));
        $shortSlug = Str::limit(Str::slug($masjid->city, ''), 10, '');

        $adminRole = Role::where('name', 'admin')->first();
        $operatorRole = Role::where('name', 'operator')->first();
        $viewerRole = Role::where('name', 'viewer')->first();

        $admin = User::firstOrCreate(
            ['email' => "admin@{$shortSlug}.masjid.local"],
            [
                'name' => "Admin {$masjid->name}",
                'password' => Hash::make('password'),
                'role_id' => $adminRole->id,
                'is_superadmin' => false,
                'masjid_id' => $masjid->id,
            ]
        );

        $operator = User::firstOrCreate(
            ['email' => "operator@{$shortSlug}.masjid.local"],
            [
                'name' => "Operator {$masjid->name}",
                'password' => Hash::make('password'),
                'role_id' => $operatorRole->id,
                'is_superadmin' => false,
                'masjid_id' => $masjid->id,
            ]
        );

        $viewer = User::firstOrCreate(
            ['email' => "viewer@{$shortSlug}.masjid.local"],
            [
                'name' => "Viewer {$masjid->name}",
                'password' => Hash::make('password'),
                'role_id' => $viewerRole->id,
                'is_superadmin' => false,
                'masjid_id' => $masjid->id,
            ]
        );

        return compact('admin', 'operator', 'viewer');
    }

    private function createCategoriesForMasjid(Masjid $masjid): array
    {
        $names = [
            'Peralatan Ibadah',
            'Peralatan Kebersihan',
            'Furniture',
            'Elektronik',
            'Perlengkapan Acara',
            'Perlengkapan Kantor',
        ];

        $categories = [];
        foreach ($names as $name) {
            $categories[] = Category::firstOrCreate(
                ['name' => $name, 'masjid_id' => $masjid->id],
                ['name' => $name, 'masjid_id' => $masjid->id]
            );
        }
        return $categories;
    }

    private function createLocationsForMasjid(Masjid $masjid): array
    {
        $names = [
            'Ruang Utama (Sholat)',
            'Gudang',
            'Kantor Sekretariat',
            'Teras/Serambi',
            'Ruang Wudhu Pria',
            'Ruang Wudhu Wanita',
        ];

        $locations = [];
        foreach ($names as $name) {
            $locations[] = Location::firstOrCreate(
                ['name' => $name, 'masjid_id' => $masjid->id],
                ['name' => $name, 'masjid_id' => $masjid->id]
            );
        }
        return $locations;
    }

    private function createItemsForMasjid(Masjid $masjid, array $categories, array $locations): array
    {
        // Kategori map by name for easy lookup
        $catMap = collect($categories)->keyBy('name');
        $locMap = collect($locations)->keyBy('name');

        $itemsData = [
            ['name' => 'Sajadah Merah', 'category' => 'Peralatan Ibadah', 'location' => 'Ruang Utama (Sholat)', 'quantity' => 50, 'unit' => 'pcs', 'condition' => 'baik', 'note' => 'Warna merah maroon'],
            ['name' => 'Al-Quran', 'category' => 'Peralatan Ibadah', 'location' => 'Ruang Utama (Sholat)', 'quantity' => 30, 'unit' => 'pcs', 'condition' => 'baik'],
            ['name' => 'Mukena Putih', 'category' => 'Peralatan Ibadah', 'location' => 'Ruang Utama (Sholat)', 'quantity' => 20, 'unit' => 'set', 'condition' => 'baik'],
            ['name' => 'Sarung', 'category' => 'Peralatan Ibadah', 'location' => 'Ruang Utama (Sholat)', 'quantity' => 15, 'unit' => 'pcs', 'condition' => 'baik'],
            ['name' => 'Sapu Lantai', 'category' => 'Peralatan Kebersihan', 'location' => 'Gudang', 'quantity' => 5, 'unit' => 'pcs', 'condition' => 'baik'],
            ['name' => 'Vacuum Cleaner', 'category' => 'Peralatan Kebersihan', 'location' => 'Gudang', 'quantity' => 2, 'unit' => 'unit', 'condition' => 'perlu_perbaikan', 'note' => 'Perlu ganti filter'],
            ['name' => 'Pel Lantai', 'category' => 'Peralatan Kebersihan', 'location' => 'Gudang', 'quantity' => 4, 'unit' => 'pcs', 'condition' => 'baik'],
            ['name' => 'Meja Lipat', 'category' => 'Furniture', 'location' => 'Gudang', 'quantity' => 10, 'unit' => 'unit', 'condition' => 'baik'],
            ['name' => 'Kursi Plastik', 'category' => 'Furniture', 'location' => 'Gudang', 'quantity' => 100, 'unit' => 'pcs', 'condition' => 'baik', 'note' => 'Warna hijau'],
            ['name' => 'Lemari Arsip', 'category' => 'Furniture', 'location' => 'Kantor Sekretariat', 'quantity' => 2, 'unit' => 'unit', 'condition' => 'baik'],
            ['name' => 'AC Split 1 PK', 'category' => 'Elektronik', 'location' => 'Ruang Utama (Sholat)', 'quantity' => 4, 'unit' => 'unit', 'condition' => 'baik', 'note' => 'Merk Daikin'],
            ['name' => 'Sound System', 'category' => 'Elektronik', 'location' => 'Kantor Sekretariat', 'quantity' => 1, 'unit' => 'set', 'condition' => 'baik'],
            ['name' => 'Kipas Angin Berdiri', 'category' => 'Elektronik', 'location' => 'Teras/Serambi', 'quantity' => 3, 'unit' => 'unit', 'condition' => 'rusak', 'note' => 'Perlu diganti motor'],
            ['name' => 'Proyektor', 'category' => 'Elektronik', 'location' => 'Kantor Sekretariat', 'quantity' => 1, 'unit' => 'unit', 'condition' => 'baik'],
            ['name' => 'Tenda Lipat', 'category' => 'Perlengkapan Acara', 'location' => 'Gudang', 'quantity' => 3, 'unit' => 'unit', 'condition' => 'baik'],
            ['name' => 'Panggung Portable', 'category' => 'Perlengkapan Acara', 'location' => 'Gudang', 'quantity' => 5, 'unit' => 'set', 'condition' => 'baik'],
            ['name' => 'Printer', 'category' => 'Perlengkapan Kantor', 'location' => 'Kantor Sekretariat', 'quantity' => 1, 'unit' => 'unit', 'condition' => 'baik'],
            ['name' => 'Whiteboard', 'category' => 'Perlengkapan Kantor', 'location' => 'Kantor Sekretariat', 'quantity' => 2, 'unit' => 'unit', 'condition' => 'baik'],
        ];

        $items = [];
        foreach ($itemsData as $data) {
            $category = $catMap[$data['category']] ?? null;
            $location = $locMap[$data['location']] ?? null;
            if (!$category || !$location) {
                continue;
            }

            $items[] = Item::firstOrCreate(
                [
                    'name' => $data['name'],
                    'masjid_id' => $masjid->id,
                ],
                [
                    'category_id' => $category->id,
                    'location_id' => $location->id,
                    'quantity' => $data['quantity'],
                    'unit' => $data['unit'],
                    'condition' => $data['condition'],
                    'note' => $data['note'] ?? null,
                    'masjid_id' => $masjid->id,
                ]
            );
        }

        $this->command->info("  📦 {$masjid->name}: " . count($items) . " items");
        return $items;
    }

    private function createLoansForMasjid(Masjid $masjid, array $items, User $admin): void
    {
        if (count($items) < 3) {
            return;
        }

        $loansData = [
            // Active loan (dipinjam)
            [
                'item' => $items[0],
                'borrower_name' => 'Pak RT 05',
                'borrower_phone' => '081234567890',
                'quantity' => 5,
                'borrowed_at' => now()->subDays(3),
                'due_at' => now()->addDays(4),
            ],
            // Active loan (dipinjam)
            [
                'item' => $items[7],
                'borrower_name' => 'Ibu Pengajian',
                'borrower_phone' => '081298765432',
                'quantity' => 3,
                'borrowed_at' => now()->subDays(1),
                'due_at' => now()->addDays(6),
            ],
            // Overdue loan (terlambat)
            [
                'item' => $items[1],
                'borrower_name' => 'Remaja Masjid',
                'borrower_phone' => '085612345678',
                'quantity' => 10,
                'borrowed_at' => now()->subDays(14),
                'due_at' => now()->subDays(7),
            ],
            // Returned loan (sudah kembali)
            [
                'item' => $items[3],
                'borrower_name' => 'Takmir Masjid',
                'borrower_phone' => '087812345678',
                'quantity' => 5,
                'borrowed_at' => now()->subDays(10),
                'due_at' => now()->subDays(3),
                'returned_at' => now()->subDays(4),
                'returned_condition' => 'baik',
            ],
        ];

        $created = 0;
        foreach ($loansData as $data) {
            $item = $data['item'];
            unset($data['item']);

            Loan::firstOrCreate(
                [
                    'item_id' => $item->id,
                    'borrower_name' => $data['borrower_name'],
                    'masjid_id' => $masjid->id,
                ],
                array_merge($data, ['masjid_id' => $masjid->id])
            );
            $created++;
        }

        $this->command->info("  📋 {$masjid->name}: {$created} loans");
    }

    private function createStockMovementsForMasjid(Masjid $masjid, array $items): void
    {
        if (count($items) < 5) {
            return;
        }

        $movements = [
            ['item' => $items[0], 'type' => 'in', 'quantity' => 20, 'reason' => 'Donasi jamaah', 'moved_at' => now()->subDays(30)],
            ['item' => $items[1], 'type' => 'in', 'quantity' => 10, 'reason' => 'Pembelian baru', 'moved_at' => now()->subDays(20)],
            ['item' => $items[4], 'type' => 'out', 'quantity' => 2, 'reason' => 'Rusak/disposal', 'moved_at' => now()->subDays(15)],
            ['item' => $items[8], 'type' => 'in', 'quantity' => 50, 'reason' => 'Pembelian acara maulid', 'moved_at' => now()->subDays(10)],
            ['item' => $items[5], 'type' => 'out', 'quantity' => 1, 'reason' => 'Dikirim ke service', 'moved_at' => now()->subDays(5)],
        ];

        $created = 0;
        foreach ($movements as $data) {
            $item = $data['item'];
            unset($data['item']);

            StockMovement::create(array_merge($data, [
                'item_id' => $item->id,
                'masjid_id' => $masjid->id,
            ]));
            $created++;
        }

        $this->command->info("  📊 {$masjid->name}: {$created} stock movements");
    }

    private function createMaintenancesForMasjid(Masjid $masjid, array $items, User $operator): void
    {
        if (count($items) < 12) {
            return;
        }

        $maintenancesData = [
            // Pending maintenance
            [
                'item' => $items[5],
                'type' => 'perbaikan',
                'status' => 'pending',
                'description' => 'Ganti filter vacuum cleaner',
                'vendor' => 'Toko Elektronik Jaya',
                'vendor_phone' => '021-5551234',
                'cost' => 150000,
                'started_at' => null,
                'completed_at' => null,
                'estimated_completion' => now()->addDays(7),
            ],
            // In-progress maintenance
            [
                'item' => $items[12],
                'type' => 'perbaikan',
                'status' => 'in_progress',
                'description' => 'Ganti motor kipas angin',
                'vendor' => 'Bengkel Pak Ahmad',
                'vendor_phone' => '085611112222',
                'cost' => 250000,
                'started_at' => now()->subDays(3),
                'completed_at' => null,
                'estimated_completion' => now()->addDays(2),
            ],
            // Completed maintenance
            [
                'item' => $items[10],
                'type' => 'perawatan',
                'status' => 'completed',
                'description' => 'Service AC rutin tahunan',
                'vendor' => 'CV Sejuk Selalu',
                'vendor_phone' => '021-7778899',
                'cost' => 500000,
                'started_at' => now()->subDays(14),
                'completed_at' => now()->subDays(12),
                'estimated_completion' => now()->subDays(10),
            ],
        ];

        $created = 0;
        foreach ($maintenancesData as $data) {
            $item = $data['item'];
            unset($data['item']);

            Maintenance::firstOrCreate(
                [
                    'item_id' => $item->id,
                    'description' => $data['description'],
                    'masjid_id' => $masjid->id,
                ],
                array_merge($data, [
                    'user_id' => $operator->id,
                    'masjid_id' => $masjid->id,
                ])
            );
            $created++;
        }

        $this->command->info("  🔧 {$masjid->name}: {$created} maintenances");
    }

    private function createFeedbacksForMasjid(Masjid $masjid, array $users): void
    {
        $feedbacksData = [
            [
                'user' => $users['viewer'],
                'module' => 'items',
                'type' => 'suggestion',
                'message' => 'Tambahkan fitur barcode scanner untuk input barang lebih cepat',
                'status' => 'new',
            ],
            [
                'user' => $users['operator'],
                'module' => 'loans',
                'type' => 'bug',
                'message' => 'Notifikasi peminjaman jatuh tempo tidak muncul di dashboard',
                'status' => 'in_progress',
            ],
            [
                'user' => $users['admin'],
                'module' => 'reports',
                'type' => 'suggestion',
                'message' => 'Tambahkan laporan bulanan otomatis via email',
                'status' => 'resolved',
                'admin_notes' => 'Akan ditambahkan di versi berikutnya',
            ],
        ];

        foreach ($feedbacksData as $data) {
            $user = $data['user'];
            unset($data['user']);

            Feedback::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'message' => $data['message'],
                    'masjid_id' => $masjid->id,
                ],
                array_merge($data, [
                    'user_id' => $user->id,
                    'masjid_id' => $masjid->id,
                ])
            );
        }
    }

    private function createSettingsForMasjid(Masjid $masjid): void
    {
        $settings = [
            ['key' => 'app_name', 'value' => "Inventaris {$masjid->name}", 'type' => 'text', 'group' => 'appearance', 'label' => 'Nama Aplikasi', 'sort_order' => 1],
            ['key' => 'org_name', 'value' => $masjid->name, 'type' => 'text', 'group' => 'organization', 'label' => 'Nama Organisasi', 'sort_order' => 1],
            ['key' => 'org_address', 'value' => $masjid->address, 'type' => 'textarea', 'group' => 'organization', 'label' => 'Alamat', 'sort_order' => 2],
            ['key' => 'org_phone', 'value' => $masjid->phone, 'type' => 'text', 'group' => 'organization', 'label' => 'Telepon', 'sort_order' => 3],
            ['key' => 'org_email', 'value' => $masjid->email, 'type' => 'text', 'group' => 'organization', 'label' => 'Email', 'sort_order' => 4],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key'], 'masjid_id' => $masjid->id],
                array_merge($setting, ['masjid_id' => $masjid->id])
            );
        }
    }

    /**
     * Assign existing data (from previous seeders) to the first masjid.
     */
    private function assignExistingDataToMasjid(Masjid $masjid): void
    {
        $tables = [
            'items', 'categories', 'locations', 'loans',
            'stock_movements', 'maintenances', 'maintenance_photos',
            'scan_logs', 'activity_logs', 'feedbacks', 'backups',
            'import_logs', 'settings',
        ];

        foreach ($tables as $table) {
            \DB::table($table)
                ->whereNull('masjid_id')
                ->update(['masjid_id' => $masjid->id]);
        }

        // Assign existing non-superadmin users without masjid
        User::whereNull('masjid_id')
            ->where('is_superadmin', false)
            ->update(['masjid_id' => $masjid->id]);

        $this->command->info("  🔗 Assigned orphan data to: {$masjid->name}");
    }

    private function printCredentials(User $superadmin, array $masjids): void
    {
        $this->command->warn('┌─────────────────────────────────────────────────────────────┐');
        $this->command->warn('│                    KREDENSIAL LOGIN                          │');
        $this->command->warn('├─────────────────────────────────────────────────────────────┤');
        $this->command->warn('│ 👑 SUPERADMIN                                               │');
        $this->command->line('│    Email    : superadmin@inventaris.id                       │');
        $this->command->line('│    Password : superadmin123                                  │');
        $this->command->warn('├─────────────────────────────────────────────────────────────┤');
        $this->command->warn('│ 🏢 ADMIN MASJID (password: "password")                      │');

        foreach ($masjids as $masjid) {
            $shortSlug = Str::limit(Str::slug($masjid->city, ''), 10, '');
            $this->command->line("│    {$masjid->name}");
            $this->command->line("│      Admin    : admin@{$shortSlug}.masjid.local");
            $this->command->line("│      Operator : operator@{$shortSlug}.masjid.local");
            $this->command->line("│      Viewer   : viewer@{$shortSlug}.masjid.local");
        }

        $this->command->warn('├─────────────────────────────────────────────────────────────┤');
        $this->command->warn('│ 🔑 Existing admin: admin@masjid.local / password            │');
        $this->command->warn('└─────────────────────────────────────────────────────────────┘');
    }
}
