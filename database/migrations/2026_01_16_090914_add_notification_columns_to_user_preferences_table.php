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
        Schema::table('user_preferences', function (Blueprint $table) {
            // Add missing notification columns if they don't exist
            if (!Schema::hasColumn('user_preferences', 'transaction_alerts')) {
                $table->boolean('transaction_alerts')->default(true)->after('email_notifications');
            }
            if (!Schema::hasColumn('user_preferences', 'weekly_summary')) {
                $table->boolean('weekly_summary')->default(true)->after('large_transaction_threshold');
            }
            if (!Schema::hasColumn('user_preferences', 'goal_progress')) {
                $table->boolean('goal_progress')->default(true)->after('weekly_summary');
            }
            if (!Schema::hasColumn('user_preferences', 'group_expense')) {
                $table->boolean('group_expense')->default(true)->after('goal_progress');
            }
            if (!Schema::hasColumn('user_preferences', 'login_alerts')) {
                $table->boolean('login_alerts')->default(true)->after('group_expense');
            }
            if (!Schema::hasColumn('user_preferences', 'new_device_alerts')) {
                $table->boolean('new_device_alerts')->default(true)->after('login_alerts');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_preferences', function (Blueprint $table) {
            $columns = ['transaction_alerts', 'weekly_summary', 'goal_progress', 'group_expense', 'login_alerts', 'new_device_alerts'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('user_preferences', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
