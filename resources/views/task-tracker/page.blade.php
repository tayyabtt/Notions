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
        
        .status-not_started { color: #9B9A97; }
        .status-in_progress { color: #0074E4; }
        .status-done { color: #00B34A; }
        .status-complete { color: #00B34A; }
        
        .priority-low { background-color: rgba(0, 179, 74, 0.15); color: #00B34A; }
        .priority-medium { background-color: rgba(245, 158, 11, 0.15); color: #D97706; }
        .priority-high { background-color: rgba(239, 68, 68, 0.15); color: #DC2626; }
        
        .task-type-polish { background-color: rgba(236, 72, 153, 0.15); color: #EC4899; }
        .task-type-feature_request { background-color: rgba(0, 116, 228, 0.15); color: #0074E4; }
        .task-type-bug { background-color: rgba(239, 68, 68, 0.15); color: #DC2626; }
        .task-type-enhancement { background-color: rgba(139, 92, 246, 0.15); color: #8B5CF6; }
        .task-type-documentation { background-color: rgba(0, 179, 74, 0.15); color: #00B34A; }
        
        .effort-small { background-color: rgba(16, 185, 129, 0.1); color: #10B981; }
        .effort-medium { background-color: rgba(245, 158, 11, 0.1); color: #F59E0B; }
        .effort-large { background-color: rgba(239, 68, 68, 0.1); color: #EF4444; }

        .table-header {
            background: #fafafa;
            border-bottom: 1px solid #e5e5e3;
            font-size: 12px;
            font-weight: 500;
            color: #9B9A97;
            text-transform: uppercase;
            letter-spacing: 0.025em;
        }
        
        .table-row:hover {
            background: rgba(55, 53, 47, 0.03);
        }
        
        .table-cell {
            padding: 2px 1px !important;
            border-bottom: 1px solid #e5e5e3;
            font-size: 14px;
            color: #37352f;
        }

        .new-task-button {
            background: #0074E4;
            color: white;
            border: none;
            border-radius: 6px;
            padding: 8px 12px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 4px;
        }
        
        .new-task-button:hover {
            background: #0056B3;
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
            gap: 6px;
            padding: 6px 12px;
            font-size: 14px;
            color: #787774;
            cursor: pointer;
            border-radius: 6px;
            text-decoration: none;
            background: transparent;
            border: none;
            font-weight: 400;
        }
        
        .view-tab.active {
            color: #37352f;
            background: #f1f1ef;
            font-weight: 500;
        }
        
        .view-tab:hover:not(.active) {
            color: #37352f;
            background: rgba(55, 53, 47, 0.08);
        }

        input, select, textarea {
            font-family: inherit;
        }
        
        .editable-field {
            cursor: pointer;
            padding: 1px 2px !important;
            border-radius: 2px;
            min-height: 18px;
            line-height: 1.2;
        }
        
        .editable-field:hover {
            background: rgba(55, 53, 47, 0.08);
        }
        
        .excel-box {
            background: white;
            border: 1px solid #e2e6ea;
            border-radius: 3px;
            padding: 8px;
            min-height: 36px;
            display: flex;
            align-items: center;
        }
        
        .excel-cell {
            background: white;
            border: 1px solid #e2e6ea;
            padding: 6px 8px !important;
            min-height: 32px;
        }
        
        .excel-table th {
            background: white;
            border-left: 1px solid #e2e6ea;
            border-right: 1px solid #e2e6ea;
            border-bottom: 1px solid #e2e6ea;
            padding: 8px;
        }
        
        .excel-table th:first-child {
            border-left: 1px solid #e2e6ea;
        }
        
        .excel-table th:last-child {
            border-right: 1px solid #e2e6ea;
        }
        
        .excel-table td {
            background: white;
            border: 1px solid #e2e6ea;
            padding: 2px 4px;
        }
        
        .excel-table {
            border-collapse: collapse;
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
                            <a href="{{ route('task-tracker.index') }}" class="flex items-center space-x-2 flex-1 text-sm notion-text">
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
                                   class="flex items-center space-x-2 flex-1 text-sm notion-text {{ (isset($page) && $page->id === $taskPage->id) ? 'bg-blue-50' : '' }}">
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
            <div class="border-b notion-border px-3 py-6 ml-[10%]">
                <div class="flex items-start space-x-4">
                    <span class="text-4xl">{{ $page->icon }}</span>
                    <div class="flex-1">
                        <details class="relative inline-block">
                            <summary class="text-3xl font-bold notion-text cursor-pointer list-none hover:bg-gray-50 px-1 py-1 rounded">
                                {{ $page->name }}
                            </summary>
                            <div class="absolute z-10 mt-1 bg-white border notion-border rounded-lg shadow-lg p-3 min-w-80">
                                <form action="{{ route('task-tracker-page.update', $page) }}" method="POST">
                                    @csrf
                                    <input type="text" name="name" value="{{ $page->name }}" 
                                           class="w-full px-2 py-1 border notion-border rounded focus:outline-none focus:ring-1 focus:ring-blue-500 text-lg font-bold mb-2">
                                    <div class="flex justify-end space-x-2">
                                        <button type="button" onclick="this.closest('details').open = false" 
                                                class="px-2 py-1 text-xs bg-gray-200 text-gray-700 rounded hover:bg-gray-300">Cancel</button>
                                        <button type="submit" class="px-2 py-1 text-xs bg-blue-500 text-white rounded hover:bg-blue-600">Save</button>
                                    </div>
                                </form>
                            </div>
                        </details>
                        <div class="mt-1">
                            <details class="relative inline-block">
                                <summary class="text-sm notion-gray cursor-pointer list-none hover:bg-gray-50 px-1 py-1 rounded">
                                    {{ $page->description ?: 'Stay organized with tasks, your way.' }}
                                </summary>
                                <div class="absolute z-10 mt-1 bg-white border notion-border rounded-lg shadow-lg p-3 min-w-80">
                                    <form action="{{ route('task-tracker-page.update', $page) }}" method="POST">
                                        @csrf
                                        <textarea name="description" rows="2" placeholder="Page description"
                                                  class="w-full px-2 py-1 border notion-border rounded focus:outline-none focus:ring-1 focus:ring-blue-500 text-sm mb-2 resize-none">{{ $page->description }}</textarea>
                                        <div class="flex justify-end space-x-2">
                                            <button type="button" onclick="this.closest('details').open = false" 
                                                    class="px-2 py-1 text-xs bg-gray-200 text-gray-700 rounded hover:bg-gray-300">Cancel</button>
                                            <button type="submit" class="px-2 py-1 text-xs bg-blue-500 text-white rounded hover:bg-blue-600">Save</button>
                                        </div>
                                    </form>
                                </div>
                            </details>
                        </div>
                    </div>
                </div>
            </div>

            <!-- View Tabs -->
            <div class="px-3 py-4 ml-[10%]">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('task-tracker-page.show', array_merge(['page' => $page], request()->query(), ['view' => 'all'])) }}" 
                           class="view-tab {{ (isset($view) && $view === 'all') || !isset($view) ? 'active' : '' }}">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2L13.09 8.26L22 9L17 14L18.18 22L12 19L5.82 22L7 14L2 9L10.91 8.26L12 2Z"/>
                            </svg>
                            <span>All Tasks</span>
                        </a>
                        <a href="{{ route('task-tracker-page.show', array_merge(['page' => $page], request()->query(), ['view' => 'by_status'])) }}" 
                           class="view-tab {{ isset($view) && $view === 'by_status' ? 'active' : '' }}">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M3 13H15V11H3M3 6V8H21V6M3 18H9V16H3V18Z"/>
                            </svg>
                            <span>By Status</span>
                        </a>
                        <a href="{{ route('task-tracker-page.show', array_merge(['page' => $page], request()->query(), ['view' => 'my_tasks'])) }}" 
                           class="view-tab {{ isset($view) && $view === 'my_tasks' ? 'active' : '' }}">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12,4A4,4 0 0,1 16,8A4,4 0 0,1 12,12A4,4 0 0,1 8,8A4,4 0 0,1 12,4M12,14C16.42,14 20,15.79 20,18V20H4V18C4,15.79 7.58,14 12,14Z"/>
                            </svg>
                            <span>My Tasks</span>
                        </a>
                        
                    </div>
                    
                    <div class="flex items-center space-x-1">
                        <button class="filter-button">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
                            </svg>
                        </button>
                        
                        <button class="filter-button">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                            </svg>
                        </button>
                        
                        <button class="filter-button">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </button>
                        
                        <button class="filter-button">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </button>
                        
                        <button class="filter-button">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                            </svg>
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
                                    <input type="hidden" name="page_id" value="{{ $page->id }}">
                                    
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
                                                <option value="complete">🟢 Done</option>
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
                
                <!-- Filter Section -->
                <div class="mt-3">
                    <button class="flex items-center space-x-1 px-3 py-1 text-sm notion-gray hover:notion-text rounded border-none bg-transparent">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        <span>Filter</span>
                    </button>
                </div>
            </div>

            <!-- Task Display -->
            <div class="flex-1 overflow-x-auto overflow-y-auto ml-[10%]">
                @if(isset($view) && $view === 'by_status' && isset($groupedTasks) && $groupedTasks)
                    <!-- Status Groups View -->
                    <div class="p-6 space-y-6">
                        @php
                            $statusConfig = [
                                'not_started' => ['title' => 'Not started', 'color' => 'gray', 'icon' => '🔘', 'count' => $groupedTasks ? $groupedTasks->get('not_started', collect())->count() : 0],
                                'in_progress' => ['title' => 'In progress', 'color' => 'blue', 'icon' => '🔵', 'count' => $groupedTasks ? $groupedTasks->get('in_progress', collect())->count() : 0],
                                'complete' => ['title' => 'Done', 'color' => 'green', 'icon' => '🟢', 'count' => $groupedTasks ? $groupedTasks->get('complete', collect())->count() : 0]
                            ];
                        @endphp

                        @foreach($statusConfig as $status => $config)
                            <div class="bg-white rounded-lg border notion-border">
                                @if($status === 'not_started')
                                    <div class="px-4 py-3 bg-gray-50 border-b notion-border flex items-center justify-between">
                                        <div class="flex items-center space-x-2">
                                            <span class="text-base">{{ $config['icon'] }}</span>
                                            <h3 class="font-semibold text-gray-700">{{ $config['title'] }}</h3>
                                            <span class="bg-gray-100 text-gray-600 text-xs px-2 py-1 rounded-full">{{ $config['count'] }}</span>
                                        </div>
                                @elseif($status === 'in_progress')
                                    <div class="px-4 py-3 bg-blue-50 border-b notion-border flex items-center justify-between">
                                        <div class="flex items-center space-x-2">
                                            <span class="text-base">{{ $config['icon'] }}</span>
                                            <h3 class="font-semibold text-blue-700">{{ $config['title'] }}</h3>
                                            <span class="bg-blue-100 text-blue-600 text-xs px-2 py-1 rounded-full">{{ $config['count'] }}</span>
                                        </div>
                                @else
                                    <div class="px-4 py-3 bg-green-50 border-b notion-border flex items-center justify-between">
                                        <div class="flex items-center space-x-2">
                                            <span class="text-base">{{ $config['icon'] }}</span>
                                            <h3 class="font-semibold text-green-700">{{ $config['title'] }}</h3>
                                            <span class="bg-green-100 text-green-600 text-xs px-2 py-1 rounded-full">{{ $config['count'] }}</span>
                                        </div>
                                    <button class="text-gray-400 hover:text-gray-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                        </svg>
                                    </button>
                                </div>
                                @endif
                                <div class="p-4">
                                    @forelse($groupedTasks ? $groupedTasks->get($status, []) : [] as $task)
                                        <div class="excel-box mb-3 hover:bg-gray-100">
                                            <div class="flex-1">
                                                <h4 class="font-medium text-gray-900 mb-1">{{ $task->name }}</h4>
                                                <div class="flex items-center space-x-4 text-sm text-gray-600">
                                                    @if($task->assignee)
                                                        <span>👤 {{ $task->assignee }}</span>
                                                    @endif
                                                    @if($task->due_date)
                                                        <span>📅 {{ $task->due_date->format('M d, Y') }}</span>
                                                    @endif
                                                    <span class="px-2 py-1 rounded text-xs font-medium priority-{{ $task->priority }}">
                                                        {{ ucfirst($task->priority) }}
                                                    </span>
                                                    <span class="px-2 py-1 rounded text-xs font-medium task-type-{{ $task->task_type }}">
                                                        {{ $task->task_type_icon }} {{ ucfirst(str_replace('_', ' ', $task->task_type)) }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="flex items-center space-x-2">
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
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-center py-8 text-gray-500">
                                            <div class="text-2xl mb-2">{{ $config['icon'] }}</div>
                                            <p>No {{ strtolower($config['title']) }} tasks</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <!-- Regular Table View -->
                    <table class="min-w-max excel-table">
                    <thead class="sticky top-0">
                        <tr>
                            <th class="text-left font-normal w-60">
                                <div class="flex items-center space-x-1 cursor-pointer hover:bg-gray-50 px-1 py-1 rounded" onclick="openNameEdit()">
                                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    <span id="displayName" class="text-sm">Tayyab</span>
                                </div>
                            </th>
                            <th class="text-left font-normal w-32">
                                <div class="flex items-center space-x-1">
                                    <svg class="w-3 h-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <span class="text-sm">Status</span>
                                </div>
                            </th>
                            <th class="text-left font-normal w-32">
                                <div class="flex items-center space-x-1">
                                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    <span class="text-sm">Assignee</span>
                                    <span class="ml-1 w-3 h-3 rounded-full text-xs flex items-center justify-center" title="Info icon">
                                        <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <circle cx="12" cy="12" r="10"></circle>
                                            <line x1="12" y1="16" x2="12" y2="12"></line>
                                            <line x1="12" y1="8" x2="12.01" y2="8"></line>
                                        </svg>
                                    </span>
                                </div>
                            </th>
                            <th class="text-left font-normal w-32">
                                <div class="flex items-center space-x-1">
                                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v2M8 16l2.879-2.879m0 0a3 3 0 104.243-4.242 3 3 0 00-4.243 4.242zM8 16v4a2 2 0 002 2h8a2 2 0 002-2v-4"/>
                                    </svg>
                                    <span class="text-sm">Due date</span>
                                </div>
                            </th>
                            <th class="text-left font-normal w-28">
                                <div class="flex items-center space-x-1">
                                    <svg class="w-4 h-4 text-gray-500" fill="currentColor" viewBox="0 0 24 24">
                                        <circle cx="12" cy="12" r="10"/>
                                        <circle cx="12" cy="12" r="3"/>
                                    </svg>
                                    <span class="text-sm">Priority</span>
                                </div>
                            </th>
                            <th class="text-left font-normal w-36">
                                <div class="flex items-center space-x-1">
                                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                    </svg>
                                    <span class="text-sm">Task type</span>
                                </div>
                            </th>
                            <th class="text-left font-normal w-48">
                                <div class="flex items-center space-x-1">
                                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
                                    </svg>
                                    <span class="text-sm">Description</span>
                                </div>
                            </th>
                            <th class="text-left font-normal w-32">
                                <div class="flex items-center space-x-1">
                                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span class="text-sm">Effort level</span>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($taskTrackers as $task)
                            <tr class="hover:bg-gray-100">
                                <!-- Task name -->
                                <td class="font-normal">
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
                                <td>
                                    <details class="relative">
                                        <summary class="editable-field text-left w-full cursor-pointer list-none">
                                            @if($task->status === 'not_started')
                                                <span class="inline-flex items-center text-sm font-normal px-2 py-1 bg-gray-100 text-gray-700 rounded-full">
                                                    <span class="w-2 h-2 bg-gray-500 rounded-full mr-2"></span>
                                                    Not started
                                                </span>
                                            @elseif($task->status === 'in_progress')
                                                <span class="inline-flex items-center text-sm font-normal px-2 py-1 bg-blue-100 text-blue-700 rounded-full">
                                                    <span class="w-2 h-2 bg-blue-500 rounded-full mr-2"></span>
                                                    In progress
                                                </span>
                                            @else
                                                <span class="inline-flex items-center text-sm font-normal px-2 py-1 bg-green-100 text-green-700 rounded-full">
                                                    <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                                                    Done
                                                </span>
                                            @endif
                                        </summary>
                                        <div class="absolute z-10 mt-1 bg-white border notion-border rounded-lg shadow-lg p-3 min-w-48">
                                            <form action="{{ route('task-tracker.update', $task) }}" method="POST">
                                                @csrf
                                                <select name="status" class="w-full px-2 py-1 border notion-border rounded focus:outline-none focus:ring-1 focus:ring-blue-500 text-sm mb-2">
                                                    <option value="not_started" {{ $task->status === 'not_started' ? 'selected' : '' }}>🔘 Not started</option>
                                                    <option value="in_progress" {{ $task->status === 'in_progress' ? 'selected' : '' }}>🔵 In progress</option>
                                                    <option value="complete" {{ $task->status === 'complete' ? 'selected' : '' }}>🟢 Done</option>
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
                                <td>
                                    <details class="relative">
                                        <summary class="editable-field text-left w-full cursor-pointer list-none text-sm">
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
                                <td>
                                    <details class="relative">
                                        <summary class="editable-field text-left w-full cursor-pointer list-none">
                                            {{ $task->due_date ? $task->due_date->format('m/d/Y') : '' }}
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
                                <td>
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
                                <td>
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
                                <td>
                                    <details class="relative">
                                        <summary class="editable-field text-left w-full cursor-pointer list-none text-sm">
                                            {{ $task->description ?: 'No description' }}
                                        </summary>
                                        <div class="absolute z-10 mt-1 bg-white border notion-border rounded-lg shadow-lg p-3 min-w-64">
                                            <form action="{{ route('task-tracker.update', $task) }}" method="POST">
                                                @csrf
                                                <textarea name="description" rows="3" placeholder="Add a description..."
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
                                <td>
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
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-8 px-1 text-center notion-gray">
                                    <div class="flex flex-col items-center">
                                        <div class="text-4xl mb-4">📝</div>
                                        <p class="text-lg font-medium">No tasks yet</p>
                                        <p class="text-sm">Click "New" to create your first task</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                        
                        @if($taskTrackers->isNotEmpty())
                            <tr class="table-row">
                                <td colspan="8">
                                    <button class="flex items-center space-x-2 text-sm notion-gray hover:notion-text py-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m-6h6m-6 0H6"/>
                                        </svg>
                                        <span>New task</span>
                                    </button>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
                @endif
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
// Load saved name from localStorage on page load
document.addEventListener('DOMContentLoaded', function() {
    const pageKey = 'taskTrackerHeaderName_page_{{ $page->id }}';
    const savedName = localStorage.getItem(pageKey);
    if (savedName) {
        document.getElementById('displayName').textContent = savedName;
    }
});

function openNameEdit() {
    const pageKey = 'taskTrackerHeaderName_page_{{ $page->id }}';
    const currentName = document.getElementById('displayName').textContent;
    const newName = prompt('Enter name:', currentName);
    if (newName && newName.trim()) {
        document.getElementById('displayName').textContent = newName.trim();
        localStorage.setItem(pageKey, newName.trim());
    }
}

function saveNameEdit() {
    // This function is no longer used but kept for compatibility
}

function cancelNameEdit() {
    // This function is no longer used but kept for compatibility
}
</script>

</body>
</html>