<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>To Do List - {{ config('app.name', 'Notion') }}</title>
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
        .todo-item { border: none; padding: 6px 0; }
        .todo-item:hover { background: rgba(55, 53, 47, 0.08); }
        .todo-checkbox { margin-right: 8px; width: 16px; height: 16px; }
        .todo-text { color: #37352f; font-size: 14px; }
        .todo-done { text-decoration: line-through; opacity: 0.6; }
        .todo-time { color: #9b9a97; font-size: 12px; }
        .todo-input { border: none; outline: none; background: transparent; width: 100%; }
        .todo-input:focus { outline: none; }
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
                        <span>Search</span>
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
                @php
                    $teams = auth()->user()->teams ?? collect();
                @endphp
                <div class="mb-4">
                    <div class="flex items-center justify-between px-2 py-1 mb-1">
                        <span class="text-xs font-medium notion-gray uppercase tracking-wide">Teamspaces</span>
                    </div>
                    
                    <div class="space-y-1">
                        @foreach($teams as $team)
                        <a href="{{ route('teams.show', $team->id) }}" class="flex items-center space-x-2 px-2 py-1 rounded notion-hover cursor-pointer text-sm notion-text">
                            <span class="text-base">🔴</span>
                            <span>{{ $team->name }}</span>
                        </a>
                        @endforeach
                    </div>
                </div>

                <!-- Shared Section -->
                <div class="mb-4">
                    <div class="flex items-center space-x-2 px-2 py-1 mb-1">
                        <span class="text-xs font-medium notion-gray uppercase tracking-wide">Shared</span>
                    </div>
                    
                    <div class="space-y-1">
                        <div class="flex items-center space-x-2 px-2 py-1 rounded bg-blue-50 cursor-pointer text-sm notion-text">
                            <span class="text-base">📋</span>
                            <span>To Do List</span>
                        </div>
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
            <div class="border-b notion-border px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="flex items-center space-x-2">
                            <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                            </svg>
                            <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                            </svg>
                            <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold notion-text">To Do List</h1>
                        </div>
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

            <!-- View Tabs -->
            <div class="border-b notion-border px-6 py-3">
                <div class="flex items-center space-x-6">
                    <a href="{{ route('todo.index', ['view' => 'todo']) }}" 
                       class="flex items-center space-x-2 text-sm pb-2 {{ ($view ?? 'todo') === 'todo' ? 'font-medium notion-text border-b-2 border-blue-500' : 'notion-gray hover:notion-text cursor-pointer' }}">
                        <span class="text-base">📋</span>
                        <span>To Do</span>
                    </a>
                    <a href="{{ route('todo.index', ['view' => 'done']) }}" 
                       class="flex items-center space-x-2 text-sm pb-2 {{ ($view ?? 'todo') === 'done' ? 'font-medium notion-text border-b-2 border-blue-500' : 'notion-gray hover:notion-text cursor-pointer' }}">
                        <span class="text-base">✅</span>
                        <span>Done</span>
                    </a>
                    
                    <div class="ml-auto flex items-center space-x-2">
                        <svg class="w-4 h-4 notion-gray" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v18"/>
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
                                <form action="{{ route('todo.store') }}" method="POST" class="space-y-3">
                                    @csrf
                                    <div>
                                        <input type="text" name="title" placeholder="What needs to be done?" required
                                               class="w-full px-3 py-2 border notion-border rounded focus:outline-none focus:ring-1 focus:ring-blue-500 text-sm">
                                    </div>
                                    
                                    <div class="flex justify-end space-x-2">
                                        <button type="button" onclick="this.closest('details').open = false" 
                                                class="px-3 py-1 text-sm notion-gray hover:notion-text">
                                            Cancel
                                        </button>
                                        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded text-sm">
                                            Add Todo
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </details>
                    </div>
                </div>
            </div>

            <!-- Todo List Content -->
            <div class="flex-1 overflow-auto p-6">
                <div class="max-w-4xl">
                    <!-- Todo Items -->
                    <div class="space-y-1">
                        @forelse($todoItems as $item)
                        <div class="todo-item flex items-center px-2 py-1 rounded group">
                            <form action="{{ route('todo.toggle', $item) }}" method="POST" class="flex items-center flex-1">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="view" value="{{ $view ?? 'todo' }}">
                                <input type="checkbox" 
                                       class="todo-checkbox text-blue-600 {{ $item->is_completed ? 'bg-blue-100 border-blue-300' : 'border-gray-300' }} rounded focus:ring-blue-500"
                                       {{ $item->is_completed ? 'checked' : '' }}
                                       onchange="this.form.submit()">
                                <span class="todo-text flex-1 {{ $item->is_completed ? 'todo-done' : '' }}">
                                    {{ $item->title }}
                                </span>
                                <span class="todo-time ml-auto">
                                    {{ $item->is_completed ? $item->completed_at->format('M j, Y g:i A') : $item->created_at->format('M j, Y g:i A') }}
                                </span>
                            </form>
                            
                            <!-- Delete button -->
                            <form action="{{ route('todo.destroy', $item) }}" method="POST" class="inline ml-2">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="view" value="{{ $view ?? 'todo' }}">
                                <button type="submit" 
                                        class="opacity-0 group-hover:opacity-100 text-red-400 hover:text-red-600 p-1"
                                        onclick="return confirm('Delete this todo item?')">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                        @empty
                        <div class="text-center py-8">
                            <div class="text-4xl mb-4">📋</div>
                            <h3 class="text-lg font-medium notion-text mb-2">
                                @if(($view ?? 'todo') === 'done')
                                    No completed tasks yet
                                @else
                                    No todos yet
                                @endif
                            </h3>
                            <p class="notion-gray">
                                @if(($view ?? 'todo') === 'done')
                                    Complete some tasks to see them here.
                                @else
                                    Click "New" to add your first todo item.
                                @endif
                            </p>
                        </div>
                        @endforelse

                        <!-- Quick add form -->
                        @if(($view ?? 'todo') !== 'done')
                        <div class="todo-item flex items-center px-2 py-3 rounded notion-hover">
                            <form action="{{ route('todo.store') }}" method="POST" class="flex items-center w-full" id="quickAddForm">
                                @csrf
                                <svg class="w-4 h-4 notion-gray mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                <input type="text" 
                                       name="title" 
                                       placeholder="New page" 
                                       class="todo-input notion-gray focus:notion-text"
                                       onfocus="this.placeholder='Type to add a todo...'"
                                       onblur="if(!this.value) this.placeholder='New page'"
                                       onkeypress="if(event.key==='Enter' && this.value.trim()) this.form.submit()">
                            </form>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
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

    <script>
        // Auto-hide success/error messages after 3 seconds
        setTimeout(function() {
            const messages = document.querySelectorAll('.fixed.top-4.right-4');
            messages.forEach(function(message) {
                message.style.opacity = '0';
                setTimeout(function() {
                    message.remove();
                }, 300);
            });
        }, 3000);
    </script>
</body>
</html>