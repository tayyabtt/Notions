import './bootstrap';

// App state
let currentUser = null;
let authToken = localStorage.getItem('auth_token');
let currentTeam = null;
let teams = [];
let tasks = [];
let allTasks = [];
let currentFilters = {
    status: null,
    priority: null,
    assignee: null
};

// API helper functions
async function apiCall(endpoint, options = {}) {
    const url = `/api${endpoint}`;
    const config = {
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            ...(authToken && { 'Authorization': `Bearer ${authToken}` })
        },
        ...options
    };

    try {
        const response = await fetch(url, config);
        const data = await response.json();
        
        if (!response.ok) {
            throw new Error(data.message || 'An error occurred');
        }
        
        return data;
    } catch (error) {
        console.error('API Error:', error);
        throw error;
    }
}

// UI Helper functions
function showMessage(message, type = 'success') {
    const container = document.getElementById('message-container');
    const messageDiv = document.createElement('div');
    messageDiv.className = `p-4 rounded-lg mb-2 shadow-lg ${
        type === 'success' ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-red-100 text-red-800 border border-red-200'
    }`;
    messageDiv.textContent = message;
    
    container.appendChild(messageDiv);
    
    setTimeout(() => {
        messageDiv.remove();
    }, 5000);
}

// Authentication functions
async function login(email, password) {
    try {
        const response = await apiCall('/login', {
            method: 'POST',
            body: JSON.stringify({ email, password })
        });
        
        authToken = response.token;
        localStorage.setItem('auth_token', authToken);
        currentUser = response.user;
        
        hideAuthModal();
        await loadAppData();
        showMessage('Welcome back!');
    } catch (error) {
        showMessage(error.message, 'error');
    }
}

async function register(name, email, password, passwordConfirmation) {
    try {
        const response = await apiCall('/register', {
            method: 'POST',
            body: JSON.stringify({
                name,
                email,
                password,
                password_confirmation: passwordConfirmation
            })
        });
        
        authToken = response.token;
        localStorage.setItem('auth_token', authToken);
        currentUser = response.user;
        
        hideAuthModal();
        await loadAppData();
        showMessage('Account created successfully!');
    } catch (error) {
        showMessage(error.message, 'error');
    }
}

function logout() {
    authToken = null;
    currentUser = null;
    localStorage.removeItem('auth_token');
    showAuthModal();
    showMessage('Logged out successfully');
}

// Data loading functions
async function loadAppData() {
    try {
        await loadUserData();
        await loadTeams();
        await loadTasks();
        updateSidebar();
        renderTasksTable();
    } catch (error) {
        console.error('Failed to load app data:', error);
    }
}

async function loadUserData() {
    try {
        const response = await apiCall('/user');
        currentUser = response;
        updateWorkspaceName();
    } catch (error) {
        logout();
    }
}

async function loadTeams() {
    try {
        const response = await apiCall('/teams');
        teams = response.teams;
        if (teams.length > 0 && !currentTeam) {
            currentTeam = teams[0];
        }
    } catch (error) {
        console.error('Failed to load teams:', error);
    }
}

async function loadTasks() {
    if (!currentTeam) return;
    
    try {
        const response = await apiCall(`/teams/${currentTeam.id}`);
        allTasks = response.team.tasks || [];
        applyFilters();
    } catch (error) {
        console.error('Failed to load tasks:', error);
    }
}

// Task management functions
// Filtering functions
function applyFilters() {
    tasks = allTasks.filter(task => {
        if (currentFilters.status && task.status !== currentFilters.status) return false;
        if (currentFilters.priority && task.priority !== currentFilters.priority) return false;
        if (currentFilters.assignee && task.assigned_to !== currentFilters.assignee) return false;
        return true;
    });
}

function setFilter(type, value) {
    currentFilters[type] = currentFilters[type] === value ? null : value;
    applyFilters();
    renderTasksTable();
    updateFilterButtons();
}

function clearAllFilters() {
    currentFilters = {
        status: null,
        priority: null,
        assignee: null
    };
    applyFilters();
    renderTasksTable();
    updateFilterButtons();
}

async function createTask(taskData) {
    if (!currentTeam) return;
    
    try {
        const response = await apiCall(`/teams/${currentTeam.id}/tasks`, {
            method: 'POST',
            body: JSON.stringify(taskData)
        });
        
        allTasks.unshift(response.task);
        applyFilters();
        renderTasksTable();
        showMessage('Task created successfully!');
    } catch (error) {
        showMessage(error.message, 'error');
    }
}

async function updateTask(taskId, updates) {
    try {
        const response = await apiCall(`/tasks/${taskId}`, {
            method: 'PUT',
            body: JSON.stringify(updates)
        });
        
        const allTaskIndex = allTasks.findIndex(t => t.id === taskId);
        if (allTaskIndex !== -1) {
            allTasks[allTaskIndex] = response.task;
            applyFilters();
            renderTasksTable();
        }
        
        showMessage('Task updated successfully!');
    } catch (error) {
        showMessage(error.message, 'error');
    }
}

// Filter UI functions
function updateFilterButtons() {
    const filterBtn = document.querySelector('[onclick="showFilterDropdown()"]');
    if (filterBtn) {
        const hasActiveFilters = Object.values(currentFilters).some(value => value !== null);
        if (hasActiveFilters) {
            filterBtn.classList.add('bg-blue-100', 'text-blue-600');
            filterBtn.classList.remove('text-gray-600');
        } else {
            filterBtn.classList.remove('bg-blue-100', 'text-blue-600');
            filterBtn.classList.add('text-gray-600');
        }
    }
}

function showFilterDropdown() {
    const dropdown = document.getElementById('filter-dropdown');
    if (!dropdown) {
        createFilterDropdown();
        return;
    }
    dropdown.classList.toggle('hidden');
}

function createFilterDropdown() {
    const existingDropdown = document.getElementById('filter-dropdown');
    if (existingDropdown) {
        existingDropdown.remove();
    }

    const filterBtn = document.querySelector('[onclick="showFilterDropdown()"]');
    const dropdown = document.createElement('div');
    dropdown.id = 'filter-dropdown';
    dropdown.className = 'absolute top-full left-0 mt-1 w-64 bg-white border border-gray-200 rounded-md shadow-lg z-50';
    
    dropdown.innerHTML = `
        <div class="p-3">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-semibold text-gray-900">Filters</h3>
                <button onclick="clearAllFilters()" class="text-xs text-blue-600 hover:text-blue-800">Clear all</button>
            </div>
            
            <!-- Status Filter -->
            <div class="mb-3">
                <label class="block text-xs font-medium text-gray-700 mb-2">Status</label>
                <div class="space-y-1">
                    <button onclick="setFilter('status', 'todo')" 
                            class="w-full text-left px-2 py-1 text-xs rounded hover:bg-gray-100 ${currentFilters.status === 'todo' ? 'bg-blue-100 text-blue-600' : 'text-gray-700'}">
                        Todo
                    </button>
                    <button onclick="setFilter('status', 'in_progress')" 
                            class="w-full text-left px-2 py-1 text-xs rounded hover:bg-gray-100 ${currentFilters.status === 'in_progress' ? 'bg-blue-100 text-blue-600' : 'text-gray-700'}">
                        In Progress
                    </button>
                    <button onclick="setFilter('status', 'done')" 
                            class="w-full text-left px-2 py-1 text-xs rounded hover:bg-gray-100 ${currentFilters.status === 'done' ? 'bg-blue-100 text-blue-600' : 'text-gray-700'}">
                        Done
                    </button>
                </div>
            </div>
            
            <!-- Priority Filter -->
            <div class="mb-3">
                <label class="block text-xs font-medium text-gray-700 mb-2">Priority</label>
                <div class="space-y-1">
                    <button onclick="setFilter('priority', 'high')" 
                            class="w-full text-left px-2 py-1 text-xs rounded hover:bg-gray-100 ${currentFilters.priority === 'high' ? 'bg-blue-100 text-blue-600' : 'text-gray-700'}">
                        High
                    </button>
                    <button onclick="setFilter('priority', 'medium')" 
                            class="w-full text-left px-2 py-1 text-xs rounded hover:bg-gray-100 ${currentFilters.priority === 'medium' ? 'bg-blue-100 text-blue-600' : 'text-gray-700'}">
                        Medium
                    </button>
                    <button onclick="setFilter('priority', 'low')" 
                            class="w-full text-left px-2 py-1 text-xs rounded hover:bg-gray-100 ${currentFilters.priority === 'low' ? 'bg-blue-100 text-blue-600' : 'text-gray-700'}">
                        Low
                    </button>
                </div>
            </div>
            
            <!-- Assignee Filter -->
            <div class="mb-3">
                <label class="block text-xs font-medium text-gray-700 mb-2">Assignee</label>
                <div class="space-y-1" id="assignee-filter-options">
                    ${renderAssigneeFilterOptions()}
                </div>
            </div>
        </div>
    `;
    
    filterBtn.parentElement.style.position = 'relative';
    filterBtn.parentElement.appendChild(dropdown);
    
    // Hide dropdown when clicking outside
    setTimeout(() => {
        document.addEventListener('click', function hideDropdown(e) {
            if (!dropdown.contains(e.target) && !filterBtn.contains(e.target)) {
                dropdown.classList.add('hidden');
                document.removeEventListener('click', hideDropdown);
            }
        });
    }, 100);
}

function renderAssigneeFilterOptions() {
    const uniqueAssignees = [...new Set(allTasks
        .filter(task => task.assignee)
        .map(task => task.assignee)
    )];
    
    let options = `
        <button onclick="setFilter('assignee', null)" 
                class="w-full text-left px-2 py-1 text-xs rounded hover:bg-gray-100 ${currentFilters.assignee === null ? 'bg-blue-100 text-blue-600' : 'text-gray-700'}">
            Unassigned
        </button>
    `;
    
    uniqueAssignees.forEach(assignee => {
        options += `
            <button onclick="setFilter('assignee', ${assignee.id})" 
                    class="w-full text-left px-2 py-1 text-xs rounded hover:bg-gray-100 ${currentFilters.assignee === assignee.id ? 'bg-blue-100 text-blue-600' : 'text-gray-700'}">
                <div class="flex items-center space-x-2">
                    <div class="w-4 h-4 bg-gray-300 rounded-full text-xs flex items-center justify-center">
                        ${assignee.name.charAt(0)}
                    </div>
                    <span>${assignee.name}</span>
                </div>
            </button>
        `;
    });
    
    return options;
}

// UI rendering functions
function updateWorkspaceName() {
    if (currentUser) {
        document.getElementById('workspace-name').textContent = currentUser.name + "'s Workspace";
    }
}

function updateSidebar() {
    // Update teamspaces
    const teamspacesContainer = document.getElementById('teamspaces');
    teamspacesContainer.innerHTML = teams.map(team => `
        <div class="flex items-center space-x-2 px-2 py-1 rounded hover:bg-gray-100 cursor-pointer ${currentTeam?.id === team.id ? 'bg-gray-100' : ''}"
             onclick="switchTeam(${team.id})">
            <div class="w-4 h-4 bg-blue-500 rounded text-white text-xs flex items-center justify-center">
                ${team.name.charAt(0)}
            </div>
            <span class="text-sm text-gray-700">${team.name}</span>
        </div>
    `).join('');

    // Add "Tasks" page under current team
    if (currentTeam) {
        teamspacesContainer.innerHTML += `
            <div class="ml-6 space-y-1">
                <div class="flex items-center space-x-2 px-2 py-1 rounded bg-blue-50 text-blue-700">
                    <span class="text-sm">📋</span>
                    <span class="text-sm font-medium">Tasks</span>
                </div>
            </div>
        `;
    }
}

function renderTasksTable() {
    const tableBody = document.getElementById('tasks-table-body');
    
    if (tasks.length === 0) {
        tableBody.innerHTML = `
            <div class="text-center py-12 text-gray-500">
                <div class="text-4xl mb-4">📋</div>
                <p class="text-lg mb-2">No tasks yet</p>
                <p class="text-sm">Click "New" to create your first task</p>
            </div>
        `;
        return;
    }

    tableBody.innerHTML = tasks.map(task => `
        <div class="border-l-4 border-transparent hover:border-blue-500 group">
            <table class="w-full">
                <tbody>
                    <tr class="hover:bg-gray-50 cursor-pointer" onclick="openTaskDetail(${task.id})">
                        <td class="px-6 py-3 w-8">
                            <input type="checkbox" ${task.status === 'done' ? 'checked' : ''} 
                                   onchange="toggleTaskStatus(${task.id})"
                                   class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" />
                        </td>
                        <td class="px-6 py-3 min-w-80">
                            <div class="text-sm text-gray-900 ${task.status === 'done' ? 'line-through text-gray-500' : ''}">
                                ${task.title}
                            </div>
                            ${task.description ? `<div class="text-xs text-gray-500 mt-1">${task.description.substring(0, 60)}...</div>` : ''}
                        </td>
                        <td class="px-6 py-3 w-32">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                ${getPriorityColor(task.priority)}">
                                ${getPriorityLabel(task.priority)}
                            </span>
                        </td>
                        <td class="px-6 py-3 w-32">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                ${getStatusColor(task.status)}">
                                ${getStatusLabel(task.status)}
                            </span>
                        </td>
                        <td class="px-6 py-3 w-40">
                            ${task.assignee ? `
                                <div class="flex items-center space-x-2">
                                    <div class="w-6 h-6 bg-gray-300 rounded-full text-xs flex items-center justify-center">
                                        ${task.assignee.name.charAt(0)}
                                    </div>
                                    <span class="text-sm text-gray-700">${task.assignee.name}</span>
                                </div>
                            ` : '<span class="text-sm text-gray-400">Unassigned</span>'}
                        </td>
                        <td class="px-6 py-3 w-32">
                            ${task.due_date ? `
                                <span class="text-sm text-gray-700">
                                    ${new Date(task.due_date).toLocaleDateString()}
                                </span>
                            ` : '<span class="text-sm text-gray-400">No date</span>'}
                        </td>
                        <td class="px-6 py-3 w-8">
                            <button class="opacity-0 group-hover:opacity-100 text-gray-400 hover:text-gray-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                                </svg>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    `).join('');
}

// Helper functions for styling
function getPriorityColor(priority) {
    switch (priority) {
        case 'high': return 'bg-red-100 text-red-800 border border-red-200';
        case 'medium': return 'bg-yellow-100 text-yellow-800 border border-yellow-200';
        case 'low': return 'bg-green-100 text-green-800 border border-green-200';
        default: return 'bg-gray-100 text-gray-800 border border-gray-200';
    }
}

function getPriorityLabel(priority) {
    switch (priority) {
        case 'high': return 'High';
        case 'medium': return 'Medium';
        case 'low': return 'Low';
        default: return 'None';
    }
}

function getStatusColor(status) {
    switch (status) {
        case 'todo': return 'bg-gray-100 text-gray-800 border border-gray-200';
        case 'in_progress': return 'bg-blue-100 text-blue-800 border border-blue-200';
        case 'done': return 'bg-green-100 text-green-800 border border-green-200';
        default: return 'bg-gray-100 text-gray-800 border border-gray-200';
    }
}

function getStatusLabel(status) {
    switch (status) {
        case 'todo': return 'Todo';
        case 'in_progress': return 'In Progress';
        case 'done': return 'Done';
        default: return 'None';
    }
}

// Auth modal functions
function showAuthModal() {
    const modal = document.getElementById('auth-modal');
    modal.classList.remove('hidden');
    modal.innerHTML = `
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4 p-8">
            <div class="text-center mb-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Welcome to Notions</h2>
                <p class="text-gray-600">Sign in to your workspace</p>
            </div>
            <div id="auth-forms">
                <!-- Login Form -->
                <form id="login-form" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" id="login-email" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                        <input type="password" id="login-password" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-md font-medium">
                        Sign In
                    </button>
                </form>
                
                <div class="mt-4 text-center">
                    <button id="show-register" class="text-sm text-blue-500 hover:text-blue-600">
                        Don't have an account? Sign up
                    </button>
                </div>
            </div>
        </div>
    `;
    
    setupAuthEventListeners();
}

function hideAuthModal() {
    document.getElementById('auth-modal').classList.add('hidden');
}

function setupAuthEventListeners() {
    document.getElementById('login-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        const email = document.getElementById('login-email').value;
        const password = document.getElementById('login-password').value;
        await login(email, password);
    });

    document.getElementById('show-register').addEventListener('click', showRegisterForm);
}

function showRegisterForm() {
    document.getElementById('auth-forms').innerHTML = `
        <form id="register-form" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                <input type="text" id="register-name" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                <input type="email" id="register-email" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                <input type="password" id="register-password" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
                <input type="password" id="register-password-confirmation" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <button type="submit" class="w-full bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded-md font-medium">
                Create Account
            </button>
        </form>
        
        <div class="mt-4 text-center">
            <button id="show-login" class="text-sm text-blue-500 hover:text-blue-600">
                Already have an account? Sign in
            </button>
        </div>
    `;

    document.getElementById('register-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        const name = document.getElementById('register-name').value;
        const email = document.getElementById('register-email').value;
        const password = document.getElementById('register-password').value;
        const passwordConfirmation = document.getElementById('register-password-confirmation').value;
        
        if (password !== passwordConfirmation) {
            showMessage('Passwords do not match', 'error');
            return;
        }
        
        await register(name, email, password, passwordConfirmation);
    });

    document.getElementById('show-login').addEventListener('click', showAuthModal);
}

// Global functions for onclick handlers
// Global functions for onclick handlers
window.switchTeam = async function(teamId) {
    const team = teams.find(t => t.id === teamId);
    if (team) {
        currentTeam = team;
        await loadTasks();
        updateSidebar();
        renderTasksTable();
    }
};

window.toggleTaskStatus = async function(taskId) {
    const task = allTasks.find(t => t.id === taskId);
    if (task) {
        const newStatus = task.status === 'done' ? 'todo' : 'done';
        await updateTask(taskId, { status: newStatus });
    }
};

window.showFilterDropdown = showFilterDropdown;
window.setFilter = setFilter;
window.clearAllFilters = clearAllFilters;

window.openTaskDetail = async function(taskId) {
    try {
        const response = await apiCall(`/tasks/${taskId}`);
        const task = response.task;
        showTaskDetailModal(task);
    } catch (error) {
        showMessage(error.message, 'error');
    }
};

// Task detail modal functions
async function showTaskDetailModal(task) {
    const modal = document.getElementById('task-detail-modal');
    modal.classList.remove('hidden');
    
    // Load team members for @mentions
    const teamMembers = await loadTeamMembers(task.id);
    
    modal.innerHTML = `
        <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="sticky top-0 bg-white border-b px-6 py-4 flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-900">${task.title}</h2>
                <button onclick="hideTaskDetailModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            <div class="p-6">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Main content -->
                    <div class="lg:col-span-2">
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                            <div class="text-gray-900">
                                ${task.description || '<em class="text-gray-500">No description</em>'}
                            </div>
                        </div>
                        
                        <!-- Comments section -->
                        <div class="border-t pt-6">
                            <h3 class="text-lg font-semibold mb-4">Comments</h3>
                            
                            <!-- Comment form -->
                            <div class="mb-6">
                                <div class="relative">
                                    <textarea id="comment-content" 
                                              placeholder="Add a comment... Type @ to mention someone"
                                              class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"
                                              rows="3"></textarea>
                                    
                                    <!-- Mention dropdown -->
                                    <div id="mention-dropdown" class="absolute z-10 w-64 bg-white border border-gray-200 rounded-md shadow-lg hidden max-h-48 overflow-y-auto">
                                    </div>
                                </div>
                                
                                <div class="mt-2 flex justify-end">
                                    <button onclick="addComment(${task.id})" 
                                            class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium">
                                        Add Comment
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Comments list -->
                            <div id="comments-list">
                                ${renderComments(task.comments || [])}
                            </div>
                        </div>
                    </div>
                    
                    <!-- Sidebar -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ${getStatusColor(task.status)}">
                                    ${getStatusLabel(task.status)}
                                </span>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ${getPriorityColor(task.priority)}">
                                    ${getPriorityLabel(task.priority)}
                                </span>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Assignee</label>
                                ${task.assignee ? `
                                    <div class="flex items-center space-x-2 mt-1">
                                        <div class="w-6 h-6 bg-gray-300 rounded-full text-xs flex items-center justify-center">
                                            ${task.assignee.name.charAt(0)}
                                        </div>
                                        <span class="text-sm text-gray-700">${task.assignee.name}</span>
                                    </div>
                                ` : '<span class="text-sm text-gray-500">Unassigned</span>'}
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Due Date</label>
                                ${task.due_date ? `
                                    <span class="text-sm text-gray-700">
                                        ${new Date(task.due_date).toLocaleDateString()}
                                    </span>
                                ` : '<span class="text-sm text-gray-500">No due date</span>'}
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Team</label>
                                <div class="flex items-center space-x-2 mt-1">
                                    <div class="w-4 h-4 bg-blue-500 rounded text-white text-xs flex items-center justify-center">
                                        ${task.team.name.charAt(0)}
                                    </div>
                                    <span class="text-sm text-gray-700">${task.team.name}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    setupMentionFunctionality(teamMembers);
}

async function loadTeamMembers(taskId) {
    try {
        const response = await apiCall(`/tasks/${taskId}/team-members`);
        return response.team_members;
    } catch (error) {
        console.error('Failed to load team members:', error);
        return [];
    }
}

function renderComments(comments) {
    if (!comments || comments.length === 0) {
        return '<p class="text-gray-500 text-sm">No comments yet</p>';
    }
    
    return comments.map(comment => `
        <div class="mb-4 pb-4 border-b border-gray-200 last:border-b-0">
            <div class="flex items-start space-x-3">
                <div class="w-8 h-8 bg-gray-300 rounded-full text-sm flex items-center justify-center">
                    ${comment.user.name.charAt(0)}
                </div>
                <div class="flex-1">
                    <div class="flex items-center space-x-2 mb-1">
                        <span class="font-medium text-sm text-gray-900">${comment.user.name}</span>
                        <span class="text-xs text-gray-500">
                            ${new Date(comment.created_at).toLocaleDateString()} at ${new Date(comment.created_at).toLocaleTimeString()}
                        </span>
                    </div>
                    <div class="text-sm text-gray-700">
                        ${renderCommentWithMentions(comment.content, comment.mentioned_users || [])}
                    </div>
                </div>
            </div>
        </div>
    `).join('');
}

function renderCommentWithMentions(content, mentionedUserIds) {
    // Simple implementation - in a real app you'd want more sophisticated mention parsing
    return content.replace(/@(\w+)/g, '<span class="bg-blue-100 text-blue-800 px-1 rounded">@$1</span>');
}

window.hideTaskDetailModal = function() {
    document.getElementById('task-detail-modal').classList.add('hidden');
};

// Mention functionality
let mentionActive = false;
let mentionStartPos = 0;
let currentTeamMembers = [];

function setupMentionFunctionality(teamMembers) {
    currentTeamMembers = teamMembers;
    const textarea = document.getElementById('comment-content');
    const dropdown = document.getElementById('mention-dropdown');
    
    textarea.addEventListener('input', handleMentionInput);
    textarea.addEventListener('keydown', handleMentionKeydown);
    
    // Hide dropdown when clicking outside
    document.addEventListener('click', (e) => {
        if (!textarea.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.classList.add('hidden');
            mentionActive = false;
        }
    });
}

function handleMentionInput(e) {
    const textarea = e.target;
    const text = textarea.value;
    const cursorPos = textarea.selectionStart;
    
    // Check if @ was typed
    if (text[cursorPos - 1] === '@') {
        mentionActive = true;
        mentionStartPos = cursorPos - 1;
        showMentionDropdown('');
        return;
    }
    
    if (mentionActive) {
        const mentionText = text.substring(mentionStartPos + 1, cursorPos);
        
        // If user typed space or moved cursor away, hide dropdown
        if (mentionText.includes(' ') || cursorPos < mentionStartPos) {
            hideMentionDropdown();
            return;
        }
        
        showMentionDropdown(mentionText);
    }
}

function handleMentionKeydown(e) {
    if (!mentionActive) return;
    
    const dropdown = document.getElementById('mention-dropdown');
    const items = dropdown.querySelectorAll('.mention-item');
    const activeItem = dropdown.querySelector('.mention-item.active');
    
    if (e.key === 'ArrowDown') {
        e.preventDefault();
        const nextItem = activeItem ? activeItem.nextElementSibling : items[0];
        if (nextItem) {
            if (activeItem) activeItem.classList.remove('active');
            nextItem.classList.add('active');
        }
    } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        const prevItem = activeItem ? activeItem.previousElementSibling : items[items.length - 1];
        if (prevItem) {
            if (activeItem) activeItem.classList.remove('active');
            prevItem.classList.add('active');
        }
    } else if (e.key === 'Enter' && activeItem) {
        e.preventDefault();
        selectMention(activeItem.dataset.userId, activeItem.dataset.userName);
    } else if (e.key === 'Escape') {
        hideMentionDropdown();
    }
}

function showMentionDropdown(filter) {
    const dropdown = document.getElementById('mention-dropdown');
    const filtered = currentTeamMembers.filter(member => 
        member.name.toLowerCase().includes(filter.toLowerCase())
    );
    
    if (filtered.length === 0) {
        hideMentionDropdown();
        return;
    }
    
    dropdown.innerHTML = filtered.map((member, index) => `
        <div class="mention-item px-3 py-2 cursor-pointer hover:bg-gray-100 ${index === 0 ? 'active bg-gray-100' : ''}"
             data-user-id="${member.id}" data-user-name="${member.name}"
             onclick="selectMention(${member.id}, '${member.name}')">
            <div class="flex items-center space-x-2">
                <div class="w-6 h-6 bg-gray-300 rounded-full text-xs flex items-center justify-center">
                    ${member.name.charAt(0)}
                </div>
                <span class="text-sm">${member.name}</span>
            </div>
        </div>
    `).join('');
    
    dropdown.classList.remove('hidden');
}

function hideMentionDropdown() {
    document.getElementById('mention-dropdown').classList.add('hidden');
    mentionActive = false;
}

window.selectMention = function(userId, userName) {
    const textarea = document.getElementById('comment-content');
    const text = textarea.value;
    const beforeMention = text.substring(0, mentionStartPos);
    const afterMention = text.substring(textarea.selectionStart);
    
    const newText = beforeMention + '@' + userName + ' ' + afterMention;
    textarea.value = newText;
    
    // Move cursor after the mention
    const newCursorPos = mentionStartPos + userName.length + 2;
    textarea.setSelectionRange(newCursorPos, newCursorPos);
    
    hideMentionDropdown();
    textarea.focus();
};

// Comment submission
window.addComment = async function(taskId) {
    const content = document.getElementById('comment-content').value.trim();
    if (!content) return;
    
    // Extract mentioned user IDs from content
    const mentionedUsers = extractMentionsFromContent(content);
    
    try {
        await apiCall(`/tasks/${taskId}/comments`, {
            method: 'POST',
            body: JSON.stringify({
                content: content,
                mentioned_users: mentionedUsers
            })
        });
        
        // Reload task detail to show new comment
        const response = await apiCall(`/tasks/${taskId}`);
        const commentsList = document.getElementById('comments-list');
        commentsList.innerHTML = renderComments(response.task.comments || []);
        
        // Clear comment form
        document.getElementById('comment-content').value = '';
        
        showMessage('Comment added successfully!');
    } catch (error) {
        showMessage(error.message, 'error');
    }
};

function extractMentionsFromContent(content) {
    const mentions = [];
    const mentionRegex = /@(\w+)/g;
    let match;
    
    while ((match = mentionRegex.exec(content)) !== null) {
        const mentionedName = match[1];
        const user = currentTeamMembers.find(member => 
            member.name.toLowerCase().includes(mentionedName.toLowerCase())
        );
        if (user) {
            mentions.push(user.id);
        }
    }
    
    return [...new Set(mentions)]; // Remove duplicates
};

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Check authentication
    if (authToken) {
        loadAppData();
    } else {
        showAuthModal();
    }

    // Logout button
    document.getElementById('logout-btn').addEventListener('click', logout);

    // New task button
    document.getElementById('new-task-btn').addEventListener('click', () => {
        const title = prompt('Task title:');
        if (title) {
            createTask({ 
                title, 
                status: 'todo', 
                priority: 'medium',
                team_id: currentTeam?.id 
            });
        }
    });

    // New task row click
    document.getElementById('new-task-row').addEventListener('click', () => {
        const title = prompt('Task title:');
        if (title) {
            createTask({ 
                title, 
                status: 'todo', 
                priority: 'medium',
                team_id: currentTeam?.id 
            });
        }
    });
});