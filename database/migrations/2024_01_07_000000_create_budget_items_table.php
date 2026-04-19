<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('budget_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('budget_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->decimal('limit_amount', 15, 2);
            $table->decimal('spent_amount', 15, 2)->default(0);
            $table->timestamps();

            $table->unique(['budget_id', 'category_id']);
            $table->index('budget_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('budget_items');
    }
};
