document.addEventListener('DOMContentLoaded', function() {
    // Initial variables
    const studentSearchInput = document.getElementById('studentSearchInput');
    const studentStatusFilter = document.getElementById('studentStatusFilter');
    const studentTableBody = document.getElementById('studentTableBody');
    const studentPrevPage = document.getElementById('studentPrevPage');
    const studentNextPage = document.getElementById('studentNextPage');
    const studentPageNumbers = document.getElementById('studentPageNumbers');
    
    // Make sure the status filter options match the database schema
    if (studentStatusFilter) {
        studentStatusFilter.innerHTML = `
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
    loadStudentData();
    
    // Add event listeners
    studentSearchInput.addEventListener('input', function() {
        // Debounce search inputs
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => {
            currentPage = 1;
            loadStudentData();
        }, 500);
    });
    
    studentStatusFilter.addEventListener('change', function() {
        currentPage = 1;
        loadStudentData();
    });
    
    studentPrevPage.addEventListener('click', function() {
        if (currentPage > 1) {
            currentPage--;
            loadStudentData();
        }
    });
    
    studentNextPage.addEventListener('click', function() {
        if (currentPage < totalPages) {
            currentPage++;
            loadStudentData();
        }
    });
    
    // Function to load student data
    function loadStudentData() {
        const search = studentSearchInput.value;
        const status = studentStatusFilter.value;
        
        // Show loading state
        studentTableBody.innerHTML = '<tr><td colspan="6" class="text-center">Loading...</td></tr>';
        
        // Fetch data from server
        fetch(`IO-fetch-students.php?search=${encodeURIComponent(search)}&status=${encodeURIComponent(status)}&page=${currentPage}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update pagination
                    totalPages = data.pagination.total_pages;
                    updatePagination();
                    
                    // Update table with student data
                    displayStudentData(data.students);
                    
                    // Update button states
                    studentPrevPage.disabled = currentPage === 1;
                    studentNextPage.disabled = currentPage === totalPages;
                } else {
                    studentTableBody.innerHTML = `<tr><td colspan="6" class="text-center">${data.message || 'Failed to load data'}</td></tr>`;
                }
            })
            .catch(error => {
                console.error('Error fetching student data:', error);
                studentTableBody.innerHTML = '<tr><td colspan="6" class="text-center">Error loading data. Please try again.</td></tr>';
            });
    }
    
    // Function to display student data in the table
    function displayStudentData(students) {
        if (students.length === 0) {
            studentTableBody.innerHTML = '<tr><td colspan="6" class="text-center">No students found</td></tr>';
            return;
        }
        
        let tableHtml = '';
        
        students.forEach(student => {
            // Determine status class
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
            
            // Format student name
            const fullName = `${escapeHtml(student.first_name)} ${escapeHtml(student.last_name)}`;
            
            // Build table row
            tableHtml += `
                <tr data-student-id="${student.id}">
                    <td>${student.id}</td>
                    <td>${escapeHtml(fullName)}</td>
                    <td>${escapeHtml(student.email)}</td>
                    <td>${escapeHtml(student.country)}</td>
                    <td><span class="status-badge ${statusClass}">${statusText}</span></td>
                    <td class="action-buttons">
                        <button class="btn-action btn-view" onclick="viewStudent(${student.id})" title="View Details">
                            <i class="fas fa-eye"></i>
                        </button>
                    </td>
                </tr>
            `;
        });
        
        studentTableBody.innerHTML = tableHtml;
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
                        onclick="goToStudentPage(${i})">
                    ${i}
                </button>
            `;
        }
        
        studentPageNumbers.innerHTML = paginationHtml;
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
    window.goToStudentPage = function(page) {
        currentPage = page;
        loadStudentData();
    };
});

// Student action functions
function viewStudent(studentId) {
    // This function will be defined in IO_dashboard-StudentViewer.js
    // and will show the student details modal
    if (typeof showStudentModal === 'function') {
        showStudentModal(studentId);
    } else {
        console.error('Student viewer not loaded');
        alert('Unable to view student details. Please try refreshing the page.');
    }
}