<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('masjid_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type'); // loan_overdue, maintenance_pending, feedback_new, low_stock, etc.
            $table->string('title');
            $table->text('message');
            $table->string('link')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'read_at']);
            $table->index('masjid_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
