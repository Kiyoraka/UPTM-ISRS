// agent_dashboard-ProfileSection.js
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('agentProfileForm');
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

    // Fetch Profile Data
    function fetchProfileData() {
        fetch('fetch-agent-profile.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    populateProfileData(data.data);
                } else {
                    console.error('Error:', data.message);
                }
            })
            .catch(error => console.error('Error:', error));
    }

    // Populate Profile Data
    function populateProfileData(data) {
        // Personal Details
        if (data.personal.photo_path) {
            document.getElementById('photo-preview').src = '../' + data.personal.photo_path;
            document.getElementById('photo-preview').style.display = 'block';
            document.getElementById('upload-placeholder').style.display = 'none';
        }
        document.getElementById('company_name').value = data.personal.company_name;
        document.getElementById('registration_no').value = data.personal.registration_no;
        document.getElementById('address').value = data.personal.address;

        // Contact Information
        document.getElementById('contact_phone').value = data.contact.contact_phone;
        document.getElementById('contact_email').value = data.contact.contact_email;

        // Bank Details
        document.getElementById('account_name').value = data.bank.account_name;
        document.getElementById('account_no').value = data.bank.account_no;
        document.getElementById('bank_name').value = data.bank.bank_name;
        document.getElementById('bank_branch').value = data.bank.bank_branch;
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
        fetch('update-agent-profile.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Profile updated successfully');
                // Refresh data
                fetchProfileData();
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
    document.querySelectorAll('.edit-button').forEach(button => {
        button.addEventListener('click', function() {
            const section = this.closest('.profile-section');
            const inputs = section.querySelectorAll('.form-control, input[type="file"]');
            const saveBtn = section.querySelector('.btn-save') || section.querySelector('.btn-next');
            
            if (this.textContent === 'Edit') {
                inputs.forEach(input => input.disabled = false);
                this.textContent = 'Cancel';
                saveBtn.textContent = 'Save Changes';
                saveBtn.classList.add('btn-save');
            } else {
                inputs.forEach(input => input.disabled = true);
                this.textContent = 'Edit';
                saveBtn.textContent = (section.querySelector('.btn-next') ? 'Next' : 'Save');
                saveBtn.classList.remove('btn-save');
                // Reset changes
                fetchProfileData();
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
                const editButton = currentSectionElement.querySelector('.edit-button');
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

    // Photo upload handler
    document.getElementById('passport_photo')?.addEventListener('change', function() {
        previewPhoto(this);
    });

    // Initialize
    showSection(0);
    fetchProfileData(); // Load initial data
});