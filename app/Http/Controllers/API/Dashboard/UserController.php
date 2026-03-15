<?php

namespace App\Http\Controllers\API\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function index()
    {
        try {
            $users = User::with(['roles'])
                ->paginate(10);
            Log::info('Users fetched successfully', ['users' => $users]);
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
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users',
                'password' => 'required|string|min:8',
                'role_id' => 'required|exists:roles,id',
                'status' => 'required|string|in:active,inactive',
                'created_by' => 'nullable|exists:users,id',
                'updated_by' => 'nullable|exists:users,id'
            ]);

            // Check if trying to assign admin role
            $role = Role::findOrFail($validated['role_id']);
            if ($role->name === 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot create user with admin role'
                ], 403);
            }

            $validated['password'] = Hash::make($validated['password']);
            $user = User::create($validated);

            // Attach role to user
            $user->roles()->attach($validated['role_id']);

            return response()->json([
                'success' => true,
                'message' => 'User created successfully',
                'data' => $user->load('roles')
            ], 201);
        } catch (\Exception $e) {
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
            $user = User::with(['roles', 'creator', 'updater'])
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
        try {
            $user = User::findOrFail($id);

            // Check if user has admin role
            if ($user->hasRole('admin')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Admin user cannot be modified'
                ], 403);
            }

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $id,
                'password' => 'nullable|string|min:8',
                'role_id' => 'required|exists:roles,id',
                'status' => 'required|string|in:active,inactive',
                'updated_by' => 'nullable|exists:users,id'
            ]);

            // Check if trying to assign admin role
            $role = Role::findOrFail($validated['role_id']);
            if ($role->name === 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot assign admin role to user'
                ], 403);
            }

            if (isset($validated['password'])) {
                $validated['password'] = Hash::make($validated['password']);
            }

            $user->update($validated);

            // Sync roles
            $user->roles()->sync([$validated['role_id']]);

            return response()->json([
                'success' => true,
                'message' => 'User updated successfully',
                'data' => $user->load('roles')
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating user: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update user'
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);

            // Prevent deleting admin user
            if ($user->hasRole('admin')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Admin user cannot be deleted'
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

            // Prevent force deleting admin user
            if ($user->hasRole('admin')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Admin user cannot be deleted'
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