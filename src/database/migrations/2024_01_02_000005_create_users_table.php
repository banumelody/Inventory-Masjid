<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->foreignId('role_id')->constrained()->restrictOnDelete();
            $table->rememberToken();
            $table->timestamps();
        });

        // Insert default admin user
        $adminRole = DB::table('roles')->where('name', 'admin')->first();
        DB::table('users')->insert([
            'name' => 'Administrator',
            'email' => 'admin@masjid.local',
            'password' => Hash::make('password'),
            'role_id' => $adminRole->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
