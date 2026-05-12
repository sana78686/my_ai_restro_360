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
use Stancl\Tenancy\Database\Models\Domain;
use Stancl\Tenancy\Facades\Tenancy; // Import this
use App\Helpers\TenantHost;

class TenantRegistrationController extends Controller
{
    public function showRegistrationForm()
    {
        return view('frontend.tenant-register');
    }

    public function register(Request $request)
    {
        dd('hello');
        $request->validate([
            'name' => 'required|string|max:255',
            'domain' => 'required|string|max:255|unique:tenants,domain',
            'email' => 'required|string|email|max:255',
            'phone' => 'required|string|max:255',
            'password' => 'required|string|min:8|confirmed',
            'address' => 'required|string',
            'terms' => 'required|accepted',
        ]);

        // Create tenant in central database
        $tenant = Tenant::create([
            'id' => $request->domain,
            'name' => $request->name,
            'domain' => $request->domain,
            'status' => 'trial',
            'trial_ends_at' => now()->addDays(14),
        ]);

        // Create domain entry if using Stancl tenancy
        Domain::create([
            'domain' => TenantHost::fqdn($request->domain),
            'tenant_id' => $tenant->id,
        ]);

        // 🔹 Send notification to super admin (central DB)
        $superAdmin = User::role('super_user')->first();
        if ($superAdmin) {
            Mail::to($superAdmin->email)->send(new TenantRegistrationNotification($tenant));
        }

        // 🔹 Switch to tenant database context
        tenancy()->initialize($tenant);

        // Create tenant's first user inside tenant DB
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'address' => $request->address,
        ]);

        $user->assignRole('restaurant_owner');

        // 🔹 Send welcome email to tenant
        Mail::to($user->email)->send(new TenantWelcomeEmail($tenant, $user));

        // Login user (within tenant)
        auth()->login($user);

        // End tenancy context
        tenancy()->end();

        return response()->json([
            'success' => true,
            'message' => 'Registration successful! Welcome email sent.',
            'redirect_url' => route('tenant.dashboard'),
        ]);
    }
}
