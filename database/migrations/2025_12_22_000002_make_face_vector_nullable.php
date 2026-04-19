<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class MakeFaceVectorNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Use raw SQL to avoid requiring doctrine/dbal for column changes
        DB::statement('ALTER TABLE `face_authentications` MODIFY `face_vector` TEXT NULL');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE `face_authentications` MODIFY `face_vector` TEXT NOT NULL');
    }
}
