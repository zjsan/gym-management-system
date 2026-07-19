<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_loggings', function (Blueprint $table) {
            $table->id();
            
            // The staff/admin who recorded the entry
            $table->foreignId('recorded_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            // Nullable targets to accommodate the hybrid structure
            $table->foreignId('member_id')
                  ->nullable()
                  ->constrained('members')
                  ->cascadeOnDelete();

            $table->foreignId('walkin_id')
                  ->nullable()
                  ->constrained('walkins')
                  ->cascadeOnDelete();

            // Unified audit tracking
            $table->enum('entry_method', ['qr_scan', 'manual_member', 'manual_walkin']);
            $table->timestamp('check_in');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_loggings');
    }
};
