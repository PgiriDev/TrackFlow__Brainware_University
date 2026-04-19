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
        // Community Posts Table
        Schema::create('community_posts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('title', 255);
            $table->longText('content');
            $table->enum('type', ['feedback', 'suggestion', 'opinion', 'bug', 'announcement'])->default('feedback');
            $table->enum('status', ['open', 'under_review', 'planned', 'in_progress', 'implemented', 'rejected'])->default('open');
            $table->boolean('is_anonymous')->default(false);
            $table->boolean('is_pinned')->default(false);
            $table->integer('comment_count')->default(0);
            $table->integer('vote_score')->default(0);
            $table->integer('view_count')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['status', 'created_at']);
            $table->index(['type', 'created_at']);
            $table->index('vote_score');
        });

        // Community Comments Table (Threaded)
        Schema::create('community_comments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('post_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->text('content');
            $table->integer('vote_score')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('post_id')->references('id')->on('community_posts')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('parent_id')->references('id')->on('community_comments')->onDelete('cascade');
            $table->index(['post_id', 'created_at']);
        });

        // Community Votes Table
        Schema::create('community_votes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('post_id')->nullable();
            $table->unsignedBigInteger('comment_id')->nullable();
            $table->enum('vote', ['up', 'down'])->default('up');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('post_id')->references('id')->on('community_posts')->onDelete('cascade');
            $table->foreign('comment_id')->references('id')->on('community_comments')->onDelete('cascade');
            $table->unique(['user_id', 'post_id'], 'unique_user_post_vote');
            $table->unique(['user_id', 'comment_id'], 'unique_user_comment_vote');
        });

        // Community Reactions Table
        Schema::create('community_reactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('post_id');
            $table->enum('reaction', ['love', 'useful', 'mindblown', 'confused'])->default('love');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('post_id')->references('id')->on('community_posts')->onDelete('cascade');
            $table->unique(['user_id', 'post_id', 'reaction'], 'unique_user_post_reaction');
        });

        // Community Tags Table
        Schema::create('community_tags', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->string('slug', 50)->unique();
            $table->string('color', 7)->default('#6366f1');
            $table->timestamps();
        });

        // Community Post Tags (Pivot Table)
        Schema::create('community_post_tags', function (Blueprint $table) {
            $table->unsignedBigInteger('post_id');
            $table->unsignedBigInteger('tag_id');

            $table->foreign('post_id')->references('id')->on('community_posts')->onDelete('cascade');
            $table->foreign('tag_id')->references('id')->on('community_tags')->onDelete('cascade');
            $table->primary(['post_id', 'tag_id']);
        });

        // Community Reports Table
        Schema::create('community_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('reporter_id');
            $table->unsignedBigInteger('post_id')->nullable();
            $table->unsignedBigInteger('comment_id')->nullable();
            $table->string('reason', 255);
            $table->text('description')->nullable();
            $table->enum('status', ['pending', 'reviewed', 'dismissed'])->default('pending');
            $table->timestamps();

            $table->foreign('reporter_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('post_id')->references('id')->on('community_posts')->onDelete('cascade');
            $table->foreign('comment_id')->references('id')->on('community_comments')->onDelete('cascade');
        });

        // Community Notifications Table
        Schema::create('community_notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('type', 50);
            $table->json('data')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['user_id', 'is_read', 'created_at']);
        });

        // Community Reputation Table
        Schema::create('community_reputation', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->primary();
            $table->integer('points')->default(0);
            $table->string('level', 50)->default('Newbie');
            $table->timestamp('last_updated')->nullable();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // Insert default tags
        \DB::table('community_tags')->insert([
            ['name' => 'Ideas', 'slug' => 'ideas', 'color' => '#eab308', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Bugs', 'slug' => 'bugs', 'color' => '#ef4444', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Expense Tips', 'slug' => 'expense-tips', 'color' => '#22c55e', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Productivity', 'slug' => 'productivity', 'color' => '#3b82f6', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Group Use', 'slug' => 'group-use', 'color' => '#8b5cf6', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Feature Requests', 'slug' => 'feature-requests', 'color' => '#ec4899', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('community_reputation');
        Schema::dropIfExists('community_notifications');
        Schema::dropIfExists('community_reports');
        Schema::dropIfExists('community_post_tags');
        Schema::dropIfExists('community_tags');
        Schema::dropIfExists('community_reactions');
        Schema::dropIfExists('community_votes');
        Schema::dropIfExists('community_comments');
        Schema::dropIfExists('community_posts');
    }
};
