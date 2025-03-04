<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    
    // Basic validation
    if (empty($email) || empty($password)) {
        $error = "All fields are required";
    } else {
        // Enhanced logging
        error_log("Login attempt for email: $email");

        // Join student_login with students table to get student information
        // Removed s.status since that column doesn't exist
        $sql = "SELECT sl.*, s.first_name, s.last_name
                FROM student_login sl 
                JOIN students s ON sl.student_id = s.id 
                WHERE sl.email = ?";
        
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($row = mysqli_fetch_assoc($result)) {
            // Log full user details for debugging (using sl.status instead of s.status)
            error_log("User found - Status: " . $row['status']);

            // Verify password
            if (password_verify($password, $row['password'])) {
                // More flexible status check
                if (in_array($row['status'], ['active', 'pending', 'approved'])) {
                    // Store basic login information
                    $_SESSION['user_id'] = $row['student_id'];
                    $_SESSION['login_id'] = $row['id'];
                    $_SESSION['role'] = 'student';
                    $_SESSION['username'] = $row['first_name'] . ' ' . $row['last_name'];
                    
                    // Redirect to dashboard
                    header('Location: student-dashboard.php');
                    exit();
                } else {
                    $error = "Your account status is: " . $row['status'] . ". Please contact support.";
                    error_log("Login failed - Unexpected account status: " . $row['status']);
                }
            } else {
                $error = "Invalid credentials";
            }
        } else {
            $error = "Invalid credentials";
            error_log("No user found with email: $email");
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Login - UPTM ISRS</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/login-style.css">
</head>
<body>
    <div class="auth-wrapper">
        <div class="login-box">
            <div class="login-header">
                <a href="../index.html" class="logo-link">
                    <img src="../assets/img/uptm-logo.png" alt="UPTM Logo" class="logo">
                </a>
                <h1>STUDENT</h1>
            </div>
            
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="login-form">
                <?php if ($error): ?>
                    <div class="error-message"><?php echo $error; ?></div>
                <?php endif; ?>

                <div class="form-group">
                    <input type="email" 
                           name="email" 
                           placeholder="Email" 
                           class="form-input"
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                           required>
                </div>

                <div class="form-group">
                    <input type="password" 
                           name="password" 
                           placeholder="Password" 
                           class="form-input"
                           required>
                </div>

                <div class="form-options">
                    <label class="remember-me">
                        <input type="checkbox" name="remember">
                        <span>Remember Me?</span>
                    </label>
                    <a href="#" class="forgot-password">Forgot Password?</a>
                </div>

                <div class="button-container">
                    <button type="submit" class="btn login-btn">LOGIN</button>
                    <a href="student-register.php" class="btn back-btn">REGISTER</a>
                </div>
            </form>
        </div>
    </div>
    <script src="../assets/js/main.js"></script>
</body>
</html>