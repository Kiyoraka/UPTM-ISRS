// Document Upload and Management Functionality
document.addEventListener('DOMContentLoaded', function() {
    // Get document modal elements
    const documentModal = document.getElementById('documentModal');
    const closeDocumentModal = documentModal.querySelector('.close-modal');
    const cancelDocumentUpload = document.getElementById('cancelDocumentUpload');
    const documentForm = document.getElementById('uploadDocumentForm');
    const documentMessage = document.getElementById('document-message');
    const documentTypeInput = document.getElementById('document_type');
    
    // File input upload triggers
    const uploadInputs = document.querySelectorAll('.upload-input');
    
    // Add event listeners to file inputs
    uploadInputs.forEach(input => {
        input.addEventListener('change', function(e) {
            const fileType = this.name; // academic_certificates, passport_copy, or health_declaration
            documentTypeInput.value = fileType;
            
            // Display appropriate title based on document type
            let modalTitle = "Upload Document";
            if (fileType === 'academic_certificates') {
                modalTitle = "Upload Academic Certificates";
            } else if (fileType === 'passport_copy') {
                modalTitle = "Upload Passport Copy";
            } else if (fileType === 'health_declaration') {
                modalTitle = "Upload Health Declaration";
            }
            
            documentModal.querySelector('.modal-header h2').textContent = modalTitle;
            
            // Show modal
            documentModal.style.display = 'block';
        });
    });
    
    // Close modal functions
    function closeDocumentModal() {
        documentModal.style.display = 'none';
        documentForm.reset();
        documentMessage.innerHTML = '';
    }
    
    closeDocumentModal.addEventListener('click', closeDocumentModal);
    cancelDocumentUpload.addEventListener('click', closeDocumentModal);
    
    // Close modal when clicking outside
    window.addEventListener('click', function(e) {
        if (e.target === documentModal) {
            closeDocumentModal();
        }
    });
    
    // Handle document form submission
    documentForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        formData.append('action', 'upload_document');
        
        // Validate file size
        const file = document.getElementById('document_file').files[0];
        if (file.size > 5 * 1024 * 1024) { // 5MB
            showDocumentMessage('File size exceeds the maximum limit of 5MB', 'error');
            return;
        }
        
        // Send AJAX request
        fetch('student-actions.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showDocumentMessage(data.message, 'success');
                
                // Close modal after 2 seconds and reload page
                setTimeout(() => {
                    closeDocumentModal();
                    window.location.reload();
                }, 2000);
            } else {
                showDocumentMessage(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showDocumentMessage('An error occurred while uploading document', 'error');
        });
    });
    
    // Helper function to show messages
    function showDocumentMessage(message, type) {
        let className = type === 'success' ? 'alert-success' : 'alert-danger';
        documentMessage.innerHTML = `<div class="alert ${className}">${message}</div>`;
    }
});