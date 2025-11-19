/**
 * Clamping Records Filters
 * - Search by plate number, ticket number
 * - Filter by status
 */
document.addEventListener('DOMContentLoaded', () => {
    const clampingTable = document.querySelector('.custom-table');
    if (!clampingTable) return;

    // Get filter inputs (support both form and inline)
    let searchInput = document.querySelector('input[name="search"]');
    let statusSelect = document.querySelector('select[name="status"]');

    // If form-based filters don't exist, they'll be null and filtering will still work with table rows
    const tbody = clampingTable.querySelector('tbody');
    if (!tbody) return;

    const performFilter = () => {
        const searchQuery = (searchInput?.value || '').toLowerCase();
        const selectedStatus = (statusSelect?.value || '').toLowerCase();

        const rows = tbody.querySelectorAll('tr');

        rows.forEach(row => {
            // Get cells - match the table structure
            const ticketCell = row.cells[1]?.textContent.toLowerCase() || '';
            const plateCell = row.cells[2]?.textContent.toLowerCase() || '';
            const statusCell = row.cells[7]?.textContent.toLowerCase() || '';

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

    // Add event listeners if filters exist
    if (searchInput) {
        searchInput.addEventListener('keyup', performFilter);
    }
    if (statusSelect) {
        statusSelect.addEventListener('change', performFilter);
    }
});
