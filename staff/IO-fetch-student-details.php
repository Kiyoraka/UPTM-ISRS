<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in and has IO role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'io') {
    $response = [
        'success' => false,
        'message' => 'Unauthorized access'
    ];
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}

// Check if student ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $response = [
        'success' => false,
        'message' => 'Student ID is required'
    ];
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}

$student_id = intval($_GET['id']);

// Fetch student details
$query = "SELECT * FROM students WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $student_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) === 0) {
    $response = [
        'success' => false,
        'message' => 'Student not found'
    ];
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}

$student = mysqli_fetch_assoc($result);

// Fetch student qualifications
$qual_query = "SELECT * FROM student_qualifications WHERE student_id = ? ORDER BY year_completed DESC";
$qual_stmt = mysqli_prepare($conn, $qual_query);
mysqli_stmt_bind_param($qual_stmt, "i", $student_id);
mysqli_stmt_execute($qual_stmt);
$qual_result = mysqli_stmt_get_result($qual_stmt);
$qualifications = [];

while ($qual = mysqli_fetch_assoc($qual_result)) {
    $qualifications[] = $qual;
}

// Fetch student login status
$login_query = "SELECT status FROM student_login WHERE student_id = ?";
$login_stmt = mysqli_prepare($conn, $login_query);
mysqli_stmt_bind_param($login_stmt, "i", $student_id);
mysqli_stmt_execute($login_stmt);
$login_result = mysqli_stmt_get_result($login_stmt);
$login_status = "inactive";

if ($login_row = mysqli_fetch_assoc($login_result)) {
    $login_status = $login_row['status'];
}

// Get agent information if the student was registered by an agent
$agent_info = null;
if (!empty($student['agent_id'])) {
    $agent_query = "SELECT a.id, a.company_name, a.contact_name, a.contact_email, a.contact_phone 
                   FROM agent a WHERE a.id = ?";
    $agent_stmt = mysqli_prepare($conn, $agent_query);
    mysqli_stmt_bind_param($agent_stmt, "i", $student['agent_id']);
    mysqli_stmt_execute($agent_stmt);
    $agent_result = mysqli_stmt_get_result($agent_stmt);
    
    if ($agent_row = mysqli_fetch_assoc($agent_result)) {
        $agent_info = $agent_row;
    }
}

// Get program details for the selected programs
$program_details = [];
$program_fields = ['programme_code_1', 'programme_code_2', 'programme_code_3', 'programme_code_4', 'programme_code_5'];

foreach ($program_fields as $field) {
    if (!empty($student[$field])) {
        $program_details[$field] = [
            'code' => $student[$field],
            'name' => getProgramName($student[$field])
        ];
    }
}

// Function to get program name from code (you may have this in your database)
function getProgramName($code) {
    // This is a placeholder - ideally you would fetch this from a programs table
    $programs = [
        'BAC' => 'Bachelor of Accountancy (Honours)',
        'BBAHRM' => 'Bachelor of Business Administration (Honours) in Human Resource Management',
        'BBA' => 'Bachelor of Business Administration (Honours)',
        'BCC' => 'Bachelor of Communication (Honours) in Corporate Communication',
        'BBAH' => 'Bachelor of Business Administration (Hybrid)'
        // Add other program codes and names as needed
    ];
    
    return isset($programs[$code]) ? $programs[$code] : 'Unknown Program';
}

// Structure the document data
$documents = [
    'photo' => [
        'type' => 'Passport Photo',
        'path' => $student['photo_path'],
        'status' => !empty($student['photo_path']) ? 'uploaded' : 'missing'
    ],
    'academic_certificates' => [
        'type' => 'Academic Certificates',
        'path' => $student['academic_certificates_path'],
        'status' => !empty($student['academic_certificates_path']) ? 'uploaded' : 'missing'
    ],
    'passport_copy' => [
        'type' => 'Passport Copy',
        'path' => $student['passport_copy_path'],
        'status' => !empty($student['passport_copy_path']) ? 'uploaded' : 'missing'
    ],
    'health_declaration' => [
        'type' => 'Health Declaration',
        'path' => $student['health_declaration_path'],
        'status' => !empty($student['health_declaration_path']) ? 'uploaded' : 'missing'
    ]
];

// Return the response
$response = [
    'success' => true,
    'student' => $student,
    'qualifications' => $qualifications,
    'programs' => $program_details,
    'documents' => $documents,
    'login_status' => $login_status,
    'agent_info' => $agent_info
];

header('Content-Type: application/json');
echo json_encode($response);
exit();
?>