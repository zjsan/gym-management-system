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
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->string('membership_no')->unique(); // e.g., GYM-001
            $table->string('first_name');
            $table->string('last_name');
            $table->string('contact_number');
            $table->text('address');
            $table->enum('gender', ['male', 'female', 'other']);
            $table->string('photo_path')->nullable();
            $table->boolean('is_active')->default(true);

            // Membership Logic
            $table->timestamp('membership_start')->nullable();
            $table->timestamp('membership_end')->nullable();
            $table->timestamp('last_renewal_at')->nullable();

            $table->timestamps();
          
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
