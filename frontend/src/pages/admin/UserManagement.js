import React, { useState, useEffect } from 'react';
import { Button, Modal, Form, Table } from 'react-bootstrap';
import api from '../../api/client';


export default function UserManagement() {
    const [users, setUsers] = useState([]);
    const [showAdd, setShowAdd] = useState(false);
    const [showEdit, setShowEdit] = useState(false);
    const [editingUser, setEditingUser] = useState(null);
    const [refresh, setRefresh] = useState(0);
    const [showDelete, setShowDelete] = useState(false);
    const [userToDelete, setUserToDelete] = useState(null);

    useEffect(() => {
        loadData();}, 
        [refresh]);
    
    async function loadData() {
        const usersData = await api('/users');
        setUsers(usersData);
    }
    async function handleCreate(e){
        e.preventDefault();
        const formData = newFormData(e.target);
        await api('/users', {
            method: 'POST',
            body: JSON.stringify({
                username: formData.get('username'),
                password: formData.get('password'),
                role: formData.get('role')
            })
        });
        setShowAdd(false);
        setRefresh(r => r+1);
    }
    async function handleEdit(e) {
        e.preventDefault();
        const data = {
            user_id: editingUser.id,
            username: formData.get('username'),
            password: formData.get('password'),
            role: formData.get('role')
        };
        await api(`/users/${editingUser.id}`, {
            method: 'PATCH',
            body: JSON.stringify(data)
        });
        setShowEdit(false);
        setRefresh(r => r+1);
    }

    function handledDelete(userID){
        setUserToDelete(userID);
        setShowDelete(true);
    }

    async function confirmDelete() {
        if (!userToDelete) return;

        await api(`/users/${userToDelete}`, {
            method: 'DELETE'
        });
        setShowDelete(false);
        setRefresh(r => r+1);
    }

    function cancelDelete() {
        setShowDelete(false);
        setUserToDelete(ull);
    }

    function openEdit(user) {
        setEditingUser(user);
        setShowEdit(true);
    }

    return(
        <section>
            <h2>User Management</h2>
            <Button onClick={() => setShowAdd(true)} className = "mb-3">Add User</Button>

            {/* Add Modal */}
            <Modal show={showAdd} onHide={() => setShowAdd(false)}>
                <Modal.Header close Button>
                    <Modal.Title>Add User </Modal.Title>
                </Modal.Header>
                <Modal.Body>
                    <Form onSubmit={handleCreate}>
                        <Form.Group className="mb-3">
                            <Form.Control name="username" placeholder="Enter username" required />
                        </Form.Group>
                        <Form.Group className="mb-3">
                            <Form.Control name="password" placeholder="Enter password" required />
                        </Form.Group>
                        <Form.Group className="mb-3">
                            <Form.Select name="role" required>
                                <option value="admin">Admin</option>
                                <option value="developers">Developer</option>
                                <option value="hr">HR</option>
                                <option value="accounting">Accounting</option>
                                <option value="user">User</option>
                            </Form.Select>
                        </Form.Group>
                        <Button type="submit">Create</Button>
                    </Form>
                </Modal.Body>
            </Modal>

            {/* Edit Modal */}  
            <Modal show={showEdit} onHide={() => setShowEdit(false)}>
                <Modal.Header closeButton>
                    <Modal.Title>Edit User</Modal.Title>
                </Modal.Header>
                <Modal.Body>
                    {editingUser && (
                        <Form onSubmit={handleEdit}>
                            <input type="hidden" name="user_id" value={editingUser.id} />
                            <Form.Group className="mb-3">
                                <Form.Label>Username</Form.Label>
                                <Form.Control
                                    name="username"
                                    defaultValue={editingUser.username}
                                    required
                                />
                            </Form.Group>
                            <Form.Group className="mb-3">
                                <Form.Label>Password</Form.Label>
                                <Form.Control
                                    name="password"
                                    defaultValue={editingUser.password}
                                    required
                                />
                            </Form.Group>
                            <Form.Group className="mb-3">
                                <Form.Label>Role</Form.Label>
                                <Form.Select name="role" defaultValue={editingUser.role}>
                                    <option value="admin">Admin</option>
                                    <option value="developers">Developer</option>
                                    <option value="hr">HR</option>
                                    <option value="accounting">Accounting</option>
                                    <option value="user">User</option>
                                </Form.Select>                        
                            </Form.Group>
                    )}
                    </Modal.Body>    


        </section>
                )
    }
