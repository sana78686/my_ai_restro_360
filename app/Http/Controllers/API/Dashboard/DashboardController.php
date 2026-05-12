<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function getRecentTenants()
    {
        $tenants = Tenant::query()->orderBy('created_at', 'desc')->take(5)->get();
        $tenantData = [];
        foreach ($tenants as $tenant) {
            $ownerApproved = false;
            $logoUrl = null;
            try {
                tenancy()->initialize($tenant);
                $logoUrl = DB::table('media')
                    ->where('model_type', 'App\\Models\\Setting')
                    ->value('custom_properties->public_url');
                $ownerUser = User::query()->where('email', $tenant->owner_email)->first();
                $ownerApproved = $ownerUser && $ownerUser->is_verified_by_admin;
            } catch (\Throwable $e) {
                Log::error("getRecentTenants: tenant {$tenant->id}: ".$e->getMessage());
            } finally {
                tenancy()->end();
            }

            $resolvedLogo = $logoUrl ?: $tenant->logo_url;
            $tenantData[] = [
                'id' => $tenant->id,
                'name' => $tenant->name,
                'owner_name' => $tenant->owner_name,
                'owner_email' => $tenant->owner_email,
                'status' => $tenant->status,
                'logo' => $resolvedLogo,
                'logo_url' => $resolvedLogo,
                'created_at' => $tenant->created_at,
                'owner_account_approved' => $ownerApproved,
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $tenantData,
        ]);
    }
    public function getStats()
    {
        $totalTenants = \App\Models\Tenant::count();
        $activeTenants = \App\Models\Tenant::where('status', 'active')->count();
        $pendingTenants = \App\Models\Tenant::where('status', 'trial')->count();
        $suspendedTenants = \App\Models\Tenant::where('status', 'suspended')->count();
        $totalUsers = User::count();
        $totalRoles = Role::count();
        $pendingTenants = \App\Models\Tenant::where('status', 'pending')->orWhere('status', 'trial')->count();
        $activeUsersToday = User::whereDate('created_at', Carbon::today())->count();
        Log::info('Stats:', ['totalTenants' => $totalTenants, 'activeTenants' => $activeTenants, 'pendingTenants' => $pendingTenants, 'suspendedTenants' => $suspendedTenants, 'totalUsers' => $totalUsers, 'totalRoles' => $totalRoles, 'activeUsersToday' => $activeUsersToday]);
        return response()->json([
            'success' => true,
            'data' => [
                'totalTenants' => $totalTenants,
                'activeTenants' => $activeTenants,
                'pendingTenants' => $pendingTenants,
                'suspendedTenants' => $suspendedTenants,
                'totalUsers' => $totalUsers,
                'totalRoles' => $totalRoles,
                'activeUsersToday' => $activeUsersToday,
            ]
        ]);
    }
    
}
            