<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

// Make sure we're running in a session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
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
            }
        }
        
        // Academic Certificates
        $academic_certificates_path = null;
        if (isset($_FILES['academic_certificates']) && $_FILES['academic_certificates']['error'] === UPLOAD_ERR_OK) {
            $academic_info = handleUpload($_FILES['academic_certificates'], 'student_documents');
            if ($academic_info['success']) {
                $academic_certificates_path = 'uploads/student_documents/' . $academic_info['filename'];
            }
        }
        
        // Passport Copy
        $passport_copy_path = null;
        if (isset($_FILES['passport_copy']) && $_FILES['passport_copy']['error'] === UPLOAD_ERR_OK) {
            $passport_info = handleUpload($_FILES['passport_copy'], 'student_documents');
            if ($passport_info['success']) {
                $passport_copy_path = 'uploads/student_documents/' . $passport_info['filename'];
            }
        }
        
        // Health Declaration
        $health_declaration_path = null;
        if (isset($_FILES['health_declaration']) && $_FILES['health_declaration']['error'] === UPLOAD_ERR_OK) {
            $health_info = handleUpload($_FILES['health_declaration'], 'student_documents');
            if ($health_info['success']) {
                $health_declaration_path = 'uploads/student_documents/' . $health_info['filename'];
            }
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
            declaration_agreed, signature_date, agent_id
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 
            "sssssissssssssssssssssssssssssssssssssssssi",
            $first_name, $last_name, $passport_no, $nationality, $date_of_birth, $age, $place_of_birth,
            $home_address, $city, $postcode, $state, $country, $contact_no, $email, $gender, $photo_path,
            $guardian_name, $guardian_passport, $guardian_address, $guardian_nationality, 
            $guardian_postcode, $guardian_state, $guardian_city, $guardian_country,
            $muet_year, $muet_score, $ielts_year, $ielts_score, $toefl_year, $toefl_score, $toiec_year, $toiec_score,
            $programme_code_1, $programme_code_2, $programme_code_3, $programme_code_4, $programme_code_5,
            $financial_support, $account_no, $bank_name, $financial_support_others,
            $academic_certificates_path, $passport_copy_path, $health_declaration_path,
            $declaration_agreed, $signature_date, $agent_id
        );
        
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Error inserting student data: " . mysqli_stmt_error($stmt));
        }
        
        $student_id = mysqli_insert_id($conn);
        
        // Insert qualifications
        if (isset($_POST['qualification']) && is_array($_POST['qualification'])) {
            $qualifications = $_POST['qualification'];
            $institutions = $_POST['institution'];
            $grades = $_POST['grade'];
            $durations = $_POST['duration'];
            $year_completed = $_POST['year_completed'];
            
            $qual_sql = "INSERT INTO student_qualifications (student_id, qualification, institution, grade, duration, year_completed) VALUES (?, ?, ?, ?, ?, ?)";
            $qual_stmt = mysqli_prepare($conn, $qual_sql);
            
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
                }
            }
        }
        
        // Commit transaction
        mysqli_commit($conn);
        
        // Return success response
        $response = [
            'success' => true,
            'message' => 'Student registration submitted successfully',
            'student_id' => $student_id
        ];
        
        // Redirect based on type of user
        if (isset($_SESSION['role']) && $_SESSION['role'] === 'agent') {
            header('Location: agent-dashboard.php?registration=success');
        } else {
            header('Location: success-page.php');
        }
        exit();
        
    } catch (Exception $e) {
        // Rollback transaction on error
        mysqli_rollback($conn);
        
        $response = [
            'success' => false,
            'message' => 'Registration failed: ' . $e->getMessage()
        ];
        
        // Log the error
        error_log("Student registration error: " . $e->getMessage());
        
        // Redirect to error page
        header('Location: error-page.php');
        exit();
    }
} else {
    // If not a POST request, redirect to the form
    header('Location: student-register.php');
    exit();
}