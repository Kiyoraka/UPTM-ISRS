<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Start transaction
        mysqli_begin_transaction($conn);
        
        // Sanitize input data
        $application_type = sanitize($_POST['application_type']);
        $company_name = sanitize($_POST['company_name']);
        $registration_no = sanitize($_POST['registration_no']);
        $address = sanitize($_POST['address']);
        $postal_code = sanitize($_POST['postal_code']);
        $country = sanitize($_POST['country']);
        
        $contact_name = sanitize($_POST['contact_name']);
        $contact_designation = sanitize($_POST['contact_designation']);
        $contact_phone = sanitize($_POST['contact_phone']);
        $contact_fax = sanitize($_POST['contact_fax'] ?? '');
        $contact_mobile = sanitize($_POST['contact_mobile']);
        $contact_email = sanitize($_POST['contact_email']);
        $website = sanitize($_POST['website'] ?? '');
        
        $countries_covered = sanitize($_POST['countries_covered']);
        $recruitment_experience = sanitize($_POST['recruitment_experience']);
        
        $account_name = sanitize($_POST['account_name'] ?? '');
        $account_no = sanitize($_POST['account_no'] ?? '');
        $bank_name = sanitize($_POST['bank_name'] ?? '');
        $bank_branch = sanitize($_POST['bank_branch'] ?? '');
        $swift_code = sanitize($_POST['swift_code'] ?? '');
        $bank_address = sanitize($_POST['bank_address'] ?? '');
        
        $signee_full_name = sanitize($_POST['signee_full_name']);
        $signee_designation = sanitize($_POST['signee_designation']);
        $signee_nric = sanitize($_POST['signee_nric']);
        $witness_full_name = sanitize($_POST['witness_full_name']);
        $witness_designation = sanitize($_POST['witness_designation']);
        $witness_nric = sanitize($_POST['witness_nric']);
        $signature_date = sanitize($_POST['signature_date']);

        // Insert main agent data
        $sql = "INSERT INTO agent (
            application_type, company_name, registration_no, address, postal_code, country,
            contact_name, contact_designation, contact_phone, contact_fax, contact_mobile,
            contact_email, website, countries_covered, recruitment_experience,
            account_name, account_no, bank_name, bank_branch, swift_code, bank_address,
            signee_full_name, signee_designation, signee_nric,
            witness_full_name, witness_designation, witness_nric, signature_date
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssssssssssssssssssssssssssss",
            $application_type, $company_name, $registration_no, $address, $postal_code, $country,
            $contact_name, $contact_designation, $contact_phone, $contact_fax, $contact_mobile,
            $contact_email, $website, $countries_covered, $recruitment_experience,
            $account_name, $account_no, $bank_name, $bank_branch, $swift_code, $bank_address,
            $signee_full_name, $signee_designation, $signee_nric,
            $witness_full_name, $witness_designation, $witness_nric, $signature_date
        );
        
        mysqli_stmt_execute($stmt);
        $agent_id = mysqli_insert_id($conn);

        //---------------------DEBUG MODE---------------------
        // Insert into agent_login table
        $hashed_password = password_hash($registration_no, PASSWORD_DEFAULT); // Hash the registration number
        $login_sql = "INSERT INTO agent_login (agent_id, name, email, password) VALUES (?, ?, ?, ?)";
        $login_stmt = mysqli_prepare($conn, $login_sql);
        mysqli_stmt_bind_param($login_stmt, "isss",
            $agent_id,
            $company_name,
            $contact_email,
            $hashed_password
        );
        mysqli_stmt_execute($login_stmt);
        //---------------------DEBUG MODE---------------------

        // Insert experience details if any
        if ($recruitment_experience === 'yes' && isset($_POST['institution'])) {
            $institutions = $_POST['institution'];
            $from_dates = $_POST['from'];
            $until_dates = $_POST['until'];
            $recruited_numbers = $_POST['recruited'];

            $exp_sql = "INSERT INTO agent_experiences (agent_id, institution, date_from, date_until, students_recruited) VALUES (?, ?, ?, ?, ?)";
            $exp_stmt = mysqli_prepare($conn, $exp_sql);

            for ($i = 0; $i < count($institutions); $i++) {
                if (!empty($institutions[$i])) {
                    mysqli_stmt_bind_param($exp_stmt, "isssi",
                        $agent_id,
                        $institutions[$i],
                        $from_dates[$i],
                        $until_dates[$i],
                        $recruited_numbers[$i]
                    );
                    mysqli_stmt_execute($exp_stmt);
                }
            }
        }

        // Handle file uploads
        $upload_dir = '../uploads/agent_documents/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $agent_type = sanitize($_POST['agent_type']);
        $document_types = ($agent_type === 'corporate') 
            ? ['company_profile', 'forms_24', 'forms_32A', 'forms_44', 'forms_49']
            : ['curriculum_vitae', 'passport_copy'];

        foreach ($document_types as $doc_type) {
            if (isset($_FILES[$doc_type]) && $_FILES[$doc_type]['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES[$doc_type];
                $file_info = handleUpload($file, 'agent_documents');
                
                if ($file_info['success']) {
                    $doc_sql = "INSERT INTO agent_documents (agent_id, document_type, file_name, file_path) VALUES (?, ?, ?, ?)";
                    $doc_stmt = mysqli_prepare($conn, $doc_sql);
                    $file_path = 'uploads/agent_documents/' . $file_info['filename'];
                    
                    mysqli_stmt_bind_param($doc_stmt, "isss",
                        $agent_id,
                        $doc_type,
                        $file_info['filename'],
                        $file_path
                    );
                    mysqli_stmt_execute($doc_stmt);
                }
            }
        }

        // Handle passport photo upload
        if (isset($_FILES['passport_photo']) && $_FILES['passport_photo']['error'] === UPLOAD_ERR_OK) {
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

        // Commit transaction
        mysqli_commit($conn);
        
        // Return success response
        $response = [
            'success' => true,
            'message' => 'Registration submitted successfully',
            'agent_id' => $agent_id
        ];
        echo json_encode($response);
        
    } catch (Exception $e) {
        // Rollback transaction on error
        mysqli_rollback($conn);
        
        $response = [
            'success' => false,
            'message' => 'Registration failed: ' . $e->getMessage()
        ];
        echo json_encode($response);
    }
} else {
    // Invalid request method
    $response = [
        'success' => false,
        'message' => 'Invalid request method'
    ];
    echo json_encode($response);
}