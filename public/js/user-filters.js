/**
 * User Management Filters
 * - Search by name, username, email, phone
 * - Filter by status (active, pending, inactive)
 * - Filter by role (admin, enforcer, user)
 */
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const roleFilter = document.getElementById('roleFilter');
    const userTable = document.getElementById('userTable');

    if (!userTable) return;

    // Perform filtering
    const performFilter = () => {
        const searchQuery = (searchInput?.value || '').toLowerCase();
        const selectedStatus = statusFilter?.value || '';
        const selectedRole = roleFilter?.value || '';

        const rows = userTable.querySelectorAll('tbody tr');

        rows.forEach(row => {
            // Get cell content
            const nameCell = row.cells[0]?.textContent.toLowerCase() || '';
            const usernameCell = row.cells[1]?.textContent.toLowerCase() || '';
            const emailCell = row.cells[3]?.textContent.toLowerCase() || '';
            const phoneCell = row.cells[4]?.textContent.toLowerCase() || '';
            const roleCell = row.cells[5]?.textContent.toLowerCase() || '';
            const statusCell = row.cells[6]?.textContent.toLowerCase() || '';

            // Match search query (check all relevant fields)
            const matchesSearch =
                searchQuery === '' ||
                nameCell.includes(searchQuery) ||
                usernameCell.includes(searchQuery) ||
                emailCell.includes(searchQuery) ||
                phoneCell.includes(searchQuery);

            // Match status filter
            const matchesStatus = selectedStatus === '' || statusCell.includes(selectedStatus.toLowerCase());

            // Match role filter
            const matchesRole = selectedRole === '' || roleCell.includes(selectedRole.toLowerCase());

            // Show or hide row
            if (matchesSearch && matchesStatus && matchesRole) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    };

    // Add event listeners
    if (searchInput) {
        searchInput.addEventListener('keyup', performFilter);
    }
    if (statusFilter) {
        statusFilter.addEventListener('change', performFilter);
    }
    if (roleFilter) {
        roleFilter.addEventListener('change', performFilter);
    }
});
