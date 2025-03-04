document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('studentRegistrationForm');
    const sections = document.querySelectorAll('.form-section');
    const progressSteps = document.querySelectorAll('.step');
    let currentSection = 0;
    
    // Debug mode flag - set to true to bypass validation
    let debugMode = false;
    
    // Add debug control panel
    createDebugPanel();

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
            if (currentSection < sections.length - 1) {
                // If debug mode is on, skip validation
                if (debugMode) {
                    currentSection++;
                    showSection(currentSection);
                    window.scrollTo(0, 0);
                    return;
                }
                
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

        // If debug mode is on, skip validation
        if (debugMode) {
            alert("Form would be submitted now (Debug mode is ON)");
            return;
        }

        // Validate the entire form
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

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
                // Redirect to success page
                window.location.href = 'success-page.html';
            } else {
                alert(data.message || 'An error occurred during submission');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred during submission. Please try again later.');
        });
    });
    
    // Create debug panel function
    function createDebugPanel() {
        const debugPanel = document.createElement('div');
        debugPanel.className = 'debug-panel';
        debugPanel.innerHTML = `
            <div class="debug-controls">
                <label class="debug-toggle">
                    <input type="checkbox" id="debug-mode-toggle">
                    <span>Debug Mode</span>
                </label>
                <div class="debug-navigation">
                    <span>Jump to section:</span>
                    <select id="debug-section-jump">
                        <option value="0">1. Personal Details</option>
                        <option value="1">2. Guardian Information</option>
                        <option value="2">3. Qualifications</option>
                        <option value="3">4. Program Selection</option>
                        <option value="4">5. Financial Support</option>
                        <option value="5">6. Documents & Declaration</option>
                    </select>
                </div>
            </div>
        `;
        
        document.body.appendChild(debugPanel);
        
        // Add debug mode toggle event listener
        const debugToggle = document.getElementById('debug-mode-toggle');
        debugToggle.addEventListener('change', function() {
            debugMode = this.checked;
            document.body.classList.toggle('debug-active', debugMode);
            
            // Update form visuals when debug mode changes
            const requiredInputs = document.querySelectorAll('[required]');
            requiredInputs.forEach(input => {
                if (debugMode) {
                    input.dataset.required = "true";
                    input.removeAttribute('required');
                } else {
                    if (input.dataset.required === "true") {
                        input.setAttribute('required', 'required');
                    }
                }
            });
            
            console.log('Debug mode ' + (debugMode ? 'enabled' : 'disabled'));
        });
        
        // Add section jump functionality
        const sectionJump = document.getElementById('debug-section-jump');
        sectionJump.addEventListener('change', function() {
            currentSection = parseInt(this.value);
            showSection(currentSection);
            window.scrollTo(0, 0);
        });
    }

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