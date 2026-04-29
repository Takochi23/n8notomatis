// Global helper: get current user ID (email)
function getUserId() {
    return localStorage.getItem('takosaving_user_id') || '';
}

document.addEventListener('DOMContentLoaded', () => {
    const storedName = localStorage.getItem('takosaving_user');
    if (storedName) {
        const nameEl = document.getElementById('sidebar-name');
        const avatarEl = document.getElementById('sidebar-avatar');
        if(nameEl) nameEl.textContent = storedName;
        if(avatarEl) avatarEl.textContent = storedName.charAt(0).toUpperCase();
    }

    // Redirect to login if no user_id (not logged in)
    const isAuthPage = window.location.pathname === '/login' || window.location.pathname === '/register' || window.location.pathname === '/';
    if (!getUserId() && !isAuthPage) {
        window.location.href = '/login';
    }

    // Logout: clear localStorage
    const logoutLink = document.getElementById('logout-link');
    if (logoutLink) {
        logoutLink.addEventListener('click', function(e) {
            localStorage.removeItem('takosaving_user');
            localStorage.removeItem('takosaving_user_id');
        });
    }
});
