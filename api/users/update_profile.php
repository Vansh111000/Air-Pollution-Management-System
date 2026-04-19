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

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
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
$user_id = $_SESSION['user_id'];

if (empty($name) || empty($email)) {
    echo json_encode(['success' => false, 'message' => 'Name and email are required.']);
    exit();
}

try {
    // Check if email is already taken by someone else
    $checkStmt = $pdo->prepare("SELECT user_id FROM users WHERE email = :email AND user_id != :id LIMIT 1");
    $checkStmt->execute([':email' => $email, ':id' => $user_id]);
    if ($checkStmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Email is already taken by another account.']);
        exit();
    }

    if (!empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET name = :name, email = :email, password = :password WHERE user_id = :id");
        $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':password' => $hashed_password,
            ':id' => $user_id
        ]);
    } else {
        $stmt = $pdo->prepare("UPDATE users SET name = :name, email = :email WHERE user_id = :id");
        $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':id' => $user_id
        ]);
    }

    echo json_encode([
        'success' => true,
        'message' => 'Profile updated successfully.'
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error', 'error' => $e->getMessage()]);
}
?>
