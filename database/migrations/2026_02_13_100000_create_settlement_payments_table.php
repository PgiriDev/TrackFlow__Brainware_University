<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('settlement_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained()->onDelete('cascade');
            $table->foreignId('payer_member_id')->constrained('group_members')->onDelete('cascade');
            $table->foreignId('receiver_member_id')->constrained('group_members')->onDelete('cascade');
            $table->decimal('amount', 12, 2);
            $table->string('currency', 3)->default('INR');
            $table->enum('status', ['pending', 'verification_pending', 'auto_verified', 'paid', 'rejected'])->default('pending');
            $table->string('transaction_id')->nullable();
            $table->string('proof_screenshot')->nullable();
            $table->text('payment_note')->nullable();
            $table->string('upi_id_used')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('verified_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();

            // Indexes for faster queries
            $table->index(['group_id', 'status']);
            $table->index(['payer_member_id', 'status']);
            $table->index(['transaction_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settlement_payments');
    }
};
