<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Attach the community posts foreign key after the posts table exists.
     */
    public function up(): void
    {
        if (!Schema::hasTable('community_polls') || !Schema::hasTable('community_posts')) {
            return;
        }

        $hasConstraint = DB::selectOne("
            SELECT CONSTRAINT_NAME AS name
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'community_polls'
              AND COLUMN_NAME = 'post_id'
              AND REFERENCED_TABLE_NAME = 'community_posts'
            LIMIT 1
        ");

        if (!$hasConstraint) {
            Schema::table('community_polls', function (Blueprint $table) {
                $table->foreign('post_id')->references('id')->on('community_posts')->onDelete('cascade');
            });
        }
    }

    /**
     * Remove the foreign key if present.
     */
    public function down(): void
    {
        if (!Schema::hasTable('community_polls')) {
            return;
        }

        $hasConstraint = DB::selectOne("
            SELECT CONSTRAINT_NAME AS name
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'community_polls'
              AND COLUMN_NAME = 'post_id'
              AND REFERENCED_TABLE_NAME = 'community_posts'
            LIMIT 1
        ");

        if ($hasConstraint) {
            Schema::table('community_polls', function (Blueprint $table) {
                $table->dropForeign(['post_id']);
            });
        }
    }
};
