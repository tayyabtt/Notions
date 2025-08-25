<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Notions') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .font-ui-sans-serif { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; }
        .antialiased { -webkit-font-smoothing: antialiased; -moz-osx-font-smoothing: grayscale; }
    </style>
</head>
<body class="bg-white text-gray-900 font-ui-sans-serif antialiased">
    <div id="app" class="flex h-screen overflow-hidden">
        <!-- Notion-style Left Sidebar -->
        <div id="sidebar" class="w-64 bg-gray-50 border-r border-gray-200 flex flex-col">
            <!-- Workspace Header -->
            <div class="p-3 border-b border-gray-200">
                <div class="flex items-center space-x-2 px-2 py-1 rounded hover:bg-gray-100 cursor-pointer">
                    <div class="w-6 h-6 bg-blue-500 rounded text-white text-xs flex items-center justify-center font-medium">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                    <span class="text-sm font-medium text-gray-900">{{ auth()->user()->name }}'s Workspace</span>
                </div>
            </div>

            <!-- Search -->
            <div class="p-3">
                <div class="flex items-center space-x-2 px-3 py-2 bg-white border border-gray-200 rounded-md hover:border-gray-300">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <span class="text-sm text-gray-400">Search</span>
                </div>
            </div>

            <!-- Navigation -->
            <div class="flex-1 px-3 pb-3">
                <!-- Main Navigation -->
                <div class="space-y-1 mb-4">
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-2 px-2 py-1 rounded hover:bg-gray-100 cursor-pointer">
                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        <span class="text-sm text-gray-700">Home</span>
                    </a>
                </div>

                <!-- Teamspaces Section -->
                <div class="mb-4">
                    <div class="flex items-center justify-between px-2 py-1 mb-1">
                        <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Teamspaces</span>
                        <details>
                            <summary class="w-4 h-4 text-gray-400 hover:text-gray-600 cursor-pointer list-none">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                            </summary>
                            <!-- New Team Form -->
                            <div class="mb-2 px-2 mt-2">
                                <form action="{{ route('teams.store') }}" method="POST" class="space-y-2">
                                    @csrf
                                    <input type="text" name="name" placeholder="Team name" 
                                           class="w-full px-2 py-1 text-xs border rounded focus:outline-none focus:ring-1 focus:ring-blue-500">
                                    <button type="submit" class="w-full px-2 py-1 text-xs bg-blue-500 text-white rounded hover:bg-blue-600">
                                        Create
                                    </button>
                                </form>
                            </div>
                        </details>
                    </div>
                    
                    <div class="space-y-1">
                        @foreach($teams as $team)
                            <a href="{{ route('teams.show', $team->id) }}" 
                               class="flex items-center space-x-2 px-2 py-1 rounded hover:bg-gray-100 cursor-pointer {{ (isset($currentTeam) && $currentTeam->id === $team->id) ? 'bg-gray-100' : '' }}">
                                <div class="w-4 h-4 bg-blue-500 rounded text-white text-xs flex items-center justify-center">
                                    {{ substr($team->name, 0, 1) }}
                                </div>
                                <span class="text-sm text-gray-700">{{ $team->name }}</span>
                            </a>
                            
                            @if(isset($currentTeam) && $currentTeam->id === $team->id)
                                <div class="ml-6 space-y-1">
                                    <div class="flex items-center space-x-2 px-2 py-1 rounded bg-blue-50 text-blue-700">
                                        <span class="text-sm">📋</span>
                                        <span class="text-sm font-medium">Tasks</span>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Bottom Section -->
            <div class="border-t border-gray-200 p-3">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="flex items-center space-x-2 px-2 py-1 rounded hover:bg-gray-100 cursor-pointer w-full text-left">
                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        <span class="text-sm text-gray-700">Logout</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Bar -->
            <div class="bg-white border-b border-gray-200 px-6 py-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-6 h-6 text-blue-500">📋</div>
                        <h1 class="text-lg font-semibold text-gray-900">Tasks</h1>
                    </div>
                    <div class="flex items-center space-x-2">
                        <!-- New Task Button -->
                        <details>
                            <summary class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm font-medium cursor-pointer list-none">
                                New
                            </summary>
                            
                            <!-- New Task Form -->
                            <div class="absolute top-full left-0 right-0 bg-gray-50 border-b px-6 py-4 z-10">
                @if(isset($currentTeam))
                    <form action="{{ route('tasks.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <input type="hidden" name="team_id" value="{{ $currentTeam->id }}">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                                <input type="text" name="title" required 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
                                <select name="priority" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="low">Low</option>
                                    <option value="medium" selected>Medium</option>
                                    <option value="high">High</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="todo" selected>Todo</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="done">Done</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Assignee</label>
                                <select name="assigned_to" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Unassigned</option>
                                    @foreach($currentTeam->users ?? [] as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <textarea name="description" rows="3" 
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Due Date</label>
                            <input type="date" name="due_date" 
                                   class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div class="flex space-x-2">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded text-sm">
                                Create Task
                            </button>
                        </div>
                    </form>
                @else
                    <p class="text-gray-500">Please create or select a team first.</p>
                @endif
                            </div>
                        </details>
                    </div>
                </div>
            </div>

            <!-- Task Database Table -->
            <div class="flex-1 overflow-auto bg-white">
                @if($tasks->count() > 0)
                    <!-- Table Header -->
                    <div class="sticky top-0 bg-gray-50 border-b border-gray-200">
                        <table class="w-full">
                            <thead>
                                <tr class="text-left">
                                    <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider w-8"></th>
                                    <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider min-w-80">Name</th>
                                    <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Priority</th>
                                    <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Status</th>
                                    <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider w-40">Assignee</th>
                                    <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Due Date</th>
                                    <th class="px-6 py-3 w-8">Actions</th>
                                </tr>
                            </thead>
                        </table>
                    </div>

                    <!-- Table Body -->
                    <div>
                        @foreach($tasks as $task)
                            <div class="border-l-4 border-transparent hover:border-blue-500 group">
                                <table class="w-full">
                                    <tbody>
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-3 w-8">
                                                <form action="{{ route('tasks.update', $task->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="{{ $task->status === 'done' ? 'todo' : 'done' }}">
                                                    <input type="checkbox" {{ $task->status === 'done' ? 'checked' : '' }} 
                                                           onchange="this.form.submit()"
                                                           class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" />
                                                </form>
                                            </td>
                                            <td class="px-6 py-3 min-w-80">
                                                <a href="{{ route('tasks.show', $task->id) }}" class="block">
                                                    <div class="text-sm text-gray-900 {{ $task->status === 'done' ? 'line-through text-gray-500' : '' }}">
                                                        {{ $task->title }}
                                                    </div>
                                                    @if($task->description)
                                                        <div class="text-xs text-gray-500 mt-1">{{ Str::limit($task->description, 60) }}</div>
                                                    @endif
                                                </a>
                                            </td>
                                            <td class="px-6 py-3 w-32">
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                                    @if($task->priority === 'high') bg-red-100 text-red-800 border border-red-200
                                                    @elseif($task->priority === 'medium') bg-yellow-100 text-yellow-800 border border-yellow-200
                                                    @elseif($task->priority === 'low') bg-green-100 text-green-800 border border-green-200
                                                    @else bg-gray-100 text-gray-800 border border-gray-200 @endif">
                                                    {{ ucfirst($task->priority) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-3 w-32">
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                                    @if($task->status === 'todo') bg-gray-100 text-gray-800 border border-gray-200
                                                    @elseif($task->status === 'in_progress') bg-blue-100 text-blue-800 border border-blue-200
                                                    @elseif($task->status === 'done') bg-green-100 text-green-800 border border-green-200
                                                    @else bg-gray-100 text-gray-800 border border-gray-200 @endif">
                                                    {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-3 w-40">
                                                @if($task->assignee)
                                                    <div class="flex items-center space-x-2">
                                                        <div class="w-6 h-6 bg-gray-300 rounded-full text-xs flex items-center justify-center">
                                                            {{ substr($task->assignee->name, 0, 1) }}
                                                        </div>
                                                        <span class="text-sm text-gray-700">{{ $task->assignee->name }}</span>
                                                    </div>
                                                @else
                                                    <span class="text-sm text-gray-400">Unassigned</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-3 w-32">
                                                @if($task->due_date)
                                                    <span class="text-sm text-gray-700">
                                                        {{ \Carbon\Carbon::parse($task->due_date)->format('M j, Y') }}
                                                    </span>
                                                @else
                                                    <span class="text-sm text-gray-400">No date</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-3 w-8">
                                                <div class="opacity-0 group-hover:opacity-100">
                                                    <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-400 hover:text-red-600">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                            </svg>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="flex-1 flex items-center justify-center">
                        <div class="text-center py-12 text-gray-500">
                            <div class="text-4xl mb-4">📋</div>
                            <p class="text-lg mb-2">No tasks yet</p>
                            <p class="text-sm">Click "New" to create your first task</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

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