import { loadOrders, renderPaginationLinks, importOrders } from './helpers.js';

document.addEventListener('DOMContentLoaded', function () {
    loadOrders();

    // Event for pagination
    document.getElementById('pagination-links').addEventListener('click', function (e) {
        if (e.target.tagName === 'A' && !e.target.classList.contains('disabled')) {
            e.preventDefault();
            const pageUrl = e.target.href;
            const page = new URL(pageUrl).searchParams.get('page');

            // Get the currently selected financial status
            const financialStatus = document.getElementById('financial-status-filter').value;

            // Load orders with the selected financial status
            loadOrders(page, financialStatus);
        }
    });

    // Filter orders by financial status
    document.getElementById('financial-status-filter').addEventListener('change', function () {
        const financial_status = this.value;
        loadOrders(1, financial_status);
    });

    // Event listener for the import button
    document.getElementById('import-button').addEventListener('click', importOrders);
});
