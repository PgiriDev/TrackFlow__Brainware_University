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
        // Drop feature_interests first (has foreign key to coming_soon_features)
        Schema::dropIfExists('feature_interests');

        // Drop coming_soon_features table
        Schema::dropIfExists('coming_soon_features');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate coming_soon_features table
        Schema::create('coming_soon_features', function (Blueprint $table) {
            $table->id();
            $table->string('feature_key')->unique();
            $table->string('feature_name');
            $table->text('description');
            $table->string('category')->default('general');
            $table->string('icon')->nullable();
            $table->string('color')->default('#6366F1');
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->enum('status', ['planned', 'in_progress', 'testing', 'completed'])->default('planned');
            $table->integer('progress_percentage')->default(0);
            $table->date('estimated_release')->nullable();
            $table->date('actual_release')->nullable();
            $table->integer('interest_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index('feature_key');
            $table->index('status');
            $table->index('category');
        });

        // Recreate feature_interests table
        Schema::create('feature_interests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('feature_id')->constrained('coming_soon_features')->onDelete('cascade');
            $table->text('note')->nullable();
            $table->boolean('notify_on_release')->default(true);
            $table->timestamp('notified_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'feature_id']);
            $table->index('notify_on_release');
        });
    }
};
