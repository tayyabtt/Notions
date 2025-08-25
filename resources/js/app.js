import './bootstrap';

// App state
let currentUser = null;
let authToken = localStorage.getItem('auth_token');

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
    messageDiv.className = `p-4 rounded-lg mb-2 ${
        type === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
    }`;
    messageDiv.textContent = message;
    
    container.appendChild(messageDiv);
    
    setTimeout(() => {
        messageDiv.remove();
    }, 5000);
}

function showPage(pageId) {
    // Hide all pages
    document.querySelectorAll('#landing-page, #login-form, #register-form, #dashboard').forEach(page => {
        page.classList.add('hidden');
    });
    
    // Show selected page
    document.getElementById(pageId).classList.remove('hidden');
}

function updateAuthState() {
    const isLoggedIn = !!authToken;
    const authButtons = document.getElementById('auth-buttons');
    const guestButtons = document.getElementById('guest-buttons');
    
    if (isLoggedIn) {
        authButtons.classList.remove('hidden');
        guestButtons.classList.add('hidden');
        showPage('dashboard');
        loadUserData();
    } else {
        authButtons.classList.add('hidden');
        guestButtons.classList.remove('hidden');
        showPage('landing-page');
    }
}

async function loadUserData() {
    try {
        const response = await apiCall('/user');
        currentUser = response;
    } catch (error) {
        console.error('Failed to load user data:', error);
        logout();
    }
}

function logout() {
    authToken = null;
    currentUser = null;
    localStorage.removeItem('auth_token');
    updateAuthState();
    showMessage('Logged out successfully');
}

// Authentication functions
async function register(formData) {
    try {
        const response = await apiCall('/register', {
            method: 'POST',
            body: JSON.stringify(formData)
        });
        
        authToken = response.token;
        localStorage.setItem('auth_token', authToken);
        currentUser = response.user;
        
        showMessage('Account created successfully!');
        updateAuthState();
    } catch (error) {
        showMessage(error.message, 'error');
    }
}

async function login(formData) {
    try {
        const response = await apiCall('/login', {
            method: 'POST',
            body: JSON.stringify(formData)
        });
        
        authToken = response.token;
        localStorage.setItem('auth_token', authToken);
        currentUser = response.user;
        
        showMessage('Logged in successfully!');
        updateAuthState();
    } catch (error) {
        showMessage(error.message, 'error');
    }
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Check if user is already logged in
    updateAuthState();
    
    // Navigation
    document.addEventListener('click', function(e) {
        if (e.target.matches('a[href="#login"]')) {
            e.preventDefault();
            showPage('login-form');
        } else if (e.target.matches('a[href="#register"]')) {
            e.preventDefault();
            showPage('register-form');
        } else if (e.target.id === 'get-started-btn') {
            showPage('register-form');
        }
    });
    
    // Logout button
    document.getElementById('logout-btn').addEventListener('click', async function() {
        try {
            await apiCall('/logout', { method: 'POST' });
        } catch (error) {
            console.error('Logout error:', error);
        } finally {
            logout();
        }
    });
    
    // Login form
    document.getElementById('login-form-element').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = {
            email: document.getElementById('login-email').value,
            password: document.getElementById('login-password').value
        };
        
        await login(formData);
    });
    
    // Register form
    document.getElementById('register-form-element').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const password = document.getElementById('register-password').value;
        const passwordConfirmation = document.getElementById('register-password-confirmation').value;
        
        if (password !== passwordConfirmation) {
            showMessage('Passwords do not match', 'error');
            return;
        }
        
        const formData = {
            name: document.getElementById('register-name').value,
            email: document.getElementById('register-email').value,
            password: password,
            password_confirmation: passwordConfirmation
        };
        
        await register(formData);
    });

    // Team management functionality
    let currentTeam = null;

    // Team API functions
    async function loadTeams() {
        try {
            const response = await apiCall('/teams');
            displayTeams(response.teams);
            updateDashboardStats(response.teams);
        } catch (error) {
            showMessage(error.message, 'error');
        }
    }

    async function createTeam(formData) {
        try {
            const response = await apiCall('/teams', {
                method: 'POST',
                body: JSON.stringify(formData)
            });
            
            showMessage('Team created successfully!');
            showPage('dashboard');
            loadTeams();
        } catch (error) {
            showMessage(error.message, 'error');
        }
    }

    async function loadTeamDetails(teamId) {
        try {
            const response = await apiCall(`/teams/${teamId}`);
            currentTeam = response.team;
            displayTeamDetails(response.team);
            showPage('team-details');
        } catch (error) {
            showMessage(error.message, 'error');
        }
    }

    async function inviteTeamMember(teamId, formData) {
        try {
            const response = await apiCall(`/teams/${teamId}/invite`, {
                method: 'POST',
                body: JSON.stringify(formData)
            });
            
            showMessage('Invitation sent successfully!');
            closeInviteModal();
        } catch (error) {
            showMessage(error.message, 'error');
        }
    }

    async function joinTeamByCode(inviteCode) {
        try {
            const response = await apiCall('/teams/join', {
                method: 'POST',
                body: JSON.stringify({ invite_code: inviteCode })
            });
            
            showMessage('Successfully joined team!');
            closeJoinModal();
            loadTeams();
        } catch (error) {
            showMessage(error.message, 'error');
        }
    }

    // UI helper functions
    function displayTeams(teams) {
        const teamsList = document.getElementById('teams-list');
        
        if (teams.length === 0) {
            teamsList.innerHTML = `
                <div class="col-span-full text-center py-8">
                    <p class="text-gray-500 mb-4">No teams yet</p>
                    <button id="create-first-team" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                        Create Your First Team
                    </button>
                </div>
            `;
            return;
        }

        teamsList.innerHTML = teams.map(team => `
            <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg cursor-pointer transition-shadow" 
                 onclick="loadTeamDetails(${team.id})">
                <h4 class="text-lg font-semibold text-gray-900 mb-2">${team.name}</h4>
                <p class="text-gray-600 text-sm mb-4">${team.description || 'No description'}</p>
                <div class="flex justify-between items-center text-sm text-gray-500">
                    <span>${team.users?.length || 0} members</span>
                    <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded">
                        ${getUserRole(team)}
                    </span>
                </div>
            </div>
        `).join('');
    }

    function displayTeamDetails(team) {
        document.getElementById('team-name-display').textContent = team.name;
        document.getElementById('team-description-display').textContent = team.description || 'No description';
        document.getElementById('team-members-count').textContent = team.users?.length || 0;
        document.getElementById('team-tasks-count').textContent = team.tasks?.length || 0;
        
        // Display team members
        const membersList = document.getElementById('team-members-list');
        if (team.users && team.users.length > 0) {
            membersList.innerHTML = team.users.map(user => `
                <div class="flex justify-between items-center p-3 border-b border-gray-200">
                    <div>
                        <p class="font-medium text-gray-900">${user.name}</p>
                        <p class="text-sm text-gray-600">${user.email}</p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded text-sm">
                            ${user.pivot.role}
                        </span>
                        ${canRemoveMember(user) ? `
                            <button onclick="removeMember(${user.id})" class="text-red-600 hover:text-red-800 text-sm">
                                Remove
                            </button>
                        ` : ''}
                    </div>
                </div>
            `).join('');
        } else {
            membersList.innerHTML = '<p class="text-gray-500">No members yet.</p>';
        }
    }

    function updateDashboardStats(teams) {
        const teamsCount = document.querySelector('#dashboard .text-blue-600');
        if (teamsCount) {
            teamsCount.textContent = teams.length;
        }
    }

    function getUserRole(team) {
        const user = currentUser;
        const member = team.users?.find(u => u.id === user?.id);
        return member?.pivot?.role || 'member';
    }

    function canRemoveMember(user) {
        return currentUser && currentTeam && 
               getUserRole(currentTeam) === 'admin' && 
               user.id !== currentTeam.owner_id;
    }

    function showPage(pageId) {
        // Hide all pages
        document.querySelectorAll('#landing-page, #login-form, #register-form, #dashboard, #create-team-form, #team-details').forEach(page => {
            page.classList.add('hidden');
        });
        
        // Show selected page
        document.getElementById(pageId).classList.remove('hidden');
    }

    function closeInviteModal() {
        document.getElementById('invite-modal').classList.add('hidden');
        document.getElementById('invite-form').reset();
    }

    function closeJoinModal() {
        document.getElementById('join-team-modal').classList.add('hidden');
        document.getElementById('join-team-form').reset();
    }

    // Team event listeners
    document.addEventListener('click', function(e) {
        // Create team buttons
        if (e.target.matches('#create-team-btn, #create-team-btn-2, #create-first-team')) {
            showPage('create-team-form');
        }
        
        // Close create team form
        if (e.target.id === 'close-create-team') {
            showPage('dashboard');
        }
        
        // Back to dashboard
        if (e.target.id === 'back-to-dashboard') {
            showPage('dashboard');
            loadTeams();
        }
        
        // Invite member
        if (e.target.id === 'invite-member-btn') {
            document.getElementById('invite-modal').classList.remove('hidden');
        }
        
        // Close invite modal
        if (e.target.matches('#close-invite-modal, #cancel-invite')) {
            closeInviteModal();
        }
        
        // Join team modal
        if (e.target.id === 'view-teams-btn') {
            document.getElementById('join-team-modal').classList.remove('hidden');
        }
        
        // Close join modal
        if (e.target.matches('#close-join-modal, #cancel-join')) {
            closeJoinModal();
        }
    });

    // Create team form submission
    document.getElementById('create-team-form-element').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = {
            name: document.getElementById('team-name').value,
            description: document.getElementById('team-description').value
        };
        
        await createTeam(formData);
    });

    // Invite form submission
    document.getElementById('invite-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        if (!currentTeam) {
            showMessage('Please select a team first', 'error');
            return;
        }
        
        const formData = {
            email: document.getElementById('invite-email').value,
            role: document.getElementById('invite-role').value
        };
        
        await inviteTeamMember(currentTeam.id, formData);
    });

    // Join team form submission
    document.getElementById('join-team-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const inviteCode = document.getElementById('invite-code').value;
        await joinTeamByCode(inviteCode);
    });

    // Load teams when user logs in
    const originalUpdateAuthState = updateAuthState;
    updateAuthState = function() {
        originalUpdateAuthState();
        if (authToken) {
            loadTeams();
        }
    };

    // Make functions globally available for onclick handlers
    window.loadTeamDetails = loadTeamDetails;
});
