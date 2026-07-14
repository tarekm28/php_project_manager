let currentUser = null;

async function checkAuth() {
    try {
        currentUser = await api('/me');
        return currentUser;
    } catch (e) {
        window.location.href = 'login.html';
        return null;
    }
}

function getUser() {
    return currentUser;
}

function isAdmin() {
    return currentUser && currentUser.role === 'admin';
}

async function logout() {
    try {
        await api('/logout', { method: 'POST' });
    } catch (e) {}
    window.location.href = 'login.html';
}