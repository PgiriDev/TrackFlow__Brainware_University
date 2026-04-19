<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('account_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_account_id')->constrained()->onDelete('cascade');
            $table->text('access_token'); // encrypted
            $table->text('refresh_token')->nullable(); // encrypted
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index('bank_account_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('account_tokens');
    }
};
