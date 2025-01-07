document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('search-input');
    const searchButton = document.getElementById('search-button');

    const updateSearchResults = () => {
        const query = searchInput.value.trim();

        // Update the URL with the search query
        const urlParams = new URLSearchParams(window.location.search);
        urlParams.set('search', query);
        urlParams.delete('page'); // Reset pagination to the first page
        window.location.search = urlParams.toString();
    };

    // Trigger search on button click
    searchButton.addEventListener('click', updateSearchResults);

    // Optionally, trigger search on pressing Enter
    searchInput.addEventListener('keypress', (event) => {
        if (event.key === 'Enter') {
            event.preventDefault();
            updateSearchResults();
        }
    });
});


