<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

// Authentication routes
Route::get('/', function () {
    if (auth()->check()) {
        return redirect('/dashboard');
    }
    return view('auth.login');
})->name('home');

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', [LoginController::class, 'login'])->name('login.post');

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

Route::post('/register', [RegisterController::class, 'register'])->name('register.post');

Route::post('/logout', function () {
    auth()->logout();
    return redirect('/');
})->name('logout');

// Dashboard route
Route::get('/dashboard', function () {
    $teams = auth()->user()->teams ?? collect();
    $currentTeam = $teams->first();
    $tasks = $currentTeam ? $currentTeam->tasks()->with(['assignee', 'creator'])->get() : collect();
    
    return view('dashboard', compact('teams', 'currentTeam', 'tasks'));
})->middleware('auth')->name('dashboard');

// Task routes
Route::middleware('auth')->group(function () {
    Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::patch('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');
    Route::get('/tasks/{task}', [TaskController::class, 'show'])->name('tasks.show');
    Route::post('/tasks/{task}/comments', [TaskController::class, 'addComment'])->name('tasks.comments.store');
    
    // Team routes
    Route::post('/teams', [TeamController::class, 'store'])->name('teams.store');
    Route::get('/teams/{team}', function ($teamId) {
        $team = auth()->user()->teams()->findOrFail($teamId);
        $tasks = $team->tasks()->with(['assignee', 'creator'])->get();
        $teams = auth()->user()->teams;
        
        return view('dashboard', compact('teams', 'team', 'tasks'))->with('currentTeam', $team);
    })->name('teams.show');
});
