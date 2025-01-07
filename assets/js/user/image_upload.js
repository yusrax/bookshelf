document.addEventListener('DOMContentLoaded', () => {
    const profilePictureInput = document.getElementById('profile-picture-input');
    const profilePicturePreview = document.getElementById('profile-picture-preview');

    // When the user clicks on the image, trigger the file input
    profilePicturePreview.addEventListener('click', () => {
        profilePictureInput.click();
    });

    // When a file is selected, preview it
    profilePictureInput.addEventListener('change', async (event) => {
        const file = event.target.files[0];
        if (file) {
            // Show a preview of the image
            const reader = new FileReader();
            reader.onload = (e) => {
                profilePicturePreview.src = e.target.result;
            };
            reader.readAsDataURL(file);

            // Submit the file via AJAX
            const formData = new FormData();
            formData.append('profilePicture', file);

            try {
                const response = await fetch('/user/profile-picture', {
                    method: 'POST',
                    body: formData,
                });

                const result = await response.json();
                if (response.ok) {
                    alert('Profile picture updated successfully!');
                } else {
                    alert(result.error || 'An error occurred while uploading the profile picture.');
                }
            } catch (error) {
                console.error('Error uploading profile picture:', error);
            }
        }
    });
});
