// Student Details Modal Functionality
document.addEventListener('DOMContentLoaded', function() {
    // Add the modal HTML structure to the page
    addStudentModalToDOM();
    
    // Get modal elements
    const studentModal = document.getElementById('studentDetailsModal');
    const closeStudentModal = document.getElementById('closeStudentModal');
    const studentModalLoading = document.getElementById('student-modal-loading');
    const studentDetailsContent = document.getElementById('student-details-content');
    
    // Tab functionality
    const tabButtons = document.querySelectorAll('.student-tab-btn');
    const tabPanes = document.querySelectorAll('.student-tab-pane');
    
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
    if (closeStudentModal) {
        closeStudentModal.addEventListener('click', function() {
            studentModal.style.display = 'none';
        });
    }
    
    // Close modal when clicking outside
    window.addEventListener('click', function(e) {
        if (e.target === studentModal) {
            studentModal.style.display = 'none';
        }
    });
    
    // Update status buttons functionality
    const approveBtn = document.getElementById('approve-btn');
    const rejectBtn = document.getElementById('reject-btn');
    const statusReasonInput = document.getElementById('status-reason-input');
    
    if (approveBtn) {
        approveBtn.addEventListener('click', function() {
            if (confirm('Are you sure you want to approve this student application?')) {
                const studentId = this.dataset.studentId;
                const reason = statusReasonInput.value.trim();
                updateStudentStatusFromModal(studentId, 'approved', reason);
            }
        });
    }
    
    if (rejectBtn) {
        rejectBtn.addEventListener('click', function() {
            if (confirm('Are you sure you want to reject this student application?')) {
                const studentId = this.dataset.studentId;
                const reason = statusReasonInput.value.trim();
                
                if (!reason) {
                    alert('Please provide a reason for rejection');
                    statusReasonInput.focus();
                    return;
                }
                
                updateStudentStatusFromModal(studentId, 'rejected', reason);
            }
        });
    }
    
    // Make showStudentModal function globally accessible
    window.showStudentModal = function(studentId) {
        // Show modal
        studentModal.style.display = 'block';
        
        // Show loading, hide content
        studentModalLoading.style.display = 'block';
        studentDetailsContent.style.display = 'none';
        
        // Reset to first tab
        tabButtons.forEach(btn => btn.classList.remove('active'));
        tabPanes.forEach(pane => pane.classList.remove('active'));
        tabButtons[0].classList.add('active');
        tabPanes[0].classList.add('active');
        
        // Fetch student details
        fetch(`IO-fetch-student-details.php?id=${studentId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    populateStudentDetails(data);
                    
                    // Set student ID for action buttons
                    if (approveBtn) approveBtn.dataset.studentId = studentId;
                    if (rejectBtn) rejectBtn.dataset.studentId = studentId;
                    
                    // Hide loading, show content
                    studentModalLoading.style.display = 'none';
                    studentDetailsContent.style.display = 'block';
                } else {
                    alert(data.message || 'Failed to load student details');
                    studentModal.style.display = 'none';
                }
            })
            .catch(error => {
                console.error('Error fetching student details:', error);
                alert('An error occurred while loading student details');
                studentModal.style.display = 'none';
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

// Add student modal to the DOM
function addStudentModalToDOM() {
    const modalHTML = `
    <div id="studentDetailsModal" class="modal" style="display: none;">
        <div class="modal-content" style="width: 80%; max-width: 900px;">
            <div class="modal-body">
                <div id="student-modal-loading" style="text-align: center; padding: 20px; display: block;">
                <i class="fas fa-spinner fa-spin" style="font-size: 2rem;"></i>
            <p>Loading student details...</p>
            </div>
                
                <div id="student-details-content" style="display: none;">
                    <!-- Tabs Navigation -->
                    <div class="student-tabs">
                        <button class="student-tab-btn active" data-tab="personal">Personal Info</button>
                        <button class="student-tab-btn" data-tab="academic">Academic Info</button>
                        <button class="student-tab-btn" data-tab="programs">Programs</button>
                        <button class="student-tab-btn" data-tab="documents">Documents</button>
                        <button class="student-tab-btn" data-tab="status">Status</button>
                    </div>
                    
                    <!-- Tab Content -->
                    <div class="student-tab-content">
                        <!-- Personal Info Tab -->
                        <div id="personal-tab" class="student-tab-pane active">
                            <div class="student-photo-container" id="student-photo-container"></div>
                            
                            <div class="detail-group">
                                <h3>Personal Information</h3>
                                <div class="detail-row">
                                    <div class="detail-label">Full Name</div>
                                    <div class="detail-value" id="full-name"></div>
                                </div>
                                <div class="detail-row">
                                    <div class="detail-label">Passport Number</div>
                                    <div class="detail-value" id="passport-no"></div>
                                </div>
                                <div class="detail-row">
                                    <div class="detail-label">Nationality</div>
                                    <div class="detail-value" id="nationality"></div>
                                </div>
                                <div class="detail-row">
                                    <div class="detail-label">Date of Birth</div>
                                    <div class="detail-value" id="date-of-birth"></div>
                                </div>
                                <div class="detail-row">
                                    <div class="detail-label">Age</div>
                                    <div class="detail-value" id="age"></div>
                                </div>
                                <div class="detail-row">
                                    <div class="detail-label">Gender</div>
                                    <div class="detail-value" id="gender"></div>
                                </div>
                                <div class="detail-row">
                                    <div class="detail-label">Place of Birth</div>
                                    <div class="detail-value" id="place-of-birth"></div>
                                </div>
                            </div>
                            
                            <div class="detail-group">
                                <h3>Contact Information</h3>
                                <div class="detail-row">
                                    <div class="detail-label">Email</div>
                                    <div class="detail-value" id="email"></div>
                                </div>
                                <div class="detail-row">
                                    <div class="detail-label">Contact Number</div>
                                    <div class="detail-value" id="contact-no"></div>
                                </div>
                                <div class="detail-row">
                                    <div class="detail-label">Address</div>
                                    <div class="detail-value" id="home-address"></div>
                                </div>
                                <div class="detail-row">
                                    <div class="detail-label">City</div>
                                    <div class="detail-value" id="city"></div>
                                </div>
                                <div class="detail-row">
                                    <div class="detail-label">State</div>
                                    <div class="detail-value" id="state"></div>
                                </div>
                                <div class="detail-row">
                                    <div class="detail-label">Postal Code</div>
                                    <div class="detail-value" id="postcode"></div>
                                </div>
                                <div class="detail-row">
                                    <div class="detail-label">Country</div>
                                    <div class="detail-value" id="country"></div>
                                </div>
                            </div>
                            
                            <div class="detail-group">
                                <h3>Guardian Information</h3>
                                <div class="detail-row">
                                    <div class="detail-label">Guardian Name</div>
                                    <div class="detail-value" id="guardian-name"></div>
                                </div>
                                <div class="detail-row">
                                    <div class="detail-label">Guardian Passport</div>
                                    <div class="detail-value" id="guardian-passport"></div>
                                </div>
                                <div class="detail-row">
                                    <div class="detail-label">Guardian Nationality</div>
                                    <div class="detail-value" id="guardian-nationality"></div>
                                </div>
                                <div class="detail-row">
                                    <div class="detail-label">Guardian Address</div>
                                    <div class="detail-value" id="guardian-address"></div>
                                </div>
                                <div class="detail-row">
                                    <div class="detail-label">Guardian Country</div>
                                    <div class="detail-value" id="guardian-country"></div>
                                </div>
                            </div>
                            
                            <div class="detail-group" id="agent-info-container" style="display: none;">
                                <h3>Agent Information</h3>
                                <div class="detail-row">
                                    <div class="detail-label">Agent Company</div>
                                    <div class="detail-value" id="agent-company"></div>
                                </div>
                                <div class="detail-row">
                                    <div class="detail-label">Agent Contact</div>
                                    <div class="detail-value" id="agent-contact"></div>
                                </div>
                                <div class="detail-row">
                                    <div class="detail-label">Agent Email</div>
                                    <div class="detail-value" id="agent-email"></div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Academic Info Tab -->
                        <div id="academic-tab" class="student-tab-pane">
                            <div class="detail-group">
                                <h3>Educational Qualifications</h3>
                                <div id="qualifications-container">
                                    <table class="qualification-table">
                                        <thead>
                                            <tr>
                                                <th>Qualification</th>
                                                <th>Institution</th>
                                                <th>Grade</th>
                                                <th>Duration</th>
                                                <th>Year Completed</th>
                                            </tr>
                                        </thead>
                                        <tbody id="qualifications-table-body">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            <div class="detail-group">
                                <h3>English Proficiency</h3>
                                <div class="english-proficiency-container">
                                    <div class="proficiency-row">
                                        <div class="proficiency-test">
                                            <div class="detail-row">
                                                <div class="detail-label">MUET</div>
                                                <div class="detail-value">
                                                    Score: <span id="muet-score">N/A</span> | 
                                                    Year: <span id="muet-year">N/A</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="proficiency-row">
                                        <div class="proficiency-test">
                                            <div class="detail-row">
                                                <div class="detail-label">IELTS</div>
                                                <div class="detail-value">
                                                    Score: <span id="ielts-score">N/A</span> | 
                                                    Year: <span id="ielts-year">N/A</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="proficiency-row">
                                        <div class="proficiency-test">
                                            <div class="detail-row">
                                                <div class="detail-label">TOEFL</div>
                                                <div class="detail-value">
                                                    Score: <span id="toefl-score">N/A</span> | 
                                                    Year: <span id="toefl-year">N/A</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="proficiency-row">
                                        <div class="proficiency-test">
                                            <div class="detail-row">
                                                <div class="detail-label">TOIEC</div>
                                                <div class="detail-value">
                                                    Score: <span id="toiec-score">N/A</span> | 
                                                    Year: <span id="toiec-year">N/A</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="detail-group">
                                <h3>Financial Support</h3>
                                <div class="detail-row">
                                    <div class="detail-label">Financial Support</div>
                                    <div class="detail-value" id="financial-support"></div>
                                </div>
                                <div class="detail-row" id="bank-details" style="display: none;">
                                    <div class="detail-label">Bank Details</div>
                                    <div class="detail-value">
                                        <span id="bank-name"></span>
                                        <span id="account-no"></span>
                                    </div>
                                </div>
                                <div class="detail-row" id="financial-others" style="display: none;">
                                    <div class="detail-label">Other Details</div>
                                    <div class="detail-value" id="financial-support-others"></div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Programs Tab -->
                        <div id="programs-tab" class="student-tab-pane">
                            <div class="detail-group">
                                <h3>Program Choices</h3>
                                <div id="program-choices-container">
                                    <!-- Program choices will be added here -->
                                </div>
                            </div>
                        </div>
                        
                        <!-- Documents Tab -->
                        <div id="documents-tab" class="student-tab-pane">
                            <div class="detail-group">
                                <h3>Uploaded Documents</h3>
                                <div id="documents-container" class="documents-grid">
                                    <!-- Documents will be added here dynamically -->
                                </div>
                            </div>
                        </div>
                        
                        <!-- Status Tab -->
                        <div id="status-tab" class="student-tab-pane">
                            <div class="detail-group">
                                <h3>Application Status</h3>
                                <div class="status-display">
                                    <div class="status-badge-container">
                                        <span id="status-badge" class="status-badge-large">Pending</span>
                                    </div>
                                    
                                    <div class="detail-row" id="status-reason-container" style="display: none;">
                                        <div class="detail-label">Reason</div>
                                        <div class="detail-value" id="status-reason"></div>
                                    </div>
                                    
                                    <div id="status-actions-container">
                                        <div id="status-form-container">
                                            <textarea id="status-reason-input" class="reason-input" placeholder="Enter reason or comments (required for rejection)"></textarea>
                                            <div class="status-buttons">
                                                <button id="approve-btn" class="action-btn action-btn-approve">Approve Application</button>
                                                <button id="reject-btn" class="action-btn action-btn-reject">Reject Application</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>`;
    
    // Append modal HTML to body
    document.body.insertAdjacentHTML('beforeend', modalHTML);
}

// Function to populate student details in the modal
function populateStudentDetails(data) {
    const student = data.student;
    const qualifications = data.qualifications;
    const programs = data.programs;
    const documents = data.documents;
    const agent_info = data.agent_info;
    
    // Populate personal info
    document.getElementById('full-name').textContent = `${student.first_name} ${student.last_name}`;
    document.getElementById('passport-no').textContent = student.passport_no || 'N/A';
    document.getElementById('nationality').textContent = student.nationality || 'N/A';
    document.getElementById('date-of-birth').textContent = formatDate(student.date_of_birth) || 'N/A';
    document.getElementById('age').textContent = student.age || 'N/A';
    document.getElementById('gender').textContent = capitalizeFirstLetter(student.gender) || 'N/A';
    document.getElementById('place-of-birth').textContent = student.place_of_birth || 'N/A';
    
    // Populate contact info
    document.getElementById('email').textContent = student.email || 'N/A';
    document.getElementById('contact-no').textContent = student.contact_no || 'N/A';
    document.getElementById('home-address').textContent = student.home_address || 'N/A';
    document.getElementById('city').textContent = student.city || 'N/A';
    document.getElementById('state').textContent = student.state || 'N/A';
    document.getElementById('postcode').textContent = student.postcode || 'N/A';
    document.getElementById('country').textContent = student.country || 'N/A';
    
    // Populate guardian info
    document.getElementById('guardian-name').textContent = student.guardian_name || 'N/A';
    document.getElementById('guardian-passport').textContent = student.guardian_passport || 'N/A';
    document.getElementById('guardian-nationality').textContent = student.guardian_nationality || 'N/A';
    document.getElementById('guardian-address').textContent = student.guardian_address || 'N/A';
    document.getElementById('guardian-country').textContent = student.guardian_country || 'N/A';
    
    // Populate agent info if available
    const agentInfoContainer = document.getElementById('agent-info-container');
    if (agent_info) {
        document.getElementById('agent-company').textContent = agent_info.company_name || 'N/A';
        document.getElementById('agent-contact').textContent = agent_info.contact_name || 'N/A';
        document.getElementById('agent-email').textContent = agent_info.contact_email || 'N/A';
        agentInfoContainer.style.display = 'block';
    } else {
        agentInfoContainer.style.display = 'none';
    }
    
    // Populate photo if available
    const photoContainer = document.getElementById('student-photo-container');
    if (student.photo_path) {
        photoContainer.innerHTML = `<img src="../${student.photo_path}" alt="Student Photo">`;
    } else {
        photoContainer.innerHTML = `<div class="no-photo">No photo available</div>`;
    }
    
    // Populate qualifications
    const qualificationsTableBody = document.getElementById('qualifications-table-body');
    qualificationsTableBody.innerHTML = '';
    
    if (qualifications && qualifications.length > 0) {
        qualifications.forEach(qual => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${qual.qualification || 'N/A'}</td>
                <td>${qual.institution || 'N/A'}</td>
                <td>${qual.grade || 'N/A'}</td>
                <td>${qual.duration || 'N/A'}</td>
                <td>${qual.year_completed || 'N/A'}</td>
            `;
            qualificationsTableBody.appendChild(row);
        });
    } else {
        qualificationsTableBody.innerHTML = `<tr><td colspan="5" class="text-center">No qualifications provided</td></tr>`;
    }
    
    // Populate English proficiency
    document.getElementById('muet-score').textContent = student.muet_score || 'N/A';
    document.getElementById('muet-year').textContent = student.muet_year || 'N/A';
    document.getElementById('ielts-score').textContent = student.ielts_score || 'N/A';
    document.getElementById('ielts-year').textContent = student.ielts_year || 'N/A';
    document.getElementById('toefl-score').textContent = student.toefl_score || 'N/A';
    document.getElementById('toefl-year').textContent = student.toefl_year || 'N/A';
    document.getElementById('toiec-score').textContent = student.toiec_score || 'N/A';
    document.getElementById('toiec-year').textContent = student.toiec_year || 'N/A';
    
    // Populate financial support
    document.getElementById('financial-support').textContent = student.financial_support || 'N/A';
    
    // Show/hide bank details based on financial support type
    const bankDetails = document.getElementById('bank-details');
    if (student.financial_support === 'Self' && (student.bank_name || student.account_no)) {
        document.getElementById('bank-name').textContent = student.bank_name || 'N/A';
        document.getElementById('account-no').textContent = student.account_no ? ` (Account: ${student.account_no})` : '';
        bankDetails.style.display = 'flex';
    } else {
        bankDetails.style.display = 'none';
    }
    
    // Show/hide other financial support details
    const financialOthers = document.getElementById('financial-others');
    if (student.financial_support_others) {
        document.getElementById('financial-support-others').textContent = student.financial_support_others;
        financialOthers.style.display = 'flex';
    } else {
        financialOthers.style.display = 'none';
    }
    
    // Populate program choices
    const programChoicesContainer = document.getElementById('program-choices-container');
    programChoicesContainer.innerHTML = '';
    
    if (programs && Object.keys(programs).length > 0) {
        let choiceNumber = 1;
        
        for (const key in programs) {
            const programDetail = programs[key];
            const programDiv = document.createElement('div');
            programDiv.className = 'detail-row';
            programDiv.innerHTML = `
                <div class="detail-label">Choice ${choiceNumber}</div>
                <div class="detail-value">${programDetail.code} - ${programDetail.name}</div>
            `;
            programChoicesContainer.appendChild(programDiv);
            choiceNumber++;
        }
    } else {
        programChoicesContainer.innerHTML = '<p>No program choices found</p>';
    }
    
    // Populate documents
    const documentsContainer = document.getElementById('documents-container');
    documentsContainer.innerHTML = '';
    
    if (documents) {
        for (const key in documents) {
            const doc = documents[key];
            if (doc.path) {
                const card = document.createElement('div');
                card.className = 'document-card';
                card.innerHTML = `
                    <div class="document-icon">
                        <i class="fas fa-file-pdf"></i>
                    </div>
                    <div class="document-name">${doc.type}</div>
                    <div class="document-actions">
                        <button class="document-btn" onclick="viewDocument('${doc.path}', '${doc.type}')">
                            View
                        </button>
                    </div>
                `;
                documentsContainer.appendChild(card);
            }
        }
        
        // If no documents were added, show a message
        if (documentsContainer.children.length === 0) {
            documentsContainer.innerHTML = '<p>No documents uploaded</p>';
        }
    } else {
        documentsContainer.innerHTML = '<p>No documents available</p>';
    }
    
    // Populate status info
    const statusBadge = document.getElementById('status-badge');
    const statusReasonContainer = document.getElementById('status-reason-container');
    const statusReason = document.getElementById('status-reason');
    const statusActionsContainer = document.getElementById('status-actions-container');
    
    // Set status badge
    let statusClass = '';
    let statusText = student.status || 'pending';
    
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
    if (student.status_reason) {
        statusReasonContainer.style.display = 'flex';
        statusReason.textContent = student.status_reason;
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

// Helper function to update student status from modal
function updateStudentStatusFromModal(studentId, status, reason = '') {
    // Create FormData for the AJAX request
    const formData = new FormData();
    formData.append('student_id', studentId);
    formData.append('status', status);
    formData.append('reason', reason);
    formData.append('action', 'update_status');
    
    // Send AJAX request
    fetch('IO-student-actions.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message || `Student application ${status} successfully`);
            // Close modal
            document.getElementById('studentDetailsModal').style.display = 'none';
            // Refresh the student list
            document.getElementById('studentSearchInput').dispatchEvent(new Event('input'));
        } else {
            alert(data.message || 'Failed to update student status');
        }
    })
    .catch(error => {
        console.error('Error updating student status:', error);
        alert('An error occurred while updating student status');
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