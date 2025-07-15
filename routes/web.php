<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\GroupInvitationController;
use App\Http\Controllers\SubtaskController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserActivityLogController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Protected Routes
Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/tasks/completed', [TaskController::class, 'completed'])->name('tasks.completed');

    Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
    Route::get('/tasks/create', [TaskController::class, 'create'])->name('tasks.create');
    Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::get('/tasks/{task}/edit', [TaskController::class, 'edit'])->name('tasks.edit');
    Route::put('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');
    Route::patch('/tasks/{task}/complete', [TaskController::class, 'complete'])->name('tasks.complete');
    Route::patch('/tasks/{task}/uncomplete', [TaskController::class, 'uncomplete'])->name('tasks.uncomplete');
    Route::delete('/tasks/{task}/clear', [TaskController::class, 'clear'])->name('tasks.clear');
    Route::delete('/tasks/cleared', [TaskController::class, 'cleared'])->name('tasks.cleared');
    
    // Novas rotas para limpar tarefas concluídas
    Route::delete('/tasks/clear-selected', [TaskController::class, 'clearSelected'])->name('tasks.clear-selected');
    Route::get('/tasks/clear-all', [TaskController::class, 'clearAll'])->name('tasks.clear-all');
    
    // Rotas para ações em massa
    Route::post('/tasks/complete-multiple', [TaskController::class, 'completeMultiple'])->name('tasks.complete-multiple');
    Route::post('/tasks/delete-multiple', [TaskController::class, 'deleteMultiple'])->name('tasks.delete-multiple');
    
    // Rotas para subtarefas
    Route::get('/tasks/{task}/details', [TaskController::class, 'details']);
    Route::get('/tasks/{task}/can-complete', [TaskController::class, 'canComplete']);
    Route::get('/tasks/{task}/subtasks', [SubtaskController::class, 'index']);
    Route::post('/tasks/{task}/subtasks', [SubtaskController::class, 'store']);
    Route::post('/subtasks/{subtask}/toggle', [SubtaskController::class, 'toggleComplete']);
    Route::delete('/subtasks/{subtask}', [SubtaskController::class, 'destroy']);

    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    // Notes routes
    Route::get('/notes', [NoteController::class, 'index'])->name('notes.index');
    Route::post('/notes', [NoteController::class, 'store'])->name('notes.store');
    Route::put('/notes/{note}', [NoteController::class, 'update'])->name('notes.update');
    Route::delete('/notes/{note}', [NoteController::class, 'destroy'])->name('notes.destroy');
    Route::get('/notes/{note}/details', [NoteController::class, 'details'])->name('notes.details');
    Route::get('/notes/{note}/edit', [NoteController::class, 'edit'])->name('notes.edit');
    Route::get('/tasks/list', [NoteController::class, 'tasksList'])->name('notes.tasks-list');
    Route::get('/categories/list', [NoteController::class, 'categoriesList'])->name('notes.categories-list');

    // Report routes
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    
    // User Activity Logs routes
    Route::get('/activity-logs', [UserActivityLogController::class, 'getRecentActivities'])->name('activity-logs.recent');

    Route::middleware(['auth'])->group(function () {
        Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
        Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
        
        // Rotas de Grupos
        Route::resource('groups', GroupController::class)->except(['edit', 'update']);
        Route::post('/groups/{group}/members', [GroupController::class, 'addMember'])->name('groups.add-member');
        Route::delete('/groups/{group}/members', [GroupController::class, 'removeMember'])->name('groups.remove-member');
        Route::delete('/groups/{group}/delete', [GroupController::class, 'delete'])->name('groups.delete');
        Route::delete('/groups/{group}/leave', [GroupController::class, 'leave'])->name('groups.leave');
        
        // Rotas para configurações de grupo
        Route::get('/groups/{group}/settings', [GroupController::class, 'getSettings'])->name('groups.get-settings');
        Route::post('/groups/{group}/settings', [GroupController::class, 'saveSettings'])->name('groups.save-settings');
        
        // Group Invitations
        Route::get('/notifications', [GroupInvitationController::class, 'index'])->name('notifications.index');
        Route::post('/notifications/{invitation}/accept', [GroupInvitationController::class, 'accept'])->name('notifications.accept');
        Route::post('/notifications/{invitation}/reject', [GroupInvitationController::class, 'reject'])->name('notifications.reject');
        Route::post('/notifications/clear-all', [GroupInvitationController::class, 'clearAll'])->name('notifications.clear-all');
    });
});

Route::get('/portfolio', function () {
    return view('portfolio');
})->name('portfolio');

Route::get('/terms', function () {
    return view('terms.terms');
})->name('terms');

Route::get('/politic', function () {
    return view('terms.politic');
})->name('politic');
