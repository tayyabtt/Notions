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
            padding: 2px 1px !important; /* match index.blade.php tight spacing */
            border-bottom: 1px solid #e5e5e3;
            font-size: 14px;
            color: #37352f;
        }

        /* Force tight spacing on all table cells */
        .excel-table .table-cell {
            padding: 2px 4px !important;
        }
        
        table td.table-cell {
            padding: 2px 4px !important;
        }

        /* Match the index.blade.php exact CSS */
        .excel-table td {
            background: white;
            border: 1px solid #e2e6ea;
            padding: 2px 4px !important; /* make this explicit to avoid overrides */
        }
        
        /* Nuclear option - force tight spacing everywhere */
        .excel-table tbody td {
            padding: 2px 4px !important;
        }
        
        .excel-table td.table-cell {
            padding: 2px 4px !important;
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
            padding: 4px !important; /* reduced from 8px */
            min-height: auto;
            display: flex;
            align-items: flex-start;
            gap: 8px;
        }
        
        /* Reduce heading and paragraph spacing inside boxes */
        .excel-box h4,
        .excel-box p {
            margin: 0;
        }

        .excel-box h4 {
            font-size: 13px;
            line-height: 1.1;
            margin-bottom: 2px;
        }

        /* Ensure inline labels inside boxes keep small padding */
        .excel-box .px-2,
        .excel-box .rounded.text-xs {
            padding: 2px 6px !important;
            font-size: 12px;
        }

        /* Keep editable fields compact */
        .editable-field {
            padding: 1px 4px !important;
            min-height: 18px;
            line-height: 1.2;
        }
        
        /* table collapse to avoid browser default spacing surprises */
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
                        <div class="flex items-center space-x-2 px-2 py-1 rounded notion-hover cursor-pointer text-sm notion-text">
                            <a href="{{ route('todo.index') }}" class="flex items-center space-x-2">
                                <span class="text-base">📋</span>
                            </a>
                            <span id="todo-list-text" class="editable-text cursor-pointer" onclick="makeEditable(this, 'todo-list')" title="Click to edit">
                                To Do List
                            </span>
                        </div>
                        <div class="flex items-center justify-between px-2 py-1 rounded notion-hover group">
                            <a href="{{ route('task-tracker.index') }}" class="flex items-center space-x-2 flex-1 text-sm notion-text">
                                <span class="text-base">✅</span>
                                <span>Tasks Trackers</span>
                            </a>
                            <form action="{{ route('task-tracker-page.quick-store') }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="opacity-0 group-hover:opacity-100 text-gray-400 hover:text-gray-600 p-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                    </svg>
                                </button>
                            </form>
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
                            <th class="text-left font-normal w-16">
                                <div class="flex items-center space-x-1">
                                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    <span class="text-sm">Actions</span>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($taskTrackers as $task)
                            <tr class="hover:bg-gray-50">
                                <!-- Task name -->
                                <td class="table-cell font-normal group relative">
                                    <!-- Hidden Open button that appears on hover of name field -->
                                    <div class="absolute right-2 top-1/2 transform -translate-y-1/2 opacity-0 group-hover:opacity-100 transition-opacity z-20">
                                        <a href="#task-panel-{{ $task->id }}" data-target="#task-panel-{{ $task->id }}" class="open-panel px-2 py-1 text-xs bg-gray-100 hover:bg-gray-200 text-gray-600 rounded border cursor-pointer">
                                             Open
                                        </a>
                                    </div>
                                    
                                    <details class="relative">
                                        <summary class="editable-field text-left w-full cursor-pointer list-none">
                                            {{ $task->name }}
                                            @if($task->comment || $task->comment_file_name)
                                                <span class="message-emoji ml-2 cursor-pointer hover:bg-gray-100 px-1 rounded" onclick="showComment({{ $task->id }}, '{{ addslashes($task->comment) }}', '{{ addslashes($task->comment_file_name) }}', '{{ $task->comment_file_path }}')" title="View comment">💬</span>
                                            @endif
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
                                <td class="table-cell">
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
                                <td class="table-cell">
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
                                <td class="table-cell">
                                    <details class="relative">
                                        <summary class="editable-field text-left w-full cursor-pointer list-none text-sm">
                                            {{ $task->description ?: 'No description' }} 💬
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
                                        <button type="submit" class="text-red-400 hover:text-red-600 p-1 rounded hover:bg-red-50"
                                                onclick="return confirm('Delete this task?')" 
                                                title="Delete task">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="py-8 px-1 text-center notion-gray">
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
                                <td colspan="9">
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

    <!-- Task Detail Side Panels for each task (Tailwind + CSS transitions) -->
    @foreach($taskTrackers as $task)
    <div id="task-panel-{{ $task->id }}"
         class="fixed inset-0 z-50 pointer-events-none opacity-0 transition-opacity duration-300"
         aria-hidden="true">
        <!-- overlay -->
        <div class="absolute inset-0 bg-black bg-opacity-25 transition-opacity duration-300 opacity-0"></div>

        <!-- sliding panel (task-panel-inner) -->
        <aside class="absolute right-0 top-0 h-full w-96 bg-white shadow-2xl transform translate-x-full transition-transform duration-300 task-panel-inner"
               role="dialog" aria-modal="true">
            <div class="flex flex-col h-full">
                <!-- Header Section with Task Title and Metadata -->
                <div class="px-6 py-6 border-b notion-border">
                    <div class="flex items-start justify-between mb-4">
                        <!-- Large Task Title -->
                        <input name="name" type="text" value="{{ $task->name }}" 
                               class="text-2xl font-bold notion-text leading-tight w-full border-0 bg-transparent focus:outline-none focus:bg-gray-50 rounded px-1 py-1"
                               style="font-size: 24px;">
                        
                        <!-- Close Button -->
                        <button type="button" class="close-panel p-2 hover:bg-gray-100 rounded ml-4" aria-label="Close">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    
                    <!-- Metadata Row -->
                    <div class="flex items-center justify-between text-sm">
                        <!-- Assignee with Avatar -->
                        <div class="flex items-center space-x-2">
                            <span class="w-6 h-6 rounded-full bg-blue-600 text-white flex items-center justify-center text-sm font-medium">
                                {{ substr($task->assignee ?: 'T', 0, 1) }}
                            </span>
                            <span class="notion-text font-medium">{{ $task->assignee ?: 'Tayyab Tahir' }}</span>
                        </div>
                        
                        <div style="width: 20px;"></div>
                        
                        <!-- Status Badge -->
                        <div class="flex items-center">
                            @if($task->status === 'not_started')
                                <span class="inline-flex items-center text-sm font-normal px-3 py-1 bg-gray-100 text-gray-700 rounded-full">
                                    <span class="w-2 h-2 bg-gray-500 rounded-full mr-2"></span>
                                    Not started
                                </span>
                            @elseif($task->status === 'in_progress')
                                <span class="inline-flex items-center text-sm font-normal px-3 py-1 bg-blue-100 text-blue-700 rounded-full">
                                    <span class="w-2 h-2 bg-blue-500 rounded-full mr-2"></span>
                                    In progress
                                </span>
                            @else
                                <span class="inline-flex items-center text-sm font-normal px-3 py-1 bg-green-100 text-green-700 rounded-full">
                                    <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                                    Done
                                </span>
                            @endif
                        </div>
                        
                        <div style="width: 20px;"></div>
                        
                        <!-- Due Date -->
                        <div class="flex items-center space-x-2">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3M3 11h18M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span class="notion-text">{{ $task->due_date ? $task->due_date->format('m/d/Y') : '02/03/2025' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Panel body with form -->
                <form action="{{ route('task-tracker.update', $task) }}" method="post" enctype="multipart/form-data" class="flex-1 flex flex-col overflow-y-auto">
                    @csrf
                    <!-- Hidden inputs for sub-tasks -->
                    <input type="hidden" name="subtask_1" id="subtask_1_input" value="{{ $task->subtask_1 ?? 'To-do' }}">
                    <input type="hidden" name="subtask_2" id="subtask_2_input" value="{{ $task->subtask_2 ?? 'To-do' }}">
                    <input type="hidden" name="subtask_3" id="subtask_3_input" value="{{ $task->subtask_3 ?? 'To-do' }}">

                    <div class="px-6 py-0">
                        <!-- Assignee Section -->
                        <div class="py-4 border-b notion-border">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    <span class="text-sm font-medium text-gray-700">Assignee</span>
                                </div>
                                <div class="flex-1 ml-4">
                                    <input name="assignee" type="text" value="{{ $task->assignee ?: 'Tayyab Tahir' }}" placeholder="Enter assignee name"
                                           class="w-full px-3 py-2 text-sm border-0 bg-gray-50 rounded focus:outline-none focus:bg-white focus:ring-2 focus:ring-blue-500">
                                </div>
                            </div>
                        </div>

                        <!-- Status Section -->
                        <div class="py-4 border-b notion-border">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    <svg class="w-3 h-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <span class="text-sm font-medium text-gray-700">Status</span>
                                </div>
                                <div class="flex-1 ml-4">
                                    <div class="relative">
                                        <select name="status" class="w-full px-3 py-2 text-sm border-0 bg-gray-50 rounded focus:outline-none focus:bg-white focus:ring-2 focus:ring-blue-500 appearance-none">
                                            <option value="not_started" {{ $task->status === 'not_started' ? 'selected' : '' }}>🔘 Not started</option>
                                            <option value="in_progress" {{ $task->status === 'in_progress' ? 'selected' : '' }}>🔵 In progress</option>
                                            <option value="complete" {{ $task->status === 'complete' ? 'selected' : '' }}>🟢 Done</option>
                                        </select>
                                        <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                                            <svg class="w-4 h-4 fill-current text-gray-400" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Due Date Section -->
                        <div class="py-4 border-b notion-border">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3M3 11h18M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span class="text-sm font-medium text-gray-700">Due date</span>
                                </div>
                                <div class="flex-1 ml-4">
                                    <input name="due_date" type="date" value="{{ $task->due_date ? $task->due_date->format('Y-m-d') : '2025-03-02' }}"
                                           class="w-full px-3 py-2 text-sm border-0 bg-gray-50 rounded focus:outline-none focus:bg-white focus:ring-2 focus:ring-blue-500 cursor-pointer">
                                </div>
                            </div>
                        </div>

                        <!-- Comments Section -->
                        <div class="py-6">
                            <h3 class="text-base font-semibold notion-text mb-3" style="font-size: 16px;">Comments</h3>
                            
                            <!-- Existing Comments Display -->
                            @if($task->comment || $task->comment_file_name)
                                <div class="mb-4">
                                    <div class="flex items-start space-x-3 mb-4">
                                        <span class="w-6 h-6 rounded-full bg-blue-600 text-white flex items-center justify-center text-sm font-medium">
                                            T
                                        </span>
                                        <div class="flex-1">
                                            <div class="flex items-baseline space-x-2 mb-1">
                                                <span class="font-semibold notion-text">Tayyab Tahir</span>
                                                <span class="text-xs text-gray-400">26m</span>
                                            </div>
                                            @if($task->comment)
                                                <p class="notion-text text-sm">{{ $task->comment }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Sample Comment -->
                            <div class="mb-4">
                                <div class="flex items-start space-x-3 mb-4">
                                    <span class="w-6 h-6 rounded-full bg-blue-600 text-white flex items-center justify-center text-sm font-medium">
                                        T
                                    </span>
                                    <div class="flex-1">
                                        <div class="flex items-baseline space-x-2 mb-1">
                                            <span class="font-semibold notion-text">Tayyab Tahir</span>
                                            <span class="text-xs text-gray-400">26m</span>
                                        </div>
                                        <p class="notion-text text-sm">great</p>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <div class="flex items-start space-x-3">
                                    <span class="w-6 h-6 rounded-full bg-blue-600 text-white flex items-center justify-center text-sm font-medium">
                                        T
                                    </span>
                                    <div class="flex-1">
                                        <div class="flex items-baseline space-x-2 mb-1">
                                            <span class="font-semibold notion-text">Tayyab Tahir</span>
                                            <span class="text-xs text-gray-400">24m</span>
                                        </div>
                                        <p class="notion-text text-sm">b</p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- New Comment Input -->
                            <div class="flex items-start space-x-3">
                                <span class="w-6 h-6 rounded-full bg-blue-600 text-white flex items-center justify-center text-sm font-medium">
                                    T
                                </span>
                                <div class="flex-1 space-y-3">
                                    <textarea name="comment" placeholder="Add a comment..." rows="3" class="w-full px-3 py-2 text-sm border-0 bg-gray-50 rounded focus:outline-none focus:bg-white focus:ring-2 focus:ring-blue-500 resize-none" style="min-height: 60px;"></textarea>
                                    
                                    <!-- File Upload Section -->
                                    <div class="flex items-center space-x-3">
                                        <label for="comment_file_{{ $task->id }}" class="cursor-pointer flex items-center space-x-2 px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded text-sm text-gray-700">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                            </svg>
                                            <span>Attach File</span>
                                        </label>
                                        <input type="file" id="comment_file_{{ $task->id }}" name="comment_file" class="hidden" accept="*/*" onchange="showFileName(this)">
                                        <span id="file_name_{{ $task->id }}" class="text-xs text-gray-500"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Task Description Section -->
                        <div class="py-6 border-t notion-border">
                            <h3 class="text-base font-semibold notion-text mb-2" style="font-size: 16px;">Task description</h3>
                            <div style="margin-bottom: 8px;"></div>
                            <textarea name="description" placeholder="Provide an overview of the task and related details." rows="3" 
                                      class="w-full px-0 py-0 text-sm border-0 bg-transparent notion-gray focus:outline-none resize-none"
                                      style="color: #787774;">{{ $task->description ?: 'Provide an overview of the task and related details.' }}</textarea>
                        </div>

                        <!-- Sub-tasks Section -->
                        <div class="py-6 border-t notion-border">
                            <h3 class="text-base font-semibold notion-text mb-3" style="font-size: 16px;">
                                <span id="subtasks-text" class="editable-text cursor-pointer" onclick="makeEditable(this, 'subtasks')" title="Click to edit">
                                    Sub-tasks
                                </span>
                            </h3>
                            <div style="margin-bottom: 12px;"></div>
                            
                            <!-- Sub-task items with proper spacing -->
                            <div class="space-y-2 pl-4">
                                <div class="flex items-center space-x-3">
                                    <input type="checkbox" class="w-6 h-6 border-2 border-gray-300 rounded focus:ring-blue-500">
                                    <span id="subtask-1-{{ $task->id }}" class="text-sm notion-gray editable-text cursor-pointer" onclick="makeEditableWithSave(this, 'subtask_1', {{ $task->id }})" title="Click to edit">{{ $task->subtask_1 ?? 'To-do' }}</span>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <input type="checkbox" class="w-6 h-6 border-2 border-gray-300 rounded focus:ring-blue-500">
                                    <span id="subtask-2-{{ $task->id }}" class="text-sm notion-gray editable-text cursor-pointer" onclick="makeEditableWithSave(this, 'subtask_2', {{ $task->id }})" title="Click to edit">{{ $task->subtask_2 ?? 'To-do' }}</span>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <input type="checkbox" class="w-6 h-6 border-2 border-gray-300 rounded focus:ring-blue-500">
                                    <span id="subtask-3-{{ $task->id }}" class="text-sm notion-gray editable-text cursor-pointer" onclick="makeEditableWithSave(this, 'subtask_3', {{ $task->id }})" title="Click to edit">{{ $task->subtask_3 ?? 'To-do' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="px-6 py-4 border-t notion-border mt-auto">
                        <div class="flex space-x-2">
                            <button type="submit" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Save changes</button>
                            <button type="button" class="px-4 py-2 border notion-border rounded hover:bg-gray-50 close-panel">Cancel</button>
                        </div>
                    </div>
                </form>
            </div>
        </aside>
    </div>
    @endforeach

    <!-- Comment Modal -->
    <div id="commentModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">Task Comment</h3>
                <button onclick="hideComment()" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            <!-- Comment Text -->
            <div id="commentTextSection" class="bg-gray-50 rounded p-4 mb-4">
                <p id="commentText" class="text-gray-800"></p>
            </div>
            
            <!-- Attached File -->
            <div id="attachedFileSection" class="hidden bg-blue-50 rounded p-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <div>
                            <p id="fileName" class="font-medium text-gray-800"></p>
                            <p class="text-sm text-gray-600">Attached File</p>
                        </div>
                    </div>
                    <button id="downloadBtn" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
                        Download
                    </button>
                </div>
            </div>
        </div>
    </div>

<script>
// Show comment modal
function showComment(taskId, comment, fileName, filePath) {
    // Show/hide comment section
    if (comment && comment.trim() !== '') {
        document.getElementById('commentText').textContent = comment;
        document.getElementById('commentTextSection').classList.remove('hidden');
    } else {
        document.getElementById('commentTextSection').classList.add('hidden');
    }
    
    // Show/hide file section
    if (fileName && fileName.trim() !== '') {
        document.getElementById('fileName').textContent = fileName;
        document.getElementById('downloadBtn').onclick = () => downloadFile(filePath, fileName);
        document.getElementById('attachedFileSection').classList.remove('hidden');
    } else {
        document.getElementById('attachedFileSection').classList.add('hidden');
    }
    
    document.getElementById('commentModal').classList.remove('hidden');
}

// Download file function
function downloadFile(filePath, fileName) {
    const link = document.createElement('a');
    link.href = '/storage/' + filePath;
    link.download = fileName;
    link.target = '_blank';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// Show selected file name
function showFileName(input) {
    const fileNameSpan = document.getElementById('file_name_' + input.id.split('_').pop());
    if (input.files && input.files[0]) {
        fileNameSpan.textContent = input.files[0].name;
        fileNameSpan.classList.add('text-blue-600');
    } else {
        fileNameSpan.textContent = '';
        fileNameSpan.classList.remove('text-blue-600');
    }
}

// Hide comment modal
function hideComment() {
    document.getElementById('commentModal').classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('commentModal').addEventListener('click', function(e) {
    if (e.target === this) {
        hideComment();
    }
});

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
    const newName = prompt('Enter new name for the page:', currentName);
    if (newName !== null && newName.trim() !== '') {
        // Save the new name to localStorage
        localStorage.setItem(pageKey, newName.trim());

        // Update the displayed name
        document.getElementById('displayName').textContent = newName.trim();

        // Optionally, you can also send an AJAX request to update the name on the server
        // For example, using fetch API:
        /*
        fetch('{{ route("task-tracker-page.update", $page) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ name: newName.trim() })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('Page name updated successfully.');
            } else {
                console.error('Error updating page name:', data.error);
            }
        })
        .catch(error => console.error('AJAX error:', error));
        */
    }
}

// This function is no longer used but kept for compatibility
function cancelNameEdit() {
    // Do nothing
}

document.addEventListener('DOMContentLoaded', function () {
    // select all panel wrappers
    const panels = document.querySelectorAll('[id^="task-panel-"]');

    // helper to open a panel element (wrapper DOM node)
    function openPanel(wrapper) {
        if (!wrapper) return;
        const overlay = wrapper.querySelector('.absolute.inset-0') || wrapper.firstElementChild;
        const inner = wrapper.querySelector('.task-panel-inner');

        // enable pointer events + fade wrapper in
        wrapper.classList.remove('pointer-events-none','opacity-0');
        wrapper.classList.add('pointer-events-auto','opacity-100');
        wrapper.setAttribute('aria-hidden', 'false');

        // overlay fade
        if (overlay) overlay.classList.remove('opacity-0'); overlay?.classList?.add?.('opacity-100');

        // slide panel in
        if (inner) {
            inner.classList.remove('translate-x-full');
            inner.classList.add('translate-x-0');
            inner.setAttribute('tabindex', '-1');
            inner.focus({ preventScroll: true });
        }
    }

    // helper to close a panel wrapper
    function closePanel(wrapper) {
        if (!wrapper) return;
        const overlay = wrapper.querySelector('.absolute.inset-0') || wrapper.firstElementChild;
        const inner = wrapper.querySelector('.task-panel-inner');

        // slide out
        if (inner) {
            inner.classList.remove('translate-x-0');
            inner.classList.add('translate-x-full');
        }

        // overlay fade out
        if (overlay) overlay.classList.remove('opacity-100'); overlay?.classList?.add?.('opacity-0');

        // hide wrapper after transition (use timeout matching duration-300 = 300ms)
        setTimeout(() => {
            wrapper.classList.add('pointer-events-none','opacity-0');
            wrapper.classList.remove('pointer-events-auto','opacity-100');
            wrapper.setAttribute('aria-hidden', 'true');
        }, 300);
    }

    // open/close by hash (anchors like href="#task-panel-<id>")
    function updateFromHash() {
        const targetId = location.hash ? location.hash.slice(1) : '';
        panels.forEach(wrapper => {
            if (wrapper.id === targetId) openPanel(wrapper);
            else closePanel(wrapper);
        });
    }

    // intercept anchor clicks and pushState to avoid jump
    document.querySelectorAll('a[href^="#task-panel-"]').forEach(a => {
        a.addEventListener('click', function (e) {
            e.preventDefault();
            const href = this.getAttribute('href');
            history.pushState(null, '', href);
            updateFromHash();
        });
    });

    // also allow buttons (e.g., from your table) with data-target="#task-panel-<id>"
    document.querySelectorAll('[data-target^="#task-panel-"]').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const tgt = this.dataset.target.slice(1); // remove leading #
            history.pushState(null, '', '#' + tgt);
            updateFromHash();
        });
    });

    // wire close buttons and overlay clicks
    panels.forEach(wrapper => {
        const overlay = wrapper.querySelector('.absolute.inset-0') || wrapper.firstElementChild;
        const closeBtns = wrapper.querySelectorAll('.close-panel');
        if (overlay) overlay.addEventListener('click', function () {
            history.pushState(null, '', location.pathname + location.search);
            updateFromHash();
        });
        closeBtns.forEach(cb => cb.addEventListener('click', function () {
            history.pushState(null, '', location.pathname + location.search);
            updateFromHash();
        }));
    });

    // respond to back/forward and initial load
    window.addEventListener('popstate', updateFromHash);
    updateFromHash();
});

// Function to make text editable inline
function makeEditable(element, identifier) {
    const currentText = element.textContent.trim();
    
    // Create input element
    const input = document.createElement('input');
    input.type = 'text';
    input.value = currentText;
    input.className = 'px-1 py-0 border notion-border rounded focus:outline-none focus:ring-1 focus:ring-blue-500 text-sm bg-white';
    input.style.width = 'auto';
    input.style.minWidth = '100px';
    
    // Replace the span with input
    element.parentNode.replaceChild(input, element);
    input.focus();
    input.select();
    
    // Handle saving on Enter or blur
    function saveText() {
        const newText = input.value.trim();
        if (newText && newText !== currentText) {
            // Create a new span with the updated text
            const newSpan = document.createElement('span');
            newSpan.id = element.id;
            newSpan.className = element.className;
            newSpan.onclick = element.onclick;
            newSpan.title = element.title;
            newSpan.textContent = newText;
            
            input.parentNode.replaceChild(newSpan, input);
            
            // Here you could add AJAX call to save to server if needed
            // For now, it just updates the display
        } else {
            // Restore original element if no change or empty
            input.parentNode.replaceChild(element, input);
        }
    }
    
    // Save on Enter key
    input.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            saveText();
        }
    });
    
    // Save on blur (clicking outside)
    input.addEventListener('blur', saveText);
    
    // Cancel on Escape
    input.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            input.parentNode.replaceChild(element, input);
        }
    });
}

// Function to make text editable with form integration for saving
function makeEditableWithSave(element, fieldName, taskId) {
    const currentText = element.textContent.trim();
    
    // Create input element
    const input = document.createElement('input');
    input.type = 'text';
    input.value = currentText;
    input.className = 'px-1 py-0 border notion-border rounded focus:outline-none focus:ring-1 focus:ring-blue-500 text-sm bg-white';
    input.style.width = 'auto';
    input.style.minWidth = '100px';
    
    // Replace the span with input
    element.parentNode.replaceChild(input, element);
    input.focus();
    input.select();
    
    // Handle saving on Enter or blur
    function saveText() {
        const newText = input.value.trim();
        if (newText && newText !== currentText) {
            // Update the hidden form input
            const hiddenInput = document.getElementById(fieldName + '_input');
            if (hiddenInput) {
                hiddenInput.value = newText;
            }
            
            // Create a new span with the updated text
            const newSpan = document.createElement('span');
            newSpan.id = element.id;
            newSpan.className = element.className;
            newSpan.onclick = function() { makeEditableWithSave(newSpan, fieldName, taskId); };
            newSpan.title = element.title;
            newSpan.textContent = newText;
            
            input.parentNode.replaceChild(newSpan, input);
        } else {
            // Restore original element if no change or empty
            input.parentNode.replaceChild(element, input);
        }
    }
    
    // Save on Enter key
    input.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            saveText();
        }
    });
    
    // Save on blur (clicking outside)
    input.addEventListener('blur', saveText);
    
    // Cancel on Escape
    input.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            input.parentNode.replaceChild(element, input);
        }
    });
}
</script>

</body>
</html>