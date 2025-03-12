<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

// Ensure clean JSON response
header('Content-Type: application/json');

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in with IO role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'io') {
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized access'
    ]);
    exit();
}

// Handle different actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'update_io_status':
                updateStudentIOStatus();
                break;
            default:
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid action'
                ]);
                break;
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'No action specified'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
}

// Function to update student IO status
function updateStudentIOStatus() {
    global $conn;
    
    // Validate input
    $student_id = isset($_POST['student_id']) ? intval($_POST['student_id']) : 0;
    $io_status = isset($_POST['io_status']) ? sanitize($_POST['io_status']) : '';
    $io_status_reason = isset($_POST['io_status_reason']) ? sanitize($_POST['io_status_reason']) : '';
    
    // Validate student ID and status
    if (empty($student_id)) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid student ID'
        ]);
        exit();
    }
    
    // Validate status
    $valid_statuses = ['pending', 'approved', 'rejected'];
    if (empty($io_status) || !in_array($io_status, $valid_statuses)) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid IO status'
        ]);
        exit();
    }
    
    try {
        // Start transaction
        mysqli_begin_transaction($conn);
        
        // Check if IO status columns exist
        $columns_to_check = ['io_status', 'io_status_reason'];
        foreach ($columns_to_check as $column) {
            $check_column_sql = "SHOW COLUMNS FROM students LIKE '$column'";
            $check_result = mysqli_query($conn, $check_column_sql);
            
            if (mysqli_num_rows($check_result) == 0) {
                // Add column if not exists
                $add_column_sql = $column === 'io_status' 
                    ? "ALTER TABLE students ADD COLUMN io_status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending'"
                    : "ALTER TABLE students ADD COLUMN io_status_reason TEXT NULL";
                mysqli_query($conn, $add_column_sql);
            }
        }
        
        // Update student IO status
        $update_sql = "UPDATE students 
                       SET io_status = ?, 
                           io_status_reason = ?, 
                           updated_at = NOW() 
                       WHERE id = ?";
        $update_stmt = mysqli_prepare($conn, $update_sql);
        mysqli_stmt_bind_param($update_stmt, "ssi", 
            $io_status, 
            $io_status_reason, 
            $student_id
        );
        
        if (!mysqli_stmt_execute($update_stmt)) {
            throw new Exception("Failed to update student IO status");
        }
        
        // Update login status for approved students
        if ($io_status === 'approved') {
            $login_update_sql = "UPDATE student_login 
                                  SET status = 'active', 
                                      access_level = 'limited' 
                                  WHERE student_id = ?";
            $login_stmt = mysqli_prepare($conn, $login_update_sql);
            mysqli_stmt_bind_param($login_stmt, "i", $student_id);
            
            if (!mysqli_stmt_execute($login_stmt)) {
                throw new Exception("Failed to update student login status");
            }
        }
        
        // Commit transaction
        mysqli_commit($conn);
        
        echo json_encode([
            'success' => true,
            'message' => "Student application " . ucfirst($io_status) . " successfully"
        ]);
        
    } catch (Exception $e) {
        // Rollback transaction
        mysqli_rollback($conn);
        
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
    
    exit();
}
?>