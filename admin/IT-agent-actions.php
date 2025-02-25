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
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'update_status':
                updateAgentStatus();
                break;
            case 'change_password':
                changePassword();
                break;
            default:
                $response = [
                    'success' => false,
                    'message' => 'Invalid action'
                ];
                header('Content-Type: application/json');
                echo json_encode($response);
                break;
        }
    } else {
        $response = [
            'success' => false,
            'message' => 'No action specified'
        ];
        header('Content-Type: application/json');
        echo json_encode($response);
    }
} else {
    // Invalid request method
    $response = [
        'success' => false,
        'message' => 'Invalid request method'
    ];
    header('Content-Type: application/json');
    echo json_encode($response);
}

// Function to update agent status
function updateAgentStatus() {
    global $conn;
    
    $agent_id = isset($_POST['agent_id']) ? intval($_POST['agent_id']) : 0;
    $status = isset($_POST['status']) ? sanitize($_POST['status']) : '';
    $reason = isset($_POST['reason']) ? sanitize($_POST['reason']) : '';
    
    // Validate input
    if (empty($agent_id) || empty($status)) {
        $response = [
            'success' => false,
            'message' => 'Agent ID and status are required'
        ];
    } elseif (!in_array($status, ['approved', 'rejected', 'pending'])) {
        $response = [
            'success' => false,
            'message' => 'Invalid status value'
        ];
    } else {
        // First, check if we need to alter the table to add a status_reason column
        $check_column_sql = "SHOW COLUMNS FROM agent LIKE 'status_reason'";
        $check_result = mysqli_query($conn, $check_column_sql);
        
        if (mysqli_num_rows($check_result) == 0) {
            // Column doesn't exist, we can add it
            $add_column_sql = "ALTER TABLE agent ADD COLUMN status_reason TEXT NULL AFTER status";
            mysqli_query($conn, $add_column_sql);
        }
        
        // Update agent status
        $sql = "UPDATE agent SET status = ?, status_reason = ?, updated_at = NOW() WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssi", $status, $reason, $agent_id);
        
        if (mysqli_stmt_execute($stmt)) {
            // If agent was approved, also update agent_login status if that table exists
            try {
                if ($status === 'approved') {
                    $login_check_sql = "SHOW TABLES LIKE 'agent_login'";
                    $login_check_result = mysqli_query($conn, $login_check_sql);
                    
                    if (mysqli_num_rows($login_check_result) > 0) {
                        $login_sql = "UPDATE agent_login SET status = 'active' WHERE agent_id = ?";
                        $login_stmt = mysqli_prepare($conn, $login_sql);
                        mysqli_stmt_bind_param($login_stmt, "i", $agent_id);
                        mysqli_stmt_execute($login_stmt);
                    }
                }
            } catch (Exception $e) {
                // Silently handle this exception - it's not critical if this fails
            }
            
            $response = [
                'success' => true,
                'message' => "Agent status updated to " . ucfirst($status) . " successfully"
            ];
        } else {
            $response = [
                'success' => false,
                'message' => 'Error updating agent status: ' . mysqli_error($conn)
            ];
        }
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}

// Function to handle password change - reusing the existing code
function changePassword() {
    global $conn;
    
    $admin_id = $_SESSION['user_id'];
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
        $sql = "SELECT password FROM staff WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $admin_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($row = mysqli_fetch_assoc($result)) {
            if (password_verify($current_password, $row['password'])) {
                // Hash new password
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                
                // Update password
                $update_sql = "UPDATE staff SET password = ? WHERE id = ?";
                $update_stmt = mysqli_prepare($conn, $update_sql);
                mysqli_stmt_bind_param($update_stmt, "si", $hashed_password, $admin_id);
                
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