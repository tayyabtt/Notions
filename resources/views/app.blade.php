<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Notions') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen">
    <div id="app">
        <!-- Navigation -->
        <nav class="bg-white shadow-sm border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <h1 class="text-xl font-bold text-gray-900">Notions</h1>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div id="auth-buttons" class="hidden">
                            <button id="logout-btn" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">
                                Logout
                            </button>
                        </div>
                        <div id="guest-buttons">
                            <a href="#login" class="text-gray-600 hover:text-gray-900 mr-4">Login</a>
                            <a href="#register" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                                Sign Up
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            
            <!-- Landing Page -->
            <div id="landing-page" class="text-center">
                <div class="max-w-3xl mx-auto">
                    <h1 class="text-4xl font-bold text-gray-900 mb-4">
                        Welcome to Notions
                    </h1>
                    <p class="text-xl text-gray-600 mb-8">
                        The shared todo list app for teams. Organize tasks, collaborate with teammates, and get things done together.
                    </p>
                    <div class="space-x-4">
                        <button id="get-started-btn" class="bg-blue-600 text-white px-8 py-3 rounded-lg text-lg hover:bg-blue-700">
                            Get Started
                        </button>
                        <button id="learn-more-btn" class="border border-gray-300 text-gray-700 px-8 py-3 rounded-lg text-lg hover:bg-gray-50">
                            Learn More
                        </button>
                    </div>
                </div>
            </div>

            <!-- Login Form -->
            <div id="login-form" class="hidden max-w-md mx-auto">
                <div class="bg-white p-8 rounded-lg shadow-md">
                    <h2 class="text-2xl font-bold text-center text-gray-900 mb-6">Sign In</h2>
                    <form id="login-form-element">
                        <div class="mb-4">
                            <label for="login-email" class="block text-sm font-medium text-gray-700 mb-2">
                                Email Address
                            </label>
                            <input type="email" id="login-email" name="email" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div class="mb-6">
                            <label for="login-password" class="block text-sm font-medium text-gray-700 mb-2">
                                Password
                            </label>
                            <input type="password" id="login-password" name="password" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <button type="submit" 
                            class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            Sign In
                        </button>
                    </form>
                    <div class="mt-4 text-center">
                        <a href="#register" class="text-blue-600 hover:text-blue-800">
                            Don't have an account? Sign up
                        </a>
                    </div>
                    <div class="mt-2 text-center">
                        <a href="#forgot-password" class="text-sm text-gray-600 hover:text-gray-800">
                            Forgot your password?
                        </a>
                    </div>
                </div>
            </div>

            <!-- Register Form -->
            <div id="register-form" class="hidden max-w-md mx-auto">
                <div class="bg-white p-8 rounded-lg shadow-md">
                    <h2 class="text-2xl font-bold text-center text-gray-900 mb-6">Create Account</h2>
                    <form id="register-form-element">
                        <div class="mb-4">
                            <label for="register-name" class="block text-sm font-medium text-gray-700 mb-2">
                                Full Name
                            </label>
                            <input type="text" id="register-name" name="name" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div class="mb-4">
                            <label for="register-email" class="block text-sm font-medium text-gray-700 mb-2">
                                Email Address
                            </label>
                            <input type="email" id="register-email" name="email" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div class="mb-4">
                            <label for="register-password" class="block text-sm font-medium text-gray-700 mb-2">
                                Password
                            </label>
                            <input type="password" id="register-password" name="password" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div class="mb-6">
                            <label for="register-password-confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                                Confirm Password
                            </label>
                            <input type="password" id="register-password-confirmation" name="password_confirmation" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <button type="submit" 
                            class="w-full bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                            Create Account
                        </button>
                    </form>
                    <div class="mt-4 text-center">
                        <a href="#login" class="text-blue-600 hover:text-blue-800">
                            Already have an account? Sign in
                        </a>
                    </div>
                </div>
            </div>

            <!-- Dashboard -->
            <div id="dashboard" class="hidden">
                <div class="mb-6">
                    <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
                    <p class="text-gray-600">Welcome back! Here's your Notions workspace.</p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Teams</h3>
                        <p class="text-3xl font-bold text-blue-600">0</p>
                        <p class="text-sm text-gray-600">Teams you're part of</p>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Tasks</h3>
                        <p class="text-3xl font-bold text-green-600">0</p>
                        <p class="text-sm text-gray-600">Assigned to you</p>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Completed</h3>
                        <p class="text-3xl font-bold text-purple-600">0</p>
                        <p class="text-sm text-gray-600">Tasks completed</p>
                    </div>
                </div>

                <div class="mt-8 bg-white p-6 rounded-lg shadow-md">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                    <div class="space-y-2">
                        <button id="create-team-btn" class="w-full text-left p-3 border border-gray-200 rounded-lg hover:bg-gray-50">
                            + Create Team
                        </button>
                        <button class="w-full text-left p-3 border border-gray-200 rounded-lg hover:bg-gray-50">
                            + Add Task
                        </button>
                        <button id="view-teams-btn" class="w-full text-left p-3 border border-gray-200 rounded-lg hover:bg-gray-50">
                            👥 View Teams
                        </button>
                    </div>
                </div>

                <!-- My Teams Section -->
                <div id="my-teams-section" class="mt-8">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">My Teams</h3>
                        <button id="create-team-btn-2" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                            + New Team
                        </button>
                    </div>
                    <div id="teams-list" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <!-- Teams will be loaded here -->
                    </div>
                </div>
            </div>

            <!-- Create Team Form -->
            <div id="create-team-form" class="hidden max-w-md mx-auto">
                <div class="bg-white p-8 rounded-lg shadow-md">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold text-gray-900">Create Team</h2>
                        <button id="close-create-team" class="text-gray-500 hover:text-gray-700">
                            ✕
                        </button>
                    </div>
                    <form id="create-team-form-element">
                        <div class="mb-4">
                            <label for="team-name" class="block text-sm font-medium text-gray-700 mb-2">
                                Team Name *
                            </label>
                            <input type="text" id="team-name" name="name" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div class="mb-6">
                            <label for="team-description" class="block text-sm font-medium text-gray-700 mb-2">
                                Description (Optional)
                            </label>
                            <textarea id="team-description" name="description" rows="3"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                        </div>
                        <button type="submit" 
                            class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            Create Team
                        </button>
                    </form>
                </div>
            </div>

            <!-- Team Details View -->
            <div id="team-details" class="hidden">
                <div class="mb-6">
                    <button id="back-to-dashboard" class="text-blue-600 hover:text-blue-800 mb-4">
                        ← Back to Dashboard
                    </button>
                    <div class="flex justify-between items-center">
                        <div>
                            <h1 id="team-name-display" class="text-3xl font-bold text-gray-900"></h1>
                            <p id="team-description-display" class="text-gray-600"></p>
                        </div>
                        <div class="flex space-x-2">
                            <button id="invite-member-btn" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
                                📧 Invite Member
                            </button>
                            <button id="team-settings-btn" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
                                ⚙️ Settings
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Team Stats -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                    <div class="bg-white p-4 rounded-lg shadow-md">
                        <h3 class="text-sm font-medium text-gray-500">Members</h3>
                        <p id="team-members-count" class="text-2xl font-bold text-blue-600">0</p>
                    </div>
                    <div class="bg-white p-4 rounded-lg shadow-md">
                        <h3 class="text-sm font-medium text-gray-500">Tasks</h3>
                        <p id="team-tasks-count" class="text-2xl font-bold text-green-600">0</p>
                    </div>
                    <div class="bg-white p-4 rounded-lg shadow-md">
                        <h3 class="text-sm font-medium text-gray-500">In Progress</h3>
                        <p id="team-progress-count" class="text-2xl font-bold text-yellow-600">0</p>
                    </div>
                    <div class="bg-white p-4 rounded-lg shadow-md">
                        <h3 class="text-sm font-medium text-gray-500">Completed</h3>
                        <p id="team-completed-count" class="text-2xl font-bold text-purple-600">0</p>
                    </div>
                </div>

                <!-- Team Members -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Team Members</h3>
                    <div id="team-members-list">
                        <!-- Members will be loaded here -->
                    </div>
                </div>

                <!-- Team Tasks Preview -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Tasks</h3>
                    <div id="team-tasks-list">
                        <p class="text-gray-500">No tasks yet. Create your first task to get started!</p>
                    </div>
                </div>
            </div>

            <!-- Invite Member Modal -->
            <div id="invite-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white p-8 rounded-lg shadow-lg max-w-md w-full mx-4">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-bold text-gray-900">Invite Team Member</h3>
                        <button id="close-invite-modal" class="text-gray-500 hover:text-gray-700">
                            ✕
                        </button>
                    </div>
                    <form id="invite-form">
                        <div class="mb-4">
                            <label for="invite-email" class="block text-sm font-medium text-gray-700 mb-2">
                                Email Address *
                            </label>
                            <input type="email" id="invite-email" name="email" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div class="mb-6">
                            <label for="invite-role" class="block text-sm font-medium text-gray-700 mb-2">
                                Role
                            </label>
                            <select id="invite-role" name="role"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="member">Member</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <div class="flex space-x-3">
                            <button type="submit" class="flex-1 bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700">
                                Send Invitation
                            </button>
                            <button type="button" id="cancel-invite" class="flex-1 bg-gray-300 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-400">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Join Team Modal -->
            <div id="join-team-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white p-8 rounded-lg shadow-lg max-w-md w-full mx-4">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-bold text-gray-900">Join Team</h3>
                        <button id="close-join-modal" class="text-gray-500 hover:text-gray-700">
                            ✕
                        </button>
                    </div>
                    <form id="join-team-form">
                        <div class="mb-6">
                            <label for="invite-code" class="block text-sm font-medium text-gray-700 mb-2">
                                Invite Code
                            </label>
                            <input type="text" id="invite-code" name="invite_code" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Enter invite code">
                        </div>
                        <div class="flex space-x-3">
                            <button type="submit" class="flex-1 bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700">
                                Join Team
                            </button>
                            <button type="button" id="cancel-join" class="flex-1 bg-gray-300 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-400">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            </div>

            <!-- Messages -->
            <div id="message-container" class="fixed top-4 right-4 z-50"></div>
        </main>
    </div>
</body>
</html>