<?php
// api/stations/fetch_single.php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");

require_once '../db.php';

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] != 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
    exit();
}

$station_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($station_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Valid Station ID is required']);
    exit();
}

try {
    $stmt = $pdo->prepare("SELECT s.*, a.area_name, a.city, a.state 
                           FROM monitoring_stations s 
                           LEFT JOIN areas a ON s.area_id = a.area_id 
                           WHERE s.station_id = :id");
    $stmt->execute([':id' => $station_id]);
    $station = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($station) {
        echo json_encode([
            'success' => true,
            'data' => $station
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Station not found'
        ]);
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
