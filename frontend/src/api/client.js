const API_BASE = '/index.php?route=';

async function api(endpoint, options = {}) {
    const url = `${API_BASE}${endpoint}`;
    
    const response = await fetch(url, {
        credentials: 'include',  // Send cookies for session
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            ...options.headers
        },
        ...options
    });
    
    if (response.status === 401) {
        window.location.href = '/login';
        throw new Error('Unauthorized');
    }
    
    const data = await response.json().catch(() => ({}));
    
    if (!response.ok) {
        throw new Error(data.error || `HTTP ${response.status}`);
    }
    
    return data;
}

export default api;