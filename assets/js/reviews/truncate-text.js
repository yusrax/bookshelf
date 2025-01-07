document.addEventListener('DOMContentLoaded', () => {
    const reviewTexts = document.querySelectorAll('.review-text');

    reviewTexts.forEach((text) => {
        // Check if the text content exceeds the visible area
        if (text.scrollHeight > text.clientHeight) {
            // Create the "Read More" button with the desired styling
            const button = document.createElement('a');
            button.className = 'btn btn-link p-0 read-more-btn link-dark';
            button.innerHTML = '<small>Read More</small>';

            // Add the button after the text element
            text.parentNode.appendChild(button);

            // Add event listener to toggle the expanded state
            button.addEventListener('click', (event) => {
                event.preventDefault();
                const isExpanded = text.classList.toggle('expanded');
                button.innerHTML = isExpanded
                    ? '<small>Read Less</small>'
                    : '<small>Read More</small>';
            });
        }
    });
});

