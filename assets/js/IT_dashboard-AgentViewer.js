// Agent Details Modal Functionality
document.addEventListener('DOMContentLoaded', function() {
    // Get modal elements
    const agentModal = document.getElementById('agentDetailsModal');
    const closeAgentModal = document.getElementById('closeAgentModal');
    const agentModalLoading = document.getElementById('agent-modal-loading');
    const agentDetailsContent = document.getElementById('agent-details-content');
    
    // Tab functionality
    const tabButtons = document.querySelectorAll('.agent-tab-btn');
    const tabPanes = document.querySelectorAll('.agent-tab-pane');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Remove active class from all buttons and panes
            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabPanes.forEach(pane => pane.classList.remove('active'));
            
            // Add active class to current button
            this.classList.add('active');
            
            // Show the corresponding tab pane
            const tabId = this.dataset.tab + '-tab';
            document.getElementById(tabId).classList.add('active');
        });
    });
    
    // Close modal when clicking the X
    if (closeAgentModal) {
        closeAgentModal.addEventListener('click', function() {
            agentModal.style.display = 'none';
        });
    }
    
    // Close modal when clicking outside
    window.addEventListener('click', function(e) {
        if (e.target === agentModal) {
            agentModal.style.display = 'none';
        }
    });
    
    // Update status buttons functionality
    const approveBtn = document.getElementById('approve-btn');
    const rejectBtn = document.getElementById('reject-btn');
    const statusReasonInput = document.getElementById('status-reason-input');
    
    if (approveBtn) {
        approveBtn.addEventListener('click', function() {
            if (confirm('Are you sure you want to approve this agent?')) {
                const agentId = this.dataset.agentId;
                const reason = statusReasonInput.value.trim();
                updateAgentStatusFromModal(agentId, 'approved', reason);
            }
        });
    }
    
    if (rejectBtn) {
        rejectBtn.addEventListener('click', function() {
            if (confirm('Are you sure you want to reject this agent?')) {
                const agentId = this.dataset.agentId;
                const reason = statusReasonInput.value.trim();
                
                if (!reason) {
                    alert('Please provide a reason for rejection');
                    statusReasonInput.focus();
                    return;
                }
                
                updateAgentStatusFromModal(agentId, 'rejected', reason);
            }
        });
    }
    
    // Make viewAgent function globally accessible
    window.viewAgent = function(agentId) {
        // Show modal
        agentModal.style.display = 'block';
        
        // Show loading, hide content
        agentModalLoading.style.display = 'block';
        agentDetailsContent.style.display = 'none';
        
        // Reset to first tab
        tabButtons.forEach(btn => btn.classList.remove('active'));
        tabPanes.forEach(pane => pane.classList.remove('active'));
        tabButtons[0].classList.add('active');
        tabPanes[0].classList.add('active');
        
        // Fetch agent details
        fetch(`IT-fetch-agent-details.php?id=${agentId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    populateAgentDetails(data);
                    
                    // Set agent ID for action buttons
                    if (approveBtn) approveBtn.dataset.agentId = agentId;
                    if (rejectBtn) rejectBtn.dataset.agentId = agentId;
                    
                    // Hide loading, show content
                    agentModalLoading.style.display = 'none';
                    agentDetailsContent.style.display = 'block';
                } else {
                    alert(data.message || 'Failed to load agent details');
                    agentModal.style.display = 'none';
                }
            })
            .catch(error => {
                console.error('Error fetching agent details:', error);
                alert('An error occurred while loading agent details');
                agentModal.style.display = 'none';
            });
    };
    
    // Document viewer functionality
    window.viewDocument = function(documentPath, documentName) {
        // Create the document viewer modal if it doesn't exist
        let documentViewer = document.getElementById('documentViewerModal');
        
        if (!documentViewer) {
            documentViewer = document.createElement('div');
            documentViewer.id = 'documentViewerModal';
            documentViewer.className = 'document-viewer-modal';
            
            const viewerContent = document.createElement('div');
            viewerContent.className = 'document-viewer-content';
            
            const closeBtn = document.createElement('span');
            closeBtn.className = 'close-document-viewer';
            closeBtn.innerHTML = '&times;';
            closeBtn.onclick = function() {
                documentViewer.style.display = 'none';
            };
            
            const iframe = document.createElement('iframe');
            iframe.className = 'document-frame';
            iframe.id = 'documentFrame';
            
            viewerContent.appendChild(closeBtn);
            viewerContent.appendChild(iframe);
            documentViewer.appendChild(viewerContent);
            document.body.appendChild(documentViewer);
            
            // Close when clicking outside
            documentViewer.addEventListener('click', function(e) {
                if (e.target === documentViewer) {
                    documentViewer.style.display = 'none';
                }
            });
        }
        
        // Set the document source and show the viewer
        const iframe = document.getElementById('documentFrame');
        iframe.src = '../' + documentPath;
        documentViewer.style.display = 'block';
    };
});

// Function to populate agent details in the modal
function populateAgentDetails(data) {
    const agent = data.agent;
    const documents = data.documents;
    const experiences = data.experiences;
    
    // Populate general info
    document.getElementById('application-type').textContent = capitalizeFirstLetter(agent.application_type) || 'N/A';
    document.getElementById('company-name').textContent = agent.company_name || 'N/A';
    document.getElementById('registration-no').textContent = agent.registration_no || 'N/A';
    document.getElementById('address').textContent = agent.address || 'N/A';
    document.getElementById('postal-code').textContent = agent.postal_code || 'N/A';
    document.getElementById('country').textContent = agent.country || 'N/A';
    
    // Populate contact info
    document.getElementById('contact-name').textContent = agent.contact_name || 'N/A';
    document.getElementById('contact-designation').textContent = agent.contact_designation || 'N/A';
    document.getElementById('contact-phone').textContent = agent.contact_phone || 'N/A';
    document.getElementById('contact-fax').textContent = agent.contact_fax || 'N/A';
    document.getElementById('contact-mobile').textContent = agent.contact_mobile || 'N/A';
    document.getElementById('contact-email').textContent = agent.contact_email || 'N/A';
    document.getElementById('website').textContent = agent.website || 'N/A';
    
    // Populate bank info
    document.getElementById('account-name').textContent = agent.account_name || 'N/A';
    document.getElementById('account-no').textContent = agent.account_no || 'N/A';
    document.getElementById('bank-name').textContent = agent.bank_name || 'N/A';
    document.getElementById('bank-branch').textContent = agent.bank_branch || 'N/A';
    document.getElementById('swift-code').textContent = agent.swift_code || 'N/A';
    document.getElementById('bank-address').textContent = agent.bank_address || 'N/A';
    
    // Populate business info
    document.getElementById('countries-covered').textContent = agent.countries_covered || 'N/A';
    document.getElementById('recruitment-experience').textContent = capitalizeFirstLetter(agent.recruitment_experience) || 'N/A';
    
    // Populate agreement info
    document.getElementById('signee-full-name').textContent = agent.signee_full_name || 'N/A';
    document.getElementById('signee-designation').textContent = agent.signee_designation || 'N/A';
    document.getElementById('signee-nric').textContent = agent.signee_nric || 'N/A';
    document.getElementById('witness-full-name').textContent = agent.witness_full_name || 'N/A';
    document.getElementById('witness-designation').textContent = agent.witness_designation || 'N/A';
    document.getElementById('witness-nric').textContent = agent.witness_nric || 'N/A';
    document.getElementById('signature-date').textContent = formatDate(agent.signature_date) || 'N/A';
    
    // Populate photo if available
    const photoContainer = document.getElementById('agent-photo-container');
    if (agent.photo_path) {
        photoContainer.innerHTML = `<img src="../${agent.photo_path}" alt="Agent Photo">`;
    } else {
        photoContainer.innerHTML = '';
    }
    
    // Populate experiences table
    const experiencesContainer = document.getElementById('experiences-container');
    const experienceTableBody = document.getElementById('experience-table-body');
    
    if (experiences && experiences.length > 0) {
        experiencesContainer.style.display = 'block';
        experienceTableBody.innerHTML = '';
        
        experiences.forEach(exp => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${exp.institution || 'N/A'}</td>
                <td>${formatDate(exp.date_from) || 'N/A'}</td>
                <td>${formatDate(exp.date_until) || 'N/A'}</td>
                <td>${exp.students_recruited || '0'}</td>
            `;
            experienceTableBody.appendChild(row);
        });
    } else {
        experiencesContainer.style.display = 'none';
    }
    
    // Populate documents
    const documentsContainer = document.getElementById('documents-container');
    documentsContainer.innerHTML = '';
    
    if (documents && documents.length > 0) {
        documents.forEach(doc => {
            const docName = formatDocumentType(doc.document_type);
            const card = document.createElement('div');
            card.className = 'document-card';
            card.innerHTML = `
                <div class="document-icon">
                    <i class="fas fa-file-pdf"></i>
                </div>
                <div class="document-name">${docName}</div>
                <div class="document-actions">
                    <button class="document-btn" onclick="viewDocument('${doc.file_path}', '${docName}')">
                        View
                    </button>
                </div>
            `;
            documentsContainer.appendChild(card);
        });
    } else {
        documentsContainer.innerHTML = '<p>No documents uploaded</p>';
    }
    
    // Populate status info
    const statusBadge = document.getElementById('status-badge');
    const statusReasonContainer = document.getElementById('status-reason-container');
    const statusReason = document.getElementById('status-reason');
    const statusActionsContainer = document.getElementById('status-actions-container');
    
    // Set status badge
    let statusClass = '';
    let statusText = agent.status || 'pending';
    
    switch (statusText.toLowerCase()) {
        case 'approved':
            statusClass = 'status-approved';
            break;
        case 'rejected':
            statusClass = 'status-rejected';
            break;
        default:
            statusClass = 'status-pending';
            statusText = 'Pending';
            break;
    }
    
    statusBadge.className = 'status-badge-large ' + statusClass;
    statusBadge.textContent = capitalizeFirstLetter(statusText);
    
    // Show/hide reason
    if (agent.status_reason) {
        statusReasonContainer.style.display = 'flex';
        statusReason.textContent = agent.status_reason;
    } else {
        statusReasonContainer.style.display = 'none';
    }
    
    // Show/hide action buttons based on status
    if (statusText.toLowerCase() === 'pending') {
        statusActionsContainer.style.display = 'block';
    } else {
        statusActionsContainer.style.display = 'none';
    }
}

// Helper function to update agent status from modal
function updateAgentStatusFromModal(agentId, status, reason = '') {
    // Create FormData for the AJAX request
    const formData = new FormData();
    formData.append('agent_id', agentId);
    formData.append('status', status);
    formData.append('reason', reason);
    formData.append('action', 'update_status');
    
    // Send AJAX request
    fetch('IT-agent-actions.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message || `Agent ${status} successfully`);
            // Close modal
            document.getElementById('agentDetailsModal').style.display = 'none';
            // Refresh the agent list
            document.getElementById('agentSearchInput').dispatchEvent(new Event('input'));
        } else {
            alert(data.message || 'Failed to update agent status');
        }
    })
    .catch(error => {
        console.error('Error updating agent status:', error);
        alert('An error occurred while updating agent status');
    });
}

// Helper function to capitalize first letter
function capitalizeFirstLetter(string) {
    if (!string) return '';
    return string.charAt(0).toUpperCase() + string.slice(1);
}

// Helper function to format date
function formatDate(dateString) {
    if (!dateString) return '';
    
    const options = { year: 'numeric', month: 'short', day: 'numeric' };
    const date = new Date(dateString);
    
    if (isNaN(date.getTime())) {
        return dateString; // Return as is if invalid date
    }
    
    return date.toLocaleDateString('en-US', options);
}

// Helper function to format document type
function formatDocumentType(type) {
    if (!type) return 'Document';
    
    return type
        .split('_')
        .map(word => word.charAt(0).toUpperCase() + word.slice(1))
        .join(' ');
}