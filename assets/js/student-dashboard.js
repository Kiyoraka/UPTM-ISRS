document.addEventListener('DOMContentLoaded', function() {
    // Get content sections
    const mainContent = document.getElementById('main-content');
    const profileContent = document.getElementById('profile-content');
    const documentContent = document.getElementById('document-content');
    const paymentContent = document.getElementById('payment-content');

    // Get navigation links
    const navLinks = document.querySelectorAll('.nav-link');
    
    // Function to show selected content
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
        navLinks.forEach(link => {
            link.classList.remove('active');
        });
        document.querySelector(`.nav-link[data-section="${contentId}"]`).classList.add('active');
    }

    // Add click event listeners to navigation links
    navLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const section = link.getAttribute('data-section');
            showContent(section);
        });
    });

    // Document upload functionality
    const uploadButtons = document.querySelectorAll('.document-upload-btn');
    const uploadForm = document.querySelector('.upload-form');
    const cancelUploadBtn = document.querySelector('.cancel-upload-btn');
    const documentTypeInput = document.getElementById('document_type');

    // Show upload form when clicking on upload button
    uploadButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Get document type from parent element
            const documentCard = this.closest('.document-card');
            const documentTitle = documentCard.querySelector('.document-title').textContent.toLowerCase();
            
            // Set document type in the hidden input
            documentTypeInput.value = documentTitle;
            
            // Show upload form
            uploadForm.style.display = 'block';
            
            // Scroll to upload form
            uploadForm.scrollIntoView({ behavior: 'smooth' });
        });
    });

    // Hide upload form when clicking cancel
    if (cancelUploadBtn) {
        cancelUploadBtn.addEventListener('click', function() {
            uploadForm.style.display = 'none';
        });
    }

    // Initialize the dashboard with main content
    showContent('main');
});