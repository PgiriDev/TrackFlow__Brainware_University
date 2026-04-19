<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        if (!Schema::hasTable('account_deletion_otps')) {
            Schema::create('account_deletion_otps', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('user_id');
                $table->string('otp', 16);
                $table->timestamp('expires_at')->nullable();
                $table->timestamps();
                $table->index('user_id');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('account_deletion_otps');
    }
};
