<?php
header('Content-Type: application/json');

// This script forces the insertion of a baseline secure admin profile, bypassing UI.
require_once '../db.php';

try {
    $email = 'admin@apms.com';
    $password_plaintext = 'admin123';
    
    // Check if the admin already exists
    $checkStmt = $pdo->prepare("SELECT user_id, password FROM users WHERE email = :email");
    $checkStmt->execute([':email' => $email]);
    $existingAdmin = $checkStmt->fetch(PDO::FETCH_ASSOC);

    if ($existingAdmin) {
        // If it exists, let's automatically patch the password in case it was inserted manually without password_hash()
        if (!password_verify($password_plaintext, $existingAdmin['password'])) {
             $hashed = password_hash($password_plaintext, PASSWORD_DEFAULT);
             $updateStmt = $pdo->prepare("UPDATE users SET password = :password WHERE email = :email");
             $updateStmt->execute([':password' => $hashed, ':email' => $email]);
             
             echo json_encode(['success' => true, 'message' => 'Admin already existed. Password was plain-text and has been securely re-hashed!']);
             exit;
        }

        echo json_encode(['success' => true, 'message' => 'Secure Admin already exists and is fully verified.']);
        exit;
    }

    // Insert new baseline Admin correctly
    $hashedPassword = password_hash($password_plaintext, PASSWORD_DEFAULT);
    
    $query = "INSERT INTO users (name, email, password, user_type, station_id) 
              VALUES (:name, :email, :password, :user_type, NULL)";
              
    $stmt = $pdo->prepare($query);
    $result = $stmt->execute([
        ':name' => 'System Admin',
        ':email' => $email,
        ':password' => $hashedPassword,
        ':user_type' => 'admin'
    ]);

    if ($result) {
        echo json_encode([
            'success' => true, 
            'message' => 'Default System Admin successfully created!',
            'credentials' => [
                'email' => $email,
                'password' => $password_plaintext
            ]
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to create base admin profile.']);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database exception', 'error' => $e->getMessage()]);
}
?>
