import React from 'react';
import { Routes, Route, Link, useLocation } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';
import { Navbar, Nav, Button, Container } from 'react-bootstrap';

import ProjectOverview from './employee/ProjectOverview';
import TeamTasks from './employee/TeamTasks';
import CurrentTasks from './employee/CurrentTasks';

export default function EmployeeDashboard() {
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
            <header className="bg-white border-bottom px-4 py-3 position-relative">
                <h1 className="h3 mb-0 fw-semibold">Employee Dashboard</h1>
                <div className="position-absolute top-0 end-0 p-3 d-flex align-items-center gap-3">
                    <span className="text-muted small d-none d-md-inline">
                        Signed in as: <strong>{user?.username}</strong>
                    </span>
                    <Button variant="outline-secondary" size="sm" onClick={logout}>
                        Logout
                    </Button>
                </div>
            </header>

            <Navbar bg="body-tertiary" className="px-3 border-bottom">
                <Nav>
                    {navLink('/', 'Project Overview')}
                    {navLink('/team', 'Team Tasks')}
                    {navLink('/mine', 'Current Tasks')}
                </Nav>
            </Navbar>

            <Container className="py-4">
                <Routes>
                    <Route path="/" element={<ProjectOverview />} />
                    <Route path="/team" element={<TeamTasks />} />
                    <Route path="/mine" element={<CurrentTasks />} />
                </Routes>
            </Container>
        </div>
    );
}