<?php

namespace App\Support;

use Illuminate\Support\Facades\Log;

/**
 * Exposes OTPs in local env only (API + log) so devs can copy without opening mail.
 */
class MailDebug
{
    public static function otpPayload(string $otp, string $email): array
    {
        if (! app()->environment('local')) {
            return [];
        }

        $otp = (string) $otp;
        Log::info("[mail-debug] OTP for {$email}: {$otp}");

        return ['debug_mail_otp' => $otp];
    }
}
