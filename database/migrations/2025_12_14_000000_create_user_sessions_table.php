<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('user_sessions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('session_id')->unique();
            $table->string('platform')->nullable();
            $table->string('browser')->nullable();
            $table->string('device')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('location')->nullable();
            $table->timestamp('login_time')->nullable();
            $table->timestamp('last_activity')->useCurrent();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index('session_id');
            $table->index('user_id');
            $table->index('last_activity');
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_sessions');
    }
};
