<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sync_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('bank_account_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('action'); // sync_transactions, fetch_accounts, refresh_token
            $table->enum('status', ['success', 'failed', 'partial'])->default('success');
            $table->text('message')->nullable();
            $table->integer('transactions_fetched')->default(0);
            $table->json('error_details')->nullable();
            $table->timestamps();

            $table->index(['bank_account_id', 'created_at']);
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sync_logs');
    }
};
