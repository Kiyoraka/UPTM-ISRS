document.addEventListener('DOMContentLoaded', function() {
    // Content switching functionality
    const mainContent = document.getElementById('main-content');
    const studentListContent = document.getElementById('student-list-content');

    // Get navigation links
    const mainLink = document.querySelector('.nav-link[data-section="main"]');
    const studentListLink = document.querySelector('.nav-link[data-section="student-list"]');

    function showContent(contentId) {
        // Hide all content sections
        mainContent.style.display = 'none';
        studentListContent.style.display = 'none';

        // Show selected content
        switch(contentId) {
            case 'main':
                mainContent.style.display = 'block';
                break;
            case 'student-list':
                studentListContent.style.display = 'block';
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

    studentListLink.addEventListener('click', (e) => {
        e.preventDefault();
        showContent('student-list');
    });
});