import React, { useState, useEffect } from 'react';
import { Table, Badge, Pagination, Button, Modal } from 'react-bootstrap';
import api from '../../api/client';
import PageLoader from '../../components/PageLoader';

export default function ActivityLogs() {
    const [logs, setLogs] = useState([]);
    const [page, setPage] = useState(1);
    const [totalPages, setTotalPages] = useState(1);
    const [total, setTotal] = useState(0);
    const [selectedLog, setSelectedLog] = useState(null);
    const [showDetail, setShowDetail] = useState(false);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        loadLogs();
    }, [page]);

    async function loadLogs() {
        setLoading(true);
        try {
            const data = await api(`/logs?page=${page}`);
            setLogs(data.logs);
            setPage(data.page);
            setTotalPages(data.total_pages);
            setTotal(data.total);
        } catch (error) {
            console.error('Failed to load logs:', error);
        } finally {
            setLoading(false);
        }
    }

    function viewDetail(log) {
        setSelectedLog(log);
        setShowDetail(true);
    }

    function formatChanges(log) {
        const oldVals = log.old_values ? JSON.parse(log.old_values) : null;
        const newVals = log.new_values ? JSON.parse(log.new_values) : null;

        if (!oldVals && newVals) return <span className="text-success">Created</span>;
        if (oldVals && !newVals) return <span className="text-danger">Deleted</span>;
        if (!oldVals && !newVals) return <span className="text-muted">—</span>;

        const changedFields = Object.keys(newVals).filter(
            key => oldVals[key] !== newVals[key] && key !== 'password'
        );

        if (changedFields.length === 0) return <span className="text-muted">No changes</span>;

        return (
            <div className="small">
                {changedFields.map(field => (
                    <div key={field}>
                        <strong>{field}:</strong>{' '}
                        <span className="text-danger">{String(oldVals[field] ?? 'null')}</span>
                        {' → '}
                        <span className="text-success">{String(newVals[field])}</span>
                    </div>
                ))}
            </div>
        );
    }

    const actionColors = {
        create: 'success',
        update: 'primary',
        delete: 'danger',
        take: 'info',
        complete: 'warning',
        login: 'secondary',
        logout: 'dark'
    };

    if (loading) return <PageLoader />;

    return (
        <section>
            <h2 className="h4 mb-3">Activity Logs</h2>
            <p className="text-muted">Total: {total} entries</p>

            <div className="table-responsive">
                <Table striped hover className="align-middle">
                    <thead>
                        <tr>
                            <th>Time</th>
                            <th>User</th>
                            <th>Action</th>
                            <th>Entity</th>
                            <th>ID</th>
                            <th>Changes</th>
                            <th style={{ width: '80px' }}>Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        {logs.map(log => (
                            <tr key={log.id}>
                                <td className="text-nowrap small">{new Date(log.created_at).toLocaleString()}</td>
                                <td>{log.username}</td>
                                <td>
                                    <Badge bg={actionColors[log.action] || 'secondary'}>
                                        {log.action}
                                    </Badge>
                                </td>
                                <td>{log.entity_type}</td>
                                <td>{log.entity_id}</td>
                                <td>{formatChanges(log)}</td>
                                <td>
                                    <Button size="sm" variant="outline-info" onClick={() => viewDetail(log)}>
                                        View
                                    </Button>
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </Table>
            </div>

            <div className="d-flex justify-content-center">
                <Pagination>
                    <Pagination.Prev disabled={page === 1} onClick={() => setPage(p => p - 1)} />
                    {Array.from({ length: totalPages }, (_, i) => i + 1).map(i => (
                        <Pagination.Item key={i} active={i === page} onClick={() => setPage(i)}>
                            {i}
                        </Pagination.Item>
                    ))}
                    <Pagination.Next disabled={page === totalPages} onClick={() => setPage(p => p + 1)} />
                </Pagination>
            </div>

            <Modal show={showDetail} onHide={() => setShowDetail(false)} size="lg">
                <Modal.Header closeButton>
                    <Modal.Title>Log Entry #{selectedLog?.id}</Modal.Title>
                </Modal.Header>
                <Modal.Body>
                    {selectedLog && (
                        <>
                            <Table bordered size="sm">
                                <tbody>
                                    <tr>
                                        <td style={{ width: '120px' }}><strong>Time</strong></td>
                                        <td>{new Date(selectedLog.created_at).toLocaleString()}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>User</strong></td>
                                        <td>{selectedLog.username} (ID: {selectedLog.user_id})</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Action</strong></td>
                                        <td>
                                            <Badge bg={actionColors[selectedLog.action] || 'secondary'}>
                                                {selectedLog.action}
                                            </Badge>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Entity</strong></td>
                                        <td>{selectedLog.entity_type} #{selectedLog.entity_id}</td>
                                    </tr>
                                </tbody>
                            </Table>

                            <h5 className="mt-4">Changes</h5>
                            {selectedLog.old_values && (
                                <>
                                    <h6 className="text-danger small fw-bold mt-3">Before</h6>
                                    <pre className="bg-light p-2 rounded border small mb-3">
                                        {JSON.stringify(JSON.parse(selectedLog.old_values), null, 2)}
                                    </pre>
                                </>
                            )}
                            {selectedLog.new_values && (
                                <>
                                    <h6 className="text-success small fw-bold mt-3">After</h6>
                                    <pre className="bg-light p-2 rounded border small mb-3">
                                        {JSON.stringify(JSON.parse(selectedLog.new_values), null, 2)}
                                    </pre>
                                </>
                            )}
                            {!selectedLog.old_values && !selectedLog.new_values && (
                                <p className="text-muted">No data captured</p>
                            )}
                        </>
                    )}
                </Modal.Body>
            </Modal>
        </section>
    );
}