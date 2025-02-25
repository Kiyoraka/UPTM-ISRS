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

// Get total agents count
$agent_sql = "SELECT COUNT(*) as total FROM agent";
$agent_result = mysqli_query($conn, $agent_sql);
$agent_count = mysqli_fetch_assoc($agent_result)['total'];

// Get total staff count (excluding IT admins)
$staff_sql = "SELECT COUNT(*) as total FROM staff WHERE role != 'it'";
$staff_result = mysqli_query($conn, $staff_sql);
$staff_count = mysqli_fetch_assoc($staff_result)['total'];

// Return the response
$response = [
    'success' => true,
    'stats' => [
        'total_students' => 0, // Placeholder until student registration is implemented
        'total_staff' => $staff_count,
        'total_agents' => $agent_count
    ]
];

header('Content-Type: application/json');
echo json_encode($response);
exit();
?>