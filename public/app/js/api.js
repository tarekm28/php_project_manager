const API_BASE = '../index.php?route=';

async function api(endpoint, options = {}) {
    const url = `${API_BASE}${endpoint}`;
    
    const response = await fetch(url, {
        credentials: 'same-origin',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            ...options.headers
        },
        ...options
    });
    
    if (response.status === 401) {
        window.location.href = 'login.html';
        return;
    }
    
    const data = await response.json().catch(() => ({}));
    
    if (!response.ok) {
        throw new Error(data.error || `HTTP ${response.status}`);
    }
    
    return data;
}

async function apiForm(endpoint, formData) {
    const url = `${API_BASE}${endpoint}`;
    
    const response = await fetch(url, {
        method: 'POST',
        credentials: 'same-origin',
        body: formData
    });
    
    if (response.status === 401) {
        window.location.href = 'login.html';
        return;
    }
    
    const data = await response.json().catch(() => ({}));
    
    if (!response.ok) {
        throw new Error(data.error || `HTTP ${response.status}`);
    }
    
    return data;
}