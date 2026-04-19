<?php
// api/areas/fetch_single.php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");

require_once '../db.php';

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] != 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
    exit();
}

$area_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($area_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Valid Area ID is required']);
    exit();
}

try {
    $stmt = $pdo->prepare("SELECT * FROM areas WHERE area_id = :id");
    $stmt->execute([':id' => $area_id]);
    $area = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($area) {
        echo json_encode([
            'success' => true,
            'data' => $area
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Area not found'
        ]);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error',
        'error' => $e->getMessage()
    ]);
}
?>
