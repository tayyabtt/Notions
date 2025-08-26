<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\TaskTrackerController;
use App\Http\Controllers\TaskTrackerPageController;

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
Route::get('/dashboard', function (\Illuminate\Http\Request $request) {
    $teams = auth()->user()->teams ?? collect();
    $currentTeam = $teams->first();
    
    if (!$currentTeam) {
        $tasks = collect();
        $tags = collect();
    } else {
        // Build query with filters
        $query = $currentTeam->tasks()->with(['assignee', 'creator', 'tags']);
        
        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }
        
        if ($request->filled('assignee')) {
            if ($request->assignee === 'unassigned') {
                $query->whereNull('assigned_to');
            } else {
                $query->where('assigned_to', $request->assignee);
            }
        }
        
        if ($request->filled('tag')) {
            $query->whereHas('tags', function ($q) use ($request) {
                $q->where('tags.id', $request->tag);
            });
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }
        
        $tasks = $query->orderBy('created_at', 'desc')->get();
        $tags = $currentTeam->tags()->get();
    }
    
    return view('dashboard', compact('teams', 'currentTeam', 'tasks', 'tags'));
})->middleware('auth')->name('dashboard');

// Task routes
Route::middleware('auth')->group(function () {
    Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::patch('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');
    Route::get('/tasks/{task}', [TaskController::class, 'show'])->name('tasks.show');
    Route::post('/tasks/{task}/comments', [TaskController::class, 'addComment'])->name('tasks.comments.store');
    
    // Tag routes
    Route::post('/tags', [TagController::class, 'store'])->name('tags.store');
    Route::get('/teams/{team}/tags', [TagController::class, 'index'])->name('tags.index');
    Route::patch('/tags/{tag}', [TagController::class, 'update'])->name('tags.update');
    Route::delete('/tags/{tag}', [TagController::class, 'destroy'])->name('tags.destroy');
    Route::post('/tasks/tags/attach', [TagController::class, 'attachToTask'])->name('tags.attach');
    Route::post('/tasks/tags/detach', [TagController::class, 'detachFromTask'])->name('tags.detach');
    
    // Notification routes
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/mark-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    
    // Team routes
    Route::post('/teams', [TeamController::class, 'store'])->name('teams.store');
    Route::post('/teams/{team}/update', [TeamController::class, 'update'])->name('teams.update');
    Route::delete('/teams/{team}', [TeamController::class, 'destroy'])->name('teams.destroy');
    
    // To Do List routes
    Route::get('/todo', [App\Http\Controllers\TodoController::class, 'index'])->name('todo.index');
    Route::post('/todo', [App\Http\Controllers\TodoController::class, 'store'])->name('todo.store');
    Route::patch('/todo/{todoItem}/toggle', [App\Http\Controllers\TodoController::class, 'toggle'])->name('todo.toggle');
    Route::delete('/todo/{todoItem}', [App\Http\Controllers\TodoController::class, 'destroy'])->name('todo.destroy');
    
    // Task Tracker routes
    Route::get('/task-tracker', [TaskTrackerController::class, 'index'])->name('task-tracker.index');
    Route::post('/task-tracker', [TaskTrackerController::class, 'store'])->name('task-tracker.store');
    Route::post('/task-tracker/{taskTracker}/update', [TaskTrackerController::class, 'update'])->name('task-tracker.update');
    Route::delete('/task-tracker/{taskTracker}', [TaskTrackerController::class, 'destroy'])->name('task-tracker.destroy');
    
    // Task Tracker Page routes
    Route::post('/task-tracker-page', [TaskTrackerPageController::class, 'store'])->name('task-tracker-page.store');
    Route::get('/task-tracker-page/{page}', [TaskTrackerPageController::class, 'show'])->name('task-tracker-page.show');
    Route::post('/task-tracker-page/{page}/update', [TaskTrackerPageController::class, 'update'])->name('task-tracker-page.update');
    Route::delete('/task-tracker-page/{page}', [TaskTrackerPageController::class, 'destroy'])->name('task-tracker-page.destroy');
    Route::get('/teams/{team}', function ($teamId, \Illuminate\Http\Request $request) {
        $team = auth()->user()->teams()->findOrFail($teamId);
        $teams = auth()->user()->teams;
        $tags = $team->tags()->get();
        
        // Build query with filters
        $query = $team->tasks()->with(['assignee', 'creator', 'tags']);
        
        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }
        
        if ($request->filled('assignee')) {
            if ($request->assignee === 'unassigned') {
                $query->whereNull('assigned_to');
            } else {
                $query->where('assigned_to', $request->assignee);
            }
        }
        
        if ($request->filled('tag')) {
            $query->whereHas('tags', function ($q) use ($request) {
                $q->where('tags.id', $request->tag);
            });
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }
        
        $tasks = $query->orderBy('created_at', 'desc')->get();
        
        return view('dashboard', compact('teams', 'team', 'tasks', 'tags'))->with('currentTeam', $team);
    })->name('teams.show');
});
