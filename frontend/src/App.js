import React from 'react';
import { BrowserRouter, Routes, Route, Navigate } from 'react-router-dom';
import { AuthProvider, useAuth } from './context/AuthContext';
import 'bootstrap/dist/css/bootstrap.min.css';
import './App.css';

import Login from './pages/Login';
import AdminDashboard from './pages/AdminDashboard';
import EmployeeDashboard from './pages/EmployeeDashboard';

function ProtectedRoute({ children, requireAdmin }) {
    const { user, isAdmin } = useAuth();

    if (!user) return <Navigate to="/login" replace />;
    if (requireAdmin && !isAdmin) return <Navigate to="/" replace />;

    return children;
}

function App() {
    return (
        <AuthProvider>
            <BrowserRouter>
                <Routes>
                    <Route path="/login" element={<Login />} />
                    
                    <Route path="/admin/*" element={
                        <ProtectedRoute requireAdmin>
                            <AdminDashboard />
                        </ProtectedRoute>
                    } />
                    
                    <Route path="/*" element={
                        <ProtectedRoute>
                            <RoleBasedDashboard />
                        </ProtectedRoute>
                    } />
                </Routes>
            </BrowserRouter>
        </AuthProvider>
    );
}

function RoleBasedDashboard() {
    const { isAdmin } = useAuth();
    return isAdmin ? <Navigate to="/admin" replace /> : <EmployeeDashboard />;
}

export default App;