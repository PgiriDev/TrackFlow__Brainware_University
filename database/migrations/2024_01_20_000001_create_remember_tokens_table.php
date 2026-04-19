<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     * Creates secure remember_tokens table for Remember Me functionality
     */
    public function up(): void
    {
        Schema::create('remember_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('token', 100)->unique(); // selector:hashedValidator format (32 + 1 + 64 = 97 chars)
            $table->string('user_agent', 500); // Store user agent for mandatory matching
            $table->string('ip_address', 45)->nullable(); // Optional: for logging purposes
            $table->timestamp('expires_at');
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();

            // Index for efficient token lookups
            $table->index(['token', 'expires_at']);
            $table->index(['user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('remember_tokens');
    }
};
