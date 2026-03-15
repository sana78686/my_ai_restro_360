<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\TenantWelcomeEmail;
use App\Mail\TenantRegistrationNotification;

class TenantRegistrationController extends Controller
{
    public function showRegistrationForm()
    {
        return view('frontend.tenant-register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'domain' => 'required|string|max:255|unique:tenants,domain',
            'email' => 'required|string|email|max:255|unique:users,email',
            'phone' => 'required|string|max:255',
            'password' => 'required|string|min:8|confirmed',
            'address' => 'required|string',
            'terms' => 'required|accepted',
        ]);

        // Create tenant
        $tenant = Tenant::create([
            'id' => $request->domain,
            'name' => $request->name,
            'domain' => $request->domain,
            'status' => 'trial',
            'trial_ends_at' => now()->addDays(14),
        ]);

        // Create tenant admin user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'address' => $request->address,
            'tenant_id' => $tenant->id,
        ]);

        // Assign restaurant owner role
        $user->assignRole('restaurant_owner');

        dd($user->email);
        // Send welcome email to tenant
        Mail::to($user->email)->send(new TenantWelcomeEmail($tenant, $user));

        // Send notification to super admin
        $superAdmin = User::role('super_user')->first();
        if ($superAdmin) {
            Mail::to($superAdmin->email)->send(new TenantRegistrationNotification($tenant, $user));
        }

        // Login the user
        auth()->login($user);

        return response()->json([
            'success' => true,
            'message' => 'Registration successful!',
            'redirect_url' => route('tenant.dashboard'),
        ]);
    }
} 