<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\ProfileController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\TeamInvitationController;
use App\Http\Controllers\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [RegisterController::class, 'register']);
Route::post('/login', [LoginController::class, 'login']);

Route::post('/password/email', [PasswordResetController::class, 'sendResetLink']);
Route::post('/password/reset', [PasswordResetController::class, 'reset']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout']);
    
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);
    Route::put('/profile/password', [ProfileController::class, 'updatePassword']);
    
    Route::post('/email/verification-notification', [VerificationController::class, 'send']);
    Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])->name('verification.verify');
    
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    // Team management routes
    Route::get('/teams', [TeamController::class, 'index']);
    Route::post('/teams', [TeamController::class, 'store']);
    Route::get('/teams/{team}', [TeamController::class, 'show']);
    Route::put('/teams/{team}', [TeamController::class, 'update']);
    Route::delete('/teams/{team}', [TeamController::class, 'destroy']);
    
    // Team member management
    Route::post('/teams/{team}/invite', [TeamController::class, 'inviteMember']);
    Route::delete('/teams/{team}/members/{user}', [TeamController::class, 'removeMember']);
    Route::post('/teams/join', [TeamController::class, 'joinByInviteCode']);
    
    // Team invitations
    Route::get('/invitations', [TeamInvitationController::class, 'myInvitations']);
    Route::get('/invitations/show', [TeamInvitationController::class, 'show']);
    Route::post('/invitations/accept', [TeamInvitationController::class, 'accept']);
    Route::post('/invitations/reject', [TeamInvitationController::class, 'reject']);
    
    // Task management routes
    Route::get('/tasks', [TaskController::class, 'index']);
    Route::post('/teams/{team}/tasks', [TaskController::class, 'store']);
    Route::get('/tasks/{task}', [TaskController::class, 'show']);
    Route::put('/tasks/{task}', [TaskController::class, 'update']);
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy']);
    Route::post('/tasks/{task}/comments', [TaskController::class, 'addComment']);
    Route::get('/tasks/{task}/team-members', [TaskController::class, 'getTeamMembers']);
});