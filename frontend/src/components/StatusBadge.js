import React from 'react';
import { Badge } from 'react-bootstrap';

const variants = {
    'Pending': 'warning',
    'In Progress': 'info',
    'Completed': 'success',
    'Unassigned': 'secondary'
};

export default function StatusBadge({ status }) {
    return (
        <Badge bg={variants[status] || 'secondary'} className="text-capitalize">
            {status || 'Unknown'}
        </Badge>
    );
}