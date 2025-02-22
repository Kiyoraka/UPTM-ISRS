<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    
    // Basic validation
    if (empty($email) || empty($password)) {
        $error = "All fields are required";
    } else {
        $sql = "SELECT * FROM agent_login WHERE email = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($row = mysqli_fetch_assoc($result)) {
            // Verify password
            if (password_verify($password, $row['password'])) {
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['role'] = 'agent';
                $_SESSION['username'] = $row['name'];
                
                // Redirect to dashboard
                header('Location: agent-dashboard.php');
                exit();
            } else {
                $error = "Invalid credentialsddd";
            }
        } else {
            $error = "Invalid credentialsxxx";
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
                    <a href="../agent/agent-register.php" class="btn back-btn">REGISTER</a>
                </div>
            </form>
        </div>
    </div>
    <script src="../assets/js/main.js"></script>
</body>
</html>