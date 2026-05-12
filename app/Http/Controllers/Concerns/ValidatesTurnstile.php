<?php

namespace App\Http\Controllers\Concerns;

use App\Support\TurnstileVerifier;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

trait ValidatesTurnstile
{
    protected function validateTurnstile(Request $request): void
    {
        if (! TurnstileVerifier::isConfigured()) {
            return;
        }

        $request->validate([
            'turnstile_token' => 'required|string',
        ]);

        if (! TurnstileVerifier::verify($request->input('turnstile_token'), $request->ip())) {
            throw ValidationException::withMessages([
                'turnstile_token' => ['Security verification failed. Please try again.'],
            ]);
        }
    }
}
