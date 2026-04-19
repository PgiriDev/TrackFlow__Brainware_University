<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Remove all bank account related tables and columns.
     *
     * Drops:
     *   - account_tokens table (depends on bank_accounts)
     *   - sync_logs table (bank sync logging)
     *   - bank_account_id column from transactions table
     *   - bank_account_id column from recurring_transactions table
     *   - bank_accounts table
     */
    public function up(): void
    {
        // 1. Drop account_tokens table (has FK to bank_accounts)
        Schema::dropIfExists('account_tokens');

        // 2. Drop sync_logs table (has FK to bank_accounts)
        Schema::dropIfExists('sync_logs');

        // 3. Remove bank_account_id from transactions table
        Schema::table('transactions', function (Blueprint $table) {
            // Drop foreign key constraint first (required before dropping index in MySQL)
            $table->dropForeign(['bank_account_id']);
            // Now drop the composite index
            $table->dropIndex(['bank_account_id', 'date']);
            // Finally drop the column
            $table->dropColumn('bank_account_id');
        });

        // 4. Remove bank_account_id from recurring_transactions table
        if (Schema::hasTable('recurring_transactions')) {
            Schema::table('recurring_transactions', function (Blueprint $table) {
                $table->dropForeign(['bank_account_id']);
                $table->dropColumn('bank_account_id');
            });
        }

        // 5. Drop bank_accounts table last (other FKs already removed)
        Schema::dropIfExists('bank_accounts');
    }

    /**
     * Reverse the migration - recreate bank account tables and columns.
     */
    public function down(): void
    {
        // 1. Recreate bank_accounts table
        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('bank_name');
            $table->string('account_number_masked')->nullable();
            $table->string('account_type')->nullable();
            $table->decimal('balance', 15, 2)->default(0);
            $table->string('currency', 3)->default('USD');
            $table->string('provider');
            $table->string('provider_account_id')->unique();
            $table->string('status')->default('active');
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index('provider_account_id');
        });

        // 2. Recreate account_tokens table
        Schema::create('account_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_account_id')->constrained()->onDelete('cascade');
            $table->text('access_token');
            $table->text('refresh_token')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index('bank_account_id');
        });

        // 3. Recreate sync_logs table
        Schema::create('sync_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('bank_account_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('action');
            $table->enum('status', ['success', 'failed', 'partial'])->default('success');
            $table->text('message')->nullable();
            $table->integer('transactions_fetched')->default(0);
            $table->json('error_details')->nullable();
            $table->timestamps();

            $table->index(['bank_account_id', 'created_at']);
            $table->index(['user_id', 'status']);
        });

        // 4. Re-add bank_account_id to transactions
        Schema::table('transactions', function (Blueprint $table) {
            $table->foreignId('bank_account_id')->nullable()->after('user_id')->constrained()->onDelete('set null');
            $table->index(['bank_account_id', 'date']);
        });

        // 5. Re-add bank_account_id to recurring_transactions
        if (Schema::hasTable('recurring_transactions')) {
            Schema::table('recurring_transactions', function (Blueprint $table) {
                $table->foreignId('bank_account_id')->nullable()->after('user_id')->constrained()->onDelete('set null');
            });
        }
    }
};
