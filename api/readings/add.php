<?php
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
$aqi = $input['aqi'] ?? null;
$pm25 = $input['pm25'] ?? null;
$pm10 = $input['pm10'] ?? null;
$temperature = $input['temperature'] ?? null;
$humidity = $input['humidity'] ?? null;
$recorded_at = $input['recorded_at'] ?? date('Y-m-d H:i:s'); // Insert new reading (hourly data)

try {
    $pdo->beginTransaction();

    $sql = "INSERT INTO sensor_readings (sensor_id, aqi, pm25, pm10, temperature, humidity, recorded_at)
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$sensor_id, $aqi, $pm25, $pm10, $temperature, $humidity, $recorded_at]);
    
    // Update `total_readings` for the sensor
    $updateStmt = $pdo->prepare("UPDATE sensors SET total_readings = total_readings + 1 WHERE sensor_id = ?");
    $updateStmt->execute([$sensor_id]);

    $pdo->commit();

    echo json_encode(['success' => true, 'data' => ['sensor_id' => $sensor_id, 'recorded' => true], 'error' => null]);
} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500);
    echo json_encode(['success' => false, 'data' => null, 'error' => $e->getMessage()]);
}
