<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Make sure we're running in a session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Log file for debugging
$log_file = '../logs/registration.log';
if (!file_exists('../logs/')) {
    mkdir('../logs/', 0777, recursive: true);
}

function log_debug($message) {
    global $log_file;
    file_put_contents($log_file, date('Y-m-d H:i:s') . " - " . $message . PHP_EOL, FILE_APPEND);
}

// Function to check if the request is an AJAX request
function isAjaxRequest() {
    return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') ||
           (isset($_SERVER['HTTP_ACCEPT']) && 
            strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);
}

// Function to validate required fields
function validateRequiredFields($data, $required_fields) {
    $errors = [];
    
    foreach ($required_fields as $field) {
        if (empty($data[$field])) {
            $errors[] = "The {$field} field is required.";
        }
    }
    
    return $errors;
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        log_debug("Form submitted, starting processing");
        
        // Required fields
        $required_fields = [
            'first_name', 'last_name', 'passport_no', 'nationality', 
            'date_of_birth', 'age', 'place_of_birth', 'home_address', 
            'city', 'postcode', 'state', 'country', 
            'contact_no', 'email', 'gender'
        ];
        
        // Validate required fields
        $validation_errors = validateRequiredFields($_POST, $required_fields);
        if (!empty($validation_errors)) {
            throw new Exception("Validation errors: " . implode(", ", $validation_errors));
        }
        
        log_debug("Basic validation passed");
        
        // Start transaction
        mysqli_begin_transaction($conn);
        
        // Sanitize input data from Section A: Personal Details
        $first_name = sanitize($_POST['first_name']);
        $last_name = sanitize($_POST['last_name']);
        $passport_no = sanitize($_POST['passport_no']);
        $nationality = sanitize($_POST['nationality']);
        $date_of_birth = sanitize($_POST['date_of_birth']);
        $age = intval($_POST['age']);
        $place_of_birth = sanitize($_POST['place_of_birth']);
        $home_address = sanitize($_POST['home_address']);
        $city = sanitize($_POST['city']);
        $postcode = sanitize($_POST['postcode']);
        $state = sanitize($_POST['state']);
        $country = sanitize($_POST['country']);
        $contact_no = sanitize($_POST['contact_no']);
        $email = sanitize($_POST['email']);
        $gender = sanitize($_POST['gender']);

        // Section B: Guardian Information
        $guardian_name = sanitize($_POST['guardian_name']);
        $guardian_passport = sanitize($_POST['guardian_passport'] ?? '');
        $guardian_address = sanitize($_POST['guardian_address']);
        $guardian_nationality = sanitize($_POST['guardian_nationality']);
        $guardian_postcode = sanitize($_POST['guardian_postcode']);
        $guardian_state = sanitize($_POST['guardian_state']);
        $guardian_city = sanitize($_POST['guardian_city']);
        $guardian_country = sanitize($_POST['guardian_country']);
        
        // Section C: English Proficiency
        $muet_year = sanitize($_POST['muet_year'] ?? '');
        $muet_score = sanitize($_POST['muet_score'] ?? '');
        $ielts_year = sanitize($_POST['ielts_year'] ?? '');
        $ielts_score = sanitize($_POST['ielts_score'] ?? '');
        $toefl_year = sanitize($_POST['toefl_year'] ?? '');
        $toefl_score = sanitize($_POST['toefl_score'] ?? '');
        $toiec_year = sanitize($_POST['toiec_year'] ?? '');
        $toiec_score = sanitize($_POST['toiec_score'] ?? '');
        
        // Section D: Programme
        $programme_code_1 = sanitize($_POST['programme_code_1']);
        $programme_code_2 = sanitize($_POST['programme_code_2'] ?? '');
        $programme_code_3 = sanitize($_POST['programme_code_3'] ?? '');
        $programme_code_4 = sanitize($_POST['programme_code_4'] ?? '');
        $programme_code_5 = sanitize($_POST['programme_code_5'] ?? '');
        
        // Section E: Financial Support
        $financial_support = sanitize($_POST['financial_support']);
        $account_no = sanitize($_POST['account_no'] ?? '');
        $bank_name = sanitize($_POST['bank_name'] ?? '');
        $financial_support_others = sanitize($_POST['financial_support_others'] ?? '');
        
        // Section F: Declaration
        $declaration_agreed = isset($_POST['declaration_agree']) ? 1 : 0;
        $signature_date = sanitize($_POST['signature_date']);
        
        // Get agent_id if student is registered by an agent
        $agent_id = isset($_SESSION['agent_id']) ? intval($_SESSION['agent_id']) : null;
        
        // Log data for debugging
        log_debug("Processing user: $first_name $last_name, Email: $email, DOB: $date_of_birth");
        
        // Handle file uploads
        $upload_dir = '../uploads/student_documents/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        // Passport Photo
        $photo_path = null;
        if (isset($_FILES['passport_photo']) && $_FILES['passport_photo']['error'] === UPLOAD_ERR_OK) {
            $photo_info = handleUpload($_FILES['passport_photo'], 'student_photos');
            if ($photo_info['success']) {
                $photo_path = 'uploads/student_photos/' . $photo_info['filename'];
                log_debug("Photo uploaded: $photo_path");
            } else {
                log_debug("Photo upload failed: " . $photo_info['message']);
            }
        } else {
            log_debug("No photo uploaded or error: " . ($_FILES['passport_photo']['error'] ?? 'No file'));
        }
        
        // Academic Certificates
        $academic_certificates_path = null;
        if (isset($_FILES['academic_certificates']) && $_FILES['academic_certificates']['error'] === UPLOAD_ERR_OK) {
            $academic_info = handleUpload($_FILES['academic_certificates'], 'student_documents');
            if ($academic_info['success']) {
                $academic_certificates_path = 'uploads/student_documents/' . $academic_info['filename'];
                log_debug("Academic certificates uploaded: $academic_certificates_path");
            }
        }
        
        // Passport Copy
        $passport_copy_path = null;
        if (isset($_FILES['passport_copy']) && $_FILES['passport_copy']['error'] === UPLOAD_ERR_OK) {
            $passport_info = handleUpload($_FILES['passport_copy'], 'student_documents');
            if ($passport_info['success']) {
                $passport_copy_path = 'uploads/student_documents/' . $passport_info['filename'];
                log_debug("Passport copy uploaded: $passport_copy_path");
            }
        }
        
        // Health Declaration
        $health_declaration_path = null;
        if (isset($_FILES['health_declaration']) && $_FILES['health_declaration']['error'] === UPLOAD_ERR_OK) {
            $health_info = handleUpload($_FILES['health_declaration'], 'student_documents');
            if ($health_info['success']) {
                $health_declaration_path = 'uploads/student_documents/' . $health_info['filename'];
                log_debug("Health declaration uploaded: $health_declaration_path");
            }
        }
        
        // Ensure date is properly formatted
        if (!empty($date_of_birth)) {
            // Parse date to ensure it's in YYYY-MM-DD format
            $parsed_date = date('Y-m-d', strtotime($date_of_birth));
            if ($parsed_date === '1970-01-01' || $parsed_date === false) {
                log_debug("Invalid date: $date_of_birth, using current date as fallback");
                $date_of_birth = date('Y-m-d');
            } else {
                $date_of_birth = $parsed_date;
            }
            log_debug("Formatted DOB: $date_of_birth");
        }
        
        // Insert into students table
        $sql = "INSERT INTO students (
            first_name, last_name, passport_no, nationality, date_of_birth, age, place_of_birth,
            home_address, city, postcode, state, country, contact_no, email, gender, photo_path,
            guardian_name, guardian_passport, guardian_address, guardian_nationality, 
            guardian_postcode, guardian_state, guardian_city, guardian_country,
            muet_year, muet_score, ielts_year, ielts_score, toefl_year, toefl_score, toiec_year, toiec_score,
            programme_code_1, programme_code_2, programme_code_3, programme_code_4, programme_code_5,
            financial_support, account_no, bank_name, financial_support_others,
            academic_certificates_path, passport_copy_path, health_declaration_path,
            declaration_agreed, signature_date, agent_id, status, created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        
        $status = 'pending'; // Default status for new applications
        
        $stmt = mysqli_prepare($conn, $sql);
        if (!$stmt) {
            throw new Exception("Prepare statement failed: " . mysqli_error($conn));
        }
        
        mysqli_stmt_bind_param($stmt, 
            "sssssisssssssssssssssssssssssssssssssssssisiss",
            $first_name, $last_name, $passport_no, $nationality, $date_of_birth, $age, $place_of_birth,
            $home_address, $city, $postcode, $state, $country, $contact_no, $email, $gender, $photo_path,
            $guardian_name, $guardian_passport, $guardian_address, $guardian_nationality, 
            $guardian_postcode, $guardian_state, $guardian_city, $guardian_country,
            $muet_year, $muet_score, $ielts_year, $ielts_score, $toefl_year, $toefl_score, $toiec_year, $toiec_score,
            $programme_code_1, $programme_code_2, $programme_code_3, $programme_code_4, $programme_code_5,
            $financial_support, $account_no, $bank_name, $financial_support_others,
            $academic_certificates_path, $passport_copy_path, $health_declaration_path,
            $declaration_agreed, $signature_date, $agent_id, $status
        );
        
        log_debug("Prepared student insert statement");
        
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Error inserting student data: " . mysqli_stmt_error($stmt));
        }
        
        $student_id = mysqli_insert_id($conn);
        log_debug("Inserted student with ID: $student_id");
        
        // Insert qualifications
        if (isset($_POST['qualification']) && is_array($_POST['qualification'])) {
            $qualifications = $_POST['qualification'];
            $institutions = $_POST['institution'];
            $grades = $_POST['grade'];
            $durations = $_POST['duration'];
            $year_completed = $_POST['year_completed'];
            
            $qual_sql = "INSERT INTO student_qualifications (student_id, qualification, institution, grade, duration, year_completed) VALUES (?, ?, ?, ?, ?, ?)";
            $qual_stmt = mysqli_prepare($conn, $qual_sql);
            if (!$qual_stmt) {
                throw new Exception("Prepare qualification statement failed: " . mysqli_error($conn));
            }
            
            $qualifications_added = 0;
            for ($i = 0; $i < count($qualifications); $i++) {
                // Only insert if qualification is not empty
                if (!empty($qualifications[$i])) {
                    mysqli_stmt_bind_param($qual_stmt, "isssss", 
                        $student_id,
                        $qualifications[$i],
                        $institutions[$i],
                        $grades[$i],
                        $durations[$i],
                        $year_completed[$i]
                    );
                    
                    if (!mysqli_stmt_execute($qual_stmt)) {
                        throw new Exception("Error inserting qualification data: " . mysqli_stmt_error($qual_stmt));
                    }
                    
                    $qualifications_added++;
                    log_debug("Added qualification: {$qualifications[$i]}");
                }
            }
            
            log_debug("Added $qualifications_added qualifications");
        } else {
            log_debug("No qualifications to add");
        }
        
        // Create student login credentials
        // By default, use passport number as the initial password
        $hashed_password = password_hash($passport_no, PASSWORD_DEFAULT);
        
        $login_sql = "INSERT INTO student_login (student_id, email, password, status, created_at) VALUES (?, ?, ?, 'active', NOW())";
        $login_stmt = mysqli_prepare($conn, $login_sql);
        if (!$login_stmt) {
            throw new Exception("Prepare login statement failed: " . mysqli_error($conn));
        }
        
        mysqli_stmt_bind_param($login_stmt, "iss", $student_id, $email, $hashed_password);
        
        if (!mysqli_stmt_execute($login_stmt)) {
            throw new Exception("Error creating login credentials: " . mysqli_stmt_error($login_stmt));
        }
        
        log_debug("Created login credentials for student");
        
        // Commit transaction
        mysqli_commit($conn);
        log_debug("Transaction committed successfully");
        
        // Response based on request type
        if (isAjaxRequest()) {
            // Return JSON for AJAX requests
            $response = [
                'success' => true,
                'message' => 'Student registration submitted successfully',
                'student_id' => $student_id,
                'redirect_url' => isset($_SESSION['role']) && $_SESSION['role'] === 'agent' 
                                ? 'agent-dashboard.php?registration=success' 
                                : 'success-page.php'
            ];
            
            header('Content-Type: application/json');
            echo json_encode($response);
            exit();
        } else {
            // For regular form submissions, use redirect
            if (isset($_SESSION['role']) && $_SESSION['role'] === 'agent') {
                header('Location: agent-dashboard.php?registration=success');
            } else {
                header('Location: success-page.php');
            }
            exit();
        }
        
    } catch (Exception $e) {
        // Rollback transaction on error
        if (isset($conn) && $conn->connect_errno === 0) {
            mysqli_rollback($conn);
        }
        
        log_debug("ERROR: " . $e->getMessage());
        
        $response = [
            'success' => false,
            'message' => 'Registration failed: ' . $e->getMessage()
        ];
        
        // Log the error
        error_log("Student registration error: " . $e->getMessage());
        
        // Response based on request type
        if (isAjaxRequest()) {
            // Return JSON for AJAX requests
            header('Content-Type: application/json');
            echo json_encode($response);
        } else {
            // For regular form submissions, redirect to error page
            header('Location: error-page.php');
        }
        exit();
    }
} else {
    // If not a POST request, redirect to the form
    header('Location: student-register.php');
    exit();
}