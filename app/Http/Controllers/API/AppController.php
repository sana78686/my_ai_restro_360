<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Helpers\AppNameHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
                    'app_name' => $appName,
                ],
            ]);
        } catch (\Throwable $e) {
            Log::warning('getAppName failed: '.$e->getMessage());

            return response()->json([
                'success' => true,
                'data' => [
                    'app_name' => config('app.name', 'Laravel'),
                ],
            ]);
        }
    }
}

