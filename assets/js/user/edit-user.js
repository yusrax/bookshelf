document.addEventListener('DOMContentLoaded', () => {
    const editButton = document.getElementById('edit-profile-btn');
    const cancelButton = document.getElementById('cancel-profile-btn');
    const saveButton = document.getElementById('save-profile-btn');
    const editFormContainer = document.getElementById('edit-profile-form');
    const profileDetails = document.getElementById('profile-details');
    const formElement = editFormContainer.querySelector('#edit-user-form');

    // Show the edit form
    editButton?.addEventListener('click', (event) => {
        event.preventDefault();
        profileDetails.classList.add('d-none');
        editFormContainer.classList.remove('d-none');
    });

    // Cancel editing and reset the form
    cancelButton?.addEventListener('click', (event) => {
        event.preventDefault();
        editFormContainer.classList.add('d-none');
        profileDetails.classList.remove('d-none');
    });

    // Handle form submission
    formElement?.addEventListener('submit', async (event) => {
        event.preventDefault();
        const formData = new FormData(formElement);

        try {
            const response = await fetch(formElement.action, {
                method: 'POST',
                body: formData,
            });

            if (response.status === 422) {
                const html = await response.text();
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');

                // Replace the form content and reinitialize the form
                const newForm = doc.querySelector('#edit-profile-form');
                if (newForm) {
                    editFormContainer.innerHTML = newForm.innerHTML;
                }

                return;
            }

            if (response.ok) {
                window.location.reload();
            }
        } catch (error) {
            console.error('Error submitting form:', error);
        }
    });
});


