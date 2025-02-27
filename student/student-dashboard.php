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
    <link rel="stylesheet" href="../assets/css/student_dashboard-UserDropDownMenu.css">
    <link rel="stylesheet" href="../assets/css/student_dashboard-changepassword.css">
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
                        <a href="#" class="dropdown-item" id="change-password-link">
                            <i class="fas fa-key"></i> Change Password
                        </a>
                        <a href="student-logout.php" class="dropdown-item">
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
                        <span class="nav-text">Main</span>
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
                        <span class="nav-text">Payments</span>
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

                    <div class="stats-container">
                        <!-- Application Status -->
                        <div class="stat-card">
                            <div class="stat-icon registered">
                                <i class="fas fa-clipboard-check"></i>
                            </div>
                            <div class="stat-number registered">
                                <?php 
                                    $status = isset($student['status']) ? $student['status'] : 'Pending';
                                    echo ucfirst($status);
                                ?>
                            </div>
                            <div class="stat-label">Application Status</div>
                        </div>

                        <!-- Document Status -->
                        <div class="stat-card">
                            <div class="stat-icon approved">
                                <i class="fas fa-file-alt"></i>
                            </div>
                            <div class="stat-number approved">
                                <?php
                                    $document_count = 0;
                                    if(!empty($student['academic_certificates_path'])) $document_count++;
                                    if(!empty($student['passport_copy_path'])) $document_count++;
                                    if(!empty($student['health_declaration_path'])) $document_count++;
                                    echo $document_count . '/3';
                                ?>
                            </div>
                            <div class="stat-label">Documents Uploaded</div>
                        </div>

                        <!-- Payment Status -->
                        <div class="stat-card">
                            <div class="stat-icon payment">
                                <i class="fas fa-money-bill-wave"></i>
                            </div>
                            <div class="stat-number payment">
                                <?php 
                                    echo isset($student['payment_status']) ? ucfirst($student['payment_status']) : 'Pending';
                                ?>
                            </div>
                            <div class="stat-label">Payment Status</div>
                        </div>
                    </div>
                </div>

                <!-- Profile Content -->
                <div id="profile-content" style="display: none;">
                    <h1>My Profile</h1>
                    
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
                                <span class="step-text">Guardian Information</span>
                            </div>
                        </div>

                        <form id="studentProfileForm" method="POST">
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
                                            <img id="photo-preview" src="<?php echo !empty($student['photo_path']) ? '../' . $student['photo_path'] : '#'; ?>" alt="Photo preview" style="<?php echo !empty($student['photo_path']) ? 'display: block;' : 'display: none;'; ?>">
                                            <div id="upload-placeholder" style="<?php echo !empty($student['photo_path']) ? 'display: none;' : 'display: block;'; ?>">
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
                                        <label for="first_name">First Name</label>
                                        <input type="text" id="first_name" name="first_name" class="form-control" value="<?php echo htmlspecialchars($student['first_name']); ?>" disabled>
                                    </div>
                                    <div class="form-group">
                                        <label for="last_name">Last Name</label>
                                        <input type="text" id="last_name" name="last_name" class="form-control" value="<?php echo htmlspecialchars($student['last_name']); ?>" disabled>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="passport_no">Passport No.</label>
                                        <input type="text" id="passport_no" name="passport_no" class="form-control" value="<?php echo htmlspecialchars($student['passport_no']); ?>" disabled>
                                    </div>
                                    <div class="form-group">
                                        <label for="nationality">Nationality</label>
                                        <input type="text" id="nationality" name="nationality" class="form-control" value="<?php echo htmlspecialchars($student['nationality']); ?>" disabled>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="date_of_birth">Date of Birth</label>
                                        <input type="date" id="date_of_birth" name="date_of_birth" class="form-control" value="<?php echo htmlspecialchars($student['date_of_birth']); ?>" disabled>
                                    </div>
                                    <div class="form-group">
                                        <label for="gender">Gender</label>
                                        <input type="text" id="gender" name="gender" class="form-control" value="<?php echo ucfirst(htmlspecialchars($student['gender'])); ?>" disabled>
                                    </div>
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
                                <div class="form-group">
                                    <label for="home_address">Home Address</label>
                                    <textarea id="home_address" name="home_address" class="form-control" rows="3" disabled><?php echo htmlspecialchars($student['home_address']); ?></textarea>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="city">City</label>
                                        <input type="text" id="city" name="city" class="form-control" value="<?php echo htmlspecialchars($student['city']); ?>" disabled>
                                    </div>
                                    <div class="form-group">
                                        <label for="state">State</label>
                                        <input type="text" id="state" name="state" class="form-control" value="<?php echo htmlspecialchars($student['state']); ?>" disabled>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="postcode">Postcode</label>
                                        <input type="text" id="postcode" name="postcode" class="form-control" value="<?php echo htmlspecialchars($student['postcode']); ?>" disabled>
                                    </div>
                                    <div class="form-group">
                                        <label for="country">Country</label>
                                        <input type="text" id="country" name="country" class="form-control" value="<?php echo htmlspecialchars($student['country']); ?>" disabled>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="contact_no">Contact Phone</label>
                                        <input type="tel" id="contact_no" name="contact_no" class="form-control" value="<?php echo htmlspecialchars($student['contact_no']); ?>" disabled>
                                    </div>
                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($student['email']); ?>" disabled>
                                    </div>
                                </div>
                                <div class="form-navigation">
                                    <button type="button" class="btn-prev">Previous</button>
                                    <button type="button" class="btn-edit">Edit</button>
                                    <button type="button" class="btn-next">Next</button>
                                </div>
                            </div>

                            <!-- Guardian Information Section -->
                            <div class="profile-section" id="section-guardian">
                                <div class="section-header">
                                    <h2>Guardian Information</h2>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="guardian_name">Guardian Name</label>
                                        <input type="text" id="guardian_name" name="guardian_name" class="form-control" value="<?php echo htmlspecialchars($student['guardian_name']); ?>" disabled>
                                    </div>
                                    <div class="form-group">
                                        <label for="guardian_passport">Guardian Passport/ID</label>
                                        <input type="text" id="guardian_passport" name="guardian_passport" class="form-control" value="<?php echo htmlspecialchars($student['guardian_passport']); ?>" disabled>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="guardian_address">Guardian Address</label>
                                    <textarea id="guardian_address" name="guardian_address" class="form-control" rows="3" disabled><?php echo htmlspecialchars($student['guardian_address']); ?></textarea>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="guardian_city">Guardian City</label>
                                        <input type="text" id="guardian_city" name="guardian_city" class="form-control" value="<?php echo htmlspecialchars($student['guardian_city']); ?>" disabled>
                                    </div>
                                    <div class="form-group">
                                        <label for="guardian_state">Guardian State</label>
                                        <input type="text" id="guardian_state" name="guardian_state" class="form-control" value="<?php echo htmlspecialchars($student['guardian_state']); ?>" disabled>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="guardian_postcode">Guardian Postcode</label>
                                        <input type="text" id="guardian_postcode" name="guardian_postcode" class="form-control" value="<?php echo htmlspecialchars($student['guardian_postcode']); ?>" disabled>
                                    </div>
                                    <div class="form-group">
                                        <label for="guardian_country">Guardian Country</label>
                                        <input type="text" id="guardian_country" name="guardian_country" class="form-control" value="<?php echo htmlspecialchars($student['guardian_country']); ?>" disabled>
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

                <!-- Document Content -->
                <div id="document-content" style="display: none;">
                    <h1>My Documents</h1>
                    
                    <div class="documents-container">
                        <div class="document-list">
                            <h2>Uploaded Documents</h2>
                            <div class="document-grid">
                                <!-- Academic Certificates -->
                                <div class="document-card <?php echo !empty($student['academic_certificates_path']) ? 'uploaded' : ''; ?>">
                                    <div class="document-icon">
                                        <i class="fas fa-graduation-cap"></i>
                                    </div>
                                    <div class="document-info">
                                        <h3>Academic Certificates</h3>
                                        <p>
                                            <?php 
                                                if(!empty($student['academic_certificates_path'])) {
                                                    echo "Uploaded on " . date("d M Y", strtotime($student['created_at']));
                                                } else {
                                                    echo "Not uploaded";
                                                }
                                            ?>
                                        </p>
                                    </div>
                                    <div class="document-actions">
                                        <?php if(!empty($student['academic_certificates_path'])): ?>
                                            <a href="../<?php echo $student['academic_certificates_path']; ?>" class="document-btn view" target="_blank">View</a>
                                        <?php else: ?>
                                            <label for="upload-academic" class="document-btn upload">Upload</label>
                                            <input type="file" id="upload-academic" name="academic_certificates" class="upload-input" accept=".pdf,.jpg,.jpeg,.png">
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <!-- Passport Copy -->
                                <div class="document-card <?php echo !empty($student['passport_copy_path']) ? 'uploaded' : ''; ?>">
                                    <div class="document-icon">
                                        <i class="fas fa-passport"></i>
                                    </div>
                                    <div class="document-info">
                                        <h3>Passport Copy</h3>
                                        <p>
                                            <?php 
                                                if(!empty($student['passport_copy_path'])) {
                                                    echo "Uploaded on " . date("d M Y", strtotime($student['created_at']));
                                                } else {
                                                    echo "Not uploaded";
                                                }
                                            ?>
                                        </p>
                                    </div>
                                    <div class="document-actions">
                                        <?php if(!empty($student['passport_copy_path'])): ?>
                                            <a href="../<?php echo $student['passport_copy_path']; ?>" class="document-btn view" target="_blank">View</a>
                                        <?php else: ?>
                                            <label for="upload-passport" class="document-btn upload">Upload</label>
                                            <input type="file" id="upload-passport" name="passport_copy" class="upload-input" accept=".pdf,.jpg,.jpeg,.png">
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <!-- Health Declaration -->
                                <div class="document-card <?php echo !empty($student['health_declaration_path']) ? 'uploaded' : ''; ?>">
                                    <div class="document-icon">
                                        <i class="fas fa-heartbeat"></i>
                                    </div>
                                    <div class="document-info">
                                        <h3>Health Declaration</h3>
                                        <p>
                                            <?php 
                                                if(!empty($student['health_declaration_path'])) {
                                                    echo "Uploaded on " . date("d M Y", strtotime($student['created_at']));
                                                } else {
                                                    echo "Not uploaded";
                                                }
                                            ?>
                                        </p>
                                    </div>
                                    <div class="document-actions">
                                        <?php if(!empty($student['health_declaration_path'])): ?>
                                            <a href="../<?php echo $student['health_declaration_path']; ?>" class="document-btn view" target="_blank">View</a>
                                        <?php else: ?>
                                            <label for="upload-health" class="document-btn upload">Upload</label>
                                            <input type="file" id="upload-health" name="health_declaration" class="upload-input" accept=".pdf,.jpg,.jpeg,.png">
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="document-downloads">
                            <h2>Downloads</h2>
                            <div class="document-grid">
                                <!-- Offer Letter -->
                                <div class="document-card <?php echo isset($student['offer_letter_path']) ? 'available' : 'unavailable'; ?>">
                                    <div class="document-icon">
                                        <i class="fas fa-file-alt"></i>
                                    </div>
                                    <div class="document-info">
                                        <h3>Offer Letter</h3>
                                        <p>
                                            <?php 
                                                if(isset($student['offer_letter_path'])) {
                                                    echo "Available";
                                                } else {
                                                    echo "Not available yet";
                                                }
                                            ?>
                                        </p>
                                    </div>
                                    <div class="document-actions">
                                        <?php if(isset($student['offer_letter_path'])): ?>
                                            <a href="../<?php echo $student['offer_letter_path']; ?>" class="document-btn download" download>Download</a>
                                        <?php else: ?>
                                            <span class="document-btn disabled">Download</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <!-- Student Visa Guide -->
                                <div class="document-card available">
                                    <div class="document-icon">
                                        <i class="fas fa-plane"></i>
                                    </div>
                                    <div class="document-info">
                                        <h3>Visa Application Guide</h3>
                                        <p>Available for all students</p>
                                    </div>
                                    <div class="document-actions">
                                        <a href="../uploads/guides/visa_guide.pdf" class="document-btn download" download>Download</a>
                                    </div>
                                </div>
                                
                                <!-- Student Handbook -->
                                <div class="document-card available">
                                    <div class="document-icon">
                                        <i class="fas fa-book"></i>
                                    </div>
                                    <div class="document-info">
                                        <h3>Student Handbook</h3>
                                        <p>Available for all students</p>
                                    </div>
                                    <div class="document-actions">
                                        <a href="../uploads/guides/student_handbook.pdf" class="document-btn download" download>Download</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Content -->
                <div id="payment-content" style="display: none;">
                    <h1>Payments</h1>
                    
                    <div class="payment-container">
                        <div class="payment-info-card">
                            <div class="payment-header">
                                <h2>Payment Information</h2>
                            </div>
                            <div class="payment-body">
                                <div class="payment-status">
                                    <h3>Current Status</h3>
                                    <div class="status-badge <?php echo (isset($student['payment_status']) && $student['payment_status'] == 'paid') ? 'paid' : 'pending'; ?>">
                                        <?php echo isset($student['payment_status']) ? ucfirst($student['payment_status']) : 'Pending'; ?>
                                    </div>
                                </div>
                                
                                <div class="payment-details">
                                    <div class="detail-row">
                                        <div class="detail-label">Application Fee:</div>
                                        <div class="detail-value">RM 300.00</div>
                                    </div>
                                    <div class="detail-row">
                                        <div class="detail-label">Processing Fee:</div>
                                        <div class="detail-value">RM 100.00</div>
                                    </div>
                                    <div class="detail-row total">
                                        <div class="detail-label">Total Amount:</div>
                                        <div class="detail-value">RM 400.00</div>
                                    </div>
                                </div>
                                
                                <div class="payment-instructions">
                                    <h3>Bank Details</h3>
                                    <p>Please make your payment to the following bank account:</p>
                                    <div class="bank-details">
                                        <div class="detail-row">
                                            <div class="detail-label">Account Name:</div>
                                            <div class="detail-value">Universiti Poly-Tech Malaysia</div>
                                        </div>
                                        <div class="detail-row">
                                            <div class="detail-label">Account Number:</div>
                                            <div class="detail-value">1234-5678-9012</div>
                                        </div>
                                        <div class="detail-row">
                                            <div class="detail-label">Bank Name:</div>
                                            <div class="detail-value">Malaysia Bank Berhad</div>
                                        </div>
                                        <div class="detail-row">
                                            <div class="detail-label">Swift Code:</div>
                                            <div class="detail-value">MBBEMYKL</div>
                                        </div>
                                    </div>
                                    <p class="important-note">Important: Please include your name and passport number as reference when making payment.</p>
                                </div>
                                
                                <div class="payment-action">
                                    <button id="upload-receipt-btn" class="btn-upload-receipt">Upload Payment Receipt</button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="payment-history-card">
                            <div class="payment-header">
                                <h2>Payment History</h2>
                            </div>
                            <div class="payment-body">
                                <?php if(isset($student['payment_receipt_path'])): ?>
                                <div class="payment-item">
                                    <div class="payment-date"><?php echo date("d M Y", strtotime($student['payment_date'])); ?></div>
                                    <div class="payment-detail">
                                        <div class="payment-type">Application & Processing Fee</div>
                                        <div class="payment-amount">RM 400.00</div>
                                    </div>
                                    <div class="payment-status-badge <?php echo $student['payment_status']; ?>">
                                        <?php echo ucfirst($student['payment_status']); ?>
                                    </div>
                                    <div class="payment-actions">
                                        <a href="../<?php echo $student['payment_receipt_path']; ?>" class="view-receipt-btn" target="_blank">
                                            <i class="fas fa-eye"></i> View Receipt
                                        </a>
                                    </div>
                                </div>
                                <?php else: ?>
                                <div class="no-payment-history">
                                    <i class="fas fa-exclamation-circle"></i>
                                    <p>No payment records found.</p>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
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

            <!-- Upload Receipt Modal -->
            <div id="receiptModal" class="modal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2>Upload Payment Receipt</h2>
                        <span class="close-modal">&times;</span>
                    </div>
                    <div class="modal-body">
                        <div id="receipt-message"></div>
                        
                        <form id="uploadReceiptForm" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="payment_date">Payment Date</label>
                                <input type="date" id="payment_date" name="payment_date" class="form-control" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="payment_amount">Payment Amount (RM)</label>
                                <input type="number" id="payment_amount" name="payment_amount" value="400.00" class="form-control" required readonly>
                            </div>
                            
                            <div class="form-group">
                                <label for="payment_receipt">Payment Receipt (PDF, JPG, PNG)</label>
                                <input type="file" id="payment_receipt" name="payment_receipt" class="form-control" required accept=".pdf,.jpg,.jpeg,.png">
                            </div>
                            
                            <div class="form-group">
                                <label for="payment_reference">Payment Reference / Transaction ID</label>
                                <input type="text" id="payment_reference" name="payment_reference" class="form-control" required>
                            </div>
                            
                            <div class="btn-group">
                                <button type="button" class="btn btn-secondary" id="cancelReceiptUpload">Cancel</button>
                                <button type="submit" class="btn btn-primary">Upload Receipt</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Document Upload Modal -->
            <div id="documentModal" class="modal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2>Upload Document</h2>
                        <span class="close-modal">&times;</span>
                    </div>
                    <div class="modal-body">
                        <div id="document-message"></div>
                        
                        <form id="uploadDocumentForm" enctype="multipart/form-data">
                            <input type="hidden" id="document_type" name="document_type">
                            
                            <div class="form-group">
                                <label for="document_file">Select File (PDF, JPG, PNG)</label>
                                <input type="file" id="document_file" name="document_file" class="form-control" required accept=".pdf,.jpg,.jpeg,.png">
                            </div>
                            
                            <div class="upload-instructions">
                                <p><strong>Note:</strong></p>
                                <ul>
                                    <li>Maximum file size: 5MB</li>
                                    <li>Ensure documents are clear and legible</li>
                                    <li>Supported formats: PDF, JPG, PNG</li>
                                </ul>
                            </div>
                            
                            <div class="btn-group">
                                <button type="button" class="btn btn-secondary" id="cancelDocumentUpload">Cancel</button>
                                <button type="submit" class="btn btn-primary">Upload Document</button>
                            </div>
                        </form>
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

    <script src="../assets/js/student-dashboard.js"></script>
    <script src="../assets/js/student_dashboard-ProfileSection.js"></script>
    <script src="../assets/js/student_dashboard-DocumentSection.js"></script>
    <script src="../assets/js/student_dashboard-PaymentSection.js"></script>
    <script src="../assets/js/student_dashboard-UserDropDownMenu.js"></script>
    <script src="../assets/js/student_dashboard-ChangePassword.js"></script>
</body>
</html>