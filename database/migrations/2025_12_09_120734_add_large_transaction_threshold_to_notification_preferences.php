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
        Schema::table('notification_preferences', function (Blueprint $table) {
            $table->decimal('large_transaction_threshold', 15, 2)->default(333.87)->after('push_notifications');
            $table->boolean('large_transaction_alerts')->default(true)->after('large_transaction_threshold');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notification_preferences', function (Blueprint $table) {
            $table->dropColumn(['large_transaction_threshold', 'large_transaction_alerts']);
        });
    }
};
