import * as bootstrap from 'bootstrap';
document.addEventListener('DOMContentLoaded', () => {
    const deleteUserModal = document.getElementById('deleteUserModal');
    const modalUsername = document.getElementById('modal-username');
    const confirmDeleteBtn = document.getElementById('confirm-delete-btn');

    let userIdToDelete = null;

    // Attach click event to each delete icon
    document.querySelectorAll('.fa-trash-alt').forEach((deleteIcon) => {
        deleteIcon.addEventListener('click', (event) => {
            userIdToDelete = deleteIcon.getAttribute('data-user-id');
            const username = deleteIcon.getAttribute('data-user-name');

            // Update the modal content
            modalUsername.textContent = username;
        });
    });

    // Handle delete confirmation
    confirmDeleteBtn.addEventListener('click', async () => {
        if (userIdToDelete) {
            try {
                const response = await fetch(`/users/${userIdToDelete}/delete`, {
                    method: 'DELETE',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });

                if (response.ok) {
                    // Hide the modal
                    const bootstrapModal = bootstrap.Modal.getInstance(deleteUserModal);
                    bootstrapModal.hide();

                    // Reload the page to reflect the changes
                    window.location.reload();
                } else {
                    alert('Failed to delete user. Please try again.');
                }
            } catch (error) {
                console.error('Error deleting user:', error);
                alert('An error occurred. Please try again.');
            }
        }
    });
});
