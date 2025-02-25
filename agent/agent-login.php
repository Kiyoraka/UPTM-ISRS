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
        // Join agent_login with agent table to get status information
        $sql = "SELECT al.*, a.status, a.status_reason 
                FROM agent_login al 
                JOIN agent a ON al.agent_id = a.id 
                WHERE al.email = ?";
        
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($row = mysqli_fetch_assoc($result)) {
            // Verify password
            if (password_verify($password, $row['password'])) {
                // Store basic login information
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['agent_id'] = $row['agent_id'];
                $_SESSION['role'] = 'agent';
                $_SESSION['username'] = $row['name'];
                
                // Check agent status and redirect accordingly
                if ($row['status'] === 'approved') {
                    // Approved - go to dashboard
                    header('Location: agent-dashboard.php');
                    exit();
                } elseif ($row['status'] === 'pending') {
                    // Pending - go to waiting page
                    header('Location: agent-pending.php');
                    exit();
                } elseif ($row['status'] === 'rejected') {
                    // Rejected - go to rejected page with reason
                    $_SESSION['rejection_reason'] = $row['status_reason'];
                    header('Location: agent-rejected.php');
                    exit();
                } else {
                    // Unknown status
                    $error = "Your account status is unknown. Please contact support.";
                }
            } else {
                $error = "Invalid credentials";
            }
        } else {
            $error = "Invalid credentials";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agent Login - UPTM ISRS</title>
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
                <h1>AGENT</h1>
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
                    <a href="../index.html" class="btn back-btn">BACK</a>
                </div>
            </form>
        </div>
    </div>
    <script src="../assets/js/main.js"></script>
</body>
</html>