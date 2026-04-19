<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     * Increase token column size to accommodate selector:hashedValidator format (97 chars)
     */
    public function up(): void
    {
        // Drop the index first (indexes on columns being modified need to be dropped)
        Schema::table('remember_tokens', function (Blueprint $table) {
            $table->dropIndex(['token', 'expires_at']);
            $table->dropUnique(['token']);
        });

        // Modify the column
        Schema::table('remember_tokens', function (Blueprint $table) {
            $table->string('token', 100)->change();
        });

        // Re-add the indexes
        Schema::table('remember_tokens', function (Blueprint $table) {
            $table->unique('token');
            $table->index(['token', 'expires_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('remember_tokens', function (Blueprint $table) {
            $table->dropIndex(['token', 'expires_at']);
            $table->dropUnique(['token']);
        });

        Schema::table('remember_tokens', function (Blueprint $table) {
            $table->string('token', 64)->change();
        });

        Schema::table('remember_tokens', function (Blueprint $table) {
            $table->unique('token');
            $table->index(['token', 'expires_at']);
        });
    }
};
