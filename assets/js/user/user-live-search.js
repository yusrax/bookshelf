document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('search-users-input');
    const userCards = document.querySelectorAll('.user-card');
    const noResultsMessage = document.getElementById('no-results-message');
    const userListContainer = document.getElementById('user-list-container');

    if (searchInput) {
        searchInput.addEventListener('input', () => {
            const query = searchInput.value.toLowerCase();
            let matchesFound = false;

            userCards.forEach((card) => {
                const username = card.getAttribute('data-username');
                const email = card.getAttribute('data-email');
                const name = card.getAttribute('data-name');

                if (
                    username.includes(query) ||
                    email.includes(query) ||
                    name.includes(query)
                ) {
                    card.style.display = 'block'; // Show matching users
                    matchesFound = true;
                } else {
                    card.style.display = 'none'; // Hide non-matching users
                }
            });

            // Toggle visibility of "No users found" message
            if (matchesFound) {
                noResultsMessage.classList.add('d-none');
                userListContainer.classList.remove('d-none');
            } else {
                noResultsMessage.classList.remove('d-none');
                userListContainer.classList.add('d-none');
            }
        });
    }
});


