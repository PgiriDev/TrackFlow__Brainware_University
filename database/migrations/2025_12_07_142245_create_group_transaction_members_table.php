<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('group_transaction_members', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('transaction_id');
            $table->unsignedBigInteger('member_id');
            $table->decimal('contributed_amount', 15, 2)->default(0);
            $table->decimal('final_share_amount', 15, 2)->default(0);
            $table->boolean('participated')->default(true);
            $table->timestamps();

            $table->foreign('transaction_id')->references('id')->on('group_transactions')->onDelete('cascade');
            $table->foreign('member_id')->references('id')->on('group_members')->onDelete('cascade');
            $table->index(['transaction_id', 'member_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_transaction_members');
    }
};
