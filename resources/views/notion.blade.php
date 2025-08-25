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
                        {{ substr(Auth::user()->name ?? 'N', 0, 1) }}
                    </div>
                    <span class="text-sm font-medium text-gray-900" id="workspace-name">Your Workspace</span>
                </div>
            </div>

            <!-- Search -->
            <div class="p-3">
                <div class="flex items-center space-x-2 px-3 py-2 bg-white border border-gray-200 rounded-md hover:border-gray-300 cursor-pointer">
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
                    <div class="flex items-center space-x-2 px-2 py-1 rounded hover:bg-gray-100 cursor-pointer" id="nav-home">
                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        <span class="text-sm text-gray-700">Home</span>
                    </div>
                    <div class="flex items-center space-x-2 px-2 py-1 rounded hover:bg-gray-100 cursor-pointer">
                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                        </svg>
                        <span class="text-sm text-gray-700">Inbox</span>
                    </div>
                </div>

                <!-- Private Section -->
                <div class="mb-4">
                    <div class="flex items-center justify-between px-2 py-1 mb-1">
                        <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Private</span>
                        <button class="w-4 h-4 text-gray-400 hover:text-gray-600" id="add-private">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                        </button>
                    </div>
                    <div class="space-y-1" id="private-pages">
                        <!-- Private pages will be loaded here -->
                    </div>
                </div>

                <!-- Teamspaces Section -->
                <div class="mb-4">
                    <div class="flex items-center justify-between px-2 py-1 mb-1">
                        <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Teamspaces</span>
                        <button class="w-4 h-4 text-gray-400 hover:text-gray-600" id="add-teamspace">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                        </button>
                    </div>
                    <div class="space-y-1" id="teamspaces">
                        <!-- Teamspaces will be loaded here -->
                    </div>
                </div>
            </div>

            <!-- Bottom Section -->
            <div class="border-t border-gray-200 p-3">
                <div class="flex items-center space-x-2 px-2 py-1 rounded hover:bg-gray-100 cursor-pointer mb-1">
                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span class="text-sm text-gray-700">Settings</span>
                </div>
                <div class="flex items-center space-x-2 px-2 py-1 rounded hover:bg-gray-100 cursor-pointer" id="invite-members">
                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <span class="text-sm text-gray-700">Invite members</span>
                </div>
                <div class="flex items-center space-x-2 px-2 py-1 rounded hover:bg-gray-100 cursor-pointer" id="logout-btn">
                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    <span class="text-sm text-gray-700">Logout</span>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Bar -->
            <div class="bg-white border-b border-gray-200 px-6 py-3" id="top-bar">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div id="page-icon" class="w-6 h-6 text-blue-500">
                            📋
                        </div>
                        <h1 id="page-title" class="text-lg font-semibold text-gray-900">Tasks</h1>
                    </div>
                    <div class="flex items-center space-x-2">
                        <!-- View Options -->
                        <div class="flex items-center space-x-1 bg-gray-100 rounded-md p-1">
                            <button class="px-2 py-1 text-xs rounded bg-white shadow-sm border border-gray-200 text-gray-700">Table</button>
                            <button class="px-2 py-1 text-xs rounded text-gray-500 hover:text-gray-700">Board</button>
                        </div>
                        <!-- Filters -->
                        <button onclick="showFilterDropdown()" class="flex items-center space-x-1 px-3 py-1 text-sm text-gray-600 hover:bg-gray-100 rounded">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.707A1 1 0 013 7V4z"/>
                            </svg>
                            <span>Filter</span>
                        </button>
                        <!-- Sort -->
                        <button class="flex items-center space-x-1 px-3 py-1 text-sm text-gray-600 hover:bg-gray-100 rounded">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"/>
                            </svg>
                            <span>Sort</span>
                        </button>
                        <!-- New Task Button -->
                        <button id="new-task-btn" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm font-medium">
                            New
                        </button>
                    </div>
                </div>
            </div>

            <!-- Task Database Table -->
            <div class="flex-1 overflow-auto bg-white" id="main-content">
                <!-- Table Header -->
                <div class="sticky top-0 bg-gray-50 border-b border-gray-200">
                    <table class="w-full">
                        <thead>
                            <tr class="text-left">
                                <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider w-8"></th>
                                <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider min-w-80">
                                    <div class="flex items-center space-x-1">
                                        <span>Name</span>
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21l3-3 3 3M7 3l3 3 3-3M4 8h16M4 16h16"/>
                                        </svg>
                                    </div>
                                </th>
                                <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider w-32">
                                    <div class="flex items-center space-x-1">
                                        <span>Priority</span>
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21l3-3 3 3M7 3l3 3 3-3M4 8h16M4 16h16"/>
                                        </svg>
                                    </div>
                                </th>
                                <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider w-32">
                                    <div class="flex items-center space-x-1">
                                        <span>Status</span>
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21l3-3 3 3M7 3l3 3 3-3M4 8h16M4 16h16"/>
                                        </svg>
                                    </div>
                                </th>
                                <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider w-40">
                                    <div class="flex items-center space-x-1">
                                        <span>Assignee</span>
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21l3-3 3 3M7 3l3 3 3-3M4 8h16M4 16h16"/>
                                        </svg>
                                    </div>
                                </th>
                                <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Due Date</th>
                                <th class="px-6 py-3 w-8"></th>
                            </tr>
                        </thead>
                    </table>
                </div>

                <!-- Table Body -->
                <div id="tasks-table-body">
                    <!-- Tasks will be loaded here -->
                </div>

                <!-- New Task Row -->
                <div class="border-l-4 border-transparent hover:border-blue-500 group">
                    <table class="w-full">
                        <tbody>
                            <tr id="new-task-row" class="hover:bg-gray-50 cursor-pointer">
                                <td class="px-6 py-3 w-8">
                                    <div class="w-4 h-4 border-2 border-gray-300 rounded group-hover:border-blue-500"></div>
                                </td>
                                <td class="px-6 py-3 text-sm text-gray-400">
                                    <span class="opacity-0 group-hover:opacity-100">+ New</span>
                                </td>
                                <td class="px-6 py-3"></td>
                                <td class="px-6 py-3"></td>
                                <td class="px-6 py-3"></td>
                                <td class="px-6 py-3"></td>
                                <td class="px-6 py-3"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Login/Register Modal -->
    <div id="auth-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <!-- Auth content will be loaded here -->
    </div>

    <!-- Task Detail Modal -->
    <div id="task-detail-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <!-- Task detail content will be loaded here -->
    </div>

    <!-- Messages -->
    <div id="message-container" class="fixed top-4 right-4 z-50"></div>

    <script type="module">
        // Include our notion.js content here
        @include('notion-js')
    </script>
</body>
</html>