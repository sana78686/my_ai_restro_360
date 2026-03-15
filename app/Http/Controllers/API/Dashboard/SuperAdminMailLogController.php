<?php

namespace App\Http\Controllers\API\Dashboard;

use App\Models\MailLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SuperAdminMailLogController extends Controller
{
    public function SuperAdminMailLog(Request $request)
    {
        // dd('here');
        $query = MailLog::query();

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('send_by', 'like', "%{$search}%")
                    ->orWhere('sent_to', 'like', "%{$search}%")
                    ->orWhere('mail_type', 'like', "%{$search}%")
                    ->orWhere('id', 'like', "%{$search}%");
            });
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(10);

        // dd($logs);
        return response()->json([
            'success' => true,
            'data' => $logs
        ]);
    }
}
