<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in and has IT role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'it') {
    $response = [
        'success' => false,
        'message' => 'Unauthorized access'
    ];
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}

// Check if agent ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $response = [
        'success' => false,
        'message' => 'Agent ID is required'
    ];
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}

$agent_id = intval($_GET['id']);

// Fetch agent details
$query = "SELECT * FROM agent WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $agent_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) === 0) {
    $response = [
        'success' => false,
        'message' => 'Agent not found'
    ];
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}

$agent = mysqli_fetch_assoc($result);

// Fetch agent documents
$doc_query = "SELECT * FROM agent_documents WHERE agent_id = ?";
$doc_stmt = mysqli_prepare($conn, $doc_query);
mysqli_stmt_bind_param($doc_stmt, "i", $agent_id);
mysqli_stmt_execute($doc_stmt);
$doc_result = mysqli_stmt_get_result($doc_stmt);
$documents = [];

while ($doc = mysqli_fetch_assoc($doc_result)) {
    $documents[] = $doc;
}

// Fetch agent experiences
$exp_query = "SELECT * FROM agent_experiences WHERE agent_id = ?";
$exp_stmt = mysqli_prepare($conn, $exp_query);
mysqli_stmt_bind_param($exp_stmt, "i", $agent_id);
mysqli_stmt_execute($exp_stmt);
$exp_result = mysqli_stmt_get_result($exp_stmt);
$experiences = [];

while ($exp = mysqli_fetch_assoc($exp_result)) {
    $experiences[] = $exp;
}

// Return the response
$response = [
    'success' => true,
    'agent' => $agent,
    'documents' => $documents,
    'experiences' => $experiences
];

header('Content-Type: application/json');
echo json_encode($response);
exit();
?>