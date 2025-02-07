<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Category;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        $pendingTasks = Task::where('user_id', $user->id)
            ->where('status', false)
            ->count();
            
        $completedTasks = Task::where('user_id', $user->id)
            ->where('status', true)
            ->count();
            
        $urgentTasks = Task::where('user_id', $user->id)
            ->where('status', false)
            ->where('urgency', 'high')
            ->count();
            
        $categories = Category::where('user_id', $user->id)->count();

        $upcomingTasks = Task::where('user_id', $user->id)
            ->where('status', false)
            ->whereDate('due_date', '>=', now())
            ->orderBy('due_date', 'asc')
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'pendingTasks',
            'completedTasks',
            'urgentTasks',
            'categories',
            'upcomingTasks'
        ));
    }
}
