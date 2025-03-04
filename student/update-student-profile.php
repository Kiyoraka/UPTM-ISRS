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
        
        // Start transaction for operations that affect multiple tables
        if ($section === 'qualifications') {
            mysqli_begin_transaction($conn);
        }
        
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
                $result = mysqli_stmt_execute($stmt);
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
                $result = mysqli_stmt_execute($stmt);
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
                $result = mysqli_stmt_execute($stmt);
                break;
                
            case 'qualifications':
                $qualifications = json_decode($_POST['qualifications'], true);
                $removed_ids = json_decode($_POST['removed_ids'], true);
                $result = true;
                
                // First, handle removing qualifications
                if (!empty($removed_ids)) {
                    $remove_sql = "DELETE FROM student_qualifications WHERE id = ? AND student_id = ?";
                    $remove_stmt = mysqli_prepare($conn, $remove_sql);
                    
                    foreach ($removed_ids as $removed_id) {
                        mysqli_stmt_bind_param($remove_stmt, "ii", $removed_id, $student_id);
                        $remove_result = mysqli_stmt_execute($remove_stmt);
                        if (!$remove_result) {
                            $result = false;
                            break;
                        }
                    }
                }
                
                // Next, update existing and add new qualifications
                foreach ($qualifications as $qual) {
                    if (!empty($qual['id'])) {
                        // Update existing qualification
                        $update_sql = "UPDATE student_qualifications SET 
                            qualification = ?,
                            institution = ?,
                            grade = ?,
                            duration = ?,
                            year_completed = ?
                            WHERE id = ? AND student_id = ?";
                        $update_stmt = mysqli_prepare($conn, $update_sql);
                        mysqli_stmt_bind_param($update_stmt, "sssssii",
                            sanitize($qual['qualification']),
                            sanitize($qual['institution']),
                            sanitize($qual['grade']),
                            sanitize($qual['duration']),
                            sanitize($qual['year_completed']),
                            $qual['id'],
                            $student_id
                        );
                        $update_result = mysqli_stmt_execute($update_stmt);
                        if (!$update_result) {
                            $result = false;
                            break;
                        }
                    } else {
                        // Add new qualification
                        $insert_sql = "INSERT INTO student_qualifications 
                            (student_id, qualification, institution, grade, duration, year_completed) 
                            VALUES (?, ?, ?, ?, ?, ?)";
                        $insert_stmt = mysqli_prepare($conn, $insert_sql);
                        mysqli_stmt_bind_param($insert_stmt, "isssss",
                            $student_id,
                            sanitize($qual['qualification']),
                            sanitize($qual['institution']),
                            sanitize($qual['grade']),
                            sanitize($qual['duration']),
                            sanitize($qual['year_completed'])
                        );
                        $insert_result = mysqli_stmt_execute($insert_stmt);
                        if (!$insert_result) {
                            $result = false;
                            break;
                        }
                    }
                }
                
                // Commit or rollback transaction based on result
                if ($result) {
                    mysqli_commit($conn);
                } else {
                    mysqli_rollback($conn);
                    throw new Exception("Failed to update qualifications: " . mysqli_error($conn));
                }
                break;
                
            default:
                throw new Exception("Invalid section");
        }
        
        // For non-qualification sections
        if ($section !== 'qualifications') {
            if (!isset($result) || !$result) {
                $error_message = mysqli_error($conn);
                throw new Exception("Failed to update profile: " . $error_message);
            }
        }
        
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
    } catch (Exception $e) {
        // Rollback if there was a transaction in progress
        if (mysqli_get_connection_stats($conn)['transaction_active'] ?? false) {
            mysqli_rollback($conn);
        }
        
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