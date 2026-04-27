<?php

namespace App\Http\Controllers\API\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Notification;
class TenantDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        Log::info($user);
        $user->load('roles');
        $userArr = $user->toArray();
        $userArr['role_name'] = $user->role_name;
        // Return dashboard data
        return response()->json([
            'message' => 'Tenant dashboard data',
            'user' => $userArr,
        ]);
    }
    
    public function getNotifications(Request $request)
    {
        try {
            $notifications = Notification::orderBy('created_at', 'desc')->get();

            return response()->json([
                'success' => true,
                'data' => $notifications,
            ]);
        } catch (\Throwable $e) {
            Log::error('getNotifications: '.$e->getMessage());

            return response()->json([
                'success' => true,
                'data' => [],
            ]);
        }
    }

    public function markNotificationRead(Request $request, $id)
    {
        $userId = $request->user()->id;
        $notification = Notification::where('id', $id)->where('user_id', $userId)->first();
        if (!$notification) {
            return response()->json(['success' => false, 'message' => 'Notification not found'], 404);
        }
        $notification->is_read = true;
        $notification->save();
        return response()->json(['success' => true]);
    }

    public function markAllNotificationsRead(Request $request)
    {
        $userId = $request->user()->id;
        Notification::where('user_id', $userId)->where('is_read', false)->update(['is_read' => true]);
        return response()->json(['success' => true]);
    }
}
