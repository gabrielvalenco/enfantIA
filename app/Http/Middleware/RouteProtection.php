<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

class RouteProtection
{
    /**
     * List of routes that should only be accessible by authenticated users
     * 
     * @var array
     */
    protected $protectedRoutes = [
        'dashboard',
        'tasks.*',
        'categories.*',
        'groups.*',
        'notes.*',
        'profile.*',
        'reports.*',
        'notifications.*',
        'activity-logs.*'
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get the current route name
        $routeName = Route::currentRouteName();
        
        if ($routeName) {
            // Check if the current route is in the protected routes list
            foreach ($this->protectedRoutes as $protectedRoute) {
                if ($protectedRoute === $routeName || 
                    (str_ends_with($protectedRoute, '*') && 
                     str_starts_with($routeName, str_replace('*', '', $protectedRoute)))) {
                    
                    // If user is not authenticated, redirect to login
                    if (!Auth::check()) {
                        return redirect()->route('login');
                    }
                    break;
                }
            }
        }

        return $next($request);
    }
}
