/**
 * User Management Actions (Approve/Reject)
 */
document.addEventListener('DOMContentLoaded', () => {
  const userTable = document.getElementById('userTable');
  if (!userTable) return;

  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

  // Handle approve buttons
  document.querySelectorAll('.approve-btn').forEach(btn => {
    btn.addEventListener('click', async (e) => {
      e.preventDefault();
      const row = btn.closest('tr');
      const userId = row.dataset.userId;
      if (!userId) {
        alert('User ID not found');
        return;
      }

      const ok = confirm('Are you sure you want to approve this user?');
      if (!ok) return;

      try {
        const res = await fetch(`/users/${userId}/approve`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
          },
          body: JSON.stringify({})
        });
        const data = await res.json();
        if (data.success) {
          alert('User approved successfully');
          window.location.reload();
        } else {
          alert('Failed to approve user: ' + (data.message || 'Unknown error'));
        }
      } catch (err) {
        console.error(err);
        alert('Error approving user: ' + err.message);
      }
    });
  });

  // Handle reject buttons
  document.querySelectorAll('.reject-btn').forEach(btn => {
    btn.addEventListener('click', async (e) => {
      e.preventDefault();
      const row = btn.closest('tr');
      const userId = row.dataset.userId;
      if (!userId) {
        alert('User ID not found');
        return;
      }

      const ok = confirm('Are you sure you want to reject/suspend this user?');
      if (!ok) return;

      try {
        const res = await fetch(`/users/${userId}/reject`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
          },
          body: JSON.stringify({})
        });
        const data = await res.json();
        if (data.success) {
          alert('User rejected/suspended successfully');
          window.location.reload();
        } else {
          alert('Failed to reject user: ' + (data.message || 'Unknown error'));
        }
      } catch (err) {
        console.error(err);
        alert('Error rejecting user: ' + err.message);
      }
    });
  });

  // Handle view buttons (navigate to user details page)
  document.querySelectorAll('.view-btn').forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      const row = btn.closest('tr');
      const userId = row.dataset.userId;
      if (!userId) {
        alert('User ID not found');
        return;
      }
      // Navigate to user detail page
      window.location.href = `/users/${userId}`;
    });
  });

});
