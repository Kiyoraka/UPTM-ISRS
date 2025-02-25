document.addEventListener('DOMContentLoaded', function() {
    // Initial variables
    const staffSearchInput = document.getElementById('staffSearchInput');
    const staffRoleFilter = document.getElementById('staffRoleFilter');
    const staffTableBody = document.getElementById('staffTableBody');
    const staffPrevPage = document.getElementById('staffPrevPage');
    const staffNextPage = document.getElementById('staffNextPage');
    const staffPageNumbers = document.getElementById('staffPageNumbers');
    
    // Password modal elements
    const staffPasswordModal = document.getElementById('staffPasswordModal');
    const closeStaffPasswordModal = document.getElementById('closeStaffPasswordModal');
    const cancelStaffPasswordChange = document.getElementById('cancelStaffPasswordChange');
    const changeStaffPasswordForm = document.getElementById('changeStaffPasswordForm');
    const staffPasswordMessage = document.getElementById('staff-password-message');
    
    let currentPage = 1;
    let totalPages = 1;
    let searchTimer;
    
    // Initial load
    loadStaffData();
    
    // Add event listeners
    staffSearchInput.addEventListener('input', function() {
        // Debounce search inputs
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => {
            currentPage = 1;
            loadStaffData();
        }, 500);
    });
    
    staffRoleFilter.addEventListener('change', function() {
        currentPage = 1;
        loadStaffData();
    });
    
    staffPrevPage.addEventListener('click', function() {
        if (currentPage > 1) {
            currentPage--;
            loadStaffData();
        }
    });
    
    staffNextPage.addEventListener('click', function() {
        if (currentPage < totalPages) {
            currentPage++;
            loadStaffData();
        }
    });
    
    // Password modal event listeners
    if (closeStaffPasswordModal) {
        closeStaffPasswordModal.addEventListener('click', closePasswordModal);
    }
    
    if (cancelStaffPasswordChange) {
        cancelStaffPasswordChange.addEventListener('click', closePasswordModal);
    }
    
    // Close modal when clicking outside
    window.addEventListener('click', function(e) {
        if (e.target === staffPasswordModal) {
            closePasswordModal();
        }
    });
    
    // Handle form submission
    if (changeStaffPasswordForm) {
        changeStaffPasswordForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const staffId = document.getElementById('staff_id').value;
            const newPassword = document.getElementById('new_staff_password').value;
            const confirmPassword = document.getElementById('confirm_staff_password').value;
            
            // Basic validation
            if (newPassword !== confirmPassword) {
                showPasswordMessage('Passwords do not match', 'alert-danger');
                return;
            }
            
            if (newPassword.length < 8) {
                showPasswordMessage('New password must be at least 8 characters long', 'alert-danger');
                return;
            }
            
            // Create form data
            const formData = new FormData();
            formData.append('staff_id', staffId);
            formData.append('new_staff_password', newPassword);
            formData.append('confirm_staff_password', confirmPassword);
            formData.append('action', 'change_password');
            
            // Send AJAX request
            fetch('IT-staff-actions.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showPasswordMessage(data.message, 'alert-success');
                    
                    // Close modal after 2 seconds on success
                    setTimeout(() => {
                        closePasswordModal();
                    }, 2000);
                } else {
                    showPasswordMessage(data.message, 'alert-danger');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showPasswordMessage('An error occurred while changing password', 'alert-danger');
            });
        });
    }
    
    // Function to load staff data
    function loadStaffData() {
        const search = staffSearchInput.value;
        const role = staffRoleFilter.value;
        
        // Show loading state
        staffTableBody.innerHTML = '<tr><td colspan="5" class="text-center">Loading...</td></tr>';
        
        // Fetch data from server
        fetch(`IT-fetch-staff.php?search=${encodeURIComponent(search)}&role=${encodeURIComponent(role)}&page=${currentPage}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update pagination
                    totalPages = data.pagination.total_pages;
                    updatePagination();
                    
                    // Update table with staff data
                    displayStaffData(data.staff);
                    
                    // Update button states
                    staffPrevPage.disabled = currentPage === 1;
                    staffNextPage.disabled = currentPage === totalPages;
                } else {
                    staffTableBody.innerHTML = `<tr><td colspan="5" class="text-center">${data.message || 'Failed to load data'}</td></tr>`;
                }
            })
            .catch(error => {
                console.error('Error fetching staff data:', error);
                staffTableBody.innerHTML = '<tr><td colspan="5" class="text-center">Error loading data. Please try again.</td></tr>';
            });
    }
    
    // Function to display staff data in the table
    function displayStaffData(staff) {
        if (staff.length === 0) {
            staffTableBody.innerHTML = '<tr><td colspan="5" class="text-center">No staff found</td></tr>';
            return;
        }
        
        let tableHtml = '';
        
        staff.forEach(member => {
            // Format role display
            let roleDisplay = 'Unknown';
            switch(member.role) {
                case 'io':
                    roleDisplay = 'International Office';
                    break;
                case 'ao':
                    roleDisplay = 'Academic Office';
                    break;
                case 'it':
                    roleDisplay = 'IT Admin';
                    break;
            }
            
            // Build table row
            tableHtml += `
                <tr data-staff-id="${member.id}">
                    <td>${member.id}</td>
                    <td>${escapeHtml(member.name)}</td>
                    <td>${escapeHtml(member.email)}</td>
                    <td>${roleDisplay}</td>
                    <td class="action-buttons">
                        <button class="btn-action btn-change-password" onclick="openPasswordModal(${member.id})" title="Change Password">
                            <i class="fas fa-key"></i>
                        </button>
                    </td>
                </tr>
            `;
        });
        
        staffTableBody.innerHTML = tableHtml;
    }
    
    // Function to update pagination
    function updatePagination() {
        let paginationHtml = '';
        
        // Determine range of page numbers to show
        let startPage = Math.max(1, currentPage - 2);
        let endPage = Math.min(totalPages, startPage + 4);
        
        if (endPage - startPage < 4) {
            startPage = Math.max(1, endPage - 4);
        }
        
        for (let i = startPage; i <= endPage; i++) {
            paginationHtml += `
                <button class="page-number ${i === currentPage ? 'active' : ''}" 
                        onclick="goToStaffPage(${i})">
                    ${i}
                </button>
            `;
        }
        
        staffPageNumbers.innerHTML = paginationHtml;
    }
    
    // Helper function to show password change messages
    function showPasswordMessage(message, className) {
        staffPasswordMessage.innerHTML = `<div class="alert ${className}">${message}</div>`;
    }
    
    // Close password modal function
    function closePasswordModal() {
        staffPasswordModal.style.display = 'none';
        // Reset form and message
        changeStaffPasswordForm.reset();
        staffPasswordMessage.innerHTML = '';
    }
    
    // Helper function to escape HTML entities
    function escapeHtml(text) {
        if (!text) return '';
        return text
            .toString()
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }
    
    // Make functions globally available
    window.goToStaffPage = function(page) {
        currentPage = page;
        loadStaffData();
    };
    
    window.openPasswordModal = function(staffId) {
        // Reset form
        changeStaffPasswordForm.reset();
        staffPasswordMessage.innerHTML = '';
        
        // Set staff ID in the hidden field
        document.getElementById('staff_id').value = staffId;
        
        // Show modal
        staffPasswordModal.style.display = 'block';
    };
});