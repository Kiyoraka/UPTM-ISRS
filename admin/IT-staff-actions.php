<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

// Make sure we're running in a session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'it') {
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
        changeStaffPassword();
    } else {
        $response = [
            'success' => false,
            'message' => 'Invalid action'
        ];
        header('Content-Type: application/json');
        echo json_encode($response);
    }
}

// Function to handle password change for staff
function changeStaffPassword() {
    global $conn;
    
    $staff_id = isset($_POST['staff_id']) ? intval($_POST['staff_id']) : 0;
    $new_password = $_POST['new_staff_password'];
    $confirm_password = $_POST['confirm_staff_password'];

    // Validate input
    if (empty($staff_id) || empty($new_password) || empty($confirm_password)) {
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
        // Check if staff exists
        $check_sql = "SELECT id FROM staff WHERE id = ?";
        $check_stmt = mysqli_prepare($conn, $check_sql);
        mysqli_stmt_bind_param($check_stmt, "i", $staff_id);
        mysqli_stmt_execute($check_stmt);
        $check_result = mysqli_stmt_get_result($check_stmt);
        
        if (mysqli_num_rows($check_result) === 0) {
            $response = [
                'success' => false,
                'message' => 'Staff not found'
            ];
        } else {
            // Hash new password
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            
            // Update password
            $update_sql = "UPDATE staff SET password = ? WHERE id = ?";
            $update_stmt = mysqli_prepare($conn, $update_sql);
            mysqli_stmt_bind_param($update_stmt, "si", $hashed_password, $staff_id);
            
            if (mysqli_stmt_execute($update_stmt)) {
                $response = [
                    'success' => true,
                    'message' => 'Staff password changed successfully'
                ];
            } else {
                $response = [
                    'success' => false,
                    'message' => 'Error updating password: ' . mysqli_error($conn)
                ];
            }
        }
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}
?>