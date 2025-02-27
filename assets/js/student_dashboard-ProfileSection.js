// Student Profile Functionality
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('studentProfileForm');
    const sections = document.querySelectorAll('.profile-section');
    const progressSteps = document.querySelectorAll('.step');
    let currentSection = 0;

    // Show/hide sections
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

    // Handle Section Updates
    function updateSection(sectionId) {
        const section = document.getElementById(sectionId);
        const formData = new FormData();
        
        // Add section identifier
        formData.append('section', sectionId.replace('section-', ''));
        
        // Add all form inputs from the section
        section.querySelectorAll('.form-control').forEach(input => {
            formData.append(input.name, input.value);
        });

        // Add photo if it exists
        if (sectionId === 'section-personal') {
            const photoInput = document.getElementById('passport_photo');
            if (photoInput.files.length > 0) {
                formData.append('passport_photo', photoInput.files[0]);
            }
        }

        // Send update request
        fetch('update-student-profile.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Profile updated successfully');
                // Reload current page to refresh data
                window.location.reload();
            } else {
                alert('Error updating profile: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error updating profile');
        });
    }

    // Handle edit buttons
    document.querySelectorAll('.btn-edit').forEach(button => {
        button.addEventListener('click', function() {
            const section = this.closest('.profile-section');
            const inputs = section.querySelectorAll('.form-control, input[type="file"]');
            const saveBtn = section.querySelector('.btn-save') || section.querySelector('.btn-next');
        
            if (this.textContent === 'Edit') {
                // Store original form field values
                const originalValues = {};
                inputs.forEach(input => {
                    originalValues[input.id] = input.value;
                });
                this.dataset.originalValues = JSON.stringify(originalValues);

                inputs.forEach(input => input.disabled = false);
                this.textContent = 'Cancel';
                if (saveBtn) {
                    saveBtn.textContent = 'Save Changes';
                    saveBtn.classList.remove('btn-next');
                    saveBtn.classList.add('btn-save');
                }
            } else {
                inputs.forEach(input => input.disabled = true);
                this.textContent = 'Edit';
                if (saveBtn) {
                    if (section.id === 'section-guardian') {
                        saveBtn.textContent = 'Save Changes';
                        saveBtn.classList.remove('btn-save');
                        saveBtn.classList.add('btn-save');
                    } else {
                        saveBtn.textContent = 'Next';
                        saveBtn.classList.remove('btn-save');
                        saveBtn.classList.add('btn-next');
                    }
                }

                // Restore original form field values
                const originalValues = JSON.parse(this.dataset.originalValues);
                inputs.forEach(input => {
                    input.value = originalValues[input.id];
                });
            }
        });
    });

    // Navigation and Save buttons
    document.querySelectorAll('.btn-prev').forEach(button => {
        button.addEventListener('click', () => {
            if (currentSection > 0) {
                currentSection--;
                showSection(currentSection);
            }
        });
    });

    document.querySelectorAll('.btn-next, .btn-save').forEach(button => {
        button.addEventListener('click', () => {
            const currentSectionElement = sections[currentSection];
            
            if (button.classList.contains('btn-save') || button.textContent === 'Save Changes') {
                // Save changes
                updateSection(currentSectionElement.id);
                // Reset button and inputs
                const editButton = currentSectionElement.querySelector('.btn-edit');
                if (editButton) {
                    editButton.click(); // Reset to view mode
                }
            } else if (currentSection < sections.length - 1) {
                // Normal navigation
                currentSection++;
                showSection(currentSection);
            }
        });
    });

    // Preview Photo
    document.getElementById('passport_photo')?.addEventListener('change', function() {
        previewPhoto(this);
    });

    // Function to preview uploaded photo
    function previewPhoto(input) {
        const preview = document.getElementById('photo-preview');
        const placeholder = document.getElementById('upload-placeholder');
        
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

    // Initialize
    showSection(0);
});