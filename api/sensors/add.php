<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once dirname(__DIR__) . '/db.php'; //to use PDO so we can actually write queries

/* 
Why require_once?
require → must include file or crash
once → include only one time
👉 Prevents duplicate loading
*/
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'data' => null, 'error' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
// ?? NULL COALESCING OPERATOR A ?? B if a then a otherwise b
if (!isset($input['sensor_id']) || trim($input['sensor_id']) === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'data' => null, 'error' => 'sensor_id is required']);
    exit;
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$sensor_id = trim($input['sensor_id']);
$name = $input['name'] ?? null;
$type = $input['type'] ?? null;
$status = $input['status'] ?? 'monitoring';
$health = $input['health'] ?? 'good';
$station_id = $input['station_id'] ?? null;
$area_id = $input['area_id'] ?? null;
$location = $input['location'] ?? null;

// Automatically bind to station worker's environment if assigned
if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'station_worker' && isset($_SESSION['station_id'])) {
    $station_id = $_SESSION['station_id'];
    
    // Auto-fetch related area_id linked to their station
    $findAreaStmt = $pdo->prepare("SELECT area_id FROM monitoring_stations WHERE station_id = ?");
    $findAreaStmt->execute([$station_id]);
    $linked_area_id = $findAreaStmt->fetchColumn();
    if ($linked_area_id) {
        $area_id = $linked_area_id;
    }
} else {
    // Current fallback if not station worker but passed area name
    if (!$area_id && isset($input['area'])) { 
        $astmt = $pdo->prepare("SELECT area_id FROM areas WHERE area_name = ?");
        $astmt->execute([$input['area']]);
        $fetched_area_id = $astmt->fetchColumn();
        if ($fetched_area_id) {
            $area_id = $fetched_area_id;
        } else {
            $ins = $pdo->prepare("INSERT INTO areas (area_name) VALUES (?)"); 
            $ins->execute([trim($input['area'])]);
            $area_id = $pdo->lastInsertId(); 
        }
    }
}

try {
    // Validate unique sensor_id
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM sensors WHERE sensor_id = ?");
    $stmt->execute([$sensor_id]);
    if ($stmt->fetchColumn() > 0) {
        http_response_code(409);
        echo json_encode(['success' => false, 'data' => null, 'error' => 'sensor_id already exists']);
        exit;
    }

    $sql = "INSERT INTO sensors (sensor_id, name, type, status, health, area_id, station_id, location)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$sensor_id, $name, $type, $status, $health, $area_id, $station_id, $location]);

    echo json_encode(['success' => true, 'data' => ['sensor_id' => $sensor_id], 'error' => null]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'data' => $input, 'error' => $e->getMessage()]);
}
