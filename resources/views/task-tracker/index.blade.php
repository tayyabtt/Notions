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
        
        .status-not_started { color: #6B7280; }
        .status-in_progress { color: #3B82F6; }
        .status-complete { color: #10B981; }
        
        .priority-low { background-color: rgba(16, 185, 129, 0.1); color: #10B981; }
        .priority-medium { background-color: rgba(245, 158, 11, 0.1); color: #F59E0B; }
        .priority-high { background-color: rgba(239, 68, 68, 0.1); color: #EF4444; }
        
        .task-type-polish { background-color: rgba(236, 72, 153, 0.1); color: #EC4899; }
        .task-type-feature_request { background-color: rgba(59, 130, 246, 0.1); color: #3B82F6; }
        .task-type-bug { background-color: rgba(239, 68, 68, 0.1); color: #EF4444; }
        .task-type-enhancement { background-color: rgba(139, 92, 246, 0.1); color: #8B5CF6; }
        .task-type-documentation { background-color: rgba(16, 185, 129, 0.1); color: #10B981; }
        
        .effort-small { background-color: rgba(16, 185, 129, 0.1); color: #10B981; }
        .effort-medium { background-color: rgba(245, 158, 11, 0.1); color: #F59E0B; }
        .effort-large { background-color: rgba(239, 68, 68, 0.1); color: #EF4444; }

        .table-header {
            background: #f7f7f5;
            border-bottom: 1px solid #e5e5e3;
            font-size: 12px;
            font-weight: 500;
            color: #787774;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .table-row:hover {
            background: rgba(55, 53, 47, 0.03);
        }
        
        .table-cell {
            padding: 8px 12px;
            border-bottom: 1px solid #e5e5e3;
            font-size: 14px;
            color: #37352f;
        }

        .new-task-button {
            background: #3B82F6;
            color: white;
            border: none;
            border-radius: 6px;
            padding: 6px 12px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 4px;
        }
        
        .new-task-button:hover {
            background: #2563EB;
        }
        
        .filter-button {
            background: #ffffff;
            border: 1px solid #e5e5e3;
            border-radius: 6px;
            padding: 6px 8px;
            color: #787774;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
        }
        
        .filter-button:hover {
            background: rgba(55, 53, 47, 0.08);
        }

        .view-tab {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            font-size: 14px;
            color: #787774;
            cursor: pointer;
            border-bottom: 2px solid transparent;
        }
        
        .view-tab.active {
            color: #37352f;
            border-bottom-color: #3B82F6;
            font-weight: 500;
        }
        
        .view-tab:hover:not(.active) {
            color: #37352f;
        }

        input, select, textarea {
            font-family: inherit;
        }
        
        .editable-field {
            cursor: pointer;
            padding: 4px 8px;
            border-radius: 4px;
            min-height: 20px;
        }
        
        .editable-field:hover {
            background: rgba(55, 53, 47, 0.08);
        }
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
                                <a href="{{ route('task-tracker.index', ['team' => $team->id]) }}" 
                                   class="flex items-center space-x-2 flex-1 text-sm notion-text {{ (isset($currentTeam) && $currentTeam->id === $team->id) ? 'bg-blue-50' : '' }}">
                                    <span class="text-base">✅</span>
                                    <span>{{ $team->name }}</span>
                                </a>
                                <form action="{{ route('teams.destroy', $team->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="opacity-0 group-hover:opacity-100 text-red-400 hover:text-red-600 p-1"
                                            onclick="return confirm('Delete team {{ $team->name }}? This will also delete all task trackers in this team.')">
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
                        <div class="flex items-center justify-between px-2 py-1 rounded notion-hover group">
                            <a href="{{ route('task-tracker.index') }}" class="flex items-center space-x-2 flex-1 text-sm notion-text {{ !isset($currentTeam) ? 'bg-blue-50' : '' }}">
                                <span class="text-base">✅</span>
                                <span>Tasks Trackers</span>
                            </a>
                            <details>
                                <summary class="opacity-0 group-hover:opacity-100 text-gray-400 hover:text-gray-600 p-1 cursor-pointer list-none">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                    </svg>
                                </summary>
                                <div class="absolute left-4 mt-2 bg-white border notion-border rounded-lg shadow-lg p-4 z-20 w-96">
                                    <form action="{{ route('task-tracker-page.store') }}" method="POST" class="space-y-4">
                                        @csrf
                                        
                                        <!-- Page name -->
                                        <div>
                                            <label class="block text-sm font-medium notion-text mb-2">Page name</label>
                                            <input type="text" name="name" placeholder="Enter page name" required
                                                   class="w-full px-3 py-2 border notion-border rounded focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                                        </div>
                                        
                                        <!-- Icon -->
                                        <div>
                                            <label class="block text-sm font-medium notion-text mb-2">Icon</label>
                                            <input type="text" name="icon" value="✅" placeholder="Enter icon"
                                                   class="w-full px-3 py-2 border notion-border rounded focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                                        </div>
                                        
                                        <!-- Description -->
                                        <div>
                                            <label class="block text-sm font-medium notion-text mb-2">Description</label>
                                            <textarea name="description" rows="2" placeholder="Add a description..."
                                                      class="w-full px-3 py-2 border notion-border rounded focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm resize-none"></textarea>
                                        </div>
                                        
                                        <div class="flex justify-end">
                                            <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 text-sm">
                                                Create Page
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </details>
                        </div>
                        
                        <!-- Task Tracker Pages -->
                        @foreach($taskTrackerPages as $taskPage)
                            <div class="flex items-center justify-between px-2 py-1 rounded notion-hover group">
                                <a href="{{ route('task-tracker-page.show', $taskPage) }}" 
                                   class="flex items-center space-x-2 flex-1 text-sm notion-text">
                                    <span class="text-base">{{ $taskPage->icon }}</span>
                                    <span>{{ $taskPage->name }}</span>
                                </a>
                                <form action="{{ route('task-tracker-page.destroy', $taskPage->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="opacity-0 group-hover:opacity-100 text-red-400 hover:text-red-600 p-1"
                                            onclick="return confirm('Delete page {{ $taskPage->name }}? This will also delete all tasks in this page.')">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        @endforeach
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
                            <span class="text-2xl">✅</span>
                            <div>
                                <details class="relative inline-block">
                                    <summary class="text-2xl font-bold notion-text cursor-pointer list-none hover:bg-gray-50 px-1 py-1 rounded">
                                        {{ $currentTeam->name }}
                                    </summary>
                                    <div class="absolute z-10 mt-1 bg-white border notion-border rounded-lg shadow-lg p-3 min-w-80">
                                        <form action="{{ route('teams.update', $currentTeam) }}" method="POST">
                                            @csrf
                                                                                        <input type="text" name="name" value="{{ $currentTeam->name }}" 
                                                   class="w-full px-2 py-1 border notion-border rounded focus:outline-none focus:ring-1 focus:ring-blue-500 text-lg font-bold mb-2">
                                            <div class="flex justify-end space-x-2">
                                                <button type="button" onclick="this.closest('details').open = false" 
                                                        class="px-2 py-1 text-xs bg-gray-200 text-gray-700 rounded hover:bg-gray-300">Cancel</button>
                                                <button type="submit" class="px-2 py-1 text-xs bg-blue-500 text-white rounded hover:bg-blue-600">Save</button>
                                            </div>
                                        </form>
                                    </div>
                                </details>
                                <details class="relative inline-block">
                                    <summary class="text-sm notion-gray cursor-pointer list-none hover:bg-gray-50 px-1 py-1 rounded">
                                        Stay organized with tasks, your wayyyyyyyy.
                                    </summary>
                                    <div class="absolute z-10 mt-1 bg-white border notion-border rounded-lg shadow-lg p-3 min-w-80">
                                        <form action="{{ route('teams.update', $currentTeam) }}" method="POST">
                                            @csrf
                                                                                        <textarea name="description" rows="2" placeholder="Team description"
                                                      class="w-full px-2 py-1 border notion-border rounded focus:outline-none focus:ring-1 focus:ring-blue-500 text-sm mb-2 resize-none">Stay organized with tasks, your wayyyyyyyy.</textarea>
                                            <div class="flex justify-end space-x-2">
                                                <button type="button" onclick="this.closest('details').open = false" 
                                                        class="px-2 py-1 text-xs bg-gray-200 text-gray-700 rounded hover:bg-gray-300">Cancel</button>
                                                <button type="submit" class="px-2 py-1 text-xs bg-blue-500 text-white rounded hover:bg-blue-600">Save</button>
                                            </div>
                                        </form>
                                    </div>
                                </details>
                            </div>
                        @else
                            <span class="text-2xl">✅</span>
                            <div>
                                <h1 class="text-2xl font-bold notion-text">Tasks Trackers</h1>
                                <p class="text-sm notion-gray">Stay organized with tasks, your wayyyyyyyy.</p>
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

            <!-- View Tabs -->
            <div class="border-b notion-border px-6 py-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-6">
                        <div class="view-tab active">
                            <span class="text-base">⭐</span>
                            <span>All Tasks</span>
                        </div>
                        <div class="view-tab">
                            <span class="text-base">👤</span>
                            <span>My Tasks</span>
                        </div>
                        <div class="view-tab">
                            <span class="text-base">📊</span>
                            <span>By Status</span>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-2">
                        <button class="filter-button">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                            </svg>
                        </button>
                        
                        <button class="filter-button">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                            </svg>
                        </button>
                        
                        <button class="filter-button">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </button>
                        
                        <button class="filter-button">
                            <span class="text-base">⚙️</span>
                        </button>
                        
                        <details>
                            <summary class="new-task-button list-none">
                                New
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </summary>
                            
                            <div class="absolute right-6 mt-2 bg-white border notion-border rounded-lg shadow-lg p-4 z-20 w-96">
                                <form action="{{ route('task-tracker.store') }}" method="POST" class="space-y-4">
                                    @csrf
                                    @if(isset($currentTeam))
                                        <input type="hidden" name="team_id" value="{{ $currentTeam->id }}">
                                    @endif
                                    
                                    <!-- Task name -->
                                    <div>
                                        <label class="block text-sm font-medium notion-text mb-2">Task name</label>
                                        <input type="text" name="name" placeholder="Enter task name" required
                                               class="w-full px-3 py-2 border notion-border rounded focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                                    </div>
                                    
                                    <!-- Status -->
                                    <div>
                                        <label class="block text-sm font-medium notion-text mb-2">Status</label>
                                        <div class="relative">
                                            <select name="status" class="w-full px-3 py-2 border notion-border rounded focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm appearance-none bg-white">
                                                <option value="not_started" selected>🔘 Not started</option>
                                                <option value="in_progress">🔵 In progress</option>
                                                <option value="complete">🟢 Complete</option>
                                            </select>
                                            <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                                                <svg class="w-4 h-4 fill-current text-gray-400" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Assignee -->
                                    <div>
                                        <label class="block text-sm font-medium notion-text mb-2">Assignee</label>
                                        <input type="text" name="assignee" placeholder="Enter assignee name"
                                               class="w-full px-3 py-2 border notion-border rounded focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                                    </div>
                                    
                                    <!-- Due date -->
                                    <div>
                                        <label class="block text-sm font-medium notion-text mb-2">Due date</label>
                                        <input type="date" name="due_date"
                                               class="w-full px-3 py-2 border notion-border rounded focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm cursor-pointer">
                                    </div>
                                    
                                    <!-- Priority -->
                                    <div>
                                        <label class="block text-sm font-medium notion-text mb-2">Priority</label>
                                        <select name="priority" class="w-full px-3 py-2 border notion-border rounded focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                                            <option value="low">Low</option>
                                            <option value="medium" selected>Medium</option>
                                            <option value="high">High</option>
                                        </select>
                                    </div>
                                    
                                    <!-- Task type -->
                                    <div>
                                        <label class="block text-sm font-medium notion-text mb-2">Task type</label>
                                        <select name="task_type" class="w-full px-3 py-2 border notion-border rounded focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                                            <option value="polish">✨ Polish</option>
                                            <option value="feature_request" selected>💡 Feature request</option>
                                            <option value="bug">🐛 Bug</option>
                                            <option value="enhancement">🚀 Enhancement</option>
                                            <option value="documentation">📝 Documentation</option>
                                        </select>
                                    </div>
                                    
                                    <!-- Description -->
                                    <div>
                                        <label class="block text-sm font-medium notion-text mb-2">Description</label>
                                        <textarea name="description" rows="3" placeholder="Add a description..."
                                                  class="w-full px-3 py-2 border notion-border rounded focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm resize-none"></textarea>
                                    </div>
                                    
                                    <!-- Effort level -->
                                    <div>
                                        <label class="block text-sm font-medium notion-text mb-2">Effort level</label>
                                        <select name="effort_level" class="w-full px-3 py-2 border notion-border rounded focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                                            <option value="small">Small</option>
                                            <option value="medium" selected>Medium</option>
                                            <option value="large">Large</option>
                                        </select>
                                    </div>
                                    
                                    <div class="flex justify-end">
                                        <button type="submit" class="new-task-button">
                                            Create Task
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </details>
                    </div>
                </div>
            </div>

            <!-- Task Table -->
            <div class="flex-1 overflow-auto">
                <table class="w-full">
                    <thead class="sticky top-0 table-header">
                        <tr>
                            <th class="text-left py-3 px-4 font-medium">Task name</th>
                            <th class="text-left py-3 px-4 font-medium">Status</th>
                            <th class="text-left py-3 px-4 font-medium">Assignee</th>
                            <th class="text-left py-3 px-4 font-medium">Due date</th>
                            <th class="text-left py-3 px-4 font-medium">Priority</th>
                            <th class="text-left py-3 px-4 font-medium">Task type</th>
                            <th class="text-left py-3 px-4 font-medium">Description</th>
                            <th class="text-left py-3 px-4 font-medium">Effort level</th>
                            <th class="text-left py-3 px-4 font-medium">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($taskTrackers as $task)
                            <tr class="table-row">
                                <!-- Task name -->
                                <td class="table-cell font-medium">
                                    <details class="relative">
                                        <summary class="editable-field text-left w-full cursor-pointer list-none">
                                            {{ $task->name }}
                                        </summary>
                                        <div class="absolute z-10 mt-1 bg-white border notion-border rounded-lg shadow-lg p-3 min-w-64">
                                            <form action="{{ route('task-tracker.update', $task) }}" method="POST">
                                                @csrf
                                                                                                <input type="text" name="name" value="{{ $task->name }}" 
                                                       class="w-full px-2 py-1 border notion-border rounded focus:outline-none focus:ring-1 focus:ring-blue-500 text-sm mb-2">
                                                <div class="flex justify-end space-x-2">
                                                    <button type="button" onclick="this.closest('details').open = false" 
                                                            class="px-2 py-1 text-xs bg-gray-200 text-gray-700 rounded hover:bg-gray-300">Cancel</button>
                                                    <button type="submit" class="px-2 py-1 text-xs bg-blue-500 text-white rounded hover:bg-blue-600">Save</button>
                                                </div>
                                            </form>
                                        </div>
                                    </details>
                                </td>
                                <!-- Status -->
                                <td class="table-cell">
                                    <details class="relative">
                                        <summary class="editable-field text-left w-full cursor-pointer list-none">
                                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium status-{{ $task->status }}">
                                                @if($task->status === 'not_started')
                                                    <span class="w-2 h-2 bg-gray-500 rounded-full mr-2"></span>
                                                    Not started
                                                @elseif($task->status === 'in_progress')
                                                    <span class="w-2 h-2 bg-blue-500 rounded-full mr-2"></span>
                                                    In progress
                                                @else
                                                    <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                                                    Complete
                                                @endif
                                            </span>
                                        </summary>
                                        <div class="absolute z-10 mt-1 bg-white border notion-border rounded-lg shadow-lg p-3 min-w-48">
                                            <form action="{{ route('task-tracker.update', $task) }}" method="POST">
                                                @csrf
                                                                                                <select name="status" class="w-full px-2 py-1 border notion-border rounded focus:outline-none focus:ring-1 focus:ring-blue-500 text-sm mb-2">
                                                    <option value="not_started" {{ $task->status === 'not_started' ? 'selected' : '' }}>🔘 Not started</option>
                                                    <option value="in_progress" {{ $task->status === 'in_progress' ? 'selected' : '' }}>🔵 In progress</option>
                                                    <option value="complete" {{ $task->status === 'complete' ? 'selected' : '' }}>🟢 Complete</option>
                                                </select>
                                                <div class="flex justify-end space-x-2">
                                                    <button type="button" onclick="this.closest('details').open = false" 
                                                            class="px-2 py-1 text-xs bg-gray-200 text-gray-700 rounded hover:bg-gray-300">Cancel</button>
                                                    <button type="submit" class="px-2 py-1 text-xs bg-blue-500 text-white rounded hover:bg-blue-600">Save</button>
                                                </div>
                                            </form>
                                        </div>
                                    </details>
                                </td>
                                <!-- Assignee -->
                                <td class="table-cell">
                                    <details class="relative">
                                        <summary class="editable-field text-left w-full cursor-pointer list-none">
                                            {{ $task->assignee ?: 'Unassigned' }}
                                        </summary>
                                        <div class="absolute z-10 mt-1 bg-white border notion-border rounded-lg shadow-lg p-3 min-w-64">
                                            <form action="{{ route('task-tracker.update', $task) }}" method="POST">
                                                @csrf
                                                                                                <input type="text" name="assignee" value="{{ $task->assignee }}" placeholder="Enter assignee name"
                                                       class="w-full px-2 py-1 border notion-border rounded focus:outline-none focus:ring-1 focus:ring-blue-500 text-sm mb-2">
                                                <div class="flex justify-end space-x-2">
                                                    <button type="button" onclick="this.closest('details').open = false" 
                                                            class="px-2 py-1 text-xs bg-gray-200 text-gray-700 rounded hover:bg-gray-300">Cancel</button>
                                                    <button type="submit" class="px-2 py-1 text-xs bg-blue-500 text-white rounded hover:bg-blue-600">Save</button>
                                                </div>
                                            </form>
                                        </div>
                                    </details>
                                </td>
                                <!-- Due date -->
                                <td class="table-cell">
                                    <details class="relative">
                                        <summary class="editable-field text-left w-full cursor-pointer list-none">
                                            {{ $task->due_date ? $task->due_date->format('m/d/Y') : 'No date' }}
                                        </summary>
                                        <div class="absolute z-10 mt-1 bg-white border notion-border rounded-lg shadow-lg p-3 min-w-48">
                                            <form action="{{ route('task-tracker.update', $task) }}" method="POST">
                                                @csrf
                                                                                                <input type="date" name="due_date" value="{{ $task->due_date ? $task->due_date->format('Y-m-d') : '' }}"
                                                       class="w-full px-2 py-1 border notion-border rounded focus:outline-none focus:ring-1 focus:ring-blue-500 text-sm mb-2">
                                                <div class="flex justify-end space-x-2">
                                                    <button type="button" onclick="this.closest('details').open = false" 
                                                            class="px-2 py-1 text-xs bg-gray-200 text-gray-700 rounded hover:bg-gray-300">Cancel</button>
                                                    <button type="submit" class="px-2 py-1 text-xs bg-blue-500 text-white rounded hover:bg-blue-600">Save</button>
                                                </div>
                                            </form>
                                        </div>
                                    </details>
                                </td>
                                <!-- Priority -->
                                <td class="table-cell">
                                    <details class="relative">
                                        <summary class="editable-field text-left w-full cursor-pointer list-none">
                                            <span class="px-2 py-1 rounded text-xs font-medium priority-{{ $task->priority }}">
                                                {{ ucfirst($task->priority) }}
                                            </span>
                                        </summary>
                                        <div class="absolute z-10 mt-1 bg-white border notion-border rounded-lg shadow-lg p-3 min-w-32">
                                            <form action="{{ route('task-tracker.update', $task) }}" method="POST">
                                                @csrf
                                                                                                <select name="priority" class="w-full px-2 py-1 border notion-border rounded focus:outline-none focus:ring-1 focus:ring-blue-500 text-sm mb-2">
                                                    <option value="low" {{ $task->priority === 'low' ? 'selected' : '' }}>Low</option>
                                                    <option value="medium" {{ $task->priority === 'medium' ? 'selected' : '' }}>Medium</option>
                                                    <option value="high" {{ $task->priority === 'high' ? 'selected' : '' }}>High</option>
                                                </select>
                                                <div class="flex justify-end space-x-2">
                                                    <button type="button" onclick="this.closest('details').open = false" 
                                                            class="px-2 py-1 text-xs bg-gray-200 text-gray-700 rounded hover:bg-gray-300">Cancel</button>
                                                    <button type="submit" class="px-2 py-1 text-xs bg-blue-500 text-white rounded hover:bg-blue-600">Save</button>
                                                </div>
                                            </form>
                                        </div>
                                    </details>
                                </td>
                                <!-- Task type -->
                                <td class="table-cell">
                                    <details class="relative">
                                        <summary class="editable-field text-left w-full cursor-pointer list-none">
                                            <span class="px-2 py-1 rounded text-xs font-medium task-type-{{ $task->task_type }}">
                                                {{ $task->task_type_icon }} {{ ucfirst(str_replace('_', ' ', $task->task_type)) }}
                                            </span>
                                        </summary>
                                        <div class="absolute z-10 mt-1 bg-white border notion-border rounded-lg shadow-lg p-3 min-w-48">
                                            <form action="{{ route('task-tracker.update', $task) }}" method="POST">
                                                @csrf
                                                                                                <select name="task_type" class="w-full px-2 py-1 border notion-border rounded focus:outline-none focus:ring-1 focus:ring-blue-500 text-sm mb-2">
                                                    <option value="polish" {{ $task->task_type === 'polish' ? 'selected' : '' }}>✨ Polish</option>
                                                    <option value="feature_request" {{ $task->task_type === 'feature_request' ? 'selected' : '' }}>💡 Feature request</option>
                                                    <option value="bug" {{ $task->task_type === 'bug' ? 'selected' : '' }}>🐛 Bug</option>
                                                    <option value="enhancement" {{ $task->task_type === 'enhancement' ? 'selected' : '' }}>🚀 Enhancement</option>
                                                    <option value="documentation" {{ $task->task_type === 'documentation' ? 'selected' : '' }}>📝 Documentation</option>
                                                </select>
                                                <div class="flex justify-end space-x-2">
                                                    <button type="button" onclick="this.closest('details').open = false" 
                                                            class="px-2 py-1 text-xs bg-gray-200 text-gray-700 rounded hover:bg-gray-300">Cancel</button>
                                                    <button type="submit" class="px-2 py-1 text-xs bg-blue-500 text-white rounded hover:bg-blue-600">Save</button>
                                                </div>
                                            </form>
                                        </div>
                                    </details>
                                </td>
                                <!-- Description -->
                                <td class="table-cell max-w-xs">
                                    <details class="relative">
                                        <summary class="editable-field text-left w-full cursor-pointer list-none">
                                            @if($task->description)
                                                <div class="truncate" title="{{ $task->description }}">
                                                    {{ Str::limit($task->description, 50) }}
                                                </div>
                                            @else
                                                <span class="text-gray-400">Add description</span>
                                            @endif
                                        </summary>
                                        <div class="absolute z-10 mt-1 bg-white border notion-border rounded-lg shadow-lg p-3 min-w-80">
                                            <form action="{{ route('task-tracker.update', $task) }}" method="POST">
                                                @csrf
                                                                                                <textarea name="description" rows="3" placeholder="Enter description"
                                                          class="w-full px-2 py-1 border notion-border rounded focus:outline-none focus:ring-1 focus:ring-blue-500 text-sm mb-2 resize-none">{{ $task->description }}</textarea>
                                                <div class="flex justify-end space-x-2">
                                                    <button type="button" onclick="this.closest('details').open = false" 
                                                            class="px-2 py-1 text-xs bg-gray-200 text-gray-700 rounded hover:bg-gray-300">Cancel</button>
                                                    <button type="submit" class="px-2 py-1 text-xs bg-blue-500 text-white rounded hover:bg-blue-600">Save</button>
                                                </div>
                                            </form>
                                        </div>
                                    </details>
                                </td>
                                <!-- Effort level -->
                                <td class="table-cell">
                                    <details class="relative">
                                        <summary class="editable-field text-left w-full cursor-pointer list-none">
                                            <span class="px-2 py-1 rounded text-xs font-medium effort-{{ $task->effort_level }}">
                                                {{ ucfirst($task->effort_level) }}
                                            </span>
                                        </summary>
                                        <div class="absolute z-10 mt-1 bg-white border notion-border rounded-lg shadow-lg p-3 min-w-32">
                                            <form action="{{ route('task-tracker.update', $task) }}" method="POST">
                                                @csrf
                                                                                                <select name="effort_level" class="w-full px-2 py-1 border notion-border rounded focus:outline-none focus:ring-1 focus:ring-blue-500 text-sm mb-2">
                                                    <option value="small" {{ $task->effort_level === 'small' ? 'selected' : '' }}>Small</option>
                                                    <option value="medium" {{ $task->effort_level === 'medium' ? 'selected' : '' }}>Medium</option>
                                                    <option value="large" {{ $task->effort_level === 'large' ? 'selected' : '' }}>Large</option>
                                                </select>
                                                <div class="flex justify-end space-x-2">
                                                    <button type="button" onclick="this.closest('details').open = false" 
                                                            class="px-2 py-1 text-xs bg-gray-200 text-gray-700 rounded hover:bg-gray-300">Cancel</button>
                                                    <button type="submit" class="px-2 py-1 text-xs bg-blue-500 text-white rounded hover:bg-blue-600">Save</button>
                                                </div>
                                            </form>
                                        </div>
                                    </details>
                                </td>
                                <!-- Actions -->
                                <td class="table-cell">
                                    <form action="{{ route('task-tracker.destroy', $task) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-400 hover:text-red-600 p-1"
                                                onclick="return confirm('Delete this task?')">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="table-cell text-center py-8 notion-gray">
                                    <div class="flex flex-col items-center">
                                        <div class="text-4xl mb-4">📝</div>
                                        <p class="text-lg font-medium">No tasks yet</p>
                                        <p class="text-sm">Click "New" to create your first task tracker item</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                        
                        @if($taskTrackers->isNotEmpty())
                            <tr class="table-row">
                                <td colspan="9" class="table-cell">
                                    <button class="flex items-center space-x-2 text-sm notion-gray hover:notion-text py-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                        </svg>
                                        <span>New task</span>
                                    </button>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
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


</body>
</html>