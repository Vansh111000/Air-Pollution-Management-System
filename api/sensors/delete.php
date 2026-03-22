<?php
// api/sensors/delete.php
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once dirname(__DIR__) . '/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    http_response_code(405);
    echo json_encode(['success' => false, 'data' => null, 'error' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$sensor_id = $input['sensor_id'] ?? $_GET['sensor_id'] ?? null; //sensor id from get or from post in json form 

if (!$sensor_id) {
    http_response_code(400);
    echo json_encode(['success' => false, 'data' => null, 'error' => 'sensor_id is required']);
    exit;
}

try {
    $stmt = $pdo->prepare("DELETE FROM sensors WHERE sensor_id = ?");
    $stmt->execute([$sensor_id]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'data' => ['deleted' => true], 'error' => null]);
    } else {
        http_response_code(404);
        echo json_encode(['success' => false, 'data' => null, 'error' => 'Sensor not found']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'data' => null, 'error' => $e->getMessage()]);
}
