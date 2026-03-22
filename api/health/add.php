<?php
// api/health/add.php
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once dirname(__DIR__) . '/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'data' => null, 'error' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true) ?? $_POST;

if (!isset($input['sensor_id']) || trim($input['sensor_id']) === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'data' => null, 'error' => 'sensor_id is required']);
    exit;
}

$sensor_id = trim($input['sensor_id']);
$status = $input['status'] ?? 'good';
$note = $input['note'] ?? '';
$log_date = $input['log_date'] ?? date('Y-m-d');

try {
    $pdo->beginTransaction();

    $sql = "INSERT INTO health_logs (sensor_id, status, note, log_date) VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$sensor_id, $status, $note, $log_date]);
    
    // Optionally update sensor health
    $updateSql = "UPDATE sensors SET health = ? WHERE sensor_id = ?";
    $updateStmt = $pdo->prepare($updateSql);
    $updateStmt->execute([$status, $sensor_id]);

    $pdo->commit();

    echo json_encode(['success' => true, 'data' => ['sensor_id' => $sensor_id, 'logged' => true], 'error' => null]);
} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500);
    echo json_encode(['success' => false, 'data' => null, 'error' => 'Database error']);
}
