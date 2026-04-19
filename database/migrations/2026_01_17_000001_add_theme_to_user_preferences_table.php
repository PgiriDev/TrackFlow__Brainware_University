<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('user_preferences', function (Blueprint $table) {
            if (!Schema::hasColumn('user_preferences', 'theme')) {
                $table->string('theme')->default('light')->after('user_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('user_preferences', function (Blueprint $table) {
            if (Schema::hasColumn('user_preferences', 'theme')) {
                $table->dropColumn('theme');
            }
        });
    }
};
