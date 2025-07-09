<?php

namespace App\Services;

use App\Models\UserActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class UserActivityLogService
{
    /**
     * Log a user activity
     * 
     * @param string $action The type of action (e.g., 'create', 'update', 'delete')
     * @param string $description A description of the action
     * @param string|null $entityType The type of entity affected (e.g., 'task', 'category')
     * @param int|null $entityId The ID of the entity affected
     * @return UserActivityLog
     */
    public function log(string $action, string $description, ?string $entityType = null, ?int $entityId = null): UserActivityLog
    {
        $userId = Auth::id() ?? 0;
        
        $log = UserActivityLog::create([
            'user_id' => $userId,
            'action' => $action,
            'description' => $description,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
        ]);
        
        // Clear cached user activities
        $this->clearCache($userId);
        
        return $log;
    }
    
    /**
     * Get recent user activities
     * 
     * @param int $userId
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRecentActivities($userId = null, $limit = 10)
    {
        $userId = $userId ?? Auth::id();
        $cacheKey = "user_activities_{$userId}";
        
        // Try to get from cache first
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }
        
        // If not in cache, get from database and cache the result
        $activities = UserActivityLog::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
            
        // Store in cache for 5 minutes
        Cache::put($cacheKey, $activities, now()->addMinutes(5));
        
        return $activities;
    }
    
    /**
     * Clear the cache for a user's activities
     * 
     * @param int $userId
     */
    protected function clearCache($userId)
    {
        Cache::forget("user_activities_{$userId}");
    }
}
