<?php

namespace App\Http\Controllers\Api\Tenant;

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
        $userId = $request->user()->id;
        $notifications = Notification::orderBy('created_at', 'desc')
            // ->where('user_id', $userId)
            ->get();
        Log::info('notifications: ' , [$notifications]);
        // dd($notifications);
        return response()->json([
            'success' => true,
            'data' => $notifications
        ]);
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
