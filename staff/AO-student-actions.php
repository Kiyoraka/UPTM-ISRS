<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

// Make sure we're running in a session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in with AO role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'ao') {
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
            case 'update_ao_status':
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
        // First, check if student is already IO approved
        $check_io_sql = "SELECT io_status FROM students WHERE id = ?";
        $check_stmt = mysqli_prepare($conn, $check_io_sql);
        mysqli_stmt_bind_param($check_stmt, "i", $student_id);
        mysqli_stmt_execute($check_stmt);
        $check_result = mysqli_stmt_get_result($check_stmt);
        $student = mysqli_fetch_assoc($check_result);
        
        if ($student['io_status'] !== 'approved') {
            $response = [
                'success' => false,
                'message' => 'Student must be approved by International Office first'
            ];
        } else {
            try {
                // Start transaction
                mysqli_begin_transaction($conn);
                
                // Check if AO status column exists
                $check_column_sql = "SHOW COLUMNS FROM students LIKE 'ao_status_reason'";
                $check_result = mysqli_query($conn, $check_column_sql);
                
                if (mysqli_num_rows($check_result) == 0) {
                    // Add column if not exists
                    $add_column_sql = "ALTER TABLE students ADD COLUMN ao_status_reason TEXT NULL AFTER ao_status";
                    mysqli_query($conn, $add_column_sql);
                }
                
                // Update student status
                $sql = "UPDATE students SET ao_status = ?, ao_status_reason = ?, updated_at = NOW() WHERE id = ?";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "ssi", $status, $reason, $student_id);
                
                if (!mysqli_stmt_execute($stmt)) {
                    throw new Exception("Error updating student status: " . mysqli_stmt_error($stmt));
                }
                
                // Update student login status if application is approved
                if ($status === 'approved') {
                    $login_status = 'active';
                    $login_sql = "UPDATE student_login SET status = ? WHERE student_id = ?";
                    $login_stmt = mysqli_prepare($conn, $login_sql);
                    mysqli_stmt_bind_param($login_stmt, "si", $login_status, $student_id);
                    
                    if (!mysqli_stmt_execute($login_stmt)) {
                        throw new Exception("Error updating student login status: " . mysqli_stmt_error($login_stmt));
                    }
                }
                
                // Commit the transaction
                mysqli_commit($conn);
                
                $response = [
                    'success' => true,
                    'message' => "Student application " . ucfirst($status) . " by Academic Office successfully"
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
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}
?>