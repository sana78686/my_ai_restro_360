<?php

namespace App\Http\Controllers\API\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class RoleController extends Controller
{
    public function index()
    {
        Log::info('User:', ['user' => auth()->user()]);
        Log::info('Guard:', ['guard' => auth()->guard()]);
        // dd('hit', auth()->user(), auth()->guard());
        Log::info('Fetching roles');
        try {
            $roles = Role::with('permissions')->get();
            foreach ($roles as $role) {
                $role->users_count = $role->users()->count();
                // or $role->users_list = $role->users()->get();
            }
            Log::info('Roles fetched successfully', ['roles' => $roles]);
            return response()->json([
                'status' => 'success',
                'data' => $roles
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch roles: ' . $e->getMessage(),
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255', 'unique:roles'],
                'description' => ['nullable', 'string', 'max:1000'],
                'permissions' => ['required', 'array'],
                'permissions.*' => ['exists:permissions,id']
            ]);

            DB::beginTransaction();

            $role = Role::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'guard_name' => 'web'
            ]);

            $role->syncPermissions($validated['permissions']);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Role created successfully',
                'data' => $role->load('permissions')
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function show($id)
    {
        try {
            $role = Role::with(['permissions', 'users'])
                ->withCount('users')
                ->findOrFail($id);

            return response()->json([
                'status' => 'success',
                'data' => $role
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Role not found'
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $role = Role::findOrFail($id);

            if ($role->name === 'restaurant_owner') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Restaurant Owner role cannot be modified'
                ], 403);
            }

            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255', Rule::unique('roles')->ignore($id)],
                'description' => ['nullable', 'string', 'max:1000'],
                'permissions' => ['required', 'array'],
                'permissions.*' => ['exists:permissions,id']
            ]);

            DB::beginTransaction();

            $role->update([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null
            ]);

            $role->syncPermissions($validated['permissions']);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Role updated successfully',
                'data' => $role->load('permissions')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function destroy($id)
    {
        try {
            $role = Role::findOrFail($id);

            if ($role->name === 'restaurant_owner') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Restaurant Owner role cannot be deleted'
                ], 403);
            }

            DB::beginTransaction();
            $role->delete();
            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Role deleted successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete role'
            ], 500);
        }
    }

    public function permissions()
    {
        Log::info('Fetching permissions');
        try {
            $permissions = Permission::all();
            Log::info('Permissions fetched successfully', ['permissions' => $permissions]);
            return response()->json([
                'status' => 'success',
                'data' => $permissions
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch permissions'
            ], 500);
        }
    }

    public function getUserRole(Request $request)
    {
        $user = $request->user();
        $role = $user->roles()->first();
        if (!$role) {
            return response()->json([
                'success' => false,
                'message' => 'User has no role'
            ]);
        }
        return response()->json([
            'success' => true,
            'data' => $role,
            'message' => 'User role fetched successfully'
        ]);
    }
} 