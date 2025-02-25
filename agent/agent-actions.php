<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

// Make sure we're running in a session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'agent') {
    $response = [
        'success' => false,
        'message' => 'Unauthorized access'
    ];
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}

// Handle different actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action']) && $_POST['action'] == 'change_password') {
        changePassword();
    } else {
        $response = [
            'success' => false,
            'message' => 'Invalid action'
        ];
        header('Content-Type: application/json');
        echo json_encode($response);
    }
}

// Function to handle password change
function changePassword() {
    global $conn;
    
    $agent_id = $_SESSION['user_id'];
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate input
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $response = [
            'success' => false,
            'message' => 'All fields are required'
        ];
    } elseif ($new_password !== $confirm_password) {
        $response = [
            'success' => false,
            'message' => 'New passwords do not match'
        ];
    } elseif (strlen($new_password) < 8) {
        $response = [
            'success' => false,
            'message' => 'New password must be at least 8 characters long'
        ];
    } else {
        // Verify current password
        $sql = "SELECT password FROM agent_login WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $agent_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($row = mysqli_fetch_assoc($result)) {
            if (password_verify($current_password, $row['password'])) {
                // Hash new password
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                
                // Update password
                $update_sql = "UPDATE agent_login SET password = ? WHERE id = ?";
                $update_stmt = mysqli_prepare($conn, $update_sql);
                mysqli_stmt_bind_param($update_stmt, "si", $hashed_password, $agent_id);
                
                if (mysqli_stmt_execute($update_stmt)) {
                    $response = [
                        'success' => true,
                        'message' => 'Password changed successfully'
                    ];
                } else {
                    $response = [
                        'success' => false,
                        'message' => 'Error updating password'
                    ];
                }
            } else {
                $response = [
                    'success' => false,
                    'message' => 'Current password is incorrect'
                ];
            }
        } else {
            $response = [
                'success' => false,
                'message' => 'User not found'
            ];
        }
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}
?>