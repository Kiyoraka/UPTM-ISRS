<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'agent') {
    $response = [
        'success' => false,
        'message' => 'Unauthorized access'
    ];
    echo json_encode($response);
    exit();
}

try {
    $agent_login_id = $_SESSION['user_id'];
    
    // Fetch agent details with join
    $sql = "SELECT a.*, al.email as login_email, al.name as login_name 
            FROM agent a 
            INNER JOIN agent_login al ON a.id = al.agent_id 
            WHERE al.id = ?";
    
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $agent_login_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($agent = mysqli_fetch_assoc($result)) {
        // Prepare the response data
        $response = [
            'success' => true,
            'data' => [
                'personal' => [
                    'photo_path' => $agent['photo_path'],
                    'company_name' => $agent['company_name'],
                    'registration_no' => $agent['registration_no'],
                    'address' => $agent['address']
                ],
                'contact' => [
                    'contact_phone' => $agent['contact_phone'],
                    'contact_email' => $agent['contact_email']
                ],
                'bank' => [
                    'account_name' => $agent['account_name'],
                    'account_no' => $agent['account_no'],
                    'bank_name' => $agent['bank_name'],
                    'bank_branch' => $agent['bank_branch']
                ]
            ]
        ];
    } else {
        $response = [
            'success' => false,
            'message' => 'Agent not found'
        ];
    }
    
    echo json_encode($response);
    
} catch (Exception $e) {
    $response = [
        'success' => false,
        'message' => 'Error fetching profile: ' . $e->getMessage()
    ];
    echo json_encode($response);
}
?>