<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

// Make sure we're running in a session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
    try {
        $agent_login_id = $_SESSION['user_id'];
        
        // First, get the agent_id from the agent_login table
        $query = "SELECT agent_id FROM agent_login WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $agent_login_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($row = mysqli_fetch_assoc($result)) {
            $agent_id = $row['agent_id'];
            $section = sanitize($_POST['section']);
            
            switch($section) {
                case 'personal':
                    $sql = "UPDATE agent SET 
                        company_name = ?,
                        registration_no = ?,
                        address = ?
                        WHERE id = ?";
                    $stmt = mysqli_prepare($conn, $sql);
                    mysqli_stmt_bind_param($stmt, "sssi",
                        sanitize($_POST['company_name']),
                        sanitize($_POST['registration_no']),
                        sanitize($_POST['address']),
                        $agent_id
                    );
                    break;
                    
                case 'contact':
                    $sql = "UPDATE agent SET 
                        contact_phone = ?,
                        contact_email = ?
                        WHERE id = ?";
                    $stmt = mysqli_prepare($conn, $sql);
                    mysqli_stmt_bind_param($stmt, "ssi",
                        sanitize($_POST['contact_phone']),
                        sanitize($_POST['contact_email']),
                        $agent_id
                    );
                    break;
                    
                case 'bank':
                    $sql = "UPDATE agent SET 
                        account_name = ?,
                        account_no = ?,
                        bank_name = ?,
                        bank_branch = ?
                        WHERE id = ?";
                    $stmt = mysqli_prepare($conn, $sql);
                    mysqli_stmt_bind_param($stmt, "ssssi",
                        sanitize($_POST['account_name']),
                        sanitize($_POST['account_no']),
                        sanitize($_POST['bank_name']),
                        sanitize($_POST['bank_branch']),
                        $agent_id
                    );
                    break;
            }
            
            if (mysqli_stmt_execute($stmt)) {
                // Handle photo upload if present
                if ($section === 'personal' && isset($_FILES['passport_photo']) && $_FILES['passport_photo']['error'] === UPLOAD_ERR_OK) {
                    $photo = $_FILES['passport_photo'];
                    $photo_info = handleUpload($photo, 'agent_photos');
                    
                    if ($photo_info['success']) {
                        $photo_sql = "UPDATE agent SET photo_path = ? WHERE id = ?";
                        $photo_stmt = mysqli_prepare($conn, $photo_sql);
                        $photo_path = 'uploads/agent_photos/' . $photo_info['filename'];
                        mysqli_stmt_bind_param($photo_stmt, "si", $photo_path, $agent_id);
                        mysqli_stmt_execute($photo_stmt);
                    }
                }
                
                $response = ['success' => true, 'message' => 'Profile updated successfully'];
            } else {
                $error_message = mysqli_error($conn);
                $response = ['success' => false, 'message' => 'Failed to update profile: ' . $error_message];
                error_log("Failed to update profile: " . $error_message);
            }
        } else {
            $response = ['success' => false, 'message' => 'Agent not found'];
            error_log("Agent not found for login ID: " . $agent_login_id);
        }
    } catch (Exception $e) {
        $error_message = $e->getMessage();
        $response = ['success' => false, 'message' => 'Error: ' . $error_message];
        error_log("Error updating profile: " . $error_message);
    }
    
    // Make sure no output happened before this point
    if (ob_get_length()) ob_clean();
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
} else {
    $response = ['success' => false, 'message' => 'Invalid request'];
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}
?>