<?php
session_start();

// Check if user is logged in and has agent role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'agent') {
    header('Location: agent-login.php');
    exit();
}

// Include database connection and functions
require_once '../config/db.php';
require_once '../includes/functions.php';

// Fetch agent details
$agent_id = $_SESSION['user_id'];
$query = "SELECT * FROM agent_login WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $agent_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$agent = mysqli_fetch_assoc($result);

// Fetch statistics
$total_students_query = "SELECT COUNT(*) as total FROM students WHERE agent_id = ?";
$stmt = mysqli_prepare($conn, $total_students_query);
mysqli_stmt_bind_param($stmt, "i", $agent_id);
mysqli_stmt_execute($stmt);
$total_result = mysqli_stmt_get_result($stmt);
$total_students = mysqli_fetch_assoc($total_result)['total'];

$approved_students_query = "SELECT COUNT(*) as approved FROM students WHERE agent_id = ? AND status = 'Approved'";
$stmt = mysqli_prepare($conn, $approved_students_query);
mysqli_stmt_bind_param($stmt, "i", $agent_id);
mysqli_stmt_execute($stmt);
$approved_result = mysqli_stmt_get_result($stmt);
$approved_students = mysqli_fetch_assoc($approved_result)['approved'];

$rejected_students_query = "SELECT COUNT(*) as rejected FROM students WHERE agent_id = ? AND status = 'Rejected'";
$stmt = mysqli_prepare($conn, $rejected_students_query);
mysqli_stmt_bind_param($stmt, "i", $agent_id);
mysqli_stmt_execute($stmt);
$rejected_result = mysqli_stmt_get_result($stmt);
$rejected_students = mysqli_fetch_assoc($rejected_result)['rejected'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agent Dashboard - UPTM ISRS</title>
    <link rel="stylesheet" href="../assets/css/agent_dashboard-style.css">
    <link rel="stylesheet" href="../assets/css/agent_dashboard-MainSection.css">
    <link rel="stylesheet" href="../assets/css/agent_dashboard-ProfileSection.css">
    <link rel="stylesheet" href="../assets/css/agent_dashboard-StudentListSection.css">
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
                        <span class="nav-icon">📊</span>
                        <span class="nav-text">Main</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link" data-section="profile">
                        <span class="nav-icon">📝</span>
                        <span class="nav-text">Profile</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link" data-section="student-list">
                        <span class="nav-icon">👥</span>
                        <span class="nav-text">Student List</span>
                    </a>
                </li>
            </ul>
        </div>

        <!-- Main Content Area -->
        <main class="main-content">
            <div class="content-wrapper">
                <!-- Main Dashboard Content -->
                <div id="main-content">
                    <h1>Welcome, <?php echo htmlspecialchars($agent['name']); ?></h1>
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

                <!-- Profile Content -->
                <div id="profile-content" style="display: none;">
                    <h1>Profile</h1>
                    
                    <div class="profile-container">
                        <!-- Progress Bar -->
                        <div class="progress-bar">
                            <div class="step active">
                                <span class="step-number">1</span>
                                <span class="step-text">Personal Details</span>
                            </div>
                            <div class="step">
                                <span class="step-number">2</span>
                                <span class="step-text">Contact Information</span>
                            </div>
                            <div class="step">
                                <span class="step-number">3</span>
                                <span class="step-text">Bank Account Details</span>
                            </div>
                        </div>

                        <form id="profileForm" method="POST">
                            <!-- Personal Details Section -->
                            <div class="profile-section active" id="section-personal">
                                <div class="section-header">
                                    <h2>Personal Details</h2>
                                </div>
                                <!-- Photo upload -->
                                <div class="form-row">
                                    <div class="form-group photo-upload-container">
                                        <label>Passport Size Photo</label>
                                        <div class="photo-upload-box">
                                            <img id="photo-preview" src="#" alt="Photo preview" style="display: none;">
                                            <div id="upload-placeholder">
                                                <span>Click to upload photo</span>
                                                <small>PNG, JPEG (Max 2MB)</small>
                                            </div>
                                            <input type="file" id="passport_photo" name="passport_photo" accept=".png,.jpg,.jpeg" disabled>
                                        </div>
                                    </div>
                                </div>
                                <!-- Other personal details -->
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="company_name">Company Name / Individual Name</label>
                                        <input type="text" id="company_name" name="company_name" class="form-control" disabled>
                                    </div>
                                    <div class="form-group">
                                        <label for="registration_no">Registration No.</label>
                                        <input type="text" id="registration_no" name="registration_no" class="form-control" disabled>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="address">Address</label>
                                    <textarea id="address" name="address" class="form-control" rows="3" disabled></textarea>
                                </div>
                                <div class="form-navigation">
                                    <button type="button" class="btn-prev" style="visibility: hidden;">Previous</button>
                                    <button type="button" class="btn-edit">Edit</button>
                                    <button type="button" class="btn-next">Next</button>
                                </div>
                            </div>

                            <!-- Contact Information Section -->
                            <div class="profile-section" id="section-contact">
                                <div class="section-header">
                                    <h2>Contact Information</h2>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="contact_phone">Phone</label>
                                        <input type="tel" id="contact_phone" name="contact_phone" class="form-control" disabled>
                                    </div>
                                    <div class="form-group">
                                        <label for="contact_email">Email</label>
                                        <input type="email" id="contact_email" name="contact_email" class="form-control" disabled>
                                    </div>
                                </div>
                                <div class="form-navigation">
                                    <button type="button" class="btn-prev">Previous</button>
                                    <button type="button" class="btn-edit">Edit</button>
                                    <button type="button" class="btn-next">Next</button>
                                </div>
                            </div>

                            <!-- Bank Account Details Section -->
                            <div class="profile-section" id="section-bank">
                                <div class="section-header">
                                    <h2>Bank Account Details</h2>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="account_name">Account Name</label>
                                        <input type="text" id="account_name" name="account_name" class="form-control" disabled>
                                    </div>
                                    <div class="form-group">
                                        <label for="account_no">Account Number</label>
                                        <input type="text" id="account_no" name="account_no" class="form-control" disabled>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="bank_name">Bank Name</label>
                                        <input type="text" id="bank_name" name="bank_name" class="form-control" disabled>
                                    </div>
                                    <div class="form-group">
                                        <label for="bank_branch">Bank Branch</label>
                                        <input type="text" id="bank_branch" name="bank_branch" class="form-control" disabled>
                                    </div>
                                </div>
                                <div class="form-navigation">
                                    <button type="button" class="btn-prev">Previous</button>
                                    <button type="button" class="btn-edit">Edit</button>
                                    <button type="button" class="btn-save">Save Changes</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Student List Content -->
                <div id="student-list-content" style="display: none;">
                    <h1>Student List</h1>

                    <!-- Search and Filter Section -->
                    <div class="search-filter-container">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" id="searchInput" placeholder="Search by name or email...">
                        </div>
                        <div class="filter-box">
                            <i class="fas fa-filter"></i>
                            <select id="statusFilter">
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
                                    <th>No</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Country</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="studentTableBody">
                                <!-- Table rows will be dynamically populated -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="pagination-container">
                        <button id="prevPage" class="pagination-btn">Previous</button>
                        <div class="page-numbers">
                            <!-- Page numbers will be dynamically populated -->
                        </div>
                        <button id="nextPage" class="pagination-btn">Next</button>
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

    <script src="../assets/js/agent-dashboard.js"></script>
    <script src="../assets/js/agent_dashboard-ProfileSection.js"></script>
</body>
</html>