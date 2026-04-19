<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('scheduled_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');
            $table->date('scheduled_date');
            $table->string('description');
            $table->string('merchant')->nullable();
            $table->decimal('amount', 15, 2);
            $table->string('currency', 3)->default('INR');
            $table->enum('type', ['credit', 'debit']);
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('pending');
            $table->boolean('confirmation_sent')->default(false);
            $table->boolean('reminder_sent')->default(false);
            $table->timestamp('confirmation_sent_at')->nullable();
            $table->timestamp('reminder_sent_at')->nullable();
            $table->foreignId('transaction_id')->nullable()->constrained()->onDelete('set null'); // Links to actual transaction when executed
            $table->timestamps();

            $table->index(['user_id', 'scheduled_date']);
            $table->index(['status', 'scheduled_date']);
            $table->index(['reminder_sent', 'scheduled_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scheduled_transactions');
    }
};
