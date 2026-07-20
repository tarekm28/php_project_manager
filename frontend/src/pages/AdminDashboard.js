import React from 'react';
import { Routes, Route, Link, useLocation } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';
import { Navbar, Nav, Button, Container } from 'react-bootstrap';

import ProjectOverview from './admin/ProjectOverview';
import TaskManagement from './admin/TaskManagement';
import UserManagement from './admin/UserManagement';
import ActivityLogs from './admin/ActivityLogs';

export default function AdminDashboard() {
    const { logout, user } = useAuth();
    const location = useLocation();

    const navLink = (path, label) => (
        <Nav.Link 
            as={Link} 
            to={path}
            active={location.pathname === path || location.pathname === path + '/'}
            className="fw-medium"
        >
            {label}
        </Nav.Link>
    );

    return (
        <div className="page-content">
            <Navbar bg="body-tertiary" expand="lg" className="px-3 border-bottom">
                <Container fluid className="p-0">
                    <Nav className="me-auto">
                        {navLink('/admin', 'Project Overview')}
                        {navLink('/admin/tasks', 'Task Management')}
                        {navLink('/admin/users', 'User Management')}
                        {navLink('/admin/logs', 'Activity Logs')}
                    </Nav>
                </Container>
            </Navbar>

            <Container className="py-4">
                <header className="d-flex justify-content-between align-items-center mb-4">
                    <h1 className="h3 mb-0 fw-semibold">Project Manager Dashboard</h1>
                    <div className="d-flex align-items-center gap-3">
                        <span className="text-muted small">
                            Signed in as: <strong>{user?.username}</strong>
                        </span>
                        <Button variant="outline-secondary" size="sm" onClick={logout}>
                            Logout
                        </Button>
                    </div>
                </header>

                <Routes>
                    <Route path="/" element={<ProjectOverview />} />
                    <Route path="/tasks" element={<TaskManagement />} />
                    <Route path="/users" element={<UserManagement />} />
                    <Route path="/logs" element={<ActivityLogs />} />
                </Routes>
            </Container>
        </div>
    );
}