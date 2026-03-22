<?php
// api/readings/get_by_sensor.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once dirname(__DIR__) . '/db.php';

$sensor_id = $_GET['sensor_id'] ?? null;
$aggregation = $_GET['aggregation'] ?? 'hourly'; // default to hourly 

if (!$sensor_id) {
    http_response_code(400);
    echo json_encode(['success' => false, 'data' => null, 'error' => 'sensor_id is required']);
    exit;
}

try {
    if ($aggregation === 'daily') {
        // Daily aggregation: AVG values per day
        $sql = "
            SELECT 
                DATE(recorded_at) as recorded_date,
                AVG(aqi) as aqi,
                AVG(pm25) as pm25,
                AVG(pm10) as pm10,
                AVG(temperature) as temperature,
                AVG(humidity) as humidity
            FROM sensor_readings
            WHERE sensor_id = ?
            GROUP BY DATE(recorded_at)
            ORDER BY recorded_date ASC
        ";
    } else {
        // Hourly aggregation
        $sql = "
            SELECT 
                DATE_FORMAT(recorded_at, '%Y-%m-%d %H:00:00') as recorded_hour,
                AVG(aqi) as aqi,
                AVG(pm25) as pm25,
                AVG(pm10) as pm10,
                AVG(temperature) as temperature,
                AVG(humidity) as humidity
            FROM sensor_readings
            WHERE sensor_id = ?
            GROUP BY DATE_FORMAT(recorded_at, '%Y-%m-%d %H:00:00')
            ORDER BY recorded_hour ASC
        ";
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$sensor_id]);
    $readings = $stmt->fetchAll();

    echo json_encode(['success' => true, 'data' => $readings, 'error' => null]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'data' => null, 'error' => 'Database error']);
}
