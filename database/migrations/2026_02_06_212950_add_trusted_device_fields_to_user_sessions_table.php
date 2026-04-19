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
            $table->string('device_fingerprint', 64)->nullable()->after('ip_address');
            $table->boolean('is_trusted')->default(false)->after('device_fingerprint');
            $table->boolean('requires_2fa')->default(false)->after('is_trusted');
            $table->timestamp('trusted_at')->nullable()->after('requires_2fa');

            $table->index('device_fingerprint');
            $table->index(['user_id', 'device_fingerprint']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_sessions', function (Blueprint $table) {
            $table->dropIndex(['device_fingerprint']);
            $table->dropIndex(['user_id', 'device_fingerprint']);
            $table->dropColumn(['device_fingerprint', 'is_trusted', 'requires_2fa', 'trusted_at']);
        });
    }
};
