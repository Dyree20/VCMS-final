document.addEventListener('DOMContentLoaded', () => {
    const filterButtons = document.querySelectorAll('.filter-btn');
    const entries = document.querySelectorAll('.entry');

    filterButtons.forEach(button => {
        button.addEventListener('click', () => {
            filterButtons.forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');

            const status = button.dataset.status;

            entries.forEach(entry => {
                const entryStatus = entry.dataset.status;
                if (status === 'all' || entryStatus === status) {
                    entry.style.display = 'flex';
                } else {
                    entry.style.display = 'none';
                }
            });
        });
    });
});
