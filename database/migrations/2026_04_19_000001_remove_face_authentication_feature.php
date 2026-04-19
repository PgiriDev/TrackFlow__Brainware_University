<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Remove face authentication tables and related user flag if present.
     */
    public function up(): void
    {
        Schema::dropIfExists('face_data');
        Schema::dropIfExists('face_authentications');

        if (Schema::hasTable('users') && Schema::hasColumn('users', 'face_registered')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('face_registered');
            });
        }
    }

    /**
     * Recreate minimal structures for rollback compatibility.
     */
    public function down(): void
    {
        if (!Schema::hasTable('face_authentications')) {
            Schema::create('face_authentications', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->text('face_vector')->nullable();
                $table->string('face_hash', 64)->unique();
                $table->timestamp('revoked_at')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('face_data')) {
            Schema::create('face_data', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->text('face_vector');
                $table->timestamps();
            });
        }

        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'face_registered')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('face_registered')->default(false);
            });
        }
    }
};
