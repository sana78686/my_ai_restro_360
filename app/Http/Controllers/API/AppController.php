<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Helpers\AppNameHelper;
use Illuminate\Http\Request;

class AppController extends Controller
{
    /**
     * Get the application name (dynamic based on tenant context)
     */
    public function getAppName()
    {
        try {
            $appName = AppNameHelper::getAppName();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'app_name' => $appName
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch app name',
                'data' => [
                    'app_name' => config('app.name', 'Laravel')
                ]
            ], 500);
        }
    }
}

