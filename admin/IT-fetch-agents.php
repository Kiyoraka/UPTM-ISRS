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

// Get query parameters
$search = isset($_GET['search']) ? $_GET['search'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
$offset = ($page - 1) * $limit;

// Build the SQL query
$sql = "SELECT id, company_name, contact_name, contact_email, country, status, 
               created_at, registration_no, application_type
        FROM agent
        WHERE 1=1";

// Add search condition if search parameter is provided
if (!empty($search)) {
    $search = '%' . mysqli_real_escape_string($conn, $search) . '%';
    $sql .= " AND (company_name LIKE ? OR contact_name LIKE ? OR contact_email LIKE ? OR registration_no LIKE ?)";
}

// Add status filter if provided
if (!empty($status)) {
    $status = mysqli_real_escape_string($conn, $status);
    $sql .= " AND status = ?";
}

// Add pagination
$sql .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";

// Prepare the statement
$stmt = mysqli_prepare($conn, $sql);

// Bind parameters
if (!empty($search) && !empty($status)) {
    // Both search and status provided
    mysqli_stmt_bind_param($stmt, "sssssii", $search, $search, $search, $search, $status, $limit, $offset);
} elseif (!empty($search)) {
    // Only search provided
    mysqli_stmt_bind_param($stmt, "sssii", $search, $search, $search, $search, $limit, $offset);
} elseif (!empty($status)) {
    // Only status provided
    mysqli_stmt_bind_param($stmt, "sii", $status, $limit, $offset);
} else {
    // No filters provided
    mysqli_stmt_bind_param($stmt, "ii", $limit, $offset);
}

// Execute the query
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Fetch agents
$agents = [];
while ($row = mysqli_fetch_assoc($result)) {
    $agents[] = $row;
}

// Get total count for pagination
$count_sql = "SELECT COUNT(*) as total FROM agent WHERE 1=1";

if (!empty($search)) {
    $count_sql .= " AND (company_name LIKE ? OR contact_name LIKE ? OR contact_email LIKE ? OR registration_no LIKE ?)";
}

if (!empty($status)) {
    $count_sql .= " AND status = ?";
}

$count_stmt = mysqli_prepare($conn, $count_sql);

if (!empty($search) && !empty($status)) {
    mysqli_stmt_bind_param($count_stmt, "sssss", $search, $search, $search, $search, $status);
} elseif (!empty($search)) {
    mysqli_stmt_bind_param($count_stmt, "ssss", $search, $search, $search, $search);
} elseif (!empty($status)) {
    mysqli_stmt_bind_param($count_stmt, "s", $status);
}

mysqli_stmt_execute($count_stmt);
$count_result = mysqli_stmt_get_result($count_stmt);
$count_row = mysqli_fetch_assoc($count_result);
$total_records = $count_row['total'];
$total_pages = ceil($total_records / $limit);

// Return the response
$response = [
    'success' => true,
    'agents' => $agents,
    'pagination' => [
        'current_page' => $page,
        'total_pages' => $total_pages,
        'total_records' => $total_records,
        'limit' => $limit
    ]
];

header('Content-Type: application/json');
echo json_encode($response);
exit();
?>