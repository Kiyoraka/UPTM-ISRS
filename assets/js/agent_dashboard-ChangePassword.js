// Password change functionality
document.addEventListener('DOMContentLoaded', function() {
    // Get modal elements
    const passwordModal = document.getElementById('passwordModal');
    const passwordLink = document.querySelector('.dropdown-item[href="change-password.php"]');
    const closeModal = document.querySelector('.close-modal');
    const cancelButton = document.getElementById('cancelPasswordChange');
    const passwordForm = document.getElementById('changePasswordForm');
    const messageDiv = document.getElementById('password-message');
    
    // Update link to open modal instead of navigating
    if (passwordLink) {
        passwordLink.addEventListener('click', function(e) {
            e.preventDefault();
            passwordModal.style.display = 'block';
            
            // Clear form and messages
            passwordForm.reset();
            messageDiv.innerHTML = '';
            messageDiv.className = '';
        });
    }
    
    // Close modal functions
    function closePasswordModal() {
        passwordModal.style.display = 'none';
    }
    
    if (closeModal) closeModal.addEventListener('click', closePasswordModal);
    if (cancelButton) cancelButton.addEventListener('click', closePasswordModal);
    
    // Close when clicking outside modal
    window.addEventListener('click', function(e) {
        if (e.target === passwordModal) {
            closePasswordModal();
        }
    });
    
    // Handle form submission
    if (passwordForm) {
        passwordForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const currentPassword = document.getElementById('current_password').value;
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            // Basic validation
            if (newPassword !== confirmPassword) {
                showMessage('Passwords do not match', 'alert-danger');
                return;
            }
            
            if (newPassword.length < 8) {
                showMessage('New password must be at least 8 characters long', 'alert-danger');
                return;
            }
            
            // Create form data
            const formData = new FormData();
            formData.append('current_password', currentPassword);
            formData.append('new_password', newPassword);
            formData.append('confirm_password', confirmPassword);
            formData.append('action', 'change_password');
            
            // Send AJAX request
            fetch('agent-actions.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showMessage(data.message, 'alert-success');
                    
                    // Close modal after 2 seconds on success
                    setTimeout(() => {
                        closePasswordModal();
                    }, 2000);
                } else {
                    showMessage(data.message, 'alert-danger');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showMessage('An error occurred while changing password', 'alert-danger');
            });
        });
    }
    
    // Helper function to show messages
    function showMessage(message, className) {
        messageDiv.innerHTML = `<div class="alert ${className}">${message}</div>`;
    }
});

// Password strength checker
function checkPasswordStrength() {
    const password = document.getElementById('new_password').value;
    const strengthDiv = document.getElementById('password-strength');
    
    // Clear the div if password is empty
    if (password.length === 0) {
        strengthDiv.textContent = '';
        strengthDiv.className = 'password-strength';
        return;
    }
    
    // Check strength
    let strength = 0;
    if (password.length >= 8) strength += 1;
    if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength += 1;
    if (password.match(/[0-9]/)) strength += 1;
    if (password.match(/[^a-zA-Z0-9]/)) strength += 1;
    
    // Display appropriate message
    if (strength < 2) {
        strengthDiv.textContent = 'Weak password';
        strengthDiv.className = 'password-strength weak';
    } else if (strength < 4) {
        strengthDiv.textContent = 'Medium strength password';
        strengthDiv.className = 'password-strength medium';
    } else {
        strengthDiv.textContent = 'Strong password';
        strengthDiv.className = 'password-strength strong';
    }
}

// Password match checker
function checkPasswordMatch() {
    const newPassword = document.getElementById('new_password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    const matchDiv = document.getElementById('password-match');
    
    if (confirmPassword.length === 0) {
        matchDiv.textContent = '';
        matchDiv.className = 'password-strength';
        return;
    }
    
    if (newPassword === confirmPassword) {
        matchDiv.textContent = 'Passwords match';
        matchDiv.className = 'password-strength strong';
    } else {
        matchDiv.textContent = 'Passwords do not match';
        matchDiv.className = 'password-strength weak';
    }
}