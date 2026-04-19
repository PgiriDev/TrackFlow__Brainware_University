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
        // Polls table
        Schema::create('community_polls', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('post_id')->unique();
            $table->string('question');
            $table->boolean('multiple_choice')->default(false);
            $table->timestamp('ends_at')->nullable();
            $table->timestamps();

            $table->foreign('post_id')->references('id')->on('community_posts')->onDelete('cascade');
        });

        // Poll options table
        Schema::create('community_poll_options', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('poll_id');
            $table->string('option_text');
            $table->unsignedInteger('votes_count')->default(0);
            $table->timestamps();

            $table->foreign('poll_id')->references('id')->on('community_polls')->onDelete('cascade');
        });

        // Poll votes table
        Schema::create('community_poll_votes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('poll_id');
            $table->unsignedBigInteger('option_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();

            $table->foreign('poll_id')->references('id')->on('community_polls')->onDelete('cascade');
            $table->foreign('option_id')->references('id')->on('community_poll_options')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // User can only vote once per poll (for single choice) or once per option (for multiple choice)
            $table->unique(['poll_id', 'option_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('community_poll_votes');
        Schema::dropIfExists('community_poll_options');
        Schema::dropIfExists('community_polls');
    }
};
