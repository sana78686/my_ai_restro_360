<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return response()->json([
            'success' => true,
            'data' => [
                'title' => 'Super User Login',
                'description' => 'Enter your credentials to access the super user dashboard'
            ]
        ]);
    }

    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            if (Auth::attempt($request->only('email', 'password'))) {
                $user = Auth::user();

                if (!$user->hasRole('super_user')) {
                    Auth::logout();
                    return response()->json([
                        'success' => false,
                        'message' => 'You do not have permission to access the super user dashboard.',
                        'errors' => ['role' => ['Unauthorized access']]
                    ], 403);
                }

                $token = $user->createToken('super_user_token')->plainTextToken;

                return response()->json([
                    'success' => true,
                    'message' => 'Login successful',
                    'data' => [
                        'user' => [
                            'id' => $user->id,
                            'name' => $user->name,
                            'email' => $user->email,
                            'role' => 'super_user'
                        ],
                        'token' => $token,
                        'redirect_url' => route('super-user.dashboard')
                    ]
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'The provided credentials are incorrect.',
                'errors' => ['credentials' => ['Invalid email or password']]
            ], 401);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }
    }

    public function logout(Request $request)
    {
        try {



            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Logged out successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Logout failed',
                'errors' => ['logout' => ['Failed to logout']]
            ], 500);
        }
    }
}
