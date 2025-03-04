<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

// Make sure we're running in a session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in with IO role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'io') {
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
                updateStudentStatus();
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

// Function to update student status
function updateStudentStatus() {
    global $conn;
    
    $student_id = isset($_POST['student_id']) ? intval($_POST['student_id']) : 0;
    $status = isset($_POST['status']) ? sanitize($_POST['status']) : '';
    $reason = isset($_POST['reason']) ? sanitize($_POST['reason']) : '';
    
    // Validate input
    if (empty($student_id) || empty($status)) {
        $response = [
            'success' => false,
            'message' => 'Student ID and status are required'
        ];
    } elseif (!in_array($status, ['approved', 'rejected', 'pending'])) {
        $response = [
            'success' => false,
            'message' => 'Invalid status value'
        ];
    } else {
        // First, check if we need to alter the table to add a status_reason column
        $check_column_sql = "SHOW COLUMNS FROM students LIKE 'status_reason'";
        $check_result = mysqli_query($conn, $check_column_sql);
        
        if (mysqli_num_rows($check_result) == 0) {
            // Column doesn't exist, we can add it
            $add_column_sql = "ALTER TABLE students ADD COLUMN status_reason TEXT NULL AFTER status";
            mysqli_query($conn, $add_column_sql);
        }
        
        try {
            // Start transaction
            mysqli_begin_transaction($conn);
            
            // Update student status
            $sql = "UPDATE students SET status = ?, status_reason = ?, updated_at = NOW() WHERE id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "ssi", $status, $reason, $student_id);
            
            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception("Error updating student status: " . mysqli_stmt_error($stmt));
            }
            
            // Update student login status
            $login_status = ($status === 'approved') ? 'active' : 'inactive';
            $login_sql = "UPDATE student_login SET status = ? WHERE student_id = ?";
            $login_stmt = mysqli_prepare($conn, $login_sql);
            mysqli_stmt_bind_param($login_stmt, "si", $login_status, $student_id);
            
            if (!mysqli_stmt_execute($login_stmt)) {
                throw new Exception("Error updating student login status: " . mysqli_stmt_error($login_stmt));
            }
            
            // If status is 'rejected', we may want to send an email notification
            if ($status === 'rejected' && !empty($reason)) {
                // Email notification code would go here
                // This is optional and can be implemented later
            }
            
            // Commit the transaction
            mysqli_commit($conn);
            
            $response = [
                'success' => true,
                'message' => "Student application " . ucfirst($status) . " successfully"
            ];
            
        } catch (Exception $e) {
            // Rollback the transaction on error
            mysqli_rollback($conn);
            
            $response = [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}
?>