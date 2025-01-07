import * as bootstrap from 'bootstrap';
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.modal').forEach(modal => {
        const confirmDeleteButton = modal.querySelector('.btn-danger');
        let reviewIdToDelete = null;

        modal.addEventListener('show.bs.modal', (event) => {
            const trashIcon = event.relatedTarget;
            reviewIdToDelete = trashIcon.getAttribute('data-review-id');
        });

        confirmDeleteButton?.addEventListener('click', async () => {
            if (reviewIdToDelete) {
                try {
                    const response = await fetch(`/reviews/${reviewIdToDelete}/delete`, {
                        method: 'DELETE',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    });

                    if (response.ok) {
                        // Hide the modal and reload the page
                        const bootstrapModal = bootstrap.Modal.getInstance(modal);
                        bootstrapModal.hide();

                        // Delay page reload slightly to ensure modal closes properly
                        setTimeout(() => {
                            window.location.reload();
                        }, 300);
                    } else {
                        alert('Failed to delete review. Please try again.');
                    }
                } catch (error) {
                    console.error('Error deleting review:', error);
                    alert('An error occurred. Please try again.');
                }
            }
        });
    });
});




