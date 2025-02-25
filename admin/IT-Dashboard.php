<?php
session_start();

// Check if user is logged in and has admin role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'it') {
    header('Location: admin-login.php'); 
    exit();
}

// Include database connection and functions
require_once '../config/db.php';
require_once '../includes/functions.php';

// Fetch admin details
$admin_id = $_SESSION['user_id'];
$query = "SELECT * FROM staff WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $admin_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$admin = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IT Admin Dashboard - UPTM ISRS</title>
    <link rel="stylesheet" href="../assets/css/IT_dashboard-styles.css">
    <link rel="stylesheet" href="../assets/css/IT_dashboard-MainSection.css">
    <link rel="stylesheet" href="../assets/css/IT_dashboard-AgentListSection.css">
    <link rel="stylesheet" href="../assets/css/IT_dashboard-UserDropDownMenu.css">
    <link rel="stylesheet" href="../assets/css/IT_dashboard-changepassword.css">
    <link rel="stylesheet" href="../assets/css/IT_dashboard-AgentViewer.css">
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
                    <div class="user-dropdown" style="position: fixed; z-index: 9999; top: 60px; right: 70px;">
                        <div class="user-dropdown-header">
                            <div class="user-name"><?php echo htmlspecialchars($_SESSION['username']); ?></div>
                            <div class="user-role">IT Admin</div>
                        </div>
                        <a href="#" class="dropdown-item" id="change-password-link">
                            <i class="fas fa-key"></i> Change Password
                        </a>
                        <a href="IT-logout.php" class="dropdown-item logout">
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
                        <span class="nav-icon"><i class="fas fa-tachometer-alt"></i></span>
                        <span class="nav-text">Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link" data-section="student">
                        <span class="nav-icon"><i class="fas fa-user-graduate"></i></span>
                        <span class="nav-text">Students</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link" data-section="staff">
                        <span class="nav-icon"><i class="fas fa-user-tie"></i></span>
                        <span class="nav-text">Staff</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link" data-section="agent">
                        <span class="nav-icon"><i class="fas fa-user-friends"></i></span>
                        <span class="nav-text">Agents</span>
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
                        <!-- Total Students -->
                        <div class="stat-card">
                            <div class="stat-icon registered">
                                <i class="fas fa-user-graduate"></i>
                            </div>
                            <div class="stat-number registered">0</div>
                            <div class="stat-label">Total Students</div>
                        </div>

                        <!-- Total Staff -->
                        <div class="stat-card">
                            <div class="stat-icon approved">
                                <i class="fas fa-user-tie"></i>
                            </div>
                            <div class="stat-number approved">0</div>
                            <div class="stat-label">Total Staff</div>
                        </div>

                        <!-- Total Agents -->
                        <div class="stat-card">
                            <div class="stat-icon rejected">
                                <i class="fas fa-user-friends"></i>
                            </div>
                            <div class="stat-number rejected">0</div>
                            <div class="stat-label">Total Agents</div>
                        </div>
                    </div>
                </div>

                <!-- Student Management Content -->
                <div id="student-content" style="display: none;">
                    <h1>Student Management</h1>
                    
                </div>

                <!-- Staff Management Content -->
                <div id="staff-content" style="display: none;">
                    <h1>Staff Management</h1>
                    
                    <!-- Search and Filter Section -->
                    <div class="search-filter-container">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" id="staffSearchInput" placeholder="Search by name, email, or ID...">
                        </div>
                        <div class="filter-box">
                            <i class="fas fa-filter"></i>
                            <select id="staffDepartmentFilter">
                                <option value="">All Departments</option>
                                <option value="academic">Academic</option>
                                <option value="admin">Administration</option>
                                <option value="finance">Finance</option>
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
                                    <th>Department</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="staffTableBody">
                                <!-- Table rows will be dynamically populated -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="pagination-container">
                        <button id="staffPrevPage" class="pagination-btn">Previous</button>
                        <div class="page-numbers" id="staffPageNumbers">
                            <!-- Page numbers will be dynamically populated -->
                        </div>
                        <button id="staffNextPage" class="pagination-btn">Next</button>
                    </div>
                </div>

                <!-- Agent Management Content -->
                <div id="agent-content" style="display: none;">
                    <h1>Agent Management</h1>
                    
                    <!-- Search and Filter Section -->
                    <div class="search-filter-container" style="position: relative; z-index: 10;">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" id="agentSearchInput" placeholder="Search by name, email, or ID...">
                        </div>
                        <div class="filter-box">
                            <i class="fas fa-filter"></i>
                            <select id="agentStatusFilter">
                                <option value="">All Status</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="pending">Pending Approval</option>
                            </select>
                        </div>
                    </div>

                    <!-- Table Container -->
                    <div class="table-container">
                        <table class="student-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Company Name</th>
                                    <th>Contact Person</th>
                                    <th>Email</th>
                                    <th>Country</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="agentTableBody">
                                <!-- Table rows will be dynamically populated -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="pagination-container">
                        <button id="agentPrevPage" class="pagination-btn">Previous</button>
                        <div class="page-numbers" id="agentPageNumbers">
                            <!-- Page numbers will be dynamically populated -->
                        </div>
                        <button id="agentNextPage" class="pagination-btn">Next</button>
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

    <!-- Change Password Modal -->
    <div id="passwordModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Change Password</h2>
                <span class="close-modal">&times;</span>
            </div>
            <div class="modal-body">
                <div id="password-message"></div>
                
                <form id="changePasswordForm">
                    <div class="form-group">
                        <label for="current_password">Current Password</label>
                        <input type="password" id="current_password" name="current_password" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="new_password">New Password</label>
                        <input type="password" id="new_password" name="new_password" class="form-control" required minlength="8" oninput="checkPasswordStrength()">
                        <div id="password-strength" class="password-strength"></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password">Confirm New Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" class="form-control" required minlength="8" oninput="checkPasswordMatch()">
                        <div id="password-match" class="password-strength"></div>
                    </div>
                    
                    <div class="btn-group">
                        <button type="button" class="btn btn-secondary" id="cancelPasswordChange">Cancel</button>
                        <button type="submit" class="btn btn-primary">Change Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Agent Details Modal -->
<div id="agentDetailsModal" class="modal">
    <div class="modal-content" style="width: 80%; max-width: 900px;">
        <div class="modal-header">
            <h2>Agent Details</h2>
            <span class="close-modal" id="closeAgentModal">&times;</span>
        </div>
        <div class="modal-body">
            <div id="agent-modal-loading" style="text-align: center; padding: 20px;">
                <i class="fas fa-spinner fa-spin" style="font-size: 2rem;"></i>
                <p>Loading agent details...</p>
            </div>
            
            <div id="agent-details-content" style="display: none;">
                <!-- Tabs Navigation -->
                <div class="agent-tabs">
                    <button class="agent-tab-btn active" data-tab="general">General Info</button>
                    <button class="agent-tab-btn" data-tab="contact">Contact</button>
                    <button class="agent-tab-btn" data-tab="business">Business</button>
                    <button class="agent-tab-btn" data-tab="documents">Documents</button>
                    <button class="agent-tab-btn" data-tab="status">Status</button>
                </div>
                
                <!-- Tab Content -->
                <div class="agent-tab-content">
                    <!-- General Info Tab -->
                    <div id="general-tab" class="agent-tab-pane active">
                        <div class="agent-photo-container" id="agent-photo-container"></div>
                        
                        <div class="detail-group">
                            <h3>General Information</h3>
                            <div class="detail-row">
                                <div class="detail-label">Application Type</div>
                                <div class="detail-value" id="application-type"></div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">Company Name</div>
                                <div class="detail-value" id="company-name"></div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">Registration Number</div>
                                <div class="detail-value" id="registration-no"></div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">Address</div>
                                <div class="detail-value" id="address"></div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">Postal Code</div>
                                <div class="detail-value" id="postal-code"></div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">Country</div>
                                <div class="detail-value" id="country"></div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Contact Tab -->
                    <div id="contact-tab" class="agent-tab-pane">
                        <div class="detail-group">
                            <h3>Contact Information</h3>
                            <div class="detail-row">
                                <div class="detail-label">Contact Name</div>
                                <div class="detail-value" id="contact-name"></div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">Designation</div>
                                <div class="detail-value" id="contact-designation"></div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">Phone</div>
                                <div class="detail-value" id="contact-phone"></div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">Fax</div>
                                <div class="detail-value" id="contact-fax"></div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">Mobile</div>
                                <div class="detail-value" id="contact-mobile"></div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">Email</div>
                                <div class="detail-value" id="contact-email"></div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">Website</div>
                                <div class="detail-value" id="website"></div>
                            </div>
                        </div>
                        
                        <div class="detail-group">
                            <h3>Bank Information</h3>
                            <div class="detail-row">
                                <div class="detail-label">Account Name</div>
                                <div class="detail-value" id="account-name"></div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">Account Number</div>
                                <div class="detail-value" id="account-no"></div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">Bank Name</div>
                                <div class="detail-value" id="bank-name"></div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">Bank Branch</div>
                                <div class="detail-value" id="bank-branch"></div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">Swift Code</div>
                                <div class="detail-value" id="swift-code"></div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">Bank Address</div>
                                <div class="detail-value" id="bank-address"></div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Business Tab -->
                    <div id="business-tab" class="agent-tab-pane">
                        <div class="detail-group">
                            <h3>Business Information</h3>
                            <div class="detail-row">
                                <div class="detail-label">Countries Covered</div>
                                <div class="detail-value" id="countries-covered"></div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">Recruitment Experience</div>
                                <div class="detail-value" id="recruitment-experience"></div>
                            </div>
                        </div>
                        
                        <div class="detail-group" id="experiences-container">
                            <h3>Previous Experience</h3>
                            <div id="experience-table-container">
                                <table class="experience-table">
                                    <thead>
                                        <tr>
                                            <th>Institution</th>
                                            <th>From</th>
                                            <th>Until</th>
                                            <th>Students Recruited</th>
                                        </tr>
                                    </thead>
                                    <tbody id="experience-table-body">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <div class="detail-group">
                            <h3>Agreement Information</h3>
                            <div class="detail-row">
                                <div class="detail-label">Signee Name</div>
                                <div class="detail-value" id="signee-full-name"></div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">Signee Designation</div>
                                <div class="detail-value" id="signee-designation"></div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">Signee NRIC</div>
                                <div class="detail-value" id="signee-nric"></div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">Witness Name</div>
                                <div class="detail-value" id="witness-full-name"></div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">Witness Designation</div>
                                <div class="detail-value" id="witness-designation"></div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">Witness NRIC</div>
                                <div class="detail-value" id="witness-nric"></div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">Signature Date</div>
                                <div class="detail-value" id="signature-date"></div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Documents Tab -->
                    <div id="documents-tab" class="agent-tab-pane">
                        <div class="detail-group">
                            <h3>Uploaded Documents</h3>
                            <div id="documents-container" class="documents-grid">
                                <!-- Documents will be added here dynamically -->
                            </div>
                        </div>
                    </div>
                    
                    <!-- Status Tab -->
                    <div id="status-tab" class="agent-tab-pane">
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
                                        <textarea id="status-reason-input" class="reason-input" placeholder="Enter reason or comments (optional)"></textarea>
                                        <div class="status-buttons">
                                            <button id="approve-btn" class="action-btn action-btn-approve">Approve Agent</button>
                                            <button id="reject-btn" class="action-btn action-btn-reject">Reject Agent</button>
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

    <script src="../assets/js/IT-dashboard.js"></script>
    <script src="../assets/js/IT_dashboard-UserDropDownMenu.js"></script>
    <script src="../assets/js/IT_dashboard-ChangePassword.js"></script>
    <script src="../assets/js/IT_dashboard-AgentManagement.js"></script>
    <script src="../assets/js/IT_dashboard-AgentViewer.js"></script>
    <script src="../assets/js/IT_dashboard-notification.js"></script>
</body>
</html>