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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Pending - UPTM ISRS</title>
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
                <div class="status-icon pending">⏳</div>
                <h1 class="status-title">Application Pending</h1>
                <p class="status-message">
                    Thank you for registering as an agent with UPTM International Student Registration System. 
                    Your application is currently under review by our administration team.
                </p>
                <p class="status-message">
                    You will receive notification once your application has been approved.
                    This process typically takes 3-5 business days.
                </p>
                <div class="contact-info">
                    For any inquiries, please contact us at:<br>
                    <strong>Email:</strong> international.office@uptm.edu.my<br>
                    <strong>Phone:</strong> +60 3-4145 0123
                </div>
                
                <div class="button-container">
                    <a href="agent-logout.php" class="btn logout-btn">LOGOUT</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>