import Swal from 'sweetalert2';
document.addEventListener('DOMContentLoaded', () => {
    const stars = document.querySelectorAll('.star-rating .star');
    const ratingInput = document.getElementById('rating-value');
    const submitButton = document.getElementById('submit-rating');

    // Highlight stars on hover
    stars.forEach((star) => {
        star.addEventListener('mouseover', () => {
            const value = parseInt(star.getAttribute('data-value'));
            highlightStars(value);
        });

        star.addEventListener('mouseout', () => {
            resetStars();
        });

        // Set rating on click
        star.addEventListener('click', () => {
            const value = parseInt(star.getAttribute('data-value'));
            ratingInput.value = value;
            setActiveStars(value);
            submitButton.disabled = false;
        });
    });

    // Highlight stars up to the given value
    function highlightStars(value) {
        stars.forEach((star) => {
            const starValue = parseInt(star.getAttribute('data-value'));
            if (starValue <= value) {
                star.classList.add('active');
            } else {
                star.classList.remove('active');
            }
        });
    }

    // Reset stars to the selected rating or default state
    function resetStars() {
        const selectedValue = parseInt(ratingInput.value);
        setActiveStars(selectedValue);
    }

    // Set active stars based on the selected value
    function setActiveStars(value) {
        stars.forEach((star) => {
            const starValue = parseInt(star.getAttribute('data-value'));
            if (starValue <= value) {
                star.classList.add('active');
            } else {
                star.classList.remove('active');
            }
        });
    }

    // Submit the rating via AJAX
    // Submit the rating when the "Rate this book" text is clicked
    submitButton.addEventListener('click', () => {
        const bookId = document.querySelector('.star-rating').getAttribute('data-book-id');
        const ratingValue = ratingInput.value;

        if (ratingValue === "0") {
            alert('Please select a rating before submitting.');
            return;
        }

        fetch(`/books/${bookId}/reviews/rate`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ rating: ratingValue })
        })
            .then((response) => {
                if (response.ok) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Thank you!',
                        text: 'Your rating has been submitted successfully.',
                        confirmButtonText: 'Okay',
                        timer: 3000, // Auto close after 3 seconds
                        timerProgressBar: true,
                    }).then(() => {
                        window.location.reload(); // Reload the page after the alert
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops!',
                        text: 'Something went wrong. Please try again.',
                        confirmButtonText: 'Retry',
                    });
                }

            })
            .catch((error) => console.error('Error:', error));
    });
});
