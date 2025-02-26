<?php
session_start();

// Check if user is logged in and has student role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header('Location: student-login.php');
    exit();
}

// Include database connection and functions
require_once '../config/db.php';
require_once '../includes/functions.php';

// Fetch student details
$student_id = $_SESSION['user_id'];
$query = "SELECT * FROM students WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $student_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$student = mysqli_fetch_assoc($result);

// Fetch application status
$status_query = "SELECT io_status, ao_status FROM students WHERE id = ?";
$status_stmt = mysqli_prepare($conn, $status_query);
mysqli_stmt_bind_param($status_stmt, "i", $student_id);
mysqli_stmt_execute($status_stmt);
$status_result = mysqli_stmt_get_result($status_stmt);
$status = mysqli_fetch_assoc($status_result);

// Get IO and AO status
$io_status = $status['io_status'];
$ao_status = $status['ao_status'];

// Determine overall application status
$application_status = 'pending';
if ($io_status === 'approved' && $ao_status === 'approved') {
    $application_status = 'approved';
} elseif ($io_status === 'rejected' || $ao_status === 'rejected') {
    $application_status = 'rejected';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - UPTM ISRS</title>
    <link rel="stylesheet" href="../assets/css/student_dashboard-style.css">
    <link rel="stylesheet" href="../assets/css/student_dashboard-MainSection.css">
    <link rel="stylesheet" href="../assets/css/student_dashboard-ProfileSection.css">
    <link rel="stylesheet" href="../assets/css/student_dashboard-DocumentSection.css">
    <link rel="stylesheet" href="../assets/css/student_dashboard-PaymentSection.css">
    <link rel="stylesheet" href="../assets/css/student_dashboard-UserDropDownMenu.css">
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
                        <a href="student-logout.php" class="dropdown-item">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </div>
                </div>
                <div class="notifications">
                    <span class="notification-icon">🔔</span>
                    <div class="notification-count" style="<?php echo count($notifications) > 0 ? '' : 'display: none;' ?>">
                        <?php echo count($notifications ?? []); ?>
                    </div>
                    <div class="notification-dropdown">
                        <div class="notification-header">
                            <h3>Notifications</h3>
                        </div>
                        <div class="notification-list">
                            <?php if (empty($notifications ?? [])): ?>
                                <div class="no-notifications">
                                    <p>No new notifications</p>
                                </div>
                            <?php else: ?>
                                <?php foreach ($notifications as $notification): ?>
                                    <div class="notification-item">
                                        <div class="notification-content">
                                            <p><?php echo htmlspecialchars($notification['message']); ?></p>
                                            <span class="notification-time"><?php echo htmlspecialchars($notification['created_at']); ?></span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
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
                    <a href="#" class="nav-link" data-section="profile">
                        <span class="nav-icon"><i class="fas fa-user"></i></span>
                        <span class="nav-text">Profile</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link" data-section="document">
                        <span class="nav-icon"><i class="fas fa-file-alt"></i></span>
                        <span class="nav-text">Documents</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link" data-section="payment">
                        <span class="nav-icon"><i class="fas fa-credit-card"></i></span>
                        <span class="nav-text">Payment</span>
                    </a>
                </li>
            </ul>
        </div>

        <!-- Main Content Area -->
        <main class="main-content">
            <div class="content-wrapper">
                <!-- Main Dashboard Content -->
                <div id="main-content">
                    <h1>Welcome, <?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></h1>
                    <div class="current-month">
                        <?php echo date('M Y'); ?>
                    </div>

                    <div class="application-status-banner <?php echo $application_status; ?>">
                        <div class="status-icon">
                            <?php if ($application_status === 'approved'): ?>
                                <i class="fas fa-check-circle"></i>
                            <?php elseif ($application_status === 'rejected'): ?>
                                <i class="fas fa-times-circle"></i>
                            <?php else: ?>
                                <i class="fas fa-clock"></i>
                            <?php endif; ?>
                        </div>
                        <div class="status-text">
                            <h3>Application Status: <?php echo ucfirst($application_status); ?></h3>
                            <p>
                                <?php if ($application_status === 'approved'): ?>
                                    Your application has been approved. You can now proceed with payment.
                                <?php elseif ($application_status === 'rejected'): ?>
                                    Your application has been rejected. Please check your email for more details.
                                <?php else: ?>
                                    Your application is currently being reviewed. We will notify you once processed.
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>

                    <div class="stats-container">
                        <!-- Status Cards -->
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-university"></i>
                            </div>
                            <div class="stat-title">International Office</div>
                            <div class="stat-status <?php echo $io_status; ?>">
                                <?php echo ucfirst($io_status); ?>
                            </div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-graduation-cap"></i>
                            </div>
                            <div class="stat-title">Academic Office</div>
                            <div class="stat-status <?php echo $ao_status; ?>">
                                <?php echo ucfirst($ao_status); ?>
                            </div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-file-invoice-dollar"></i>
                            </div>
                            <div class="stat-title">Payment Status</div>
                            <div class="stat-status pending">
                                Pending
                            </div>
                        </div>
                    </div>

                    <div class="program-card">
                        <h3>Program Selected</h3>
                        <div class="program-details">
                            <div class="program-icon">
                                <i class="fas fa-book"></i>
                            </div>
                            <div class="program-info">
                                <h4><?php echo getProgramName($student['programme_code_1']); ?></h4>
                                <p>Code: <?php echo htmlspecialchars($student['programme_code_1']); ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="important-dates">
                        <h3>Important Dates</h3>
                        <div class="date-list">
                            <div class="date-item">
                                <div class="date-icon">
                                    <i class="fas fa-calendar-check"></i>
                                </div>
                                <div class="date-details">
                                    <h4>Registration Deadline</h4>
                                    <p>September 30, 2024</p>
                                </div>
                            </div>
                            <div class="date-item">
                                <div class="date-icon">
                                    <i class="fas fa-calendar-day"></i>
                                </div>
                                <div class="date-details">
                                    <h4>Semester Start</h4>
                                    <p>October 15, 2024</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Profile Content -->
                <div id="profile-content" style="display: none;">
                    <h1>Profile</h1>
                    
                    <div class="profile-container">
                        <div class="profile-header">
                            <?php if (!empty($student['photo_path'])): ?>
                                <img src="../<?php echo htmlspecialchars($student['photo_path']); ?>" alt="Student Photo" class="profile-image">
                            <?php else: ?>
                                <div class="profile-image-placeholder">
                                    <i class="fas fa-user"></i>
                                </div>
                            <?php endif; ?>
                            <div class="profile-title">
                                <h2><?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></h2>
                                <p>Student ID: <?php echo htmlspecialchars($student['id']); ?></p>
                            </div>
                        </div>

                        <div class="profile-section">
                            <h3>Personal Details</h3>
                            <div class="profile-info">
                                <div class="info-row">
                                    <div class="info-label">Passport No:</div>
                                    <div class="info-value"><?php echo htmlspecialchars($student['passport_no']); ?></div>
                                </div>
                                <div class="info-row">
                                    <div class="info-label">Nationality:</div>
                                    <div class="info-value"><?php echo htmlspecialchars($student['nationality']); ?></div>
                                </div>
                                <div class="info-row">
                                    <div class="info-label">Date of Birth:</div>
                                    <div class="info-value"><?php echo date('d M Y', strtotime($student['date_of_birth'])); ?></div>
                                </div>
                                <div class="info-row">
                                    <div class="info-label">Gender:</div>
                                    <div class="info-value"><?php echo ucfirst(htmlspecialchars($student['gender'])); ?></div>
                                </div>
                                <div class="info-row">
                                    <div class="info-label">Email:</div>
                                    <div class="info-value"><?php echo htmlspecialchars($student['email']); ?></div>
                                </div>
                                <div class="info-row">
                                    <div class="info-label">Contact No:</div>
                                    <div class="info-value"><?php echo htmlspecialchars($student['contact_no']); ?></div>
                                </div>
                                <div class="info-row">
                                    <div class="info-label">Address:</div>
                                    <div class="info-value"><?php echo htmlspecialchars($student['home_address']); ?></div>
                                </div>
                                <div class="info-row">
                                    <div class="info-label">City:</div>
                                    <div class="info-value"><?php echo htmlspecialchars($student['city']); ?></div>
                                </div>
                                <div class="info-row">
                                    <div class="info-label">State:</div>
                                    <div class="info-value"><?php echo htmlspecialchars($student['state']); ?></div>
                                </div>
                                <div class="info-row">
                                    <div class="info-label">Country:</div>
                                    <div class="info-value"><?php echo htmlspecialchars($student['country']); ?></div>
                                </div>
                            </div>
                        </div>

                        <div class="profile-section">
                            <h3>Guardian Information</h3>
                            <div class="profile-info">
                                <div class="info-row">
                                    <div class="info-label">Name:</div>
                                    <div class="info-value"><?php echo htmlspecialchars($student['guardian_name']); ?></div>
                                </div>
                                <div class="info-row">
                                    <div class="info-label">Passport No:</div>
                                    <div class="info-value"><?php echo htmlspecialchars($student['guardian_passport']); ?></div>
                                </div>
                                <div class="info-row">
                                    <div class="info-label">Nationality:</div>
                                    <div class="info-value"><?php echo htmlspecialchars($student['guardian_nationality']); ?></div>
                                </div>
                                <div class="info-row">
                                    <div class="info-label">Address:</div>
                                    <div class="info-value"><?php echo htmlspecialchars($student['guardian_address']); ?></div>
                                </div>
                            </div>
                        </div>

                        <div class="profile-section">
                            <h3>Program Information</h3>
                            <div class="profile-info">
                                <div class="info-row">
                                    <div class="info-label">Program:</div>
                                    <div class="info-value"><?php echo getProgramName($student['programme_code_1']); ?></div>
                                </div>
                                <div class="info-row">
                                    <div class="info-label">Program Code:</div>
                                    <div class="info-value"><?php echo htmlspecialchars($student['programme_code_1']); ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Document Content -->
                <div id="document-content" style="display: none;">
                    <h1>Documents</h1>
                    
                    <div class="documents-container">
                        <div class="document-card">
                            <div class="document-icon">
                                <i class="fas fa-id-card"></i>
                            </div>
                            <div class="document-title">Passport Copy</div>
                            <div class="document-status">
                                <?php if (!empty($student['passport_copy_path'])): ?>
                                    <span class="status-uploaded">Uploaded</span>
                                    <a href="../<?php echo htmlspecialchars($student['passport_copy_path']); ?>" target="_blank" class="document-link">View</a>
                                <?php else: ?>
                                    <span class="status-missing">Missing</span>
                                    <a href="#" class="document-upload-btn">Upload</a>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="document-card">
                            <div class="document-icon">
                                <i class="fas fa-graduation-cap"></i>
                            </div>
                            <div class="document-title">Academic Certificates</div>
                            <div class="document-status">
                                <?php if (!empty($student['academic_certificates_path'])): ?>
                                    <span class="status-uploaded">Uploaded</span>
                                    <a href="../<?php echo htmlspecialchars($student['academic_certificates_path']); ?>" target="_blank" class="document-link">View</a>
                                <?php else: ?>
                                    <span class="status-missing">Missing</span>
                                    <a href="#" class="document-upload-btn">Upload</a>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="document-card">
                            <div class="document-icon">
                                <i class="fas fa-heart"></i>
                            </div>
                            <div class="document-title">Health Declaration</div>
                            <div class="document-status">
                                <?php if (!empty($student['health_declaration_path'])): ?>
                                    <span class="status-uploaded">Uploaded</span>
                                    <a href="../<?php echo htmlspecialchars($student['health_declaration_path']); ?>" target="_blank" class="document-link">View</a>
                                <?php else: ?>
                                    <span class="status-missing">Missing</span>
                                    <a href="#" class="document-upload-btn">Upload</a>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="document-card">
                            <div class="document-icon">
                                <i class="fas fa-file-alt"></i>
                            </div>
                            <div class="document-title">Offer Letter</div>
                            <div class="document-status">
                                <?php if ($application_status === 'approved'): ?>
                                    <span class="status-uploaded">Available</span>
                                    <a href="generate-offer-letter.php" target="_blank" class="document-link">Download</a>
                                <?php else: ?>
                                    <span class="status-pending">Pending Approval</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="upload-form" style="display: none;">
                        <h3>Upload Document</h3>
                        <form action="upload-document.php" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="document_type" id="document_type" value="">
                            <div class="form-group">
                                <label for="document_file">Select File</label>
                                <input type="file" id="document_file" name="document_file" accept=".pdf,.jpg,.jpeg,.png" required>
                                <small>Only PDF, JPG, JPEG, and PNG files are allowed (Max 5MB)</small>
                            </div>
                            <div class="form-buttons">
                                <button type="button" class="cancel-upload-btn">Cancel</button>
                                <button type="submit" class="submit-upload-btn">Upload</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Payment Content -->
                <div id="payment-content" style="display: none;">
                    <h1>Payment</h1>
                    
                    <?php if ($application_status === 'approved'): ?>
                        <div class="payment-container">
                            <div class="payment-details">
                                <h3>Payment Details</h3>
                                <div class="fee-summary">
                                    <div class="fee-item">
                                        <div class="fee-label">Application Fee</div>
                                        <div class="fee-amount">MYR 100.00</div>
                                    </div>
                                    <div class="fee-item">
                                        <div class="fee-label">Registration Fee</div>
                                        <div class="fee-amount">MYR 500.00</div>
                                    </div>
                                    <div class="fee-item">
                                        <div class="fee-label">Tuition Fee (First Semester)</div>
                                        <div class="fee-amount">MYR 5,000.00</div>
                                    </div>
                                    <div class="fee-item total">
                                        <div class="fee-label">Total Amount</div>
                                        <div class="fee-amount">MYR 5,600.00</div>
                                    </div>
                                </div>

                                <div class="payment-instructions">
                                    <h4>Bank Transfer Details</h4>
                                    <div class="bank-details">
                                        <div class="bank-info">
                                            <div class="bank-label">Account Name:</div>
                                            <div class="bank-value">UPTM University</div>
                                        </div>
                                        <div class="bank-info">
                                            <div class="bank-label">Account Number:</div>
                                            <div class="bank-value">1234-5678-9012</div>
                                        </div>
                                        <div class="bank-info">
                                            <div class="bank-label">Bank Name:</div>
                                            <div class="bank-value">Maybank Berhad</div>
                                        </div>
                                        <div class="bank-info">
                                            <div class="bank-label">Swift Code:</div>
                                            <div class="bank-value">MBBEMYKL</div>
                                        </div>
                                        <div class="bank-info">
                                            <div class="bank-label">Reference:</div>
                                            <div class="bank-value">UPTM<?php echo str_pad($student_id, 6, '0', STR_PAD_LEFT); ?></div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="payment-confirmation">
                                    <h4>Payment Confirmation</h4>
                                    <form action="upload-payment-proof.php" method="POST" enctype="multipart/form-data">
                                        <div class="form-group">
                                            <label for="payment_date">Payment Date</label>
                                            <input type="date" id="payment_date" name="payment_date" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="payment_reference">Payment Reference Number</label>
                                            <input type="text" id="payment_reference" name="payment_reference" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="payment_amount">Amount Paid (MYR)</label>
                                            <input type="number" id="payment_amount" name="payment_amount" step="0.01" value="5600.00" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="payment_proof">Payment Proof (Receipt/Screenshot)</label>
                                            <input type="file" id="payment_proof" name="payment_proof" accept=".pdf,.jpg,.jpeg,.png" required>
                                            <small>Only PDF, JPG, JPEG, and PNG files are allowed (Max 5MB)</small>
                                        </div>
                                        <button type="submit" class="payment-confirm-btn">Submit Payment Confirmation</button>
                                    </form>
                                </div>
                            </div>
                            
                            <div class="payment-history">
                                <h3>Payment History</h3>
                                <?php if (empty($payments ?? [])): ?>
                                    <p class="no-payments">No payment records found</p>
                                <?php else: ?>
                                    <table class="payment-table">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Reference</th>
                                                <th>Amount</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($payments as $payment): ?>
                                                <tr>
                                                    <td><?php echo date('d M Y', strtotime($payment['payment_date'])); ?></td>
                                                    <td><?php echo htmlspecialchars($payment['reference']); ?></td>
                                                    <td>MYR <?php echo number_format($payment['amount'], 2); ?></td>
                                                    <td><span class="status-<?php echo $payment['status']; ?>"><?php echo ucfirst($payment['status']); ?></span></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="payment-pending-notice">
                            <div class="notice-icon">
                                <i class="fas fa-hourglass-half"></i>
                            </div>
                            <div class="notice-text">
                                <h3>Payment Not Available Yet</h3>
                                <p>Your application is still being processed. Payment options will be available once your application is approved.</p>
                                <p>Current Status: <span class="status-badge <?php echo $application_status; ?>"><?php echo ucfirst($application_status); ?></span></p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Footer -->
            <footer class="dashboard-footer">
                <div class="footer-content">
                    <div class="footer-text">
                        Copyright <?php echo date('Y'); ?> © UPTM International Student Registration System
                    </div>
                </div>
            </footer>
        </main>
    </div>

    <script src="../assets/js/student-dashboard.js"></script>
    <script src="../assets/js/student-notification.js"></script>
</body>
</html>

<?php
// Helper function to get program name from program code
function getProgramName($code) {
    $programNames = [
        'BAC' => 'Bachelor of Accountancy (Honours)',
        'BBAHRM' => 'Bachelor of Business Administration (Honours) in Human Resource Management',
        'BBA' => 'Bachelor of Business Administration (Honours)',
        'BCC' => 'Bachelor of Communication (Honours) in Corporate Communication',
        'BBAH' => 'Bachelor of Business Administration (Hybrid)',
        'BAAELS' => 'Bachelor of Arts (Honours) in Applied English Language Studies',
        'BECE' => 'Bachelor of Early Childhood Education (Honours)',
        'BEDTESL' => 'Bachelor of Education (Honours) in Teaching English as a Second Language (TESL)',
        'BCA' => 'Bachelor of Corporate Administration (Honours)',
        'BA3D' => 'Bachelor of Arts in 3D Animation and Digital Media (Honours)',
        'BITBC' => 'Bachelor of Information Technology (Honours) in Business Computing',
        'BITCAD' => 'Bachelor of Information Technology (Honours) in Computer Application Development',
        'BITCS' => 'Bachelor of Information Technology (Honours) in Cyber Security',
        'MSIS' => 'Master of Science in Information Systems',
        'MBA' => 'Master of Business Administration (in collaboration with CMI)',
        'MBACAG' => 'MBA (Corporate Administration and Governance) (in collaboration with MAICSA)',
        'MAcc' => 'Master of Accountancy (in collaboration with CIMA)',
        'PhDBA' => 'Doctor of Philosophy in Business Administration',
        'PhDIT' => 'Doctor of Philosophy in Information Technology',
        'PhDEd' => 'Doctor of Philosophy in Education'
    ];
    
    return isset($programNames[$code]) ? $programNames[$code] : $code;
}
?>