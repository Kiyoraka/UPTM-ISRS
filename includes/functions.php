<?php
// Check if session has not been started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Function to check user role
function checkRole($allowed_roles) {
    if (!isset($_SESSION['role'])) {
        return false;
    }
    return in_array($_SESSION['role'], $allowed_roles);
}

// Function to redirect
function redirect($path) {
    header("Location: " . $path);
    exit();
}

// Function to sanitize input
function sanitize($input) {
    global $conn;
    return mysqli_real_escape_string($conn, trim($input));
}

// Function to show error
function showError($message) {
    return "<div class='error-message'>$message</div>";
}

// Function to show success
function showSuccess($message) {
    return "<div class='success-message'>$message</div>";
}

// Function to handle file upload
function handleUpload($file, $directory) {
    $target_dir = "../uploads/" . $directory . "/";
    $file_extension = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    $new_filename = uniqid() . '.' . $file_extension;
    $target_file = $target_dir . $new_filename;
    
    // Check file size (5MB max)
    if ($file["size"] > 5000000) {
        return ["success" => false, "message" => "File is too large"];
    }
    
    // Move file
    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return ["success" => true, "filename" => $new_filename];
    } else {
        return ["success" => false, "message" => "Upload failed"];
    }
}
?>