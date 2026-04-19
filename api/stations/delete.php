<?php
// api/stations/delete.php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

require_once '../db.php';

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] != 'POST' && $_SERVER['REQUEST_METHOD'] != 'DELETE') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    if (isset($_POST['station_id'])) {
        $data['station_id'] = $_POST['station_id'];
    } elseif (isset($_GET['station_id'])) {
        $data['station_id'] = $_GET['station_id'];
    }
}

$station_id = isset($data['station_id']) ? (int)$data['station_id'] : 0;

if ($station_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Valid Station ID is required']);
    exit();
}

try {
    $stmt = $pdo->prepare("DELETE FROM monitoring_stations WHERE station_id = :id");
    $stmt->execute([':id' => $station_id]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Station deleted successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Station not found.']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error. Ensure station is not linked to existing sensors or data.',
        'error' => $e->getMessage()
    ]);
}
?>
