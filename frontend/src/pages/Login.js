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
        <Container className="d-flex align-items-center justify-content-center" style={{ minHeight: '100vh' }}>
            <Card style={{ width: '100%', maxWidth: '400px' }} className="p-4 shadow-sm">
                <h1 className="h3 mb-3 text-center">Login</h1>
                {error && <Alert variant="danger">{error}</Alert>}
                <Form onSubmit={handleSubmit}>
                    <Form.Group className="mb-3">
                        <Form.Label>Username</Form.Label>
                        <Form.Control 
                            type="text" 
                            value={username}
                            onChange={e => setUsername(e.target.value)}
                            required 
                        />
                    </Form.Group>
                    <Form.Group className="mb-3">
                        <Form.Label>Password</Form.Label>
                        <Form.Control 
                            type="password"
                            value={password}
                            onChange={e => setPassword(e.target.value)}
                            required 
                        />
                    </Form.Group>
                    <Button type="submit" variant="primary" className="w-100">Login</Button>
                </Form>
            </Card>
        </Container>
    );
}