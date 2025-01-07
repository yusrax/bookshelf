document.addEventListener('DOMContentLoaded', () => {
    const sortSelect = document.getElementById('sort-reviews');

    if (sortSelect) {
        sortSelect.addEventListener('change', (event) => {
            const selectedSort = event.target.value;

            // Update the URL with the selected sort option
            const urlParams = new URLSearchParams(window.location.search);
            urlParams.set('sort', selectedSort);

            // Reload the page with the updated query parameters
            window.location.search = urlParams.toString();
        });
    }
});

