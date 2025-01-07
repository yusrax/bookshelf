document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('search-reviews-input');
    const reviewCards = document.querySelectorAll('.review-card');

    if (searchInput) {
        searchInput.addEventListener('input', () => {
            const query = searchInput.value.toLowerCase();

            reviewCards.forEach((card) => {
                const reviewText = card.getAttribute('data-text');
                const reviewRating = card.getAttribute('data-rating');

                if (reviewText.includes(query) || reviewRating === query) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    }

    // Handle pagination dynamically
    document.querySelectorAll('.pagination a').forEach((link) => {
        link.addEventListener('click', (event) => {
            event.preventDefault();

            const url = link.getAttribute('href');
            fetch(url)
                .then((response) => response.text())
                .then((html) => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');

                    // Replace reviews section with updated content
                    const newReviews = doc.querySelector('.list-group');
                    const pagination = doc.querySelector('.pagination');

                    if (newReviews) {
                        document.querySelector('.list-group').innerHTML = newReviews.innerHTML;
                    }
                    if (pagination) {
                        document.querySelector('.pagination').innerHTML = pagination.innerHTML;
                    }
                });
        });
    });
});

