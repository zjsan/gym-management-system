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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('receipt_no')->unique(); // e.g., PAY-20260831-0001
            $table->foreignId('member_id')->nullable()->constrained('members')->onDelete('set null');
            $table->foreignId('walkin_id')->nullable()->constrained('walkins')->onDelete('set null');
            $table->foreignId('processed_by')->constrained('users')->onDelete('cascade');
            $table->enum('category', ['walkin_fee', 'membership_registration', 'membership_renewal']);
            $table->decimal('amount', 10, 2);
            $table->string('payment_method')->default('cash');
            $table->timestamp('paid_at');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
