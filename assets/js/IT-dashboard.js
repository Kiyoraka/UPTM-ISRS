document.addEventListener('DOMContentLoaded', function() {
    // Content switching functionality
    const mainContent = document.getElementById('main-content');
    const studentContent = document.getElementById('student-content');
    const staffContent = document.getElementById('staff-content');
    const agentContent = document.getElementById('agent-content');

    // Get navigation links
    const mainLink = document.querySelector('.nav-link[data-section="main"]');
    const studentLink = document.querySelector('.nav-link[data-section="student"]');
    const staffLink = document.querySelector('.nav-link[data-section="staff"]');
    const agentLink = document.querySelector('.nav-link[data-section="agent"]');

    function showContent(contentId) {
        // Hide all content sections
        mainContent.style.display = 'none';
        studentContent.style.display = 'none';
        staffContent.style.display = 'none';
        agentContent.style.display = 'none';

        // Show selected content
        switch(contentId) {
            case 'main':
                mainContent.style.display = 'block';
                break;
            case 'student':
                studentContent.style.display = 'block';
                break;
            case 'staff':
                staffContent.style.display = 'block';
                break;
            case 'agent':
                agentContent.style.display = 'block';
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

    studentLink.addEventListener('click', (e) => {
        e.preventDefault();
        showContent('student');
    });

    staffLink.addEventListener('click', (e) => {
        e.preventDefault();
        showContent('staff');
    });

    agentLink.addEventListener('click', (e) => {
        e.preventDefault();
        showContent('agent');
    });

    // Connect change password link to modal
    document.getElementById('change-password-link').addEventListener('click', function(e) {
        e.preventDefault();
        document.getElementById('passwordModal').style.display = 'block';
    });
});