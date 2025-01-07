document.querySelectorAll('.review-button').forEach(button => {
    button.addEventListener('click', (event) => {
        event.preventDefault();

        fetch(button.getAttribute('href'), { method: 'GET', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(response => {
                if (response.status === 401) {
                    // If unauthorized, show the modal
                    const loginModal = document.getElementById('loginModal');
                    if (loginModal) {
                        const modal = bootstrap.Modal.getOrCreateInstance(loginModal);
                        modal.show();
                    }
                } else {
                    // If authorized, redirect to the URL
                    window.location.href = button.getAttribute('href');
                }
            })
            .catch(error => console.error('Error:', error));
    });
});


