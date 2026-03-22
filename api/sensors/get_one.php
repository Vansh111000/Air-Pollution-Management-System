<?php
// api/sensors/get_one.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once dirname(__DIR__) . '/db.php';

$sensor_id = $_GET['sensor_id'] ?? null;

if (!$sensor_id) {
    http_response_code(400);
    echo json_encode(['success' => false, 'data' => null, 'error' => 'sensor_id is required']);
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT 
            s.*,
            a.area_name as area_name,
            r.aqi as latest_aqi,
            r.pm25 as latest_pm25,
            r.pm10 as latest_pm10,
            r.temperature as latest_temperature,
            r.humidity as latest_humidity,
            r.recorded_at as latest_recorded_at,
            (SELECT COUNT(*) FROM sensor_readings sr WHERE sr.sensor_id = s.sensor_id) as computed_total_readings,
            (SELECT GROUP_CONCAT(image_id) FROM sensor_images si WHERE si.sensor_id = s.sensor_id) as image_ids
        FROM sensors s 
        LEFT JOIN areas a ON s.area_id = a.area_id
        LEFT JOIN (
            SELECT r1.*
            FROM sensor_readings r1
            INNER JOIN (
                SELECT sensor_id, MAX(recorded_at) as max_recorded_at
                FROM sensor_readings
                GROUP BY sensor_id
            ) r2 ON r1.sensor_id = r2.sensor_id AND r1.recorded_at = r2.max_recorded_at
        ) r ON s.sensor_id = r.sensor_id
        WHERE s.sensor_id = ?
    ");
    $stmt->execute([$sensor_id]);
    $sensor = $stmt->fetch();

    if ($sensor) {
        echo json_encode(['success' => true, 'data' => $sensor, 'error' => null]);
    } else {
        http_response_code(404);
        echo json_encode(['success' => false, 'data' => null, 'error' => 'Sensor not found']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'data' => null, 'error' => $e->getMessage()]);
}
