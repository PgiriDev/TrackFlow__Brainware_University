<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     * 
     * This unified OTPs table stores all OTPs for different purposes:
     * - password_change: OTP for changing password
     * - account_deletion: OTP for deleting account
     * - 2fa_email: OTP for enabling email-based 2FA
     * - 2fa_verify: OTP for verifying 2FA during login
     * - email_verification: OTP for verifying email address
     */
    public function up(): void
    {
        Schema::create('otps', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('email'); // The email OTP was sent to
            $table->string('otp', 16); // The OTP code (up to 16 digits)
            $table->enum('purpose', [
                'password_change',
                'account_deletion',
                '2fa_email',
                '2fa_verify',
                'email_verification'
            ])->default('password_change');
            $table->text('extra_data')->nullable(); // For storing additional data like hashed new password
            $table->timestamp('expires_at');
            $table->timestamps();

            // Indexes for faster lookups
            $table->index('user_id');
            $table->index('email');
            $table->index(['email', 'purpose']);
            $table->index(['user_id', 'purpose']);

            // Foreign key constraint
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('otps');
    }
};
