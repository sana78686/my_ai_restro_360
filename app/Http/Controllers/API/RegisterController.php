<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Helpers\FileUpload;
use App\Mail\NewTenantNotification;
use App\Mail\WelcomeTenant;
use Spatie\Permission\Models\Role;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'restaurant_name' => 'required|string|max:255|unique:tenants,name',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'street' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
            'place_id' => 'required|string'
        ]);

        try {
            // Create tenant
            $tenant = Tenant::create([
                'name' => $request->restaurant_name,
                'domain' => Str::slug($request->restaurant_name),
                'street' => $request->street,
                'city' => $request->city,
                'state' => $request->state,
                'country' => $request->country,
                'postal_code' => $request->postal_code,
                'place_id' => $request->place_id
            ]);

            // Handle logo upload (S3)
            if ($request->hasFile('logo')) {
                $result = FileUpload::upload($request->file('logo'), 'tenant_logos', null, true, (string) $tenant->id);
                if (! empty($result['paths'][0])) {
                    $tenant->logo = $result['paths'][0];
                    $tenant->save();
                }
            }

            // Create user
            $user = User::create([
                'name' => $request->restaurant_name . ' Admin',
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'tenant_id' => $tenant->id
            ]);

            // Assign restaurant_owner role
            $role = Role::findByName('restaurant_owner');
            $user->assignRole($role);

            // Generate token
            $token = $user->createToken('auth_token')->plainTextToken;

            // Send emails
            // $superAdmin = User::role('super_user')->first();
            // Mail::to($superAdmin)->send(new NewTenantNotification($tenant));
            // Mail::to($user)->send(new WelcomeTenant($tenant));

            return response()->json([
                'success' => true,
                'message' => 'Registration successful',
                'token' => $token,
                'user' => $user,
                'tenant' => $tenant
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Registration failed: ' . $e->getMessage()
            ], 500);
        }
    }
} 