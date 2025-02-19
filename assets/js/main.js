// Main JavaScript file
document.addEventListener('DOMContentLoaded', function() {
    // Add loading state to buttons when clicked
    const buttons = document.querySelectorAll('.btn');
    buttons.forEach(button => {
        button.addEventListener('click', function(e) {
            // Add loading state
            this.style.opacity = '0.7';
            this.style.cursor = 'wait';
        });
    });
});