<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('face_authentications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('face_hash', 64)->unique();
            $table->string('model_version')->default('faceapi_v1');
            $table->timestamp('revoked_at')->nullable();
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->boolean('face_registered')->default(false);
            $table->boolean('face_locked')->default(false);
            $table->string('pin_hash')->nullable();
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['face_registered', 'face_locked', 'pin_hash']);
        });
        Schema::dropIfExists('face_authentications');
    }
};
