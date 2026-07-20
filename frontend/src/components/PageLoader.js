import React from 'react';

export default function PageLoader() {
    return (
        <div className="text-center py-5">
            <div className="spinner-border text-primary" role="status">
                <span className="visually-hidden">Loading...</span>
            </div>
            <p className="text-muted mt-2 small">Loading...</p>
        </div>
    );
}