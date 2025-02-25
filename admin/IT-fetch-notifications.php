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

// Get count of pending agents
$count_sql = "SELECT COUNT(*) as total FROM agent WHERE status = 'pending'";
$count_result = mysqli_query($conn, $count_sql);
$count_row = mysqli_fetch_assoc($count_result);
$pending_count = $count_row['total'];

// Get the 5 most recent pending agents
$agents_sql = "SELECT id, company_name, contact_name, contact_email, created_at 
               FROM agent 
               WHERE status = 'pending' 
               ORDER BY created_at DESC 
               LIMIT 5";
$agents_result = mysqli_query($conn, $agents_sql);

$pending_agents = [];
while ($row = mysqli_fetch_assoc($agents_result)) {
    // Format the date
    $created_date = new DateTime($row['created_at']);
    $row['formatted_date'] = $created_date->format('M d, Y');
    $row['time_ago'] = time_elapsed_string($row['created_at']);
    
    $pending_agents[] = $row;
}

// Return the response
$response = [
    'success' => true,
    'count' => $pending_count,
    'agents' => $pending_agents
];

header('Content-Type: application/json');
echo json_encode($response);
exit();

// Helper function to get time elapsed string
function time_elapsed_string($datetime, $full = false) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = array(
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    );
    
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ago' : 'just now';
}
?>