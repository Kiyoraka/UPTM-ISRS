<?php
// Disable error reporting to prevent HTML output
error_reporting(0);
ini_set('display_errors', 0);

require_once '../config/db.php';
require_once '../includes/functions.php';

// Ensure clean JSON response
header('Content-Type: application/json');

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in and has IO role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'io') {
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized access'
    ]);
    exit();
}

try {
    // Get query parameters
    $search = isset($_GET['search']) ? $_GET['search'] : '';
    $status = isset($_GET['status']) ? $_GET['status'] : '';
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
    $offset = ($page - 1) * $limit;

    // Build the SQL query
    $sql = "SELECT s.id, s.first_name, s.last_name, s.email, s.nationality, 
                   s.country, s.passport_no, s.io_status as status, s.created_at,
                   CONCAT(s.first_name, ' ', s.last_name) as full_name,
                   s.programme_code_1
            FROM students s
            LEFT JOIN student_login sl ON s.id = sl.student_id
            WHERE 1=1";

    // Prepare parameters and conditions
    $params = [];
    $types = '';

    // Add search condition if search parameter is provided
    if (!empty($search)) {
        $searchParam = "%{$search}%";
        $sql .= " AND (s.first_name LIKE ? OR s.last_name LIKE ? OR s.email LIKE ? OR s.passport_no LIKE ? OR CONCAT(s.first_name, ' ', s.last_name) LIKE ?)";
        $params[] = $searchParam;
        $params[] = $searchParam;
        $params[] = $searchParam;
        $params[] = $searchParam;
        $params[] = $searchParam;
        $types .= 'sssss';
    }

    // Add status filter if provided
    if (!empty($status)) {
        $sql .= " AND s.io_status = ?";
        $params[] = $status;
        $types .= 's';
    }

    // Add pagination
    $sql .= " ORDER BY s.created_at DESC LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;
    $types .= 'ii';

    // Prepare and execute main query
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($params) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }
    
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // Fetch students
    $students = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $students[] = $row;
    }

    // Count total records
    $count_sql = "SELECT COUNT(*) as total FROM students s WHERE 1=1";
    $count_params = [];
    $count_types = '';

    if (!empty($search)) {
        $searchParam = "%{$search}%";
        $count_sql .= " AND (s.first_name LIKE ? OR s.last_name LIKE ? OR s.email LIKE ? OR s.passport_no LIKE ? OR CONCAT(s.first_name, ' ', s.last_name) LIKE ?)";
        $count_params[] = $searchParam;
        $count_params[] = $searchParam;
        $count_params[] = $searchParam;
        $count_params[] = $searchParam;
        $count_params[] = $searchParam;
        $count_types .= 'sssss';
    }

    if (!empty($status)) {
        $count_sql .= " AND s.io_status = ?";
        $count_params[] = $status;
        $count_types .= 's';
    }

    $count_stmt = mysqli_prepare($conn, $count_sql);
    
    if ($count_params) {
        mysqli_stmt_bind_param($count_stmt, $count_types, ...$count_params);
    }
    
    mysqli_stmt_execute($count_stmt);
    $count_result = mysqli_stmt_get_result($count_stmt);
    $count_row = mysqli_fetch_assoc($count_result);
    
    $total_records = $count_row['total'];
    $total_pages = ceil($total_records / $limit);

    // Return the response
    $response = [
        'success' => true,
        'students' => $students,
        'pagination' => [
            'current_page' => $page,
            'total_pages' => $total_pages,
            'total_records' => $total_records,
            'limit' => $limit
        ]
    ];

    echo json_encode($response);
    exit();

} catch (Exception $e) {
    // Catch any unexpected errors
    echo json_encode([
        'success' => false,
        'message' => 'An unexpected error occurred: ' . $e->getMessage()
    ]);
    exit();
}