import React, { createContext, useContext, useState, useEffect } from 'react';
import api from '../api/client';

const AuthContext = createContext(null);

export function AuthProvider({ children }) {
    const [user, setUser] = useState(null);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        checkAuth();
    }, []);

    async function checkAuth() {
        try {
            const userData = await api('/me');
            setUser(userData);
        } catch (error) {
            setUser(null);
        } finally {
            setLoading(false);
        }
    }

    async function login(username, password) {
        const result = await api('/login', {
            method: 'POST',
            body: JSON.stringify({ username, password })
        });
        await checkAuth();
        return result;
    }

    async function logout() {
        try {
            await api('/logout', { method: 'POST' });
        } finally {
            setUser(null);
        }
    }

    if (loading) return <div>Loading...</div>;

    return (
        <AuthContext.Provider value={{ user, login, logout, isAdmin: user?.role === 'admin' }}>
            {children}
        </AuthContext.Provider>
    );
}

export function useAuth() {
    return useContext(AuthContext);
}