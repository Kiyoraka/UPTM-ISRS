<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Error - UPTM ISRS</title>
    <link rel="stylesheet" href="../assets/css/thanks-and-error-page.css">
    <style>
        /* Additional styles for error page */
        .error-box {
            border-left: 5px solid #ef4444;
        }
        
        .error-icon {
            font-size: 4rem;
            color: #ef4444;
            margin-bottom: 1rem;
        }
        
        .troubleshooting {
            background-color: #fff1f2;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
            text-align: left;
        }
        
        .troubleshooting h2 {
            color: #be123c;
            margin-top: 0;
            font-size: 1.2rem;
        }
        
        .troubleshooting ul {
            text-align: left;
            margin: 10px 0;
            padding-left: 20px;
        }
        
        .troubleshooting li {
            margin-bottom: 8px;
            color: #881337;
        }
        
        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 20px;
        }
        
        .btn {
            display: inline-block;
            padding: 10px 20px;
            border-radius: 4px;
            text-decoration: none;
            font-weight: 500;
            transition: background-color 0.3s;
        }
        
        .try-again-btn {
            background-color: #1a73e8;
            color: white;
        }
        
        .try-again-btn:hover {
            background-color: #1557b0;
        }
        
        .support-btn {
            background-color: #9ca3af;
            color: white;
        }
        
        .support-btn:hover {
            background-color: #6b7280;
        }
    </style>
</head>
<body>
    <div class="success-container">
        <div class="success-box error-box">
            <a href="../index.html" class="logo-link">
                <img src="../assets/img/uptm-logo.png" alt="UPTM Logo" class="logo">
            </a>
            <div class="success-message">
                <div class="error-icon">
                    <i class="fas fa-exclamation-circle"></i>
                    ⚠️
                </div>
                <h1>Registration Error</h1>
                <p>We apologize, but there was an error processing your student application.</p>
                
                <div class="troubleshooting">
                    <h2>Possible Issues:</h2>
                    <ul>
                        <li>Some required fields might be missing or invalid</li>
                        <li>The email address might already be registered</li>
                        <li>There might be issues with the documents you uploaded</li>
                        <li>The system might be experiencing technical difficulties</li>
                    </ul>
                </div>
                
                <div class="action-buttons">
                    <a href="student-register.php" class="btn try-again-btn">Try Again</a>
                    <a href="mailto:support@uptm.edu.my" class="btn support-btn">Contact Support</a>
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