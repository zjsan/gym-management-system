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
            $table->string('membership_no')->nullable()->unique(); // e.g., GYM-001
            $table->string('qr_token')->nullable()->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('contact_number');
            $table->string('emergency_contact_number')->nullable();
            $table->text('address');
            $table->enum('gender', ['male', 'female', 'other']);
            $table->date('date_of_birth')->nullable();
            $table->string('photo_path')->nullable();
            $table->boolean('is_active')->default(true);

            // Membership Logic
            $table->timestamp('membership_start')->nullable();
            $table->timestamp('membership_end')->nullable();
            $table->timestamp('last_renewal_at')->nullable();

            $table->integer('membership_fee')->default(1100);
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
