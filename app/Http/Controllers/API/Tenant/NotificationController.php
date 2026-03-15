<?php

namespace App\Http\Controllers\API\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    // ✅ Get all notifications for the logged-in user
    public function index()
    {
        $user = Auth::user();

        $notifications = $user->notifications()
            ->withPivot('is_read')
            ->latest()
            ->get();

        return response()->json($notifications);
    }

    // ✅ Mark a notification as read
    public function markAsRead($id)
    {
        // dd('hello');
        $user = Auth::user();

        // for pivot-based notifications
        $notification = $user->notifications()->find($id);
        // dd( $notification->toArray() );
        $notification->is_read = 1;
        $notification->save();
        // dd( $notification->toArray() );
        if ($notification->is_read) {
            // dd('already read');
            $update=$user->notifications()->updateExistingPivot($id, ['is_read' => true]);
            // dd( $update );
            return response()->json(['status' => 'success']);
        }

        // fallback for legacy notifications
        $legacy = \App\Models\Notification::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if ($legacy) {
            $legacy->update(['is_read' => true]);
        }

        return response()->json(['status' => 'success']);
    }
}
