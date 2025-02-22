// Document Upload JavaScript
document.addEventListener('DOMContentLoaded', function() {
    const agentTypeRadios = document.querySelectorAll('input[name="agent_type"]');
    const corporateDocuments = document.getElementById('corporate-documents');
    const individualDocuments = document.getElementById('individual-documents');

    // Toggle document sections based on agent type selection
    agentTypeRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'corporate') {
                corporateDocuments.style.display = 'block';
                individualDocuments.style.display = 'none';
            } else {
                corporateDocuments.style.display = 'none';
                individualDocuments.style.display = 'block';
            }
        });
    });

    // File upload validation
    const fileInputs = document.querySelectorAll('input[type="file"]');
    fileInputs.forEach(input => {
        input.addEventListener('change', function() {
            const maxSize = 5 * 1024 * 1024; // 5MB
            const allowedTypes = [
                'application/pdf', 
                'application/msword', 
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 
                'image/jpeg', 
                'image/png'
            ];

            if (this.files[0]) {
                const file = this.files[0];
                
                // Check file size
                if (file.size > maxSize) {
                    alert(`File ${file.name} is too large. Maximum file size is 5MB.`);
                    this.value = ''; // Clear the file input
                    return;
                }

                // Check file type
                if (!allowedTypes.includes(file.type)) {
                    alert(`Invalid file type for ${file.name}. Allowed types are PDF, DOC, DOCX, JPG, and PNG.`);
                    this.value = ''; // Clear the file input
                }
            }
        });
    });
});