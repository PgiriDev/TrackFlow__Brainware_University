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
        Schema::table('user_sessions', function (Blueprint $table) {
            if (!Schema::hasColumn('user_sessions', 'platform')) {
                $table->string('platform')->nullable()->after('session_id');
            }
            if (!Schema::hasColumn('user_sessions', 'location')) {
                $table->string('location')->nullable()->after('ip_address');
            }
            if (!Schema::hasColumn('user_sessions', 'login_time')) {
                $table->timestamp('login_time')->nullable()->after('location');
            }

            // Update last_activity to have current timestamp as default if not set
            $table->timestamp('last_activity')->useCurrent()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_sessions', function (Blueprint $table) {
            if (Schema::hasColumn('user_sessions', 'platform')) {
                $table->dropColumn('platform');
            }
            if (Schema::hasColumn('user_sessions', 'location')) {
                $table->dropColumn('location');
            }
            if (Schema::hasColumn('user_sessions', 'login_time')) {
                $table->dropColumn('login_time');
            }
        });
    }
};
