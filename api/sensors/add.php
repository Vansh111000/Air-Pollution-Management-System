<?php
// api/sensors/add.php
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

$sensor_id = trim($input['sensor_id']);
$name = $input['name'] ?? null;
$type = $input['type'] ?? null;
$status = $input['status'] ?? 'monitoring';
$health = $input['health'] ?? 'good';
$area_id = $input['area_id'] ?? null;
if (!$area_id && isset($input['area'])) { //if we have not included area id, we will basically find it from area name.
    $astmt = $pdo->prepare("SELECT area_id FROM areas WHERE area_name = ?");
    $astmt->execute([$input['area']]);
    $fetched_area_id = $astmt->fetchColumn();
    if ($fetched_area_id) {
        $area_id = $fetched_area_id;
    } else {
        $ins = $pdo->prepare("INSERT INTO areas (area_name) VALUES (?)"); //if area is not there, then we will first insert the area 
        $ins->execute([trim($input['area'])]);
        $area_id = $pdo->lastInsertId(); //area id will obviously be last inserted id
    }
}
$station_id = $input['station_id'] ?? null; //its the foreign key and there is one station to many sensor relationship
$location = $input['location'] ?? null; //its null

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
