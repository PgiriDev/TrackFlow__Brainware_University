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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('type'); // budget_overspend, goal_completed, group_member_added, etc.
            $table->string('title');
            $table->text('message');
            $table->string('icon')->default('fa-bell');
            $table->string('color')->default('blue'); // blue, green, red, yellow, purple
            $table->json('data')->nullable(); // Additional data like IDs, amounts, etc.
            $table->string('action_url')->nullable(); // URL to navigate when clicked
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->timestamps();

            $table->index('user_id');
            $table->index('is_read');
            $table->index(['user_id', 'is_read']);
            $table->index('created_at');
        });

        Schema::create('notification_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->boolean('budget_alerts')->default(true);
            $table->boolean('goal_updates')->default(true);
            $table->boolean('group_activities')->default(true);
            $table->boolean('transaction_alerts')->default(true);
            $table->boolean('bill_reminders')->default(true);
            $table->boolean('feature_updates')->default(true);
            $table->boolean('email_notifications')->default(true);
            $table->boolean('push_notifications')->default(false);
            $table->integer('budget_threshold_percentage')->default(80); // Notify when 80% spent
            $table->timestamps();

            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_preferences');
        Schema::dropIfExists('notifications');
    }
};
