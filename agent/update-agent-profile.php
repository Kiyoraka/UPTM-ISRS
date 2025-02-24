<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
    try {
        $agent_id = $_SESSION['user_id'];
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
            if ($section === 'personal' && isset($_FILES['passport_photo'])) {
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
            $response = ['success' => false, 'message' => 'Failed to update profile'];
        }
        
    } catch (Exception $e) {
        $response = ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    }
    
    echo json_encode($response);
}
?>