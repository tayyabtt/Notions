<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Notion') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* ---------- Sidebar: pixel-accurate styles to match screenshot ---------- */
        :root{
            --sb-bg: #fbfbfa;
            --sb-divider: #ecebe9;
            --text: #37352f;
            --muted: #9b9a97;
            --pill-bg: #ffffff;
        }

        body { background: #ffffff; }

        .notion-sidebar {
            background: var(--sb-bg);
            border-right: 1px solid var(--sb-divider);
            width: 220px;               /* screenshot width */
            min-width: 220px;
            max-width: 220px;
            display: flex;
            flex-direction: column;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            font-size: 14px;
            color: var(--text);
            height: 100vh;
            box-sizing: border-box;
            overflow: hidden;
        }

        /* scroll area inside sidebar */
        .notion-sidebar .sidebar-scroll {
            padding: 12px 10px;
            overflow-y: auto;
            height: 100%;
        }
        .notion-sidebar .sidebar-scroll::-webkit-scrollbar { width: 6px; }
        .notion-sidebar .sidebar-scroll::-webkit-scrollbar-track { background: transparent; }
        .notion-sidebar .sidebar-scroll::-webkit-scrollbar-thumb { background: rgba(55,53,47,0.08); border-radius: 10px; }

        /* Workspace header (top) */
        .workspace-header {
            display:flex;
            align-items:center;
            gap:10px;
            padding:6px 4px;
            border-radius:6px;
            color:var(--text);
            font-weight:600;
            font-size:13px;
            white-space:nowrap;
            overflow:hidden;
            text-overflow:ellipsis;
        }
        .workspace-avatar {
            width:30px;
            height:30px;
            border-radius:6px;
            background:#e9e6e2;
            display:inline-flex;
            align-items:center;
            justify-content:center;
            color:#6b6a67;
            font-weight:700;
        }
        .workspace-title {
            display:inline-block;
            max-width:130px;
            overflow:hidden;
            white-space:nowrap;
            text-overflow:ellipsis;
            vertical-align:middle;
        }
        .workspace-edit {
            margin-left:auto;
            color:#6b6a67;
        }

        /* compact search row */
        .sidebar-search {
            display:flex;
            align-items:center;
            gap:8px;
            padding:8px;
            margin-top:10px;
            background:#ffffff;
            border:1px solid var(--sb-divider);
            border-radius:8px;
            color:var(--muted);
            font-size:13px;
        }

        /* top nav items (Home/Inbox/Add new) */
        .nav-item {
            display:flex;
            align-items:center;
            gap:12px;
            padding:8px 6px;
            border-radius:8px;
            color:var(--text);
            font-size:14px;
            margin-top:8px;
        }
        .nav-item .nav-icon {
            width:28px;
            height:28px;
            display:inline-flex;
            align-items:center;
            justify-content:center;
            border-radius:6px;
            color:#6b6a67;
            background:transparent;
            flex-shrink:0;
        }
        .nav-item:hover { background: rgba(55,53,47,0.04); cursor:pointer; }

        /* Section title (Teamspaces / Shared) */
        .sidebar-section-title {
            font-size:12px;
            color:var(--muted);
            margin-top:16px;
            margin-bottom:8px;
            padding:0 4px;
        }

        /* Teamspace item */
        .teamspace-item {
            display:flex;
            align-items:center;
            gap:10px;
            padding:8px 6px;
            border-radius:8px;
            font-size:14px;
            color:var(--text);
        }
        .teamspace-item .ts-icon {
            width:28px;
            height:28px;
            display:inline-flex;
            align-items:center;
            justify-content:center;
            border-radius:6px;
            flex-shrink:0;
        }
        .ts-workspace { background:#FCEAD6; color:#C76A2B; }    /* orange tile */
        .ts-goals { background:#E6F2FF; color:#0074E4; }        /* blue tile */
        .ts-tasks { background:#E8F8EE; color:#00B34A; border-radius:50%; } /* green circle */

        .teamspace-item:hover { background: rgba(55,53,47,0.04); cursor:pointer; }

        /* Selected/shared pill (To Do List) */
        .shared-pill {
            display:flex;
            align-items:center;
            gap:12px;
            padding:8px 10px;
            margin-top:6px;
            border-radius:8px;
            background: var(--pill-bg);
            box-shadow: 0 0 0 1px rgba(0,0,0,0.03);
            font-weight:600;
        }
        .shared-pill .pill-icon {
            width:28px;
            height:28px;
            display:inline-flex;
            align-items:center;
            justify-content:center;
            border-radius:6px;
            color:#117A4D;
            background:transparent;
            flex-shrink:0;
        }

        /* Footer items */
        .sidebar-footer {
            padding:12px 10px;
            border-top:1px solid var(--sb-divider);
            display:flex;
            flex-direction:column;
            gap:8px;
        }
        .footer-item {
            display:flex;
            align-items:center;
            gap:12px;
            padding:8px 6px;
            border-radius:8px;
            color:var(--text);
        }
        .footer-item .fi-icon { width:28px; height:28px; display:inline-flex; align-items:center; justify-content:center; color:#6b6a67; }

        /* small utility tweaks */
        .muted { color:var(--muted); font-size:13px; }
        .ml-auto { margin-left:auto; }
        a { text-decoration:none; color:inherit; }
    </style>
</head>
<body class="bg-white text-gray-900 font-ui-sans-serif antialiased">
    <div class="flex h-screen overflow-hidden">
        <!-- Left Sidebar -->
        <div class="notion-sidebar">
            <div class="sidebar-scroll">
                {{-- Ensure variables exist when controller doesn't provide them --}}
                @php
                    $ownedPages  = $ownedPages  ?? collect();
                    $sharedPages = $sharedPages ?? collect();
                    $page        = $page ?? null;
                @endphp

                <!-- Workspace header -->
                <div class="workspace-header">
                    <div class="workspace-avatar">T</div>
                    <div class="workspace-title">{{ auth()->user()->name }}'s Workspace</div>
                    <div class="workspace-edit" title="Edit workspace">
                        <!-- pencil icon -->
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#6b6a67" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden>
                            <path d="M12 20h9" />
                            <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z" />
                        </svg>
                    </div>
                </div>

                <!-- Search icon / expandable search -->
                <div style="margin-top:8px;">
                    <div id="searchIconRow" class="nav-item" style="padding:6px 8px;cursor:pointer;" onclick="openSidebarSearch()">
                        <div class="nav-icon" aria-hidden>
                            <svg id="searchIcon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#6b6a67" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="11" cy="11" r="7"></circle>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            </svg>
                        </div>
                        <div class="muted">Search</div>
                    </div>

                    <div id="searchExpand" class="hidden" style="margin-top:8px;">
                        <input id="sidebarSearchInput" type="text" placeholder="Search" aria-label="Search"
                               style="width:100%;padding:8px;border-radius:8px;border:1px solid var(--sb-divider);box-sizing:border-box;font-size:13px;">
                    </div>
                </div>

                <!-- Top nav (Home / Inbox / Add new) -->
                <div class="nav-item" style="margin-top:12px;">
                    <div class="nav-icon" aria-hidden>
                        <!-- home icon outline -->
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#6b6a67" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 11.5L12 4l9 7.5"></path>
                            <path d="M5 21h14a1 1 0 0 0 1-1V11"></path>
                        </svg>
                    </div>
                    <a href="{{ route('dashboard') }}" class="muted">Home</a>
                </div>

                <div class="nav-item">
                    <div class="nav-icon" aria-hidden>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#6b6a67" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 10v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-6"></path>
                            <path d="M7 8V6a5 5 0 0 1 10 0v2"></path>
                        </svg>
                    </div>
                    <a href="{{ route('notifications.index') }}" class="muted">Inbox</a>
                    @if(auth()->user()->unreadNotifications()->count() > 0)
                        <span class="ml-auto bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">{{ auth()->user()->unreadNotifications()->count() }}</span>
                    @endif
                </div>

                <div class="nav-item">
                    <div class="nav-icon" aria-hidden>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#6b6a67" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    </div>
                    <div class="muted">Add new</div>
                </div>

                <!-- Divider / Section -->
                <div class="sidebar-section-title">Teamspaces</div>

                <!-- Teamspaces list -->
                <div style="display:flex;flex-direction:column;gap:6px;">
                    <a class="teamspace-item" href="#">
                        <div class="ts-icon ts-workspace">
                            <!-- orange house -->
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#C76A2B" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 11.5L12 4l9 7.5"></path>
                                <path d="M5 21h14a1 1 0 0 0 1-1V11"></path>
                            </svg>
                        </div>
                        <div style="font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ auth()->user()->name }}'s Workspace</div>
                    </a>

                    <a class="teamspace-item" href="#">
                        <div class="ts-icon ts-goals">
                            <!-- goals chart -->
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#0074E4" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 3v18h18"></path>
                                <path d="M7 13l4-4 4 8 4-10"></path>
                            </svg>
                        </div>
                        <div class="muted">Goals</div>
                    </a>

                    <a class="teamspace-item" href="{{ route('task-tracker.index') }}">
                        <div class="ts-icon ts-tasks" style="border-radius:6px;">
                            <!-- green check -->
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#00B34A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 6L9 17l-5-5"></path>
                            </svg>
                        </div>
                        <div class="muted">Tasks Tracker</div>
                    </a>

                    <a class="teamspace-item" href="#">
                        <div class="ts-icon" style="color:#6b6a67">＋</div>
                        <div class="muted">Add new</div>
                    </a>
                </div>

                <!-- Shared -->
                <div class="sidebar-section-title">Shared</div>

                <a href="{{ route('todo.index') }}" class="shared-pill" style="margin-bottom:6px;">
                    <div class="pill-icon">
                        <!-- green list icon -->
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#117A4D" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M8 6h13"></path>
                            <path d="M8 12h13"></path>
                            <path d="M8 18h13"></path>
                            <path d="M3 6h.01"></path>
                            <path d="M3 12h.01"></path>
                            <path d="M3 18h.01"></path>
                        </svg>
                    </div>
                    <div>To Do List</div>
                </a>

                <!-- owned/shared pages compact list (keeps existing data) -->
                <div style="margin-top:6px;">
                    @foreach($ownedPages as $taskPage)
                        <a href="{{ route('task-tracker-page.show', $taskPage) }}" class="teamspace-item" style="padding:6px;">
                            <div class="ts-icon" style="background:#f1f1ef;color:#6b6a67">{{ $taskPage->icon }}</div>
                            <div class="muted" style="max-width:120px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $taskPage->name }}</div>
                        </a>
                    @endforeach

                    @if($sharedPages->count() > 0)
                        <div style="margin-top:8px;">
                            <div class="muted" style="font-size:12px;margin-bottom:6px;">Shared with me</div>
                            @foreach($sharedPages as $taskPage)
                                <a href="{{ route('task-tracker-page.show', $taskPage) }}" class="teamspace-item" style="padding:6px;">
                                    <div class="ts-icon" style="background:#f8f9f7;color:#6b6a67">{{ $taskPage->icon }}</div>
                                    <div class="muted" style="max-width:120px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $taskPage->name }}</div>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Settings / Marketplace / Trash / Invite / Logout moved into same scroll area -->
                <div style="margin-top:12px;border-top:1px solid var(--sb-divider);padding-top:10px;display:flex;flex-direction:column;gap:6px;">
                    <a href="#" class="footer-item"><div class="fi-icon">⚙️</div><div class="muted">Settings</div></a>
                    <a href="#" class="footer-item"><div class="fi-icon">🛒</div><div class="muted">Marketplace</div></a>
                    <a href="{{ route('trash.index') }}" class="footer-item"><div class="fi-icon">🗑️</div><div class="muted">Trash</div></a>

                    <div style="border-top:1px solid transparent;margin:6px 0;"></div>

                    @if(isset($currentTeam))
                    <details>
                        <summary class="footer-item" style="list-style:none;cursor:pointer;">
                            <div class="fi-icon">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#6b6a67" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M17 20h5v-2a3 3 0 00-5.196-2.121L17 20zM9 12h6m-6 4h6m0-8h3.586a1 1 0 01.707.293L21 10"/>
                                </svg>
                            </div>
                            <div class="muted">Invite members</div>
                        </summary>
                        <div style="margin-top:8px;padding:0 6px;">
                            <form action="{{ route('teams.invite', $currentTeam->id) }}" method="POST" style="display:flex;flex-direction:column;gap:8px;">
                                @csrf
                                <input type="email" name="email" placeholder="Email address" required
                                       style="width:100%;padding:6px 8px;font-size:13px;border:1px solid var(--sb-divider);border-radius:6px;background:#ffffff;">
                                <button type="submit" style="padding:6px 8px;font-size:13px;background:#0074E4;color:#ffffff;border:none;border-radius:6px;cursor:pointer;">
                                    Send Invitation
                                </button>
                            </form>
                        </div>
                    </details>
                    @else
                    <div class="footer-item" style="opacity:0.6;">
                        <div class="fi-icon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#6b6a67" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17 20h5v-2a3 3 0 00-5.196-2.121L17 20zM9 12h6m-6 4h6m0-8h3.586a1 1 0 01.707.293L21 10"/>
                            </svg>
                        </div>
                        <div class="muted">Invite members</div>
                    </div>
                    @endif

                    <form action="{{ route('logout') }}" method="POST" style="margin-top:6px;">
                        @csrf
                        <button type="submit" class="footer-item" style="width:100%;background:none;border:none;text-align:left;cursor:pointer;color:inherit;">
                            <div class="fi-icon">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#6b6a67" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                            </div>
                            <div class="muted">Logout</div>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Footer (kept empty to avoid fixed layout) -->
            <div class="sidebar-footer" style="display:none;"></div>
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

    <script>
        function openSidebarSearch() {
            const iconRow = document.getElementById('searchIconRow');
            const expand = document.getElementById('searchExpand');
            const input = document.getElementById('sidebarSearchInput');

            if (!expand || !iconRow) return;

            // toggle visibility
            if (expand.classList.contains('hidden')) {
                expand.classList.remove('hidden');
                iconRow.classList.add('hidden');
                // focus input after it becomes visible
                setTimeout(() => input && input.focus(), 50);
            } else {
                expand.classList.add('hidden');
                iconRow.classList.remove('hidden');
            }
        }

        // Close search on Escape or blur when focused out
        document.addEventListener('DOMContentLoaded', function () {
            const input = document.getElementById('sidebarSearchInput');
            if (!input) return;
            input.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    document.getElementById('searchExpand').classList.add('hidden');
                    document.getElementById('searchIconRow').classList.remove('hidden');
                }
            });
            input.addEventListener('blur', function () {
                // small timeout so clicks on results (if implemented) won't immediately close it
                setTimeout(() => {
                    document.getElementById('searchExpand').classList.add('hidden');
                    document.getElementById('searchIconRow').classList.remove('hidden');
                }, 150);
            });
        });
    </script>
</body>
</html>