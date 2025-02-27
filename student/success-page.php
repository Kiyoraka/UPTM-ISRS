<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Success - UPTM ISRS</title>
    <link rel="stylesheet" href="../assets/css/thanks-and-error-page.css">
    <style>
        /* Additional styles for student success page */
        .student-info {
            background-color: #e0f2fe;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
            text-align: left;
        }
        
        .student-info p {
            margin: 10px 0;
            color: #0c4a6e;
        }
        
        .student-info strong {
            color: #0369a1;
        }
        
        .login-instructions {
            margin-top: 20px;
            padding: 15px;
            background-color: #ecfdf5;
            border-radius: 8px;
            border-left: 4px solid #10b981;
        }
        
        .login-instructions h2 {
            color: #047857;
            margin-top: 0;
            font-size: 1.2rem;
        }
        
        .login-instructions ul {
            text-align: left;
            margin: 10px 0;
            padding-left: 20px;
        }
        
        .login-instructions li {
            margin-bottom: 8px;
            color: #065f46;
        }
        
        .login-btn {
            display: inline-block;
            background-color: #1a73e8;
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 4px;
            margin-top: 10px;
            transition: background-color 0.3s;
        }
        
        .login-btn:hover {
            background-color: #1557b0;
        }
    </style>
</head>
<body>
    <div class="success-container">
        <div class="success-box">
            <a href="../index.html" class="logo-link">
                <img src="../assets/img/uptm-logo.png" alt="UPTM Logo" class="logo">
            </a>
            <div class="success-message">
                <h1>Thank You for Applying!</h1>
                <p>Your student application has been submitted successfully.</p>
                <p>We will review your application and contact you soon via email.</p>
                
                <div class="student-info">
                    <p><strong>Important:</strong> Please note your login credentials for the student portal:</p>
                    <p><strong>Username:</strong> Your email address</p>
                    <p><strong>Password:</strong> Your passport number</p>
                </div>
                
                <div class="login-instructions">
                    <h2>Next Steps:</h2>
                    <ul>
                        <li>Your application status will be available in your student portal</li>
                        <li>You will receive an email notification when your application is reviewed</li>
                        <li>Keep your login credentials safe</li>
                        <li>For security reasons, please change your password after your first login</li>
                    </ul>
                    <a href="student-login.php" class="login-btn">Go to Student Login</a>
                </div>
                
                <div class="redirect-message">
                    Redirecting to homepage in <span id="countdown">10</span> seconds...
                </div>
            </div>
        </div>
    </div>

    <script>
        // Countdown and redirect
        let seconds = 10;
        const countdownElement = document.getElementById('countdown');
        
        const countdown = setInterval(() => {
            seconds--;
            countdownElement.textContent = seconds;
            
            if (seconds <= 0) {
                clearInterval(countdown);
                window.location.href = '../index.html';
            }
        }, 1000);
    </script>
</body>
</html>