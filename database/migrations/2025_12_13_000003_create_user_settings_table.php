<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserSettingsTable extends Migration
{
    public function up()
    {
        Schema::create('user_settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->unique();
            $table->string('base_currency', 3)->default('INR');
            $table->string('display_currency', 3)->default('INR');
            $table->timestamp('currency_updated_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // Populate defaults for existing users
        $users = \DB::table('users')->select('id')->get();
        foreach ($users as $u) {
            \DB::table('user_settings')->updateOrInsert([
                'user_id' => $u->id
            ], [
                'base_currency' => 'INR',
                'display_currency' => 'INR',
                'currency_updated_at' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }

    public function down()
    {
        Schema::dropIfExists('user_settings');
    }
}
