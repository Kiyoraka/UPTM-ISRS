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
    <link rel="stylesheet" href="../assets/css/IO-AO_dashboard-styles.css">
    <link rel="stylesheet" href="../assets/css/IO-AO_dashboard-MainSection.css">
    <link rel="stylesheet" href="../assets/css/IO-AO_dashboard-StudentListSection.css">
    <link rel="stylesheet" href="../assets/css/IO-AO_dashboard-UserDropDownMenu.css">
    <link rel="stylesheet" href="../assets/css/IO-AO_dashboard-StudentViewer.css">
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

    <!-- Load the base dashboard JS -->
    <script src="../assets/js/IO-AO_dashboard.js"></script>
    <script src="../assets/js/IO-AO_dashboard-UserDropDownMenu.js"></script>
    <script src="../assets/js/IO-AO_dashboard-notification.js"></script>
    
    <!-- Load the student management JS -->
    <script src="../assets/js/IO_dashboard-StudentManagement.js"></script>
    <script src="../assets/js/IO_dashboard-StudentViewer.js"></script>
</body>
</html>