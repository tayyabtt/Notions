<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Notifications - {{ config('app.name', 'Notions') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .font-ui-sans-serif { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; }
        .antialiased { -webkit-font-smoothing: antialiased; -moz-osx-font-smoothing: grayscale; }
    </style>
</head>
<body class="bg-gray-50 text-gray-900 font-ui-sans-serif antialiased">
    <div class="min-h-screen">
        <!-- Header -->
        <div class="bg-white border-b border-gray-200 px-6 py-4">
            <div class="flex items-center justify-between max-w-4xl mx-auto">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </a>
                    <div class="flex items-center space-x-2">
                        <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-2.21-2.21A8 8 0 1115 17z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.73 21a2 2 0 01-3.46 0"/>
                        </svg>
                        <h1 class="text-xl font-semibold text-gray-900">Notifications</h1>
                    </div>
                </div>
                
                @if($notifications->where('read_at', null)->count() > 0)
                <div>
                    <form action="{{ route('notifications.mark-all-read') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-blue-600 hover:text-blue-800 text-sm">
                            Mark all as read
                        </button>
                    </form>
                </div>
                @endif
            </div>
        </div>

        <!-- Content -->
        <div class="max-w-4xl mx-auto py-8 px-6">
            @if($notifications->count() > 0)
                <div class="space-y-4">
                    @foreach($notifications as $notification)
                        <div class="bg-white rounded-lg border border-gray-200 {{ $notification->isUnread() ? 'border-l-4 border-l-blue-500' : '' }}">
                            <div class="p-6">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-2 mb-2">
                                            <h3 class="text-sm font-medium text-gray-900">{{ $notification->title }}</h3>
                                            @if($notification->isUnread())
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    New
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-sm text-gray-600 mb-3">{{ $notification->message }}</p>
                                        <div class="text-xs text-gray-500">
                                            {{ $notification->created_at->diffForHumans() }}
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center space-x-2 ml-4">
                                        @if($notification->notifiable_type === 'App\Models\Task' && $notification->notifiable_id)
                                            <a href="{{ route('tasks.show', $notification->notifiable_id) }}" 
                                               class="text-blue-600 hover:text-blue-800 text-sm">
                                                View Task
                                            </a>
                                        @endif
                                        
                                        @if($notification->isUnread())
                                            <form action="{{ route('notifications.mark-read', $notification->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="text-gray-400 hover:text-gray-600">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($notifications->hasPages())
                    <div class="mt-8">
                        {{ $notifications->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-12">
                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-2.21-2.21A8 8 0 1115 17z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.73 21a2 2 0 01-3.46 0"/>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No notifications yet</h3>
                    <p class="text-gray-500">You'll see notifications here when team members interact with your tasks.</p>
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