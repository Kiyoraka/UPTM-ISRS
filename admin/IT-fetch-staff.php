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
$role = isset($_GET['role']) ? $_GET['role'] : '';
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
$offset = ($page - 1) * $limit;

// Build the SQL query
$sql = "SELECT id, name, email, role, created_at, updated_at
        FROM staff
        WHERE role != 'it'"; // Exclude IT admin users

// Add search condition if search parameter is provided
if (!empty($search)) {
    $search = '%' . mysqli_real_escape_string($conn, $search) . '%';
    $sql .= " AND (name LIKE ? OR email LIKE ?)";
}

// Add role filter if provided
if (!empty($role)) {
    $role = mysqli_real_escape_string($conn, $role);
    $sql .= " AND role = ?";
}

// Add pagination
$sql .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";

// Prepare the statement
$stmt = mysqli_prepare($conn, $sql);

// Bind parameters
if (!empty($search) && !empty($role)) {
    // Both search and role provided
    mysqli_stmt_bind_param($stmt, "sssii", $search, $search, $role, $limit, $offset);
} elseif (!empty($search)) {
    // Only search provided
    mysqli_stmt_bind_param($stmt, "ssii", $search, $search, $limit, $offset);
} elseif (!empty($role)) {
    // Only role provided
    mysqli_stmt_bind_param($stmt, "sii", $role, $limit, $offset);
} else {
    // No filters provided
    mysqli_stmt_bind_param($stmt, "ii", $limit, $offset);
}

// Execute the query
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Fetch staff
$staff = [];
while ($row = mysqli_fetch_assoc($result)) {
    $staff[] = $row;
}

// Get total count for pagination
$count_sql = "SELECT COUNT(*) as total FROM staff WHERE role != 'it'"; // Exclude IT admin users

if (!empty($search)) {
    $count_sql .= " AND (name LIKE ? OR email LIKE ?)";
}

if (!empty($role)) {
    $count_sql .= " AND role = ?";
}

$count_stmt = mysqli_prepare($conn, $count_sql);

if (!empty($search) && !empty($role)) {
    mysqli_stmt_bind_param($count_stmt, "sss", $search, $search, $role);
} elseif (!empty($search)) {
    mysqli_stmt_bind_param($count_stmt, "ss", $search, $search);
} elseif (!empty($role)) {
    mysqli_stmt_bind_param($count_stmt, "s", $role);
}

mysqli_stmt_execute($count_stmt);
$count_result = mysqli_stmt_get_result($count_stmt);
$count_row = mysqli_fetch_assoc($count_result);
$total_records = $count_row['total'];
$total_pages = ceil($total_records / $limit);

// Return the response
$response = [
    'success' => true,
    'staff' => $staff,
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