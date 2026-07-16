import React from 'react';
import { BrowserRouter, Routes, Route, Navigate } from 'react-router-dom';
import { AuthProvider, useAuth } from './context/AuthContext';

// Pages
import Login from './pages/Login';
import AdminDashboard from './pages/AdminDashboard';
import EmployeeDashboard from './pages/EmployeeDashboard';

function ProtectedRoute({ children, requireAdmin }) {
    const { user, isAdmin } = useAuth();

    if (!user) return <Navigate to="/login" />;
    if (requireAdmin && !isAdmin) return <Navigate to="/" />;

    return children;
}

function App() {
    return (
        <AuthProvider>
            <BrowserRouter>
                <Routes>
                    <Route path="/login" element={<Login />} />
                    
                    <Route path="/*" element={
                        <ProtectedRoute>
                            <RoleBasedDashboard />
                        </ProtectedRoute>
                    } />
                    
                    <Route path="/admin/*" element={
                        <ProtectedRoute requireAdmin>
                            <AdminDashboard />
                        </ProtectedRoute>
                    } />
                </Routes>
            </BrowserRouter>
        </AuthProvider>
    );
}

function RoleBasedDashboard() {
    const { isAdmin } = useAuth();
    return isAdmin ? <AdminDashboard /> : <EmployeeDashboard />;
}

export default App;