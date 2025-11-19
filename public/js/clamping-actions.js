document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.action-btn').forEach(btn => {
    btn.addEventListener('click', async (e) => {
      e.preventDefault();
      const id = btn.dataset.id;
      const action = btn.dataset.action; // accept|reject|approve

      // Confirm for destructive actions
      if (action === 'reject') {
        const ok = confirm('Are you sure you want to REJECT this clamping? This action cannot be undone.');
        if (!ok) return;
      }
      if (action === 'approve') {
        const ok = confirm('Approve this clamping?');
        if (!ok) return;
      }

      const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
      try {
        const res = await fetch(`/clampings/${id}/${action}`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json'
          },
          body: JSON.stringify({})
        });
        const data = await res.json();
        if (data.success) {
          // simple refresh to reflect status change
          window.location.reload();
        } else {
          alert('Action failed');
        }
      } catch (err) {
        console.error(err);
        alert('Action failed');
      }
    });
  });
});
