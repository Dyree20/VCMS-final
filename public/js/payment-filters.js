/**
 * Payment Page Filters
 * - Search by plate, ticket ID
 * - Filter by status (all, paid, unpaid, pending)
 * - Basic client-side filtering since this page shows summary
 */
document.addEventListener('DOMContentLoaded', () => {
    const paymentTable = document.querySelector('.payments-table');
    if (!paymentTable) return;

    const tbody = paymentTable.querySelector('tbody');
    if (!tbody) return;

    // Get filter inputs
    const searchInputs = document.querySelectorAll('input[type="text"]');
    const statusSelect = document.querySelector('select');

    const performFilter = () => {
        // Get the first text input as search (plate/ticket)
        const searchQuery = (searchInputs[0]?.value || '').toLowerCase();
        const selectedStatus = (statusSelect?.value || '').toLowerCase();

        const rows = tbody.querySelectorAll('tr');

        rows.forEach(row => {
            // Get cells - match the payment table structure
            const ticketCell = row.cells[0]?.textContent.toLowerCase() || '';
            const plateCell = row.cells[1]?.textContent.toLowerCase() || '';
            const statusCell = row.cells[6]?.textContent.toLowerCase() || '';

            // Search in ticket and plate
            const matchesSearch =
                searchQuery === '' ||
                ticketCell.includes(searchQuery) ||
                plateCell.includes(searchQuery);

            // Filter by status
            const matchesStatus = selectedStatus === '' || statusCell.includes(selectedStatus);

            // Show or hide row
            if (matchesSearch && matchesStatus) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    };

    // Add event listener to first search input
    if (searchInputs[0]) {
        searchInputs[0].addEventListener('keyup', performFilter);
    }
    // Add event listener to status select
    if (statusSelect) {
        statusSelect.addEventListener('change', performFilter);
    }
});
