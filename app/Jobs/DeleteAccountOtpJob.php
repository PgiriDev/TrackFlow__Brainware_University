<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class DeleteAccountOtpJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $otpId;

    public function __construct($otpId)
    {
        $this->otpId = $otpId;
    }

    public function handle()
    {
        try {
            // Delete from unified otps table
            DB::table('otps')->where('id', $this->otpId)->delete();
        } catch (\Exception $e) {
            // Log and ignore - it will be cleaned up on next actions
            \Illuminate\Support\Facades\Log::error('DeleteAccountOtpJob failed: ' . $e->getMessage());
        }
    }
}
