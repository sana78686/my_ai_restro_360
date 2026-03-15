<?php

namespace App\Http\Controllers\API\Tenant;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Validator;

class PasswordResetController extends Controller
{
    /**
     * Send password reset link
     */
    public function sendResetLink(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email'
        ], [
            'email.required' => 'Email address is required',
            'email.email' => 'Please enter a valid email address'
        ]
    );

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $status = Password::sendResetLink(
                $request->only('email')
            );

            return $status === Password::RESET_LINK_SENT
                ? response()->json([
                    'success' => true,
                    'message' => 'Password reset link sent successfully'
                ])
                : response()->json([
                    'success' => false,
                    'message' => 'Unable to send reset link. Please try again later.'
                ], 500);
                
        } catch (\Exception $e) {
            Log::error('Password reset link error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Server error. Please try again later.'
            ], 500);
        }
    }

    /**
     * Reset password
     */
    public function reset(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
            'password_confirmation' => 'required'
        ], [
            'token.required' => 'Reset token is required',
            'email.required' => 'Email address is required',
            'email.email' => 'Please enter a valid email address',
            'password.required' => 'Password is required',
            'password.min' => 'Password must be at least 8 characters',
            'password.confirmed' => 'Password confirmation does not match',
            'password_confirmation.required' => 'Please confirm your password'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $status = Password::reset(
                $request->only('email', 'password', 'password_confirmation', 'token'),
                function (User $user, string $password) {
                    $user->forceFill([
                        'password' => Hash::make($password)
                    ])->setRememberToken(Str::random(60));

                    $user->save();

                    event(new PasswordReset($user));
                }
            );

            return $status === Password::PASSWORD_RESET
                ? response()->json([
                    'success' => true,
                    'message' => 'Password reset successfully'
                ])
                : response()->json([
                    'success' => false,
                    'message' => 'Invalid or expired reset token'
                ], 400);
                
        } catch (\Exception $e) {
            Log::error('Password reset error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Server error. Please try again later.'
            ], 500);
        }
    }

    /**
     * Validate reset token
     */
    public function validateToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'email' => 'required|email'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid token or email'
            ], 422);
        }

        // Check if token exists and is valid
        $tokenExists = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', Hash::make($request->token))
            ->where('created_at', '>=', now()->subHours(2))
            ->exists();

        return response()->json([
            'success' => $tokenExists,
            'message' => $tokenExists ? 'Token is valid' : 'Invalid or expired token'
        ]);
    }
}
