<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('user_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('date_format')->default('Y-m-d');
            $table->string('first_day_of_week')->default('monday');
            $table->string('default_export_format')->default('pdf');
            $table->integer('sync_frequency_hours')->default(6);
            $table->boolean('email_notifications')->default(true);
            $table->boolean('push_notifications')->default(true);
            $table->boolean('budget_alerts')->default(true);
            $table->boolean('large_transaction_alerts')->default(true);
            $table->decimal('large_transaction_threshold', 15, 2)->default(1000);
            $table->timestamps();

            $table->unique('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_preferences');
    }
};
