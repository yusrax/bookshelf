document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.like-button').forEach(button => {
        button.addEventListener('click', async () => {
            const reviewId = button.dataset.reviewId;

            try {
                const response = await fetch(`/reviews/${reviewId}/like`, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });

                if (response.ok) {
                    const data = await response.json();
                    if (data.success) {
                        const likesCountElement = document.querySelector(`#likes-count-${reviewId}`);
                        const icon = button.querySelector('i');

                        likesCountElement.textContent = data.likes;

                        if (data.liked) {
                            button.classList.remove('btn-outline-primary');
                            button.classList.add('btn-primary');
                            icon.classList.remove('far', 'fa-heart');
                            icon.classList.add('fas', 'fa-heart');
                        } else {
                            button.classList.remove('btn-primary');
                            button.classList.add('btn-outline-primary');
                            icon.classList.remove('fas', 'fa-heart');
                            icon.classList.add('far', 'fa-heart');
                        }
                    }
                }
            } catch (error) {
                console.error('Error toggling like:', error);
            }
        });
    });
});


