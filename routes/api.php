<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// User check endpoints
Route::get('/check-user', [UserController::class, 'checkUser']);
Route::post('/check-user', [UserController::class, 'checkUser']);

// Group API routes
Route::middleware('auth:sanctum')->group(function () {
    // Group member management
    Route::post('/groups/add-member', [\App\Http\Controllers\Api\GroupController::class, 'addMember']);
    Route::post('/groups/remove-member', [\App\Http\Controllers\Api\GroupController::class, 'removeMember']);
    Route::post('/groups/{id}/settings', [\App\Http\Controllers\Api\GroupController::class, 'saveSettings']);
    Route::post('/groups/check-invite-status', [\App\Http\Controllers\Api\GroupController::class, 'checkInviteStatus']);
});
