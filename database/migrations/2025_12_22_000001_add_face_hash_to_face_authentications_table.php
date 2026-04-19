<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        if (!Schema::hasColumn('face_authentications', 'face_hash')) {
            Schema::table('face_authentications', function (Blueprint $table) {
                $table->string('face_hash', 64)->unique()->nullable(false)->after('user_id');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('face_authentications', 'face_hash')) {
            Schema::table('face_authentications', function (Blueprint $table) {
                $table->dropColumn('face_hash');
            });
        }
    }
};
