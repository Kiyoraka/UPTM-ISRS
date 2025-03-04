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

// Fetch student details with agent information
$query = "SELECT s.*, a.company_name as agent_company, a.contact_name as agent_contact
          FROM students s
          LEFT JOIN agent a ON s.agent_id = a.id
          WHERE s.id = ?";
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

// Fetch student documents
$doc_query = "SELECT * FROM student_documents WHERE student_id = ?";
$doc_stmt = mysqli_prepare($conn, $doc_query);
mysqli_stmt_bind_param($doc_stmt, "i", $student_id);
mysqli_stmt_execute($doc_stmt);
$doc_result = mysqli_stmt_get_result($doc_stmt);
$documents = [];

while ($doc = mysqli_fetch_assoc($doc_result)) {
    $documents[] = $doc;
}

// Fetch student education qualifications
$edu_query = "SELECT * FROM student_qualifications WHERE student_id = ?";
$edu_stmt = mysqli_prepare($conn, $edu_query);
mysqli_stmt_bind_param($edu_stmt, "i", $student_id);
mysqli_stmt_execute($edu_stmt);
$edu_result = mysqli_stmt_get_result($edu_stmt);
$qualifications = [];

while ($qual = mysqli_fetch_assoc($edu_result)) {
    $qualifications[] = $qual;
}

// Fetch student program choices
$prog_query = "SELECT p.*, pc.choice_number 
              FROM student_program_choices pc
              JOIN programs p ON pc.program_code = p.code
              WHERE pc.student_id = ?
              ORDER BY pc.choice_number";
$prog_stmt = mysqli_prepare($conn, $prog_query);
mysqli_stmt_bind_param($prog_stmt, "i", $student_id);
mysqli_stmt_execute($prog_stmt);
$prog_result = mysqli_stmt_get_result($prog_stmt);
$program_choices = [];

while ($prog = mysqli_fetch_assoc($prog_result)) {
    $program_choices[] = $prog;
}

// Fetch student payments
$payment_query = "SELECT * FROM student_payments WHERE student_id = ? ORDER BY payment_date DESC";
$payment_stmt = mysqli_prepare($conn, $payment_query);
mysqli_stmt_bind_param($payment_stmt, "i", $student_id);
mysqli_stmt_execute($payment_stmt);
$payment_result = mysqli_stmt_get_result($payment_stmt);
$payments = [];

while ($payment = mysqli_fetch_assoc($payment_result)) {
    $payments[] = $payment;
}

// Return the response
$response = [
    'success' => true,
    'student' => $student,
    'documents' => $documents,
    'qualifications' => $qualifications,
    'program_choices' => $program_choices,
    'payments' => $payments
];

header('Content-Type: application/json');
echo json_encode($response);
exit();
?>