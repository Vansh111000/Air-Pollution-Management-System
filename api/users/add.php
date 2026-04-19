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
    echo json_encode(['success' => false, 'message' => 'No data provided.']);
    exit();
}

$name = isset($data['name']) ? trim($data['name']) : '';
$email = isset($data['email']) ? trim($data['email']) : '';
$password = isset($data['password']) ? trim($data['password']) : '';
$user_type = isset($data['user_type']) ? trim($data['user_type']) : 'end_user';
$station_id = !empty($data['station_id']) ? (int)$data['station_id'] : null;

// Validation
if (empty($name) || empty($email) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Name, email, and password are required.']);
    exit();
}

if (!in_array($user_type, ['end_user', 'station_worker', 'admin'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid user type.']);
    exit();
}

if ($user_type === 'station_worker' && empty($station_id)) {
    echo json_encode(['success' => false, 'message' => 'Station ID is required for a station worker.']);
    exit();
}

try {
    // Check if email exists
    $checkStmt = $pdo->prepare("SELECT user_id FROM users WHERE email = :email LIMIT 1");
    $checkStmt->execute([':email' => $email]);
    if ($checkStmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'A user with this email already exists.']);
        exit();
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("
        INSERT INTO users (name, email, password, user_type, station_id) 
        VALUES (:name, :email, :password, :user_type, :station_id)
    ");
    
    $stmt->execute([
        ':name' => $name,
        ':email' => $email,
        ':password' => $hashed_password,
        ':user_type' => $user_type,
        ':station_id' => $station_id
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'User successfully created.',
        'data' => ['user_id' => $pdo->lastInsertId()]
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error', 'error' => $e->getMessage()]);
}
?>
