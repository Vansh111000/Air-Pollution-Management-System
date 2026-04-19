<?php
// api/stations/update.php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, PUT, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

require_once '../db.php';

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] != 'POST' && $_SERVER['REQUEST_METHOD'] != 'PUT') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);
if (!$data) {
    echo json_encode(['success' => false, 'message' => 'No data provided or invalid JSON']);
    exit();
}

$station_id = isset($data['station_id']) ? (int)$data['station_id'] : 0;
$name = isset($data['name']) ? trim($data['name']) : '';
$description = isset($data['description']) ? trim($data['description']) : null;
$area_id = isset($data['area_id']) && $data['area_id'] !== '' ? (int)$data['area_id'] : null;

if ($station_id <= 0 || empty($name)) {
    echo json_encode(['success' => false, 'message' => 'station_id and name are required.']);
    exit();
}

try {
    $stmt = $pdo->prepare("UPDATE monitoring_stations SET 
                            name = :name, 
                            description = :description, 
                            area_id = :area_id 
                           WHERE station_id = :station_id");
    
    $stmt->execute([
        ':name' => $name,
        ':description' => $description,
        ':area_id' => $area_id,
        ':station_id' => $station_id
    ]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Station updated successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'No changes made or station not found.']);
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
