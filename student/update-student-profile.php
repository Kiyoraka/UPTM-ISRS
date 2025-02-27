<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

// Make sure we're running in a session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id']) && $_SESSION['role'] === 'student') {
    try {
        $student_id = $_SESSION['user_id'];
        $section = sanitize($_POST['section']);
        
        switch($section) {
            case 'personal':
                $sql = "UPDATE students SET 
                    first_name = ?,
                    last_name = ?,
                    passport_no = ?,
                    nationality = ?,
                    date_of_birth = ?,
                    gender = ?
                    WHERE id = ?";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "ssssssi",
                    sanitize($_POST['first_name']),
                    sanitize($_POST['last_name']),
                    sanitize($_POST['passport_no']),
                    sanitize($_POST['nationality']),
                    sanitize($_POST['date_of_birth']),
                    sanitize($_POST['gender']),
                    $student_id
                );
                break;
                
            case 'contact':
                $sql = "UPDATE students SET 
                    home_address = ?,
                    city = ?,
                    state = ?,
                    postcode = ?,
                    country = ?,
                    contact_no = ?,
                    email = ?
                    WHERE id = ?";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "sssssssi",
                    sanitize($_POST['home_address']),
                    sanitize($_POST['city']),
                    sanitize($_POST['state']),
                    sanitize($_POST['postcode']),
                    sanitize($_POST['country']),
                    sanitize($_POST['contact_no']),
                    sanitize($_POST['email']),
                    $student_id
                );
                break;
                
            case 'guardian':
                $sql = "UPDATE students SET 
                    guardian_name = ?,
                    guardian_passport = ?,
                    guardian_address = ?,
                    guardian_city = ?,
                    guardian_state = ?,
                    guardian_postcode = ?,
                    guardian_country = ?
                    WHERE id = ?";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "sssssssi",
                    sanitize($_POST['guardian_name']),
                    sanitize($_POST['guardian_passport']),
                    sanitize($_POST['guardian_address']),
                    sanitize($_POST['guardian_city']),
                    sanitize($_POST['guardian_state']),
                    sanitize($_POST['guardian_postcode']),
                    sanitize($_POST['guardian_country']),
                    $student_id
                );
                break;
                
            default:
                throw new Exception("Invalid section");
        }
        
        if (mysqli_stmt_execute($stmt)) {
            // Handle photo upload if present
            if ($section === 'personal' && isset($_FILES['passport_photo']) && $_FILES['passport_photo']['error'] === UPLOAD_ERR_OK) {
                $photo = $_FILES['passport_photo'];
                $photo_info = handleUpload($photo, 'student_photos');
                
                if ($photo_info['success']) {
                    $photo_sql = "UPDATE students SET photo_path = ? WHERE id = ?";
                    $photo_stmt = mysqli_prepare($conn, $photo_sql);
                    $photo_path = 'uploads/student_photos/' . $photo_info['filename'];
                    mysqli_stmt_bind_param($photo_stmt, "si", $photo_path, $student_id);
                    mysqli_stmt_execute($photo_stmt);
                }
            }
            
            $response = ['success' => true, 'message' => 'Profile updated successfully'];
        } else {
            $error_message = mysqli_error($conn);
            $response = ['success' => false, 'message' => 'Failed to update profile: ' . $error_message];
            error_log("Failed to update student profile: " . $error_message);
        }
    } catch (Exception $e) {
        $error_message = $e->getMessage();
        $response = ['success' => false, 'message' => 'Error: ' . $error_message];
        error_log("Error updating student profile: " . $error_message);
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