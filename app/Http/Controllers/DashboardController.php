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
        // Contagem de tarefas pendentes (não completadas)
        $pendingTasks = auth()->user()->tasks()->where('status', false)->count();

        // Contagem de tarefas completadas
        $completedTasks = auth()->user()->tasks()->where('status', true)->count();

        // Contagem de tarefas urgentes (não completadas e com urgência alta)
        $urgentTasks = auth()->user()->tasks()
            ->where('status', false)
            ->where('urgency', 'high')
            ->where('due_date', '>', Carbon::now())
            ->count();

        // Contagem de categorias
        $categories = Category::count();

        // Buscar as 3 tarefas mais próximas do prazo
        $upcomingTasks = auth()->user()->tasks()
            ->where('status', false)
            ->where('due_date', '>=', Carbon::now())
            ->orderBy('due_date', 'asc')
            ->take(3)
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
