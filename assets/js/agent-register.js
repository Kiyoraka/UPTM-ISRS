document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('agentRegistrationForm');
    const sections = document.querySelectorAll('.form-section');
    const progressSteps = document.querySelectorAll('.step');
    let currentSection = 0;

    function showSection(index) {
        sections.forEach(section => section.style.display = 'none');
        sections[index].style.display = 'block';
        
        progressSteps.forEach((step, i) => {
            if (i <= index) {
                step.classList.add('active');
            } else {
                step.classList.remove('active');
            }
        });
    }

    // Previous button click handlers
    document.querySelectorAll('.btn-prev').forEach(button => {
        button.addEventListener('click', () => {
            if (currentSection > 0) {
                currentSection--;
                showSection(currentSection);
                window.scrollTo(0, 0);
            }
        });
    });

    // Next button click handlers
    document.querySelectorAll('.btn-next').forEach(button => {
        button.addEventListener('click', () => {
            // If it's the last section, trigger form submission
            if (currentSection === sections.length - 1) {
                if (form.checkValidity()) {
                    form.submit();
                } else {
                    // Trigger HTML5 validation
                    form.reportValidity();
                }
            } else if (currentSection < sections.length - 1) {
                // Validate current section before moving to next
                const currentInputs = sections[currentSection].querySelectorAll('input[required], select[required], textarea[required]');
                let isValid = true;

                currentInputs.forEach(input => {
                    if (!input.checkValidity()) {
                        isValid = false;
                        input.reportValidity();
                    }
                });

                if (isValid) {
                    currentSection++;
                    showSection(currentSection);
                    window.scrollTo(0, 0);
                }
            }
        });
    });

    // Form submission handler
    form.addEventListener('submit', function(e) {
        e.preventDefault();

        // Create FormData object
        const formData = new FormData(form);

        // Submit form using fetch
        fetch(form.action, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                // Redirect to success page or dashboard
                window.location.href = 'agent-dashboard.php';
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while submitting the form. Please try again.');
        });
    });

    // Initialize the first section
    showSection(0);
});