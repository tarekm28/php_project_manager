import React from 'react';
import { Routes, Route, Link, useLocation } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';
import { Navbar, Nav, Button, Container } from 'react-bootstrap';

// Sub-pages
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
            active={location.pathname === path}
        >
            {label}
        </Nav.Link>
    );

    return (
        <>
            <Navbar bg="light" className="px-3">
                <Nav className="me-auto">
                    {navLink('/', 'Project Overview')}
                    {navLink('/tasks', 'Task Management')}
                    {navLink('/users', 'User Management')}
                    {navLink('/logs', 'Activity Logs')}
                </Nav>
                <Navbar.Text className="me-3">
                    Signed in as: <strong>{user?.username}</strong>
                </Navbar.Text>
                <Button variant="outline-secondary" size="sm" onClick={logout}>
                    Logout
                </Button>
            </Navbar>

            <Container className="py-4">
                <Routes>
                    <Route path="/" element={<ProjectOverview />} />
                    <Route path="/tasks" element={<TaskManagement />} />
                    <Route path="/users" element={<UserManagement />} />
                    <Route path="/logs" element={<ActivityLogs />} />
                </Routes>
            </Container>
        </>
    );
}