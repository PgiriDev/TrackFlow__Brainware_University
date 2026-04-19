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
        Schema::create('recurring_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('bank_account_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');
            $table->string('description', 1000);
            $table->string('merchant', 255)->nullable();
            $table->decimal('amount', 16, 2);
            $table->char('currency', 3)->default('INR');
            $table->enum('type', ['debit', 'credit']);
            $table->enum('frequency', ['daily', 'weekly', 'biweekly', 'monthly', 'quarterly', 'yearly']);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->date('next_occurrence');
            $table->boolean('is_active')->default(true);
            $table->boolean('auto_create')->default(true)->comment('Auto-create transaction on occurrence');
            $table->datetime('last_created_at')->nullable();
            $table->timestamps();

            $table->index('user_id', 'idx_recurring_user');
            $table->index('next_occurrence', 'idx_recurring_next');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recurring_transactions');
    }
};
