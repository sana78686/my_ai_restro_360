<?php

namespace App\Http\Controllers\API\Tenant;

use App\Models\Role;
use App\Models\User;
use App\Models\MailLog;
use App\Models\MailLogs;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Mail\UserCreationMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Stancl\Tenancy\Contracts\Domain;

class UserController extends Controller
{
    public function index()
    {
        try {
            $users = User::with(['roles'])
                ->paginate(10);
            
            return response()->json([
                'success' => true,
                'data' => $users
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching users: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch users'
            ], 500);
        }
    }

    public function store(Request $request)
    {

        // dd('hello');
        try {
            // DB::beginTransaction();
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users',
                'password' => 'required|string|min:8',
                'role' => 'required',
            ]);


            // dd($validated);
            // Check if trying to assign restaurant_owner role
            $role = Role::where('name', $request->role)->first();
            Log::info('Role: ' . $role);
            $role_id = $role->id;
            if ($role->name === 'restaurant_owner') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot create user with Restaurant Owner role'
                ], 403);
            }

            $plainPassword = $validated['password'];
            $validated['password'] = Hash::make($validated['password']);
            $user = User::create($validated);
            $user->save();

            // Attach role to user
            $user->roles()->attach($role_id);


            
        $tenant = tenant(); // current tenant (tenant context)

        // get tenant domain from central DB
        $domain = tenancy()->central(function () use ($tenant) {
            return $tenant->domains()->first()?->domain;
        });

        $loginUrl = $domain
            ? "https://{$domain}/login"
            : config('app.url') . '/login';
            // dd($loginUrl);
        Mail::to($user->email)->send(
            new UserCreationMail($user, $plainPassword, $loginUrl)
        );

        // logmail

            MailLog::logMail(
            sendBy: Auth::user()->email ?? 'system',
            sentTo: $user->email,
            content: view('emails.user-creation', [
                'appName' => config('app.name'),
                'user' => $user,
                'plainPassword' => $plainPassword,
                'loginUrl' => $loginUrl,
            ])->render(),  // ✅ full HTML rendered
            mailType: 'user-creation' // optional
        );

      
         $notification = Notification::create([
            'type' => 'user-creation',
            'title' => 'User Created!',
            'message' => 'A new user has been created!',
            'is_read' => false,
            'user_id' => $user->id, // Replace with the correct user variable
        ]);
// send to roles admin manager 
        $notification->users()->attach(
            User::role(['restaurant_owner', 'manager'])->pluck('id')->toArray()
        );


        // DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'User created successfully',
                'data' => $user->load('roles')
            ], 201);
        } catch (\Exception $e) {
            // DB::rollback();
            dd($e->getMessage());
            Log::error('Error creating user: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create user'
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $user = User::with(['roles'])
                ->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => $user
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching user: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        Log::info('Updating user: ' . $id);
        Log::info('Request: ' . json_encode($request->all()));
        try {
            $user = User::findOrFail($id);

            // Check if user has restaurant_owner role
            if ($user->hasRole('restaurant_owner')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Restaurant Owner user cannot be modified'
                ], 403);
            }

            $role = Role::where('name', $request->role)->first();
            Log::info('Role: ' . $role);
            $role_id = $role->id;
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $id,
                'password' => 'nullable|string|min:8',
                'role' => 'required',
            ]);
            Log::info('Validated');
            // Check if trying to assign restaurant_owner role
            if ($role->name === 'restaurant_owner') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot assign Restaurant Owner role to user'
                ], 403);
            }

            if (isset($validated['password'])) {
                $validated['password'] = Hash::make($validated['password']);
            }
            Log::info('before update:');
            
            // Assign fields manually
            $user->name = $validated['name'];
            $user->email = $validated['email'];
            if (isset($validated['password']) && $validated['password']) {
                $user->password = \Hash::make($validated['password']);
            }
            $user->save();
            Log::info('after update:');

            // Sync roles
            $user->roles()->sync([$role_id]);

            return response()->json([
                'success' => true,
                'message' => 'User updated successfully',
                'data' => $user->load('roles')
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating user: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update user: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);

            // Prevent deleting restaurant_owner user
            if ($user->hasRole('restaurant_owner')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Restaurant Owner user cannot be deleted'
                ], 403);
            }

            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'User soft deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error soft deleting user: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to soft delete user'
            ], 500);
        }
    }

    public function forceDelete($id)
    {
        try {
            $user = User::withTrashed()->findOrFail($id);

            // Prevent force deleting restaurant_owner user
            if ($user->hasRole('restaurant_owner')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Restaurant Owner user cannot be deleted'
                ], 403);
            }

            $user->forceDelete();

            return response()->json([
                'success' => true,
                'message' => 'User permanently deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error permanently deleting user: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to permanently delete user'
            ], 500);
        }
    }

    public function restore($id)
    {
        try {
            $user = User::withTrashed()->findOrFail($id);
            $user->restore();

            return response()->json([
                'success' => true,
                'message' => 'User restored successfully',
                'data' => $user->load('roles')
            ]);
        } catch (\Exception $e) {
            Log::error('Error restoring user: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to restore user'
            ], 500);
        }
    }
} 