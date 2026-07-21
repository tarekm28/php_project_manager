import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';
import { Form, Button, Card, Alert, Container } from 'react-bootstrap';

export default function Login() {
    const [username, setUsername] = useState('');
    const [password, setPassword] = useState('');
    const [error, setError] = useState('');
    const { login } = useAuth();
    const navigate = useNavigate();

    async function handleSubmit(e) {
        e.preventDefault();
        setError('');
        try {
            await login(username, password);
            navigate('/');
        } catch (err) {
            setError(err.message);
        }
    }

    return (
        <div className="login-bg d-flex align-items-center justify-content-center">
            <Container style={{ maxWidth: '420px' }}>
                <Card className="login-card shadow border-0">
                    <Card.Body className="p-4 p-md-5">
                        <div className="text-center mb-4">
                            <div className="login-icon mb-3">🔐</div>
                            <h1 className="h3 fw-bold text-dark">Welcome Back</h1>
                            <p className="text-muted small mb-0">Sign in to your account</p>
                        </div>
                        
                        {error && <Alert variant="danger" className="py-2">{error}</Alert>}
                        
                        <Form onSubmit={handleSubmit}>
                            <Form.Group className="mb-3">
                                <Form.Label className="small fw-semibold text-secondary">Username</Form.Label>
                                <Form.Control 
                                    type="text" 
                                    value={username}
                                    onChange={e => setUsername(e.target.value)}
                                    placeholder="Enter username"
                                    required 
                                    className="py-2"
                                />
                            </Form.Group>
                            <Form.Group className="mb-4">
                                <Form.Label className="small fw-semibold text-secondary">Password</Form.Label>
                                <Form.Control 
                                    type="password"
                                    value={password}
                                    onChange={e => setPassword(e.target.value)}
                                    placeholder="Enter password"
                                    required 
                                    className="py-2"
                                />
                            </Form.Group>
                            <Button type="submit" variant="primary" className="w-100 py-2 fw-semibold">
                                Sign In
                            </Button>
                        </Form>
                    </Card.Body>
                </Card>
            </Container>
        </div>
    );
}