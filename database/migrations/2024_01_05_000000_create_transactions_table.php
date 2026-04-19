<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('bank_account_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');
            $table->date('date');
            $table->string('description');
            $table->string('merchant')->nullable();
            $table->decimal('amount', 15, 2);
            $table->string('currency', 3)->default('USD');
            $table->enum('type', ['credit', 'debit']);
            $table->enum('status', ['pending', 'completed'])->default('completed');
            $table->string('payment_method')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_recurring')->default(false);
            $table->boolean('is_duplicate')->default(false);
            $table->foreignId('duplicate_of_id')->nullable()->constrained('transactions')->onDelete('set null');
            $table->string('provider_tx_id')->nullable()->unique();
            $table->string('transaction_hash')->nullable()->index();
            $table->json('raw_data')->nullable(); // original provider payload
            $table->timestamps();

            $table->index(['user_id', 'date']);
            $table->index(['user_id', 'category_id']);
            $table->index(['bank_account_id', 'date']);
            $table->index(['merchant', 'date']);
            $table->index('provider_tx_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
