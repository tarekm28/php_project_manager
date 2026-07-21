import React, { useEffect, useRef } from 'react';
import $ from 'jquery';
import 'datatables.net-bs5';
import 'datatables.net-bs5/css/dataTables.bootstrap5.min.css';

export default function DataTable({ id, refreshKey = 0, children, options = {} }) {
    const tableRef = useRef(null);

    useEffect(() => {
        if (!tableRef.current) return;
        const $table = $(tableRef.current);

        if ($.fn.DataTable.isDataTable($table)) {
            $table.DataTable().destroy();
        }

        const dt = $table.DataTable({
            pageLength: 10,
            responsive: true,
            language: {
                search: '_INPUT_',
                searchPlaceholder: 'Search...',
                lengthMenu: 'Show _MENU_ entries',
                info: 'Showing _START_ to _END_ of _TOTAL_ entries',
                paginate: { previous: '‹', next: '›' }
            },
            ...options
        });

        return () => dt.destroy();
    }, [refreshKey, options]);

    return (
        <div className="table-responsive">
            <table ref={tableRef} id={id} className="table table-striped table-hover align-middle" style={{ width: '100%' }}>
                {children}
            </table>
        </div>
    );
}