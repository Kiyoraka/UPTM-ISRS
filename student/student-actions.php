<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

// Make sure we're running in a session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
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
            case 'change_password':
                changePassword();
                break;
            case 'upload_document':
                uploadDocument();
                break;
            case 'upload_receipt':
                uploadReceipt();
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
}

// Function to handle password change
function changePassword() {
    global $conn;
    
    $student_id = $_SESSION['user_id'];
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
        $sql = "SELECT password FROM student_login WHERE student_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $student_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($row = mysqli_fetch_assoc($result)) {
            if (password_verify($current_password, $row['password'])) {
                // Hash new password
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                
                // Update password
                $update_sql = "UPDATE student_login SET password = ? WHERE student_id = ?";
                $update_stmt = mysqli_prepare($conn, $update_sql);
                mysqli_stmt_bind_param($update_stmt, "si", $hashed_password, $student_id);
                
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

// Function to handle document upload
function uploadDocument() {
    global $conn;
    
    $student_id = $_SESSION['user_id'];
    $document_type = sanitize($_POST['document_type']);
    
    // Check if file was uploaded
    if (!isset($_FILES['document_file']) || $_FILES['document_file']['error'] !== UPLOAD_ERR_OK) {
        $response = [
            'success' => false,
            'message' => 'No file uploaded or upload error'
        ];
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    }
    
    // Handle file upload
    $file_info = handleUpload($_FILES['document_file'], 'student_documents');
    
    if (!$file_info['success']) {
        $response = [
            'success' => false,
            'message' => $file_info['message']
        ];
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    }
    
    // Determine which field to update based on document type
    $column_name = '';
    switch ($document_type) {
        case 'academic_certificates':
            $column_name = 'academic_certificates_path';
            break;
        case 'passport_copy':
            $column_name = 'passport_copy_path';
            break;
        case 'health_declaration':
            $column_name = 'health_declaration_path';
            break;
        default:
            $response = [
                'success' => false,
                'message' => 'Invalid document type'
            ];
            header('Content-Type: application/json');
            echo json_encode($response);
            exit();
    }
    
    // Update the database
    $file_path = 'uploads/student_documents/' . $file_info['filename'];
    $sql = "UPDATE students SET $column_name = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "si", $file_path, $student_id);
    
    if (mysqli_stmt_execute($stmt)) {
        $response = [
            'success' => true,
            'message' => 'Document uploaded successfully'
        ];
    } else {
        $response = [
            'success' => false,
            'message' => 'Error updating database'
        ];
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}

// Function to handle payment receipt upload
function uploadReceipt() {
    global $conn;
    
    $student_id = $_SESSION['user_id'];
    $payment_date = sanitize($_POST['payment_date']);
    $payment_amount = floatval($_POST['payment_amount']);
    $payment_reference = sanitize($_POST['payment_reference']);
    
    // Check if file was uploaded
    if (!isset($_FILES['payment_receipt']) || $_FILES['payment_receipt']['error'] !== UPLOAD_ERR_OK) {
        $response = [
            'success' => false,
            'message' => 'No file uploaded or upload error'
        ];
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    }
    
    // Handle file upload
    $file_info = handleUpload($_FILES['payment_receipt'], 'payment_receipts');
    
    if (!$file_info['success']) {
        $response = [
            'success' => false,
            'message' => $file_info['message']
        ];
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    }
    
    // Update the database
    $file_path = 'uploads/payment_receipts/' . $file_info['filename'];
    $sql = "UPDATE students SET 
                payment_receipt_path = ?, 
                payment_date = ?, 
                payment_amount = ?, 
                payment_reference = ?,
                payment_status = 'pending'
            WHERE id = ?";
    
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssdsi", $file_path, $payment_date, $payment_amount, $payment_reference, $student_id);
    
    if (mysqli_stmt_execute($stmt)) {
        $response = [
            'success' => true,
            'message' => 'Payment receipt uploaded successfully'
        ];
    } else {
        $response = [
            'success' => false,
            'message' => 'Error updating database'
        ];
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}
?>