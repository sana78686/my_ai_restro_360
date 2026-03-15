<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;
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
        $tenants = \App\Models\Tenant::orderBy('created_at', 'desc')->take(5)->get();
        Log::info('Tenants:', ['tenants' => $tenants]);
        return response()->json([
            'success' => true,
            'data' => $tenants
        ]);
        $data = $tenants->map(function($tenant) {
            return [
                'id' => $tenant->id,
                'name' => $tenant->name,
                'owner_name' => $tenant->owner_name,
                'owner_email' => $tenant->owner_email,
                'status' => $tenant->status,
                'logo' => $tenant->logo_url,
                'created_at' => $tenant->created_at,
            ];
        });
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
            