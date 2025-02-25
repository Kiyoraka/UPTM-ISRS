<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'agent') {
    header('Location: agent-login.php');
    exit();
}

// Get rejection reason if available
$rejection_reason = isset($_SESSION['rejection_reason']) ? $_SESSION['rejection_reason'] : 'No specific reason provided.';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Rejected - UPTM ISRS</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/login-style.css">
    <link rel="stylesheet" href="../assets/css/agent-status-pages.css">
</head>
<body>
    <div class="auth-wrapper">
        <div class="login-box" style="max-width: 600px;">
            <div class="login-header">
                <a href="../index.html" class="logo-link">
                    <img src="../assets/img/uptm-logo.png" alt="UPTM Logo" class="logo">
                </a>
            </div>
            
            <div class="status-container">
                <div class="status-icon pending">❌</div>
                <h1 class="status-title">Application Rejected</h1>
                <p class="status-message">
                    We regret to inform you that your application to become an agent for UPTM International 
                    Student Registration System has been declined.
                </p>
                
                <div class="reason-box">
                    <div class="reason-label">Reason:</div>
                    <div class="reason-text"><?php echo htmlspecialchars($rejection_reason); ?></div>
                </div>
                
                <p class="status-message">
                    If you believe this decision was made in error or would like to submit a revised application,
                    please contact our international office.
                </p>
                
                <div class="contact-info">
                    For any inquiries, please contact us at:<br>
                    <strong>Email:</strong> international.office@uptm.edu.my<br>
                    <strong>Phone:</strong> +60 3-4145 0123
                </div>
                
                <div class="button-container">
                    <a href="mailto:international.office@uptm.edu.my?subject=Agent Application Appeal" class="btn appeal-btn">CONTACT US</a>
                    <a href="agent-logout.php" class="btn logout-btn">LOGOUT</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>