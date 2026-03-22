<?php
/**
 * Navbar Component
 * Top navigation bar with logo, theme toggle, and profile menu
 */
?>
<nav class="navbar">
    <div class="navbar-container">
        <!-- Logo -->
        <div class="navbar-brand">
            <svg class="navbar-logo" viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg">
                <circle cx="20" cy="20" r="18" fill="none" stroke="currentColor" stroke-width="2"/>
                <path d="M12 20 Q20 10 28 20 Q20 30 12 20" fill="currentColor" opacity="0.3"/>
                <circle cx="20" cy="20" r="4" fill="currentColor"/>
            </svg>
            <span class="navbar-title">VayuDarpan</span>
        </div>

        <!-- Right Menu -->
        <div class="navbar-menu">
            <!-- Theme Toggle -->
            <button class="theme-toggle" id="themeToggle" aria-label="Toggle theme" title="Toggle light/dark mode">
                <svg class="theme-icon sun-icon" viewBox="0 0 24 24" fill="currentColor">
                    <circle cx="12" cy="12" r="5"/>
                    <line x1="12" y1="1" x2="12" y2="3" stroke="currentColor" stroke-width="2"/>
                    <line x1="12" y1="21" x2="12" y2="23" stroke="currentColor" stroke-width="2"/>
                    <line x1="4.22" y1="4.22" x2="5.64" y2="5.64" stroke="currentColor" stroke-width="2"/>
                    <line x1="18.36" y1="18.36" x2="19.78" y2="19.78" stroke="currentColor" stroke-width="2"/>
                    <line x1="1" y1="12" x2="3" y2="12" stroke="currentColor" stroke-width="2"/>
                    <line x1="21" y1="12" x2="23" y2="12" stroke="currentColor" stroke-width="2"/>
                    <line x1="4.22" y1="19.78" x2="5.64" y2="18.36" stroke="currentColor" stroke-width="2"/>
                    <line x1="18.36" y1="5.64" x2="19.78" y2="4.22" stroke="currentColor" stroke-width="2"/>
                </svg>
                <svg class="theme-icon moon-icon" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
                </svg>
            </button>

            <!-- Profile Menu -->
            <div class="profile-menu-wrapper">
                <button class="profile-button" id="profileBtn" aria-label="Profile menu">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <circle cx="12" cy="8" r="4"/>
                        <path d="M12 14c-5 0-7 3-7 5v3h14v-3c0-2-2-5-7-5z"/>
                    </svg>
                </button>
                
                <!-- Dropdown Menu -->
                <div class="profile-dropdown" id="profileDropdown">
                    <a href="#" class="dropdown-item" onclick="handleEditProfile(event)">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <circle cx="12" cy="8" r="4"/>
                            <path d="M12 14c-5 0-7 3-7 5v3h14v-3c0-2-2-5-7-5z"/>
                        </svg>
                        <span>Edit Profile</span>
                    </a>
                    <a href="#" class="dropdown-item" onclick="handleLogout(event)">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M10 19H3V5h7V3H3a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h7v-2z"/>
                            <polyline points="17 16 21 12 17 8" fill="none" stroke="currentColor" stroke-width="2"/>
                            <line x1="21" y1="12" x2="9" y2="12" stroke="currentColor" stroke-width="2"/>
                        </svg>
                        <span>Logout</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</nav>

<script>
/**
 * Navbar event handlers
 */
(function() {
    // Profile menu toggle
    const profileBtn = document.getElementById('profileBtn');
    const profileDropdown = document.getElementById('profileDropdown');
    
    if (profileBtn) {
        profileBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            profileDropdown?.classList.toggle('active');
        });
    }
    
    document.addEventListener('click', () => {
        profileDropdown?.classList.remove('active');
    });
})();

/**
 * Profile menu handlers
 */
function handleEditProfile(e) {
    e.preventDefault();
    showModal('Edit Profile', `
        <div class="form-group">
            <label>Name</label>
            <input type="text" value="Admin User" class="form-input">
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" value="admin@vayudarpan.com" class="form-input">
        </div>
        <div class="form-group">
            <label>Role</label>
            <input type="text" value="Administrator" class="form-input" disabled>
        </div>
    `, [
        { text: 'Save', action: () => {
            closeModal();
            showNotification('Profile updated successfully', 'success');
        }},
        { text: 'Cancel', action: closeModal }
    ]);
}

function handleLogout(e) {
    e.preventDefault();
    if (confirm('Are you sure you want to logout?')) {
        window.location.href = '/logout.php';
    }
}
</script>