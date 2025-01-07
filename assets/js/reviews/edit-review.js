// assets/js/reviews/edit-review.js
document.addEventListener('DOMContentLoaded', () => {
    const editButton = document.getElementById('edit-button');
    const cancelButton = document.getElementById('cancel-edit-button');
    const reviewDisplay = document.getElementById('review-display');
    const editForm = document.getElementById('edit-review-form');

    if (editButton && cancelButton && reviewDisplay && editForm) {
        // Show edit form and hide review text
        editButton.addEventListener('click', () => {
            reviewDisplay.classList.add('d-none');
            editForm.classList.remove('d-none');
        });

        // Cancel edit and restore review text
        cancelButton.addEventListener('click', () => {
            editForm.classList.add('d-none');
            reviewDisplay.classList.remove('d-none');
        });
    }
});



