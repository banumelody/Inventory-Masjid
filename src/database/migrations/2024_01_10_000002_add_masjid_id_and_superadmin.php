<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add is_superadmin + masjid_id to users
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_superadmin')->default(false)->after('role_id');
            $table->foreignId('masjid_id')->nullable()->after('is_superadmin')
                ->constrained('masjids')->nullOnDelete();
        });

        // Add masjid_id to all tenant-scoped tables
        $tables = [
            'items',
            'categories',
            'locations',
            'loans',
            'stock_movements',
            'maintenances',
            'maintenance_photos',
            'scan_logs',
            'activity_logs',
            'feedbacks',
            'backups',
            'import_logs',
            'settings',
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->foreignId('masjid_id')->nullable()->after('id')
                        ->constrained('masjids')->nullOnDelete();
                    $table->index('masjid_id');
                });
            }
        }
    }

    public function down(): void
    {
        $tables = [
            'settings', 'import_logs', 'backups', 'feedbacks',
            'activity_logs', 'scan_logs', 'maintenance_photos',
            'maintenances', 'stock_movements', 'loans',
            'locations', 'categories', 'items',
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'masjid_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropConstrainedForeignId('masjid_id');
                });
            }
        }

        if (Schema::hasColumn('users', 'masjid_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropConstrainedForeignId('masjid_id');
                $table->dropColumn('is_superadmin');
            });
        }
    }
};
