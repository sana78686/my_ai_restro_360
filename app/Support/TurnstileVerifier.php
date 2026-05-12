<?php

namespace App\Support;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TurnstileVerifier
{
    public static function isConfigured(): bool
    {
        return (bool) config('turnstile.secret_key');
    }

    public static function verify(?string $token, ?string $remoteIp = null): bool
    {
        $secret = config('turnstile.secret_key');
        if (! $secret) {
            return true;
        }

        if (! is_string($token) || strlen($token) < 10) {
            return false;
        }

        try {
            $payload = [
                'secret' => $secret,
                'response' => $token,
            ];
            if ($remoteIp) {
                $payload['remoteip'] = $remoteIp;
            }

            $response = Http::timeout(12)->asForm()->post(
                'https://challenges.cloudflare.com/turnstile/v0/siteverify',
                $payload
            );

            $body = $response->json();

            return ! empty($body['success']);
        } catch (\Throwable $e) {
            Log::warning('Turnstile siteverify failed: '.$e->getMessage());

            return false;
        }
    }
}
