document.addEventListener('DOMContentLoaded', function() {
    // Initial variables
    const agentSearchInput = document.getElementById('agentSearchInput');
    const agentStatusFilter = document.getElementById('agentStatusFilter');
    const agentTableBody = document.getElementById('agentTableBody');
    const agentPrevPage = document.getElementById('agentPrevPage');
    const agentNextPage = document.getElementById('agentNextPage');
    const agentPageNumbers = document.getElementById('agentPageNumbers');
    
    // Make sure the status filter options match the database schema
    if (agentStatusFilter) {
        agentStatusFilter.innerHTML = `
            <option value="">All Status</option>
            <option value="pending">Pending</option>
            <option value="approved">Approved</option>
            <option value="rejected">Rejected</option>
        `;
    }
    
    let currentPage = 1;
    let totalPages = 1;
    let searchTimer;
    
    // Initial load
    loadAgentData();
    
    // Add event listeners
    agentSearchInput.addEventListener('input', function() {
        // Debounce search inputs
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => {
            currentPage = 1;
            loadAgentData();
        }, 500);
    });
    
    agentStatusFilter.addEventListener('change', function() {
        currentPage = 1;
        loadAgentData();
    });
    
    agentPrevPage.addEventListener('click', function() {
        if (currentPage > 1) {
            currentPage--;
            loadAgentData();
        }
    });
    
    agentNextPage.addEventListener('click', function() {
        if (currentPage < totalPages) {
            currentPage++;
            loadAgentData();
        }
    });
    
    // Function to load agent data
    function loadAgentData() {
        const search = agentSearchInput.value;
        const status = agentStatusFilter.value;
        
        // Show loading state
        agentTableBody.innerHTML = '<tr><td colspan="7" class="text-center">Loading...</td></tr>';
        
        // Fetch data from server
        fetch(`IT-fetch-agents.php?search=${encodeURIComponent(search)}&status=${encodeURIComponent(status)}&page=${currentPage}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update pagination
                    totalPages = data.pagination.total_pages;
                    updatePagination();
                    
                    // Update table with agent data
                    displayAgentData(data.agents);
                    
                    // Update button states
                    agentPrevPage.disabled = currentPage === 1;
                    agentNextPage.disabled = currentPage === totalPages;
                } else {
                    agentTableBody.innerHTML = `<tr><td colspan="7" class="text-center">${data.message || 'Failed to load data'}</td></tr>`;
                }
            })
            .catch(error => {
                console.error('Error fetching agent data:', error);
                agentTableBody.innerHTML = '<tr><td colspan="7" class="text-center">Error loading data. Please try again.</td></tr>';
            });
    }
    
    // Function to display agent data in the table
    function displayAgentData(agents) {
        if (agents.length === 0) {
            agentTableBody.innerHTML = '<tr><td colspan="7" class="text-center">No agents found</td></tr>';
            return;
        }
        
        let tableHtml = '';
        
        agents.forEach(agent => {
            // Determine status class
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
            
            // Format application type
            const applicationType = agent.application_type ? 
                (agent.application_type === 'new' ? 'New' : 'Renewal') : 
                'N/A';
            
            // Build table row
            tableHtml += `
                <tr data-agent-id="${agent.id}">
                    <td>${agent.registration_no || 'N/A'}</td>
                    <td>${escapeHtml(agent.company_name)}</td>
                    <td>${escapeHtml(agent.contact_name)}</td>
                    <td>${escapeHtml(agent.contact_email)}</td>
                    <td>${escapeHtml(agent.country)}</td>
                    <td><span class="status-badge ${statusClass}">${statusText}</span></td>
                    <td class="action-buttons">
                        <button class="btn-action btn-view" onclick="viewAgent(${agent.id})" title="View Details">
                            <i class="fas fa-eye"></i>
                        </button>
                    </td>
                </tr>
            `;
        });
        
        agentTableBody.innerHTML = tableHtml;
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
                        onclick="goToPage(${i})">
                    ${i}
                </button>
            `;
        }
        
        agentPageNumbers.innerHTML = paginationHtml;
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
    window.goToPage = function(page) {
        currentPage = page;
        loadAgentData();
    };
});

// Agent action functions
function viewAgent(agentId) {
    // Open modal or navigate to agent detail page
    window.location.href = `IT-agent-detail.php?id=${agentId}`;
}

function approveAgent(agentId) {
    if (confirm('Are you sure you want to approve this agent?')) {
        updateAgentStatus(agentId, 'approved');
    }
}

function rejectAgent(agentId) {
    const reason = prompt('Please enter the reason for rejection:');
    if (reason !== null) {
        updateAgentStatus(agentId, 'rejected', reason);
    }
}

function updateAgentStatus(agentId, status, reason = '') {
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