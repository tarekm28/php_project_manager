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

    return (
        <>
            <header className="p-3 bg-light">
                <h1>Employee Dashboard</h1>
                <Button variant="outline-secondary" size="sm" onClick={logout} style={{position: 'absolute', top: 16, right: 16}}>
                    Logout
                </Button>
            </header>
            <Navbar bg="light" className="px-3">
                <Nav>
                    <Nav.Link as={Link} to="/" active={location.pathname === '/'}>Project Overview</Nav.Link>
                    <Nav.Link as={Link} to="/team" active={location.pathname === '/team'}>Team Tasks</Nav.Link>
                    <Nav.Link as={Link} to="/mine" active={location.pathname === '/mine'}>Current Tasks</Nav.Link>
                </Nav>
            </Navbar>
            <Container className="py-4">
                <Routes>
                    <Route path="/" element={<ProjectOverview />} />
                    <Route path="/team" element={<TeamTasks />} />
                    <Route path="/mine" element={<CurrentTasks />} />
                </Routes>
            </Container>
        </>
    );
}