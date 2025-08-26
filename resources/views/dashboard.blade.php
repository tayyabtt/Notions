<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Notion') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .font-ui-sans-serif { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; }
        .antialiased { -webkit-font-smoothing: antialiased; -moz-osx-font-smoothing: grayscale; }
        body { background: #ffffff; }
        .notion-sidebar { background: #f7f7f5; border-right: 1px solid #e5e5e3; }
        .notion-sidebar-item { color: #37352f; font-size: 14px; }
        .notion-sidebar-item:hover { background: rgba(55, 53, 47, 0.08); }
        .notion-gray { color: #787774; }
        .notion-text { color: #37352f; }
        .notion-border { border-color: #e5e5e3; }
        .notion-hover:hover { background: rgba(55, 53, 47, 0.08); }
    </style>
</head>
<body class="bg-white text-gray-900 font-ui-sans-serif antialiased">
    <div class="flex h-screen overflow-hidden">
        <!-- Left Sidebar -->
        <div class="notion-sidebar w-60 flex flex-col">
            <!-- Top Section -->
            <div class="p-3">
                <!-- Workspace Header -->
                <div class="flex items-center justify-between mb-1">
                    <div class="flex items-center space-x-2 px-2 py-1 rounded notion-hover cursor-pointer text-sm font-medium notion-text">
                        <span class="text-base">👤</span>
                        <span>{{ auth()->user()->name }}'s Workspace</span>
                        <svg class="w-3 h-3 ml-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                </div>

                <!-- Search -->
                <div class="px-2 py-1 mb-2">
                    <div class="flex items-center space-x-2 px-2 py-1 bg-white border notion-border rounded cursor-pointer hover:shadow-sm transition-shadow text-sm notion-gray">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <span>Search or ask a question in {{ auth()->user()->name }}'s Workspace...</span>
                    </div>
                </div>

                <!-- Navigation -->
                <div class="space-y-1 mb-4">
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-2 px-2 py-1 rounded notion-hover cursor-pointer text-sm notion-text">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        <span>Home</span>
                    </a>
                    <a href="{{ route('notifications.index') }}" class="flex items-center space-x-2 px-2 py-1 rounded notion-hover cursor-pointer text-sm notion-text">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a6 6 0 00-12 0v7m12 0v7a2 2 0 01-2 2H6a2 2 0 01-2-2v-7m12 0H8"/>
                        </svg>
                        <span>Inbox</span>
                        @if(auth()->user()->unreadNotifications()->count() > 0)
                            <span class="ml-auto bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                                {{ auth()->user()->unreadNotifications()->count() }}
                            </span>
                        @endif
                    </a>
                </div>

                <!-- Private Section -->
                <div class="mb-4">
                    <div class="flex items-center space-x-2 px-2 py-1 mb-1">
                        <span class="text-xs font-medium notion-gray uppercase tracking-wide">Private</span>
                    </div>
                    
                    <div class="space-y-1">
                        <div class="flex items-center space-x-2 px-2 py-1 rounded notion-hover cursor-pointer text-sm notion-text">
                            <span class="text-base">📄</span>
                            <span>hello</span>
                        </div>
                    </div>
                </div>

                <!-- Teamspaces Section -->
                <div class="mb-4">
                    <div class="flex items-center justify-between px-2 py-1 mb-1">
                        <span class="text-xs font-medium notion-gray uppercase tracking-wide">Teamspaces</span>
                        <details>
                            <summary class="w-4 h-4 notion-gray hover:text-gray-600 cursor-pointer list-none">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                            </summary>
                            <div class="mt-2 px-2">
                                <form action="{{ route('teams.store') }}" method="POST" class="space-y-2">
                                    @csrf
                                    <input type="text" name="name" placeholder="Team name" required
                                           class="w-full px-2 py-1 text-sm border notion-border rounded focus:outline-none focus:ring-1 focus:ring-blue-500 bg-white">
                                    <button type="submit" class="w-full px-2 py-1 text-sm bg-blue-500 text-white rounded hover:bg-blue-600">
                                        Create
                                    </button>
                                </form>
                            </div>
                        </details>
                    </div>
                    
                    <div class="space-y-1">
                        @foreach($teams as $team)
                            <div class="flex items-center justify-between px-2 py-1 rounded notion-hover group">
                                <a href="{{ route('teams.show', $team->id) }}" 
                                   class="flex items-center space-x-2 flex-1 text-sm notion-text {{ (isset($currentTeam) && $currentTeam->id === $team->id) ? 'bg-blue-50' : '' }}">
                                    <span class="text-base">🔴</span>
                                    <span>{{ $team->name }}</span>
                                </a>
                                <form action="{{ route('teams.destroy', $team->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="opacity-0 group-hover:opacity-100 text-red-400 hover:text-red-600 p-1"
                                            onclick="return confirm('Delete team {{ $team->name }}? This will also delete all tasks in this team.')">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        @endforeach
                        
                        <details>
                            <summary class="flex items-center space-x-2 px-2 py-1 rounded notion-hover cursor-pointer text-sm notion-gray list-none">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                <span>Add new</span>
                            </summary>
                        </details>
                    </div>
                </div>

                <!-- Shared Section -->
                <div class="mb-4">
                    <div class="flex items-center space-x-2 px-2 py-1 mb-1">
                        <span class="text-xs font-medium notion-gray uppercase tracking-wide">Shared</span>
                    </div>
                    
                    <div class="space-y-1">
                        <a href="{{ route('todo.index') }}" class="flex items-center space-x-2 px-2 py-1 rounded notion-hover cursor-pointer text-sm notion-text">
                            <span class="text-base">📋</span>
                            <span>To Do List</span>
                        </a>
                        <a href="{{ route('task-tracker.index') }}" class="flex items-center space-x-2 px-2 py-1 rounded notion-hover cursor-pointer text-sm notion-text">
                            <span class="text-base">✅</span>
                            <span>Tasks Trackers</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Bottom Section -->
            <div class="mt-auto border-t notion-border p-3">
                <div class="flex items-center space-x-2 px-2 py-1 rounded notion-hover cursor-pointer text-sm notion-text">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.196-2.121L17 20zM9 12h6m-6 4h6m0-8h3.586a1 1 0 01.707.293L21 10"/>
                    </svg>
                    <span>Invite members</span>
                </div>
                
                <form action="{{ route('logout') }}" method="POST" class="mt-2">
                    @csrf
                    <button type="submit" class="flex items-center space-x-2 px-2 py-1 rounded notion-hover cursor-pointer w-full text-left text-sm notion-text">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col overflow-hidden bg-white">
            <!-- Top Bar -->
            <div class="border-b notion-border px-6 py-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        @if(isset($currentTeam))
                            <span class="text-2xl">🔴</span>
                            <div>
                                <h1 class="text-2xl font-bold notion-text">{{ $currentTeam->name }}</h1>
                                <p class="text-sm notion-gray">Easily manage issues and feedback to ensure timely resolutions.</p>
                            </div>
                        @else
                            <span class="text-2xl">📋</span>
                            <div>
                                <h1 class="text-2xl font-bold notion-text">Tasks</h1>
                                <p class="text-sm notion-gray">Manage your tasks and projects efficiently.</p>
                            </div>
                        @endif
                    </div>
                    
                    <div class="flex items-center space-x-2">
                        <button class="px-3 py-1 text-sm notion-text border notion-border rounded hover:shadow-sm transition-shadow">
                            Share
                        </button>
                        <svg class="w-5 h-5 notion-gray cursor-pointer" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                        </svg>
                        <svg class="w-5 h-5 notion-gray cursor-pointer" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                        </svg>
                    </div>
                </div>
            </div>

            @if(isset($currentTeam))
            <!-- View Tabs -->
            <div class="border-b notion-border px-6 py-3">
                <div class="flex items-center space-x-6">
                    <div class="flex items-center space-x-2 text-sm font-medium notion-text border-b-2 border-blue-500 pb-2">
                        <span class="text-base">📋</span>
                        <span>Kanban</span>
                    </div>
                    <div class="flex items-center space-x-2 text-sm notion-gray hover:notion-text cursor-pointer pb-2">
                        <span class="text-base">⭐</span>
                        <span>All Issues</span>
                    </div>
                    <div class="flex items-center space-x-2 text-sm notion-gray hover:notion-text cursor-pointer pb-2">
                        <span class="text-base">👤</span>
                        <span>My Issues</span>
                    </div>
                    
                    <div class="ml-auto flex items-center space-x-2">
                        <svg class="w-4 h-4 notion-gray" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                        </svg>
                        <svg class="w-4 h-4 notion-gray" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                        </svg>
                        <svg class="w-4 h-4 notion-gray" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <svg class="w-4 h-4 notion-gray" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"/>
                        </svg>
                        
                        <details>
                            <summary class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm cursor-pointer list-none">
                                New
                                <svg class="w-3 h-3 inline ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </summary>
                            
                            <div class="absolute right-6 mt-2 bg-white border notion-border rounded-lg shadow-lg p-4 z-20 w-80">
                                <form action="{{ route('tasks.store') }}" method="POST" class="space-y-3">
                                    @csrf
                                    <input type="hidden" name="team_id" value="{{ $currentTeam->id }}">
                                    
                                    <div>
                                        <input type="text" name="title" placeholder="Issue title" required
                                               class="w-full px-3 py-2 border notion-border rounded focus:outline-none focus:ring-1 focus:ring-blue-500 text-sm">
                                    </div>
                                    
                                    <div class="grid grid-cols-2 gap-3">
                                        <select name="priority" class="px-3 py-2 border notion-border rounded focus:outline-none focus:ring-1 focus:ring-blue-500 text-sm">
                                            <option value="low">Low Priority</option>
                                            <option value="medium" selected>Medium Priority</option>
                                            <option value="high">High Priority</option>
                                        </select>
                                        
                                        <select name="status" class="px-3 py-2 border notion-border rounded focus:outline-none focus:ring-1 focus:ring-blue-500 text-sm">
                                            <option value="todo" selected>Backlog</option>
                                            <option value="in_progress">In Progress</option>
                                            <option value="done">Done</option>
                                        </select>
                                    </div>
                                    
                                    <textarea name="description" rows="3" placeholder="Add a description..."
                                              class="w-full px-3 py-2 border notion-border rounded focus:outline-none focus:ring-1 focus:ring-blue-500 text-sm resize-none"></textarea>
                                    
                                    <div class="flex justify-end">
                                        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded text-sm">
                                            Create Issue
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </details>
                    </div>
                </div>
            </div>

            <!-- Kanban Board -->
            <div class="flex-1 overflow-auto p-6">
                <div class="flex space-x-6 h-full">
                    <!-- Backlog Column -->
                    <div class="w-80 flex-shrink-0">
                        <div class="flex items-center space-x-2 mb-4">
                            <span class="w-3 h-3 bg-gray-400 rounded-full"></span>
                            <h3 class="font-medium notion-text">Backlog</h3>
                            <span class="text-sm notion-gray">{{ $tasks->where('status', 'todo')->count() }}</span>
                        </div>
                        
                        <div class="space-y-3">
                            @foreach($tasks->where('status', 'todo') as $task)
                            <div class="bg-white border notion-border rounded-lg p-4 hover:shadow-sm transition-shadow cursor-pointer group">
                                <div class="flex items-start justify-between mb-2">
                                    <div class="flex items-center space-x-2">
                                        <span class="text-base">🔴</span>
                                        <span class="text-sm font-medium notion-text">{{ $task->title }}</span>
                                    </div>
                                    <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" class="inline opacity-0 group-hover:opacity-100">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-gray-400 hover:text-red-600" onclick="return confirm('Delete this task?')">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                                
                                @if($task->description)
                                <p class="text-sm notion-gray mb-3">{{ Str::limit($task->description, 80) }}</p>
                                @endif
                                
                                <div class="flex items-center justify-between">
                                    <div class="flex space-x-2">
                                        @foreach($task->tags as $tag)
                                        <span class="inline-flex items-center px-2 py-1 rounded text-xs"
                                              style="background-color: {{ $tag->color }}1A; color: {{ $tag->color }};">
                                            {{ $tag->name }}
                                        </span>
                                        @endforeach
                                    </div>
                                    
                                    @if($task->assignee)
                                    <div class="w-6 h-6 bg-gray-300 rounded-full text-xs flex items-center justify-center">
                                        {{ substr($task->assignee->name, 0, 1) }}
                                    </div>
                                    @endif
                                </div>
                                
                                <a href="{{ route('tasks.show', $task->id) }}" class="absolute inset-0"></a>
                            </div>
                            @endforeach
                            
                            <button class="w-full text-left px-4 py-3 text-sm notion-gray hover:bg-gray-50 rounded-lg border-2 border-dashed notion-border">
                                + New issue
                            </button>
                        </div>
                    </div>

                    <!-- Open Column -->
                    <div class="w-80 flex-shrink-0">
                        <div class="flex items-center space-x-2 mb-4">
                            <span class="w-3 h-3 bg-gray-400 rounded-full"></span>
                            <h3 class="font-medium notion-text">Open</h3>
                            <span class="text-sm notion-gray">0</span>
                        </div>
                        
                        <div class="space-y-3">
                            <button class="w-full text-left px-4 py-3 text-sm notion-gray hover:bg-gray-50 rounded-lg border-2 border-dashed notion-border">
                                + New issue
                            </button>
                        </div>
                    </div>

                    <!-- In Progress Column -->
                    <div class="w-80 flex-shrink-0">
                        <div class="flex items-center space-x-2 mb-4">
                            <span class="w-3 h-3 bg-blue-500 rounded-full"></span>
                            <h3 class="font-medium notion-text">In progress</h3>
                            <span class="text-sm notion-gray">{{ $tasks->where('status', 'in_progress')->count() }}</span>
                        </div>
                        
                        <div class="space-y-3">
                            @foreach($tasks->where('status', 'in_progress') as $task)
                            <div class="bg-white border notion-border rounded-lg p-4 hover:shadow-sm transition-shadow cursor-pointer group">
                                <div class="flex items-start justify-between mb-2">
                                    <div class="flex items-center space-x-2">
                                        <span class="text-base">🔴</span>
                                        <span class="text-sm font-medium notion-text">{{ $task->title }}</span>
                                    </div>
                                    <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" class="inline opacity-0 group-hover:opacity-100">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-gray-400 hover:text-red-600" onclick="return confirm('Delete this task?')">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                                
                                @if($task->description)
                                <p class="text-sm notion-gray mb-3">{{ Str::limit($task->description, 80) }}</p>
                                @endif
                                
                                <div class="flex items-center justify-between">
                                    <div class="flex space-x-2">
                                        @foreach($task->tags as $tag)
                                        <span class="inline-flex items-center px-2 py-1 rounded text-xs"
                                              style="background-color: {{ $tag->color }}1A; color: {{ $tag->color }};">
                                            {{ $tag->name }}
                                        </span>
                                        @endforeach
                                    </div>
                                    
                                    @if($task->assignee)
                                    <div class="w-6 h-6 bg-gray-300 rounded-full text-xs flex items-center justify-center">
                                        {{ substr($task->assignee->name, 0, 1) }}
                                    </div>
                                    @endif
                                </div>
                                
                                <a href="{{ route('tasks.show', $task->id) }}" class="absolute inset-0"></a>
                            </div>
                            @endforeach
                            
                            <button class="w-full text-left px-4 py-3 text-sm notion-gray hover:bg-gray-50 rounded-lg border-2 border-dashed notion-border">
                                + New issue
                            </button>
                        </div>
                    </div>

                    <!-- In Review Column -->
                    <div class="w-80 flex-shrink-0">
                        <div class="flex items-center space-x-2 mb-4">
                            <span class="w-3 h-3 bg-orange-500 rounded-full"></span>
                            <h3 class="font-medium notion-text">In review</h3>
                            <span class="text-sm notion-gray">0</span>
                        </div>
                        
                        <div class="space-y-3">
                            <button class="w-full text-left px-4 py-3 text-sm notion-gray hover:bg-gray-50 rounded-lg border-2 border-dashed notion-border">
                                + New issue
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @else
            <!-- No Team Selected -->
            <div class="flex-1 flex items-center justify-center">
                <div class="text-center">
                    <div class="text-6xl mb-4">📋</div>
                    <h2 class="text-2xl font-bold notion-text mb-2">Welcome to Notion</h2>
                    <p class="notion-gray mb-6">Create or select a teamspace to get started with task management.</p>
                    <button class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                        Create your first team
                    </button>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="fixed top-4 right-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded z-50">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="fixed top-4 right-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded z-50">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</body>
</html>