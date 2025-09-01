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
            --text: #262624;        /* base text (kept for subtle items) */
            --text-dark: #2b2a28;   /* darker labels/icons per request */
            --icon-dark: #2b2a28;   /* darker icon color */
            --icon: #4b4a48;        /* fallback icon color */
            --muted: #9b9a97;
            --pill-bg: #f6f6f5;
            --scroll-thumb: rgba(75,73,70,0.10);
        }

        body { background: #ffffff; }

        .notion-sidebar {
            background: var(--sb-bg);
            border-right: 1px solid var(--sb-divider);
            width: 220px;
            min-width: 220px;
            max-width: 220px;
            display: flex;
            flex-direction: column;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            font-size: 14px;
            color: var(--text-dark); /* make default sidebar text darker */
        }

        /* scroll area inside sidebar */
        .notion-sidebar .sidebar-scroll {
            padding: 12px 10px;
            overflow-y: auto;
            height: 100%;
        }
        .notion-sidebar .sidebar-scroll::-webkit-scrollbar { width: 6px; }
        .notion-sidebar .sidebar-scroll::-webkit-scrollbar-track { background: transparent; }
        .notion-sidebar .sidebar-scroll::-webkit-scrollbar-thumb { background: var(--scroll-thumb); border-radius: 10px; }

        /* ensure sidebar svgs use the darker icon color */
        .notion-sidebar svg { color: var(--icon-dark); stroke: currentColor; fill: none; }

        /* Workspace header (top) */
        .workspace-header {
            display:flex;
            align-items:center;
            gap:10px;
            padding:6px 4px;
            border-radius:6px;
            color:var(--text-dark);
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
            color:var(--text-dark);
        }
        .workspace-edit { margin-left:auto; color:var(--icon-dark); }
        .workspace-edit svg { stroke: var(--icon-dark); }

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
            color:var(--text-dark);             /* darker label text */
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
            color:var(--icon-dark);             /* darker icon */
            background:transparent;
            flex-shrink:0;
        }
        .nav-item:hover { background: rgba(55,53,47,0.04); cursor:pointer; }

        /* make top nav labels darker and control top-nav gap exactly */
        .nav-list { display:flex; flex-direction:column; gap:3px; } /* exact 3px gap */
        .nav-item { margin-top:0; padding:6px 6px; } /* remove previously added margins */
        .nav-label { color: var(--text-dark); font-size:14px; display:inline-block; } /* darker labels */
        /* keep .muted for secondary/micro text only */
        .muted { color:var(--muted); font-size:13px; }

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
            color:var(--text-dark);             /* darker label text */
        }
        .teamspace-item .ts-icon {
            width:28px;
            height:28px;
            display:inline-flex;
            align-items:center;
            justify-content:center;
            border-radius:6px;
            flex-shrink:0;
            color:var(--icon-dark);             /* darker icon */
        }
        .ts-workspace { background:#FCEAD6; color:#C76A2B; }
        .ts-goals { background:#E6F2FF; color:#0074E4; }
        .ts-tasks { background:#E8F8EE; color:#00B34A; border-radius:50%; }

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
            color:var(--text-dark);             /* darker label */
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

        /* Footer items (now inside scroll area) -- small gap to match screenshot */
        .footer-block {
            margin-top:12px;
            border-top:1px solid var(--sb-divider);
            padding-top:10px;
            display:flex;
            flex-direction:column;
            gap:3px;              /* exact 3px gap */
        }
        .footer-item {
            display:flex;
            align-items:center;
            gap:12px;
            padding:6px 6px;
            border-radius:8px;
            color:var(--text-dark);     /* darker footer labels */
        }
        .footer-item .fi-icon {
            width:28px;
            height:28px;
            display:inline-flex;
            align-items:center;
            justify-content:center;
            color:var(--icon-dark);     /* darker footer icons */
            flex-shrink:0;
        }
        .footer-item:hover { background: rgba(55,53,47,0.03); cursor:pointer; }

        /* small utility tweaks */
        .muted { color:var(--muted); font-size:13px; } /* keep helper/muted texts lighter */
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
                    <div id="searchIconRow" class="nav-item" style="cursor:pointer;" onclick="openSidebarSearch()">
                        <div class="nav-icon" aria-hidden>
                            <svg id="searchIcon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#6b6a67" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="11" cy="11" r="7"></circle>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            </svg>
                        </div>
                        <div class="nav-label">Search</div>
                    </div>

                    <div id="searchExpand" class="hidden" style="margin-top:8px;">
                        <input id="sidebarSearchInput" type="text" placeholder="Search" aria-label="Search"
                               style="width:100%;padding:8px;border-radius:8px;border:1px solid var(--sb-divider);box-sizing:border-box;font-size:13px;">
                    </div>
                </div>

                <!-- Top nav (Search / Home / Inbox / Add new) -->
                <div class="nav-list" style="margin-top:6px;">
                    <div class="nav-item">
                        <div class="nav-icon" aria-hidden>
                            <!-- home icon outline -->
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#6b6a67" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 11.5L12 4l9 7.5"></path>
                                <path d="M5 21h14a1 1 0 0 0 1-1V11"></path>
                            </svg>
                        </div>
                        <div class="nav-label"><a href="{{ route('dashboard') }}">Home</a></div>
                    </div>

                    <div class="nav-item">
                        <div class="nav-icon" aria-hidden>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#6b6a67" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 10v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-6"></path>
                                <path d="M7 8V6a5 5 0 0 1 10 0v2"></path>
                            </svg>
                        </div>
                        <div class="nav-label"><a href="{{ route('notifications.index') }}">Inbox</a></div>
                        @if(auth()->user()->unreadNotifications()->count() > 0)
                            <span class="ml-auto bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">{{ auth()->user()->unreadNotifications()->count() }}</span>
                        @endif
                    </div>

                    <div class="nav-item">
                        <div class="nav-icon" aria-hidden>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#6b6a67" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        </div>
                        <div class="nav-label">Add new</div>
                    </div>
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

                    <div class="teamspace-item" onclick="showTaskTracker()" style="cursor:pointer;">
                        <div class="ts-icon ts-tasks" style="border-radius:6px;">
                            <!-- green check -->
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#00B34A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 6L9 17l-5-5"></path>
                            </svg>
                        </div>
                        <div class="muted">Tasks Tracker</div>
                    </div>

                    <div class="teamspace-item" onclick="showNewTaskTrackerModal()" style="cursor:pointer;">
                        <div class="ts-icon" style="color:#6b6a67">＋</div>
                        <div class="muted">Add new</div>
                    </div>

                    <!-- owned/shared pages compact list (moved here below Tasks Tracker) -->
                    <div style="margin-top:6px;margin-left:16px;">
                        @foreach($ownedPages as $taskPage)
                            <div class="teamspace-item" style="padding:4px 6px;cursor:pointer;position:relative;" onmouseover="showDeleteButton({{ $taskPage->id }})" onmouseout="hideDeleteButton({{ $taskPage->id }})">
                                <div onclick="showTaskTrackerPage({{ $taskPage->id }})" style="display:flex;align-items:center;gap:10px;flex:1;">
                                    <div class="ts-icon" style="background:#f1f1ef;color:#6b6a67;width:20px;height:20px;font-size:12px;">{{ $taskPage->icon }}</div>
                                    <div class="muted" style="max-width:100px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:13px;">{{ $taskPage->name }}</div>
                                </div>
                                <button id="deleteBtn{{ $taskPage->id }}" onclick="deleteTaskTrackerPage({{ $taskPage->id }}, '{{ $taskPage->name }}')" 
                                        style="display:none;position:absolute;right:6px;top:50%;transform:translateY(-50%);background:none;border:none;color:#dc2626;cursor:pointer;padding:2px;" 
                                        title="Delete page">
                                    <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        @endforeach

                        @if($sharedPages->count() > 0)
                            <div style="margin-top:8px;">
                                <div class="muted" style="font-size:11px;margin-bottom:4px;margin-left:4px;">Shared with me</div>
                                @foreach($sharedPages as $taskPage)
                                    <div class="teamspace-item" onclick="showTaskTrackerPage({{ $taskPage->id }})" style="padding:4px 6px;cursor:pointer;">
                                        <div class="ts-icon" style="background:#f8f9f7;color:#6b6a67;width:20px;height:20px;font-size:12px;">{{ $taskPage->icon }}</div>
                                        <div class="muted" style="max-width:100px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:13px;">{{ $taskPage->name }}</div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
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

                <!-- Settings / Marketplace / Trash / Invite / Logout moved into same scroll area -->
                <div class="footer-block">
                    <a href="#" class="footer-item"><div class="fi-icon">⚙️</div><div class="muted">Settings</div></a>
                    <a href="#" class="footer-item"><div class="fi-icon">🛒</div><div class="muted">Marketplace</div></a>
                    <a href="{{ route('trash.index') }}" class="footer-item"><div class="fi-icon">🗑️</div><div class="muted">Trash</div></a>

                    <div style="border-top:1px solid transparent;margin:6px 0;"></div>

                    @if(isset($currentTeam))
                    <details>
                        <summary class="footer-item" style="list-style:none;cursor:pointer;">
                            <div class="fi-icon">
                                <svg width="16" height="16" viewBox="0 0 24 24" aria-hidden>
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
                            <svg width="16" height="16" viewBox="0 0 24 24" aria-hidden>
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
                                <svg width="16" height="16" viewBox="0 0 24 24" aria-hidden>
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
        <div class="flex-1 flex flex-col overflow-hidden bg-white" id="mainDashboardContent">
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

        <!-- Task Tracker Content Section (Hidden by default) -->
        <div id="taskTrackerContent" class="flex-1 flex flex-col overflow-hidden bg-white" style="display: none;">
            <!-- Top Bar -->
            <div class="border-b notion-border px-6 py-6">
                <div class="flex items-start space-x-4">
                    <span class="text-4xl">✅</span>
                    <div class="flex-1">
                        <h1 class="text-3xl font-bold notion-text">Tasks Tracker</h1>
                        <p class="text-sm notion-gray mt-1">Stay organized with tasks, your way.</p>
                    </div>
                </div>
            </div>

            <!-- View Tabs -->
            <div class="px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <a href="#" onclick="switchTaskView('all')" id="allTasksTab"
                           class="view-tab active">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2L13.09 8.26L22 9L17 14L18.18 22L12 19L5.82 22L7 14L2 9L10.91 8.26L12 2Z"/>
                            </svg>
                            <span>All Tasks</span>
                        </a>
                        <a href="#" onclick="switchTaskView('by_status')" id="byStatusTab"
                           class="view-tab">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M3 13H15V11H3M3 6V8H21V6M3 18H9V16H3V18Z"/>
                            </svg>
                            <span>By Status</span>
                        </a>
                        <a href="#" onclick="switchTaskView('my_tasks')" id="myTasksTab"
                           class="view-tab">
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
            <div class="flex-1 overflow-x-auto overflow-y-auto px-6" id="taskTrackerDisplay">
                <!-- Task content will be dynamically loaded here -->
                <div class="p-6 text-center notion-gray">
                    <div class="text-4xl mb-4">📝</div>
                    <p class="text-lg font-medium">Loading tasks...</p>
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

    <!-- New Task Tracker Page Modal -->
    <div id="newTaskTrackerModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" style="display: none;" onclick="hideNewTaskTrackerModal()">
        <div class="bg-white rounded-lg p-6 w-96 max-w-md" onclick="event.stopPropagation()">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold notion-text">Create New Task Tracker Page</h3>
                <button onclick="hideNewTaskTrackerModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            <form action="{{ route('task-tracker-page.store') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="redirect_to" value="dashboard">
                
                <div>
                    <label class="block text-sm font-medium notion-text mb-2">Page Name</label>
                    <input type="text" name="name" placeholder="Enter page name" required
                           class="w-full px-3 py-2 border notion-border rounded focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                </div>
                
                <div>
                    <label class="block text-sm font-medium notion-text mb-2">Icon (optional)</label>
                    <input type="text" name="icon" placeholder="📋" maxlength="2"
                           class="w-full px-3 py-2 border notion-border rounded focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                    <p class="text-xs text-gray-500 mt-1">Choose an emoji icon for your page</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium notion-text mb-2">Description (optional)</label>
                    <textarea name="description" rows="3" placeholder="Brief description of this task tracker page"
                              class="w-full px-3 py-2 border notion-border rounded focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm resize-none"></textarea>
                </div>
                
                <div class="flex justify-end space-x-2 pt-4">
                    <button type="button" onclick="hideNewTaskTrackerModal()" 
                            class="px-4 py-2 text-sm border notion-border rounded hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 text-sm bg-blue-500 text-white rounded hover:bg-blue-600">
                        Create Page
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Task Tracker Content Section (Hidden by default) -->
    <div id="taskTrackerContent" class="flex-1 flex flex-col overflow-hidden bg-white" style="display: none;">
        <!-- Top Bar -->
        <div class="border-b notion-border px-6 py-6">
            <div class="flex items-start space-x-4">
                <span class="text-4xl">✅</span>
                <div class="flex-1">
                    <h1 class="text-3xl font-bold notion-text">Tasks Tracker</h1>
                    <p class="text-sm notion-gray mt-1">Stay organized with tasks, your way.</p>
                </div>
            </div>
        </div>

        <!-- View Tabs -->
        <div class="px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <a href="#" onclick="switchTaskView('all')" id="allTasksTab"
                       class="view-tab active">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2L13.09 8.26L22 9L17 14L18.18 22L12 19L5.82 22L7 14L2 9L10.91 8.26L12 2Z"/>
                        </svg>
                        <span>All Tasks</span>
                    </a>
                    <a href="#" onclick="switchTaskView('by_status')" id="byStatusTab"
                       class="view-tab">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M3 13H15V11H3M3 6V8H21V6M3 18H9V16H3V18Z"/>
                        </svg>
                        <span>By Status</span>
                    </a>
                    <a href="#" onclick="switchTaskView('my_tasks')" id="myTasksTab"
                       class="view-tab">
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
        <div class="flex-1 overflow-x-auto overflow-y-auto px-6" id="taskTrackerDisplay">
            <!-- Task content will be dynamically loaded here -->
            <div class="p-6 text-center notion-gray">
                <div class="text-4xl mb-4">📝</div>
                <p class="text-lg font-medium">Loading tasks...</p>
            </div>
        </div>
    </div>

    <style>
        /* Task Tracker Styles - copied from index.blade.php */
        .font-ui-sans-serif { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; }
        .antialiased { -webkit-font-smoothing: antialiased; -moz-osx-font-smoothing: grayscale; }
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
        
        .excel-box {
            background: white;
            border: 1px solid #e2e6ea;
            border-radius: 3px;
            padding: 8px;
            min-height: 36px;
            display: flex;
            align-items: center;
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
    </style>

    <script>
        // Task Tracker Functions
        let currentTaskView = 'all';
        let taskTrackerData = null;

        function showTaskTracker() {
            // Hide the main dashboard content
            const mainContent = document.getElementById('mainDashboardContent');
            if (mainContent) {
                mainContent.style.display = 'none';
            }

            // Show the task tracker content
            const taskTrackerContent = document.getElementById('taskTrackerContent');
            if (taskTrackerContent) {
                taskTrackerContent.style.display = 'flex';
                
                // Load task tracker data if not already loaded
                if (!taskTrackerData) {
                    loadTaskTrackerData();
                }
            }
        }

        function hideTaskTracker() {
            // Hide the task tracker content
            const taskTrackerContent = document.getElementById('taskTrackerContent');
            if (taskTrackerContent) {
                taskTrackerContent.style.display = 'none';
            }

            // Show the main dashboard content
            const mainContent = document.getElementById('mainDashboardContent');
            if (mainContent) {
                mainContent.style.display = 'flex';
            }
        }

        function switchTaskView(view) {
            currentTaskView = view;
            
            // Update active tab
            document.querySelectorAll('.view-tab').forEach(tab => tab.classList.remove('active'));
            
            if (view === 'all') {
                document.getElementById('allTasksTab').classList.add('active');
            } else if (view === 'by_status') {
                document.getElementById('byStatusTab').classList.add('active');
            } else if (view === 'my_tasks') {
                document.getElementById('myTasksTab').classList.add('active');
            }
            
            // Reload task display with new view
            loadTaskTrackerData();
        }

        function loadTaskTrackerData() {
            const display = document.getElementById('taskTrackerDisplay');
            if (!display) return;
            
            // Show loading state
            display.innerHTML = `
                <div class="p-6 text-center notion-gray">
                    <div class="text-4xl mb-4">⏳</div>
                    <p class="text-lg font-medium">Loading tasks...</p>
                </div>
            `;
            
            // Fetch task tracker data via AJAX
            const url = new URL('{{ route("task-tracker.index") }}', window.location.origin);
            url.searchParams.append('view', currentTaskView);
            url.searchParams.append('ajax', '1');
            
            fetch(url)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    taskTrackerData = data;
                    renderTaskTracker(data);
                })
                .catch(error => {
                    console.error('Error loading task tracker:', error);
                    display.innerHTML = `
                        <div class="p-6 text-center notion-gray">
                            <div class="text-4xl mb-4">❌</div>
                            <p class="text-lg font-medium">Error loading tasks: ${error.message}</p>
                            <button onclick="loadTaskTrackerData()" class="mt-2 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                                Retry
                            </button>
                        </div>
                    `;
                });
        }

        function renderTaskTracker(data) {
            const display = document.getElementById('taskTrackerDisplay');
            if (!display) return;
            
            if (currentTaskView === 'by_status' && data.groupedTasks) {
                renderStatusView(data.groupedTasks);
            } else {
                renderTableView(data.taskTrackers || []);
            }
        }

        function renderStatusView(groupedTasks) {
            // Implementation for status view would go here
            // For now, showing table view as fallback
            renderTableView(groupedTasks.all || []);
        }

        function renderTableView(tasks) {
            const display = document.getElementById('taskTrackerDisplay');
            
            if (tasks.length === 0) {
                display.innerHTML = `
                    <div class="p-6 text-center notion-gray">
                        <div class="text-4xl mb-4">📝</div>
                        <p class="text-lg font-medium">No tasks yet</p>
                        <p class="text-sm">Click "New" to create your first task tracker item</p>
                    </div>
                `;
                return;
            }
            
            // Render the Excel-like table
            let tableHTML = `
                <table class="min-w-max excel-table">
                    <thead class="sticky top-0">
                        <tr>
                            <th class="text-left font-normal w-60">
                                <div class="flex items-center space-x-1 cursor-pointer hover:bg-gray-50 px-1 py-1 rounded" onclick="openNameEdit()">
                                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    <span id="displayName" class="text-xs font-medium">{{ auth()->user()->name }}</span>
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
                            <th class="text-left font-normal w-32"><span class="text-sm">Assignee</span></th>
                            <th class="text-left font-normal w-32"><span class="text-sm">Due date</span></th>
                            <th class="text-left font-normal w-28"><span class="text-sm">Priority</span></th>
                            <th class="text-left font-normal w-36"><span class="text-sm">Task type</span></th>
                            <th class="text-left font-normal w-48"><span class="text-sm">Description</span></th>
                            <th class="text-left font-normal w-32"><span class="text-sm">Effort level</span></th>
                            <th class="text-left font-normal w-16"><span class="text-sm">Actions</span></th>
                        </tr>
                    </thead>
                    <tbody>
            `;
            
            tasks.forEach(task => {
                tableHTML += `
                    <tr class="hover:bg-gray-100">
                        <td class="table-cell font-normal">
                            <div class="editable-field">${task.name}</div>
                        </td>
                        <td class="table-cell">
                            <span class="inline-flex items-center text-sm font-normal px-2 py-1 rounded-full ${
                                task.status === 'not_started' ? 'bg-gray-100 text-gray-700' :
                                task.status === 'in_progress' ? 'bg-blue-100 text-blue-700' :
                                'bg-green-100 text-green-700'
                            }">
                                <span class="w-2 h-2 rounded-full mr-2 ${
                                    task.status === 'not_started' ? 'bg-gray-500' :
                                    task.status === 'in_progress' ? 'bg-blue-500' :
                                    'bg-green-500'
                                }"></span>
                                ${
                                    task.status === 'not_started' ? 'Not started' :
                                    task.status === 'in_progress' ? 'In progress' :
                                    'Done'
                                }
                            </span>
                        </td>
                        <td class="table-cell">
                            <div class="editable-field text-sm">${task.assignee || 'Unassigned'}</div>
                        </td>
                        <td class="table-cell">
                            <div class="editable-field">${task.due_date || ''}</div>
                        </td>
                        <td class="table-cell">
                            <span class="px-2 py-1 rounded text-xs font-medium priority-${task.priority}">
                                ${task.priority.charAt(0).toUpperCase() + task.priority.slice(1)}
                            </span>
                        </td>
                        <td class="table-cell">
                            <span class="px-2 py-1 rounded text-xs font-medium task-type-${task.task_type}">
                                ${task.task_type_icon || ''} ${task.task_type.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())}
                            </span>
                        </td>
                        <td class="table-cell">
                            <div class="editable-field text-sm">${task.description || 'No description'}</div>
                        </td>
                        <td class="table-cell">
                            <span class="px-2 py-1 rounded text-xs font-medium effort-${task.effort_level}">
                                ${task.effort_level.charAt(0).toUpperCase() + task.effort_level.slice(1)}
                            </span>
                        </td>
                        <td class="table-cell">
                            <form action="/task-tracker/${task.id}" method="POST" class="inline" onsubmit="return confirm('Delete this task?')">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" class="text-red-400 hover:text-red-600 p-1 rounded hover:bg-red-50" title="Delete task">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        </td>
                    </tr>
                `;
            });
            
            tableHTML += `
                        <tr class="table-row">
                            <td colspan="9">
                                <button class="flex items-center space-x-2 text-sm notion-gray hover:notion-text py-2" onclick="document.querySelector('details summary').click()">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                    </svg>
                                    <span>New task</span>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            `;
            
            display.innerHTML = tableHTML;
        }

        // Load saved name from localStorage on page load
        document.addEventListener('DOMContentLoaded', function() {
            const pageKey = 'taskTrackerHeaderName_main';
            const savedName = localStorage.getItem(pageKey);
            if (savedName) {
                const displayNameEl = document.getElementById('displayName');
                if (displayNameEl) {
                    displayNameEl.textContent = savedName;
                }
            }
        });

        function openNameEdit() {
            const pageKey = 'taskTrackerHeaderName_main';
            const displayNameEl = document.getElementById('displayName');
            if (!displayNameEl) return;
            
            const currentName = displayNameEl.textContent;
            const newName = prompt('Enter name:', currentName);
            if (newName && newName.trim()) {
                displayNameEl.textContent = newName.trim();
                localStorage.setItem(pageKey, newName.trim());
            }
        }

        function showNewTaskTrackerModal() {
            const modal = document.getElementById('newTaskTrackerModal');
            if (modal) {
                modal.style.display = 'flex';
            }
        }

        function hideNewTaskTrackerModal() {
            const modal = document.getElementById('newTaskTrackerModal');
            if (modal) {
                modal.style.display = 'none';
                // Reset form
                const form = modal.querySelector('form');
                if (form) {
                    form.reset();
                }
            }
        }

        function showTaskTrackerPage(pageId) {
            // Hide the main dashboard content
            const mainContent = document.getElementById('mainDashboardContent');
            if (mainContent) {
                mainContent.style.display = 'none';
            }

            // Show the task tracker content
            const taskTrackerContent = document.getElementById('taskTrackerContent');
            if (taskTrackerContent) {
                taskTrackerContent.style.display = 'flex';
                
                // Load tasks for the specific page
                loadTaskTrackerPageData(pageId);
            }
        }

        function loadTaskTrackerPageData(pageId) {
            const display = document.getElementById('taskTrackerDisplay');
            if (!display) return;
            
            // Show loading state
            display.innerHTML = `
                <div class="p-6 text-center notion-gray">
                    <div class="text-4xl mb-4">⏳</div>
                    <p class="text-lg font-medium">Loading page tasks...</p>
                </div>
            `;
            
            // Fetch task tracker page data via URL
            const url = `/task-tracker-page/${pageId}?ajax=1&view=${currentTaskView}`;
            
            fetch(url)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    taskTrackerData = data;
                    renderTaskTracker(data);
                })
                .catch(error => {
                    console.error('Error loading task tracker page:', error);
                    display.innerHTML = `
                        <div class="p-6 text-center notion-gray">
                            <div class="text-4xl mb-4">❌</div>
                            <p class="text-lg font-medium">Error loading page: ${error.message}</p>
                            <button onclick="loadTaskTrackerPageData(${pageId})" class="mt-2 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                                Retry
                            </button>
                        </div>
                    `;
                });
        }

        function showDeleteButton(pageId) {
            const deleteBtn = document.getElementById('deleteBtn' + pageId);
            if (deleteBtn) {
                deleteBtn.style.display = 'block';
            }
        }

        function hideDeleteButton(pageId) {
            const deleteBtn = document.getElementById('deleteBtn' + pageId);
            if (deleteBtn) {
                deleteBtn.style.display = 'none';
            }
        }

        function deleteTaskTrackerPage(pageId, pageName) {
            if (!confirm(`Are you sure you want to delete "${pageName}"? This action cannot be undone and will also delete all tasks in this page.`)) {
                return;
            }

            // Create a form and submit it
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/task-tracker-page/${pageId}`;
            form.style.display = 'none';

            // Add CSRF token
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            form.appendChild(csrfToken);

            // Add DELETE method
            const methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'DELETE';
            form.appendChild(methodField);

            document.body.appendChild(form);
            form.submit();
        }

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