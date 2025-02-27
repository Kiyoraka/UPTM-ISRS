// Payment Receipt Upload Functionality
document.addEventListener('DOMContentLoaded', function() {
    // Get receipt modal elements
    const receiptModal = document.getElementById('receiptModal');
    const closeReceiptModal = receiptModal.querySelector('.close-modal');
    const cancelReceiptUpload = document.getElementById('cancelReceiptUpload');
    const receiptForm = document.getElementById('uploadReceiptForm');
    const receiptMessage = document.getElementById('receipt-message');
    const uploadReceiptBtn = document.getElementById('upload-receipt-btn');
    
    // Show receipt modal when upload button is clicked
    if (uploadReceiptBtn) {
        uploadReceiptBtn.addEventListener('click', function() {
            receiptModal.style.display = 'block';
            
            // Set default payment date to today
            document.getElementById('payment_date').valueAsDate = new Date();
        });
    }
    
    // Close modal functions
    function closeReceiptModal() {
        receiptModal.style.display = 'none';
        receiptForm.reset();
        receiptMessage.innerHTML = '';
    }
    
    closeReceiptModal.addEventListener('click', closeReceiptModal);
    cancelReceiptUpload.addEventListener('click', closeReceiptModal);
    
    // Close modal when clicking outside
    window.addEventListener('click', function(e) {
        if (e.target === receiptModal) {
            closeReceiptModal();
        }
    });
    
    // Handle receipt form submission
    receiptForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        formData.append('action', 'upload_receipt');
        
        // Validate file size
        const file = document.getElementById('payment_receipt').files[0];
        if (file.size > 5 * 1024 * 1024) { // 5MB
            showReceiptMessage('File size exceeds the maximum limit of 5MB', 'error');
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
                showReceiptMessage(data.message, 'success');
                
                // Close modal after 2 seconds and reload page
                setTimeout(() => {
                    closeReceiptModal();
                    window.location.reload();
                }, 2000);
            } else {
                showReceiptMessage(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showReceiptMessage('An error occurred while uploading receipt', 'error');
        });
    });
    
    // Helper function to show messages
    function showReceiptMessage(message, type) {
        let className = type === 'success' ? 'alert-success' : 'alert-danger';
        receiptMessage.innerHTML = `<div class="alert ${className}">${message}</div>`;
    }
});