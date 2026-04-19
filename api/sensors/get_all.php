<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once dirname(__DIR__) . '/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$whereClause = "";
$params = [];
if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'station_worker' && isset($_SESSION['station_id'])) {
    $whereClause = "WHERE s.station_id = :station_id";
    $params[':station_id'] = $_SESSION['station_id'];
}

try {
    $query = "
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
        $whereClause
    ";
    //the above query will give the latest readings 
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $sensors = $stmt->fetchAll();

    echo json_encode([
        'success' => true,
        'data' => $sensors,
        'error' => null
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'data' => null,
        'error' => $e->getMessage()
    ]);
}


/* 
will return table like this 
| sensor_id | sensor_name | area_id | area_name | latest_aqi | latest_pm25 | latest_pm10 | latest_temperature | latest_humidity | latest_recorded_at | computed_total_readings | image_ids |
| --------- | ----------- | ------- | --------- | ---------- | ----------- | ----------- | ------------------ | --------------- | ------------------ | ----------------------- | --------- |

*/