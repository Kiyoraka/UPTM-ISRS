document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('studentRegistrationForm');
    const sections = document.querySelectorAll('.form-section');
    const progressSteps = document.querySelectorAll('.step');
    let currentSection = 0;

    // Calculate age from date of birth
    function calculateAge(birthDate) {
        const today = new Date();
        const birth = new Date(birthDate);
        let age = today.getFullYear() - birth.getFullYear();
        const monthDiff = today.getMonth() - birth.getMonth();
        
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birth.getDate())) {
            age--;
        }
        
        return age;
    }

    // Auto-fill age when date of birth changes
    const dobInput = document.getElementById('date_of_birth');
    const ageInput = document.getElementById('age');
    
    if (dobInput && ageInput) {
        dobInput.addEventListener('change', function() {
            if (this.value) {
                ageInput.value = calculateAge(this.value);
            }
        });
    }

    // Show/hide section
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

    // Validate section
    function validateSection(sectionIndex) {
        const currentSection = sections[sectionIndex];
        const requiredInputs = currentSection.querySelectorAll('input[required], select[required], textarea[required]');
        let isValid = true;
        
        requiredInputs.forEach(input => {
            if (!input.value.trim()) {
                isValid = false;
                input.classList.add('invalid');
                
                // Create error message if it doesn't exist
                let errorMsg = input.nextElementSibling;
                if (!errorMsg || !errorMsg.classList.contains('error-message')) {
                    errorMsg = document.createElement('div');
                    errorMsg.className = 'error-message';
                    errorMsg.textContent = 'This field is required';
                    input.parentNode.insertBefore(errorMsg, input.nextSibling);
                }
            } else {
                input.classList.remove('invalid');
                
                // Remove error message if it exists
                const errorMsg = input.nextElementSibling;
                if (errorMsg && errorMsg.classList.contains('error-message')) {
                    errorMsg.remove();
                }
            }
        });
        
        // Special validations for specific sections
        if (sectionIndex === 0) { // Personal details
            // Validate email format
            const emailInput = document.getElementById('email');
            if (emailInput && emailInput.value && !isValidEmail(emailInput.value)) {
                isValid = false;
                emailInput.classList.add('invalid');
                
                let errorMsg = emailInput.nextElementSibling;
                if (!errorMsg || !errorMsg.classList.contains('error-message')) {
                    errorMsg = document.createElement('div');
                    errorMsg.className = 'error-message';
                    errorMsg.textContent = 'Please enter a valid email address';
                    emailInput.parentNode.insertBefore(errorMsg, emailInput.nextSibling);
                }
            }
            
            // Validate date of birth (must be in the past)
            const dobInput = document.getElementById('date_of_birth');
            if (dobInput && dobInput.value) {
                const dob = new Date(dobInput.value);
                const now = new Date();
                
                if (dob > now) {
                    isValid = false;
                    dobInput.classList.add('invalid');
                    
                    let errorMsg = dobInput.nextElementSibling;
                    if (!errorMsg || !errorMsg.classList.contains('error-message')) {
                        errorMsg = document.createElement('div');
                        errorMsg.className = 'error-message';
                        errorMsg.textContent = 'Date of birth must be in the past';
                        dobInput.parentNode.insertBefore(errorMsg, dobInput.nextSibling);
                    }
                }
            }
        }
        
        return isValid;
    }

    // Email validation
    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
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
            if (currentSection < sections.length - 1) {
                if (validateSection(currentSection)) {
                    currentSection++;
                    showSection(currentSection);
                    window.scrollTo(0, 0);
                } else {
                    // Show general error message
                    alert('Please fill in all required fields correctly before proceeding.');
                }
            }
        });
    });

    // Form submission handler
    form.addEventListener('submit', function(e) {
        e.preventDefault();

        // Validate final section
        if (!validateSection(currentSection)) {
            alert('Please fill in all required fields correctly before submitting.');
            return;
        }

        // Show loading indicator
        const submitBtn = form.querySelector('.btn-submit');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Submitting...';
            submitBtn.classList.add('submitting');
        }

        // Create FormData object
        const formData = new FormData(form);

        // Add X-Requested-With header to identify as AJAX request
        const headers = new Headers({
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        });

        // Submit form using fetch
        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: headers
        })
        .then(response => {
            // Check if the response is JSON
            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                return response.json();
            } else {
                // Handle non-JSON responses (like HTML redirects)
                if (response.ok) {
                    window.location.href = 'success-page.php';
                    return { success: true };
                } else {
                    throw new Error('Received non-JSON response');
                }
            }
        })
        .then(data => {
            if (data.success) {
                // Save student ID in local storage (might be useful later)
                if (data.student_id) {
                    localStorage.setItem('lastStudentId', data.student_id);
                }
                
                // Handle redirect
                if (data.redirect_url) {
                    window.location.href = data.redirect_url;
                } else {
                    window.location.href = 'success-page.php';
                }
            } else {
                // Re-enable submit button on error
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Submit Application';
                    submitBtn.classList.remove('submitting');
                }
                
                // Display error message
                alert(data.message || 'An error occurred during submission. Please try again.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            
            // Re-enable submit button on error
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Submit Application';
                submitBtn.classList.remove('submitting');
            }
            
            alert('An error occurred during submission. Please check your internet connection and try again.');
        });
    });

    // Initialize the first section
    showSection(0);
});

// Function to preview photo
function previewPhoto(input) {
    const preview = document.getElementById('photo-preview');
    const placeholder = document.getElementById('upload-placeholder');
    
    // Validate file size (2MB max)
    const maxSize = 2 * 1024 * 1024; // 2MB in bytes
    if (input.files[0] && input.files[0].size > maxSize) {
        alert('File size must be less than 2MB');
        input.value = '';
        return;
    }

    // Validate file type
    const validTypes = ['image/jpeg', 'image/png'];
    if (input.files[0] && !validTypes.includes(input.files[0].type)) {
        alert('Please upload a PNG or JPEG file');
        input.value = '';
        return;
    }

    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
            placeholder.style.display = 'none';
        }
        
        reader.readAsDataURL(input.files[0]);
    }
}