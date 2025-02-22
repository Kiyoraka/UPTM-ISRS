document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('agentRegistrationForm');
    const sections = document.querySelectorAll('.form-section');
    const progressSteps = document.querySelectorAll('.step');
    let currentSection = 0;

    function showSection(index) {
        sections.forEach(section => section.classList.remove('active'));
        sections[index].classList.add('active');
        
        progressSteps.forEach((step, i) => {
            if (i <= index) {
                step.classList.add('active');
            } else {
                step.classList.remove('active');
            }
        });
    }

    document.querySelectorAll('.btn-next').forEach(button => {
        button.addEventListener('click', () => {
            if (currentSection < sections.length - 1) {
                currentSection++;
                showSection(currentSection);
                window.scrollTo(0, 0);
            }
        });
    });

    document.querySelectorAll('.btn-prev').forEach(button => {
        button.addEventListener('click', () => {
            if (currentSection > 0) {
                currentSection--;
                showSection(currentSection);
                window.scrollTo(0, 0);
            }
        });
    });

    // Form submission (just for demonstration)
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            console.log('Form submitted');
            alert('Thank you for your application. We will contact you soon.');
        });
    }

    // Initialize the first section
    showSection(0);
});