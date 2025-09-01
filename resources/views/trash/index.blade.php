<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trash - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .notion-text { color: #37352f; }
        .notion-gray { color: #787774; }
        .notion-border { border-color: #e5e5e3; }
        .notion-hover:hover { background-color: rgba(55, 53, 47, 0.08); }
        .notion-sidebar { background: #f7f7f5; border-right: 1px solid #e5e5e3; }
        .notion-sidebar-item { color: #37352f; font-size: 14px; }
        .notion-sidebar-item:hover { background: rgba(55, 53, 47, 0.08); }

        .priority-low { background: #10B981; color: white; }
        .priority-medium { background: #F59E0B; color: white; }
        .priority-high { background: #EF4444; color: white; }
        
        .task-type-polish { background: #EC4899; color: white; }
        .task-type-feature_request { background: #3B82F6; color: white; }
        .task-type-bug { background: #EF4444; color: white; }
        .task-type-enhancement { background: #8B5CF6; color: white; }
        .task-type-documentation { background: #10B981; color: white; }

        .deleted-item {
            opacity: 0.7;
            border-left: 4px solid #EF4444;
            background: #FEF2F2;
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
                    <a href="{{ route('task-tracker.index') }}" class="flex items-center space-x-2 px-2 py-1 rounded notion-hover cursor-pointer text-sm notion-text">
                        <span class="text-base">✅</span>
                        <span>Tasks Trackers</span>
                    </a>
                    <a href="{{ route('trash.index') }}" class="flex items-center space-x-2 px-2 py-1 rounded bg-blue-50 cursor-pointer text-sm notion-text">
                        <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        <span>Trash</span>
                        @if($deletedTasks->count() > 0)
                            <span class="ml-auto bg-red-100 text-red-600 text-xs rounded-full h-5 w-5 flex items-center justify-center">
                                {{ $deletedTasks->count() }}
                            </span>
                        @endif
                    </a>
                </div>
            </div>

            <!-- Bottom Section -->
            <div class="mt-auto border-t notion-border p-3">
                <form action="{{ route('logout') }}" method="POST">
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
            <!-- Header -->
            <div class="border-b notion-border px-6 py-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        <h1 class="text-3xl font-bold notion-text">Trash</h1>
                    </div>
                    @if($deletedTasks->count() > 0)
                        <form action="{{ route('trash.empty') }}" method="POST" class="inline"
                              onsubmit="return confirm('This will permanently delete all items in trash. This action cannot be undone. Are you sure?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 text-sm font-medium">
                                Empty Trash
                            </button>
                        </form>
                    @endif
                </div>
                <p class="text-sm notion-gray mt-2">Items in trash will be automatically deleted after 30 days</p>
            </div>

            <!-- Content -->
            <div class="flex-1 overflow-auto p-6">
                @if(session('success'))
                    <div class="mb-4 p-3 bg-green-100 border border-green-400 text-green-700 rounded">
                        {{ session('success') }}
                    </div>
                @endif

                @if($deletedTasks->count() === 0)
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        <h3 class="text-xl font-medium text-gray-600 mb-2">Trash is empty</h3>
                        <p class="text-gray-500">Deleted tasks will appear here</p>
                    </div>
                @else
                    <!-- Group deleted tasks by time -->
                    @php
                        $groups = [
                            'today' => 'Today',
                            'this_week' => 'This Week',
                            'this_month' => 'This Month',
                            'older' => 'Older'
                        ];
                    @endphp

                    @foreach($groups as $key => $label)
                        @if(count($groupedTasks[$key]) > 0)
                            <div class="mb-8">
                                <h2 class="text-lg font-semibold text-gray-700 mb-4 border-b pb-2">{{ $label }}</h2>
                                <div class="space-y-3">
                                    @foreach($groupedTasks[$key] as $task)
                                        <div class="deleted-item border notion-border rounded-lg p-4">
                                            <div class="flex items-start justify-between">
                                                <div class="flex-1">
                                                    <h3 class="font-medium text-gray-900 mb-2">{{ $task->name }}</h3>
                                                    @if($task->description)
                                                        <p class="text-sm text-gray-600 mb-2">{{ Str::limit($task->description, 100) }}</p>
                                                    @endif
                                                    
                                                    <div class="flex items-center space-x-4 text-xs">
                                                        @if($task->page)
                                                            <span class="bg-gray-100 px-2 py-1 rounded">{{ $task->page->icon }} {{ $task->page->name }}</span>
                                                        @endif
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
                                                        <span class="text-gray-500">Deleted {{ $task->deleted_at ? $task->deleted_at->diffForHumans() : 'recently' }}</span>
                                                    </div>
                                                </div>
                                                
                                                <div class="flex items-center space-x-2 ml-4">
                                                    <!-- Restore Button -->
                                                    <form action="{{ route('trash.restore', $task->id) }}" method="POST" class="inline">
                                                        @csrf
                                                        <button type="submit" class="px-3 py-1 bg-blue-500 text-white text-xs rounded hover:bg-blue-600"
                                                                title="Restore task">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                                            </svg>
                                                        </button>
                                                    </form>
                                                    
                                                    <!-- Permanent Delete Button -->
                                                    <form action="{{ route('trash.force-delete', $task->id) }}" method="POST" class="inline"
                                                          onsubmit="return confirm('This will permanently delete this task. This action cannot be undone. Are you sure?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="px-3 py-1 bg-red-500 text-white text-xs rounded hover:bg-red-600"
                                                                title="Delete permanently">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                            </svg>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</body>
</html>