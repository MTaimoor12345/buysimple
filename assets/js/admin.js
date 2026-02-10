// Admin specific JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Confirm delete actions
    const deleteLinks = document.querySelectorAll('a[onclick*="confirm"]');
    deleteLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to delete this item?')) {
                e.preventDefault();
            }
        });
    });

    // Auto-submit status changes
    const statusSelects = document.querySelectorAll('.status-form select');
    statusSelects.forEach(select => {
        select.addEventListener('change', function() {
            this.closest('form').submit();
        });
    });
});

