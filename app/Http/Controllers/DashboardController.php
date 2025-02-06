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
        
        return view('dashboard', [
            'pendingTasks' => $user->tasks()->where('status', 'pending')->count(),
            'completedTasks' => $user->tasks()->where('status', 'completed')->count(),
            'urgentTasks' => $user->tasks()->where('urgency', 'high')->where('status', 'pending')->count(),
            'categories' => Category::where('user_id', auth()->id())->count(),
            'upcomingTasks' => $user->tasks()
                ->where('status', 'pending')
                ->where('due_date', '>=', Carbon::now())
                ->orderBy('due_date', 'asc')
                ->take(5)
                ->get()
        ]);
    }
}
