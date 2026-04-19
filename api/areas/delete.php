<?php
// api/areas/delete.php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

require_once '../db.php';

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] != 'POST' && $_SERVER['REQUEST_METHOD'] != 'DELETE') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);

// If no JSON, try getting from POST data or GET string (fallback)
if (!$data) {
    if (isset($_POST['area_id'])) {
        $data['area_id'] = $_POST['area_id'];
    } elseif (isset($_GET['area_id'])) {
        $data['area_id'] = $_GET['area_id'];
    }
}

$area_id = isset($data['area_id']) ? (int)$data['area_id'] : 0;

if ($area_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Valid Area ID is required']);
    exit();
}

try {
    $stmt = $pdo->prepare("DELETE FROM areas WHERE area_id = :id");
    $stmt->execute([':id' => $area_id]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Area deleted successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Area not found.']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error. Ensure area is not linked to existing stations or sensors.',
        'error' => $e->getMessage()
    ]);
}
?>
