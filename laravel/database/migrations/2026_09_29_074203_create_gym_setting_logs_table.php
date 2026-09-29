<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('gym_setting_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gym_setting_id')->nullable()->constrained('gym_settings')->nullOnDelete();
            $table->string('key');
            $table->decimal('old_value', 10, 2)->nullable();
            $table->decimal('new_value', 10, 2);
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gym_setting_logs');
    }
};
