<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Tenant;
use App\Helpers\TenantHost;
use App\Mail\VerifyOtpMail;
use Illuminate\Http\Request;
use App\Mail\PasswordResetOtp;
use App\Mail\TenantWelcomeEmail;
use App\Http\Controllers\Controller;
use App\Support\MailDebug;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\TenantRegistrationNotification;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Unified entry: detect super admin (central users) vs tenant owner by email.
     */
    public function loginLookup(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $email = strtolower(trim($request->string('email')->toString()));

        $superUser = User::query()
            ->whereRaw('LOWER(email) = ?', [$email])
            ->first();

        if ($superUser && $superUser->hasRole('super_user')) {
            return response()->json([
                'account_type' => 'super_admin',
                'email' => $superUser->email,
            ]);
        }

        $tenant = Tenant::query()
            ->whereRaw('LOWER(owner_email) = ?', [$email])
            ->first(['id', 'business_name', 'subdomain', 'owner_email']);

        if ($tenant) {
            return response()->json([
                'account_type' => 'tenant',
                'email' => $tenant->owner_email,
                'login_url' => $this->buildTenantLoginUrl($tenant),
                'subdomain' => $tenant->subdomain,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No account found for this email.',
        ], 404);
    }

    protected function buildTenantLoginUrl(Tenant $tenant): string
    {
        return TenantHost::loginUrl((string) ($tenant->subdomain ?? ''), $tenant->id);
    }

    public function checkSubdomain($subdomain)
    {
        $tenant = \App\Models\Tenant::where('domain', $subdomain)->first();

        return response()->json([
            'exists' => (bool) $tenant,
            'tenant' => $tenant ? [
                'name' => $tenant->name,
                'domain' => $tenant->domain,
            ] : null
        ]);
    }

    public function register(Request $request)
    {
        // dd('hello');
        $request->validate([
            'restaurantName' => 'required|string|max:255',
            'domain' => 'required|string|max:255|unique:tenants,domain',
            'email' => 'required|string|email|max:255|unique:users,email',
            'phone' => 'required|string|max:255',
            'password' => 'required|string|min:8|confirmed',
            'address' => 'required|string',
            'street' => 'required|string',
            'postalCode' => 'required|string',
            'city' => 'required|string',
            'state' => 'required|string',
            'country' => 'required|string',
            'county' => 'required|string',
        ]);

        // Create tenant
        $tenant = Tenant::create([
            'id' => $request->domain,
            'name' => $request->restaurantName,
            'domain' => $request->domain,
            'status' => 'trial',
            'trial_ends_at' => now()->addDays(14),
            'data' => [
                'address' => $request->address,
                'street' => $request->street,
                'postalCode' => $request->postalCode,
                'city' => $request->city,
                'state' => $request->state,
                'country' => $request->country,
                'county' => $request->county,
            ]
        ]);

        // Create tenant admin user
        $user = User::create([
            'name' => $request->restaurantName,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'address' => $request->address,
            'tenant_id' => $tenant->id,
        ]);

        // Assign restaurant owner role
        $user->assignRole('restaurant_owner');

        // Send welcome email to tenant
        Mail::to($user->email)->send(new TenantWelcomeEmail($tenant, $user));

        // Send notification to super admin
        $superAdmin = User::role('super_user')->first();
        if ($superAdmin) {
            Mail::to($superAdmin->email)->send(new TenantRegistrationNotification($tenant, $user));
        }

        // Create token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Registration successful!',
            'data' => [
                'user' => $user,
                'tenant' => $tenant,
                'token' => $token,
            ],
            'redirect_url' => route('tenant.dashboard'),
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            'subdomain' => 'nullable|string'
        ]);

        // 🔹 Find user
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // 🔹 Tenant or Super User Check
        if ($request->subdomain) 
        {
            // Tenant login
            $tenant = Tenant::where('domain', $request->subdomain)->first();

            if (!$tenant) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid tenant domain'
                ], 404);
            }

            // Ensure user belongs to tenant
            if ($user->tenant_id !== $tenant->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have access to this restaurant'
                ], 403);
            }

            // Ensure user has restaurant_owner role
            if (!$user->hasRole('restaurant_owner')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient permissions'
                ], 403);
            }

        }
         else 
         {
            // Main domain login → must be super user
            if (!$user->hasRole('super_user')) {
                return response()->json([
                    'success' => false,
                    'message' => 'This login is for super users only'
                ], 403);
            }
        }

        // 🔹 OTP Verification Required
        if (!$user->email_verified_at) {
            $otp = rand(100000, 999999); // generate 6-digit OTP
            $user->update([
                'otp' => $otp,
                'otp_expires_at' => now()->addMinutes(10),
            ]);

            // Send email with OTP
            try {
                Mail::to($user->email)->send(new VerifyOtpMail($otp));
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send OTP. Please try again later.'
                ], 500);
            }

            return response()->json(array_merge([
                'success' => false,
                'status' => 'verify_required',
                'message' => 'OTP sent to your email. Please verify.',
            ], MailDebug::otpPayload((string) $otp, $user->email)), 403);
        }

        // 🔹 If already verified → issue token
        $user->tokens()->delete(); // remove old tokens
        $token = $user->createToken('auth_token')->plainTextToken;

        // 🔹 Roles & permissions
        $roles = $user->roles->pluck('name');
        $permissions = $user->getAllPermissions()->pluck('name');

        // 🔹 Tenant Info
        $tenant = $user->tenant ? [
            'id' => $user->tenant->id,
            'name' => $user->tenant->name,
            'domain' => $user->tenant->domain,
        ] : null;

        return response()->json([
            'success' => true,
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $roles,
                'permissions' => $permissions,
                'tenant' => $tenant,
            ],
            'redirect_url' => $this->getRedirectUrl($roles, $user->tenant)
        ]);
    }




    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|digits:6',
            'subdomain' => 'nullable|string'
        ]);

        if ($request->subdomain) {
            $tenant = Tenant::where('domain', $request->subdomain)->first();
            if (!$tenant) {
                return response()->json(['success' => false, 'message' => 'Invalid tenant domain'], 404);
            }
            tenancy()->initialize($tenant);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || $user->otp !== $request->otp || now()->greaterThan($user->otp_expires_at)) {
            return response()->json(['success' => false, 'message' => 'Invalid or expired OTP'], 401);
        }

        $user->update([
            'email_verified_at' => now(),
            'otp' => null,
            'otp_expires_at' => null
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'token' => $token,
            'user' => $user
        ]);
    }

  public function logout(Request $request)
{


    // Delete the current access token
    $user->currentAccessToken()->delete();

    return response()->json([
        'success' => true,
        'message' => 'Successfully logged out'
    ]);
}


      private function getRedirectUrl($roles, $tenant = null)
    {
        if ($roles->contains('super_user')) {
            return '/dashboard';
        }

        if ($roles->contains('restaurant_owner') && $tenant) {
            return "http://{$tenant->domain}.localhost:5000/dashboard";
        }

        return '/';
    }
}
