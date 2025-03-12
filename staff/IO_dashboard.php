<?php
session_start();

// Check if user is logged in and has IO role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'io') {
    header('Location: ../staff/staff-login.php');
    exit();
}

// Include database connection and functions
require_once '../config/db.php';
require_once '../includes/functions.php';

// Fetch staff details
$staff_id = $_SESSION['user_id'];
$query = "SELECT * FROM staff WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $staff_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$staff = mysqli_fetch_assoc($result);

// Fetch student statistics
$total_students_query = "SELECT COUNT(*) as total FROM students";
$total_result = mysqli_query($conn, $total_students_query);
$total_students = mysqli_fetch_assoc($total_result)['total'] ?? 0;

$approved_students_query = "SELECT COUNT(*) as approved FROM students WHERE io_status = 'approved'";
$approved_result = mysqli_query($conn, $approved_students_query);
$approved_students = mysqli_fetch_assoc($approved_result)['approved'] ?? 0;

$rejected_students_query = "SELECT COUNT(*) as rejected FROM students WHERE io_status = 'rejected'";
$rejected_result = mysqli_query($conn, $rejected_students_query);
$rejected_students = mysqli_fetch_assoc($rejected_result)['rejected'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>International Officer Dashboard - UPTM ISRS</title>
    <link rel="stylesheet" href="../assets/css/IO_dashboard-styles.css">
    <link rel="stylesheet" href="../assets/css/IO_dashboard-MainSection.css">
    <link rel="stylesheet" href="../assets/css/IO_dashboard-StudentListSection.css">
    <link rel="stylesheet" href="../assets/css/IO_dashboard-UserDropDownMenu.css">
    <link rel="stylesheet" href="../assets/css/IO_dashboard-StudentViewer.css">
    <link rel="stylesheet" href="../assets/css/IT_dashboard-notification.css">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

</head>
<body>
    <div class="dashboard-container">
        <!-- Top Navigation Bar -->
        <nav class="top-nav">
            <div class="logo-container">
                <img src="../assets/img/uptm-logo.png" alt="UPTM Logo" class="logo">
            </div>
            <div class="nav-right">
                <div class="user-profile">
                    <span class="user-icon">👤</span>
                    <div class="user-dropdown">
                        <div class="user-dropdown-header">
                            <div class="user-name"><?php echo htmlspecialchars($_SESSION['username']); ?></div>
                            <div class="user-role">International Office</div>
                        </div>
                        <a href="staff-logout.php" class="dropdown-item logout">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </div>
                </div>
                <div class="notifications">
                    <span class="notification-icon">🔔</span>
                </div>
            </div>
        </nav>

        <!-- Sidebar Navigation -->
        <div class="sidebar">
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="#" class="nav-link active" data-section="main">
                        <span class="nav-icon"><i class="fa fa-home"></i></span>
                        <span class="nav-text">Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link" data-section="student-list">
                        <span class="nav-icon"><i class="fas fa-user-graduate"></i></span>
                        <span class="nav-text">Students List</span>
                    </a>
                </li>
            </ul>
        </div>

        <!-- Main Content Area -->
        <main class="main-content">
            <div class="content-wrapper">
                <!-- Main Dashboard Content -->
                <div id="main-content">
                    <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></h1>
                    <div class="current-month">
                        <?php echo date('M Y'); ?>
                    </div>

                    <div class="stats-container">
                        <!-- Total Students Registered -->
                        <div class="stat-card">
                            <div class="stat-icon registered">
                                <i class="fas fa-user-graduate"></i>
                            </div>
                            <div class="stat-number registered"><?php echo $total_students; ?></div>
                            <div class="stat-label">Total Student Registered</div>
                        </div>

                        <!-- Total Students Approved -->
                        <div class="stat-card">
                            <div class="stat-icon approved">
                                <i class="fas fa-user-graduate"></i>
                            </div>
                            <div class="stat-number approved"><?php echo $approved_students; ?></div>
                            <div class="stat-label">Total Student Approved</div>
                        </div>

                        <!-- Total Students Rejected -->
                        <div class="stat-card">
                            <div class="stat-icon rejected">
                                <i class="fas fa-user-graduate"></i>
                            </div>
                            <div class="stat-number rejected"><?php echo $rejected_students; ?></div>
                            <div class="stat-label">Total Student Rejected</div>
                        </div>
                    </div>
                </div>

                <!-- Student List Content -->
                <div id="student-list-content" style="display: none;">
                    <h1>Student List</h1>

                    <!-- Search and Filter Section -->
                    <div class="search-filter-container">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" id="studentSearchInput" placeholder="Search by name, email, or ID...">
                        </div>
                        <div class="filter-box">
                            <i class="fas fa-filter"></i>
                            <select id="studentStatusFilter">
                                <option value="">All Status</option>
                                <option value="pending">Pending</option>
                                <option value="approved">Approved</option>
                                <option value="rejected">Rejected</option>
                            </select>
                        </div>
                    </div>

                    <!-- Table Container -->
                    <div class="table-container">
                        <table class="student-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Country</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="studentTableBody">
                                <!-- Table rows will be dynamically populated -->
                                <tr>
                                    <td colspan="6" class="text-center">Loading students...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="pagination-container">
                        <button id="studentPrevPage" class="pagination-btn">Previous</button>
                        <div class="page-numbers" id="studentPageNumbers">
                            <!-- Page numbers will be dynamically populated -->
                        </div>
                        <button id="studentNextPage" class="pagination-btn">Next</button>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <footer class="dashboard-footer">
                <div class="footer-content">
                    <div class="footer-text">
                        Copyright 2024 © UPTM International Student Registration System
                    </div>
                </div>
            </footer>
        </main>
    </div>

    <!-- Student Details Modal -->
<div id="studentDetailsModal" class="modal">
    <div class="modal-content" style="width: 80%; max-width: 900px;">
        <div class="modal-header">
            <h2>Student Details</h2>
            <span class="close-modal" id="closeStudentModal">&times;</span>
        </div>
        <div class="modal-body">
            <div id="student-modal-loading" style="text-align: center; padding: 20px;">
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
</div>

<!-- Document Viewer Modal -->
<div id="documentViewerModal" class="document-viewer-modal">
    <div class="document-viewer-content">
        <span class="close-document-viewer">&times;</span>
        <iframe class="document-frame" id="documentFrame"></iframe>
    </div>
</div>

    <!-- Load the base dashboard JS -->
    <script src="../assets/js/IO_dashboard.js"></script>
    <script src="../assets/js/IO_dashboard-UserDropDownMenu.js"></script>
    <script src="../assets/js/IO_dashboard-notification.js"></script>
    
    <!-- Load the student management JS -->
    <script src="../assets/js/IO_dashboard-StudentManagement.js"></script>
    <script src="../assets/js/IO_dashboard-StudentViewer.js"></script>

    <!-- Debug and testing script -->
    <!--<script src="../assets/js/IO_dashboard-studentmodaldebug.js"></script> -->

    
</body>
</html>