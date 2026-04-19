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
        Schema::table('transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('budget_id')->nullable()->after('category_id');
            $table->unsignedBigInteger('budget_item_id')->nullable()->after('budget_id');

            $table->foreign('budget_id')->references('id')->on('budgets')->onDelete('set null');
            $table->foreign('budget_item_id')->references('id')->on('budget_items')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['budget_id']);
            $table->dropForeign(['budget_item_id']);
            $table->dropColumn(['budget_id', 'budget_item_id']);
        });
    }
};
