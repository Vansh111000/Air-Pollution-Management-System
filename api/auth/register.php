<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

require_once '../db.php';

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);
if (!$data) {
    $data = $_POST;
}

// 1. STRICT INPUT WHITELIST - NEVER blindly assign $_POST or $_GET
$name = isset($data['name']) ? trim(filter_var($data['name'], FILTER_SANITIZE_STRING)) : '';
$email = isset($data['email']) ? trim(filter_var($data['email'], FILTER_SANITIZE_EMAIL)) : '';
$password = isset($data['password']) ? trim($data['password']) : '';
$confirm_password = isset($data['confirm_password']) ? trim($data['confirm_password']) : '';

// 2. CHECK REQUIRED FIELDS
if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing required fields. Name, email, and password are required.']);
    exit();
}

// 3. FORCE ROLE CONTROL - NO TRUST ON INPUT
// Even if "user_type": "admin" or "station_worker" is sent by the attacker
// We strictly override it here. 
if (isset($data['user_type']) && ($data['user_type'] === 'station_worker' || $data['user_type'] === 'admin')) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized role selection']);
    exit();
}
$user_type = 'end_user'; // System enforced

// 4. PREVENT EXTRANEOUS ASSIGNMENTS
$station_id = null; // Always null for end_user
$area_id = null;
$location = null;

// 5. VALIDATION: Passwords Match
if ($password !== $confirm_password) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Passwords do not match.']);
    exit();
}

// 6. VALIDATION: Password Policy (Min 8 chars, 1 letter, 1 number)
if (strlen($password) < 8 || !preg_match("/[a-zA-Z]/", $password) || !preg_match("/[0-9]/", $password)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Password must be at least 8 characters long and contain at least one letter and one number.']);
    exit();
}

// 7. HASH PASSWORD
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

try {
    // 8. CHECK IF EMAIL ALREADY EXISTS
    $checkStmt = $pdo->prepare("SELECT user_id FROM users WHERE email = :email");
    $checkStmt->execute([':email' => $email]);
    if ($checkStmt->rowCount() > 0) {
        http_response_code(409);
        echo json_encode(['success' => false, 'message' => 'Email already registered.']);
        exit();
    }

    // 9. INSERT USER
    $query = "INSERT INTO users (name, email, password, user_type, station_id, area_id, location) 
              VALUES (:name, :email, :password, :user_type, :station_id, :area_id, :location)";
    
    $stmt = $pdo->prepare($query);
    $result = $stmt->execute([
        ':name' => $name,
        ':email' => $email,
        ':password' => $hashedPassword,
        ':user_type' => $user_type,
        ':station_id' => $station_id,
        ':area_id' => $area_id,
        ':location' => $location
    ]);

    if ($result) {
        http_response_code(201);
        echo json_encode([
            'success' => true, 
            'message' => 'User registered successfully',
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to register user. System error.']);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'Database error', 
        // In true production, do not echo raw PDO exceptions to the frontend.
    ]);
}
?>
