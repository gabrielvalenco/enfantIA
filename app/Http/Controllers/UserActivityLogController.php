<?php

namespace App\Http\Controllers;

use App\Services\UserActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserActivityLogController extends Controller
{
    protected $activityLogService;

    /**
     * Create a new controller instance.
     */
    public function __construct(UserActivityLogService $activityLogService)
    {
        $this->middleware('auth');
        $this->activityLogService = $activityLogService;
    }

    /**
     * Get recent activities for the authenticated user
     */
    public function getRecentActivities(Request $request)
    {
        $limit = $request->get('limit', 5);
        $activities = $this->activityLogService->getRecentActivities(Auth::id(), $limit);
        
        return response()->json([
            'success' => true,
            'data' => $activities
        ]);
    }
}
