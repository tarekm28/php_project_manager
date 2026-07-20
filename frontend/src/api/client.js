const API_BASE = '/index.php?route=';

async function api(endpoint, options = {}) {
    const url = `${API_BASE}${endpoint}`;
    
    const response = await fetch(url, {
        credentials: 'include',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            ...options.headers
        },
        ...options
    });
    
    // Don't force-redirect for /me — let AuthContext handle 401 gracefully
    if (response.status === 401 && endpoint !== '/me') {
        if (window.location.pathname !== '/login') {
            window.location.href = '/login';
        }
        throw new Error('Unauthorized');
    }
    
    const data = await response.json().catch(() => ({}));
    
    if (!response.ok) {
        throw new Error(data.error || `HTTP ${response.status}`);
    }
    
    return data;
}

export default api;