<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $task->title }} - {{ config('app.name', 'Notions') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .font-ui-sans-serif { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; }
        .antialiased { -webkit-font-smoothing: antialiased; -moz-osx-font-smoothing: grayscale; }
    </style>
</head>
<body class="bg-white text-gray-900 font-ui-sans-serif antialiased">
    <div class="min-h-screen bg-gray-50">
        <!-- Header -->
        <div class="bg-white border-b border-gray-200 px-6 py-4">
            <div class="flex items-center justify-between max-w-4xl mx-auto">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('teams.show', $task->team_id) }}" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </a>
                    <div>
                        <div class="flex items-center space-x-2">
                            <h1 class="text-xl font-semibold text-gray-900">{{ $task->title }}</h1>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                @if($task->status === 'todo') bg-gray-100 text-gray-800 border border-gray-200
                                @elseif($task->status === 'in_progress') bg-blue-100 text-blue-800 border border-blue-200
                                @elseif($task->status === 'done') bg-green-100 text-green-800 border border-green-200
                                @else bg-gray-100 text-gray-800 border border-gray-200 @endif">
                                {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-500">{{ $task->team->name }}</p>
                    </div>
                </div>
                
                <div class="flex items-center space-x-2">
                    <!-- Edit Button -->
                    <details>
                        <summary class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-2 rounded text-sm cursor-pointer list-none">
                            Edit
                        </summary>
                        <div class="absolute right-0 mt-2 w-72 bg-white border border-gray-200 rounded-md shadow-lg z-10 p-4">
                            <form action="{{ route('tasks.update', $task->id) }}" method="POST" class="space-y-4">
                                @csrf
                                @method('PATCH')
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                                    <input type="text" name="title" value="{{ $task->title }}" required 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="todo" {{ $task->status === 'todo' ? 'selected' : '' }}>Todo</option>
                                        <option value="in_progress" {{ $task->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                        <option value="done" {{ $task->status === 'done' ? 'selected' : '' }}>Done</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
                                    <select name="priority" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="low" {{ $task->priority === 'low' ? 'selected' : '' }}>Low</option>
                                        <option value="medium" {{ $task->priority === 'medium' ? 'selected' : '' }}>Medium</option>
                                        <option value="high" {{ $task->priority === 'high' ? 'selected' : '' }}>High</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Assignee</label>
                                    <select name="assigned_to" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="">Unassigned</option>
                                        @foreach($task->team->users as $user)
                                            <option value="{{ $user->id }}" {{ $task->assigned_to === $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Due Date</label>
                                    <input type="date" name="due_date" value="{{ $task->due_date }}" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                
                                <div class="flex space-x-2">
                                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded text-sm">
                                        Update
                                    </button>
                                </div>
                            </form>
                        </div>
                    </details>
                    
                    <!-- Delete Button -->
                    <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-800 px-3 py-2 text-sm">
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="max-w-4xl mx-auto py-8 px-6">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2">
                    <!-- Description -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
                        <h2 class="text-lg font-semibold mb-4">Description</h2>
                        @if($task->description)
                            <div class="prose max-w-none text-gray-700">
                                {!! nl2br(e($task->description)) !!}
                            </div>
                        @else
                            <div class="text-gray-500 italic">No description provided</div>
                        @endif
                        
                        <details class="mt-4">
                            <summary class="text-blue-600 hover:text-blue-800 cursor-pointer text-sm list-none">
                                Edit Description
                            </summary>
                            <div class="mt-3">
                                <form action="{{ route('tasks.update', $task->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <textarea name="description" rows="4" 
                                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                              placeholder="Add a description...">{{ $task->description }}</textarea>
                                    <div class="mt-2">
                                        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded text-sm">
                                            Save Description
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </details>
                    </div>
                    
                    <!-- Comments -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h2 class="text-lg font-semibold mb-4">Comments</h2>
                        
                        <!-- Add Comment Form -->
                        <div class="mb-6">
                            <form action="{{ route('tasks.comments.store', $task->id) }}" method="POST" class="space-y-3">
                                @csrf
                                <textarea name="content" rows="3" 
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none" 
                                          placeholder="Add a comment... Type @username to mention someone"></textarea>
                                <div class="flex justify-end">
                                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded text-sm">
                                        Add Comment
                                    </button>
                                </div>
                            </form>
                        </div>
                        
                        <!-- Comments List -->
                        <div class="space-y-4">
                            @forelse($task->comments as $comment)
                                <div class="border-b border-gray-100 pb-4">
                                    <div class="flex items-start space-x-3">
                                        <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center text-sm font-medium">
                                            {{ substr($comment->user->name, 0, 1) }}
                                        </div>
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-2 mb-1">
                                                <span class="font-medium text-sm">{{ $comment->user->name }}</span>
                                                <span class="text-xs text-gray-500">
                                                    {{ $comment->created_at->diffForHumans() }}
                                                </span>
                                            </div>
                                            <div class="text-sm text-gray-700">
                                                {!! nl2br(e($comment->content)) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-gray-500 text-sm">No comments yet. Be the first to comment!</p>
                            @endforelse
                        </div>
                    </div>
                </div>
                
                <!-- Sidebar -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold mb-4">Task Details</h3>
                        
                        <div class="space-y-4">
                            <!-- Priority -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                    @if($task->priority === 'high') bg-red-100 text-red-800 border border-red-200
                                    @elseif($task->priority === 'medium') bg-yellow-100 text-yellow-800 border border-yellow-200
                                    @elseif($task->priority === 'low') bg-green-100 text-green-800 border border-green-200
                                    @else bg-gray-100 text-gray-800 border border-gray-200 @endif">
                                    {{ ucfirst($task->priority) }}
                                </span>
                            </div>
                            
                            <!-- Assignee -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Assignee</label>
                                @if($task->assignee)
                                    <div class="flex items-center space-x-2">
                                        <div class="w-6 h-6 bg-gray-300 rounded-full text-xs flex items-center justify-center">
                                            {{ substr($task->assignee->name, 0, 1) }}
                                        </div>
                                        <span class="text-sm text-gray-700">{{ $task->assignee->name }}</span>
                                    </div>
                                @else
                                    <span class="text-sm text-gray-500">Unassigned</span>
                                @endif
                            </div>
                            
                            <!-- Due Date -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Due Date</label>
                                @if($task->due_date)
                                    <span class="text-sm text-gray-700">
                                        {{ \Carbon\Carbon::parse($task->due_date)->format('M j, Y') }}
                                    </span>
                                @else
                                    <span class="text-sm text-gray-500">No due date</span>
                                @endif
                            </div>
                            
                            <!-- Created -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Created</label>
                                <div class="text-sm text-gray-700">
                                    <div>{{ $task->created_at->format('M j, Y') }}</div>
                                    <div class="text-xs text-gray-500">by {{ $task->creator->name }}</div>
                                </div>
                            </div>
                            
                            <!-- Team -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Team</label>
                                <div class="flex items-center space-x-2">
                                    <div class="w-4 h-4 bg-blue-500 rounded text-white text-xs flex items-center justify-center">
                                        {{ substr($task->team->name, 0, 1) }}
                                    </div>
                                    <span class="text-sm text-gray-700">{{ $task->team->name }}</span>
                                </div>
                            </div>
                        </div>
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
</body>
</html>