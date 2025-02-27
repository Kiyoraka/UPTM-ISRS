document.addEventListener('DOMContentLoaded', function() {

    // Content switching functionality
    const mainContent = document.getElementById('main-content');
    const profileContent = document.getElementById('profile-content');
    const documentContent = document.getElementById('document-content');
    const paymentContent = document.getElementById('payment-content');

    // Get navigation links
    const mainLink = document.querySelector('.nav-link[data-section="main"]');
    const profileLink = document.querySelector('.nav-link[data-section="profile"]');
    const documentLink = document.querySelector('.nav-link[data-section="document"]');
    const paymentLink = document.querySelector('.nav-link[data-section="payment"]');

    // Function to show selected content and hide others
    function showContent(contentId) {
        // Hide all content sections
        mainContent.style.display = 'none';
        profileContent.style.display = 'none';
        documentContent.style.display = 'none';
        paymentContent.style.display = 'none';

        // Show selected content
        switch(contentId) {
            case 'main':
                mainContent.style.display = 'block';
                break;
            case 'profile':
                profileContent.style.display = 'block';
                break;
            case 'document':
                documentContent.style.display = 'block';
                break;
            case 'payment':
                paymentContent.style.display = 'block';
                break;
        }

        // Update active state in navigation
        document.querySelectorAll('.nav-link').forEach(link => {
            link.classList.remove('active');
        });
        document.querySelector(`.nav-link[data-section="${contentId}"]`).classList.add('active');
    }

    // Add click event listeners to navigation links
    mainLink.addEventListener('click', (e) => {
        e.preventDefault();
        showContent('main');
    });

    profileLink.addEventListener('click', (e) => {
        e.preventDefault();
        showContent('profile');
    });

    documentLink.addEventListener('click', (e) => {
        e.preventDefault();
        showContent('document');
    });

    paymentLink.addEventListener('click', (e) => {
        e.preventDefault();
        showContent('payment');
    });

    // Connect change password link to modal
    document.getElementById('change-password-link').addEventListener('click', function(e) {
        e.preventDefault();
        document.getElementById('passwordModal').style.display = 'block';
    });

});