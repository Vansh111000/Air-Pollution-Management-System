<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../middleware/auth.php';

// Ensure the user is logged in
requireLogin(true);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['message']) || empty(trim($input['message']))) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Feedback message is required.']);
    exit();
}

$message = trim($input['message']);
$rating = isset($input['rating']) ? (int)$input['rating'] : null;
$user_id = $_SESSION['user_id'];

// Prevent XSS
$message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');

try {
    $stmt = $pdo->prepare("INSERT INTO feedback (user_id, message, rating, created_at) VALUES (:user_id, :message, :rating, NOW())");
    $stmt->execute([
        ':user_id' => $user_id,
        ':message' => $message,
        ':rating'  => $rating
    ]);
    
    echo json_encode(['success' => true, 'message' => 'Thank you for your feedback!']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
