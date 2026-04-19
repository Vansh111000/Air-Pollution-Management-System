<?php
// api/areas/update.php
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

$area_id = isset($data['area_id']) ? (int)$data['area_id'] : 0;
$state = isset($data['state']) ? trim($data['state']) : '';
$city = isset($data['city']) ? trim($data['city']) : '';
$area_name = isset($data['area_name']) ? trim($data['area_name']) : '';
$location_type = isset($data['location_type']) ? trim($data['location_type']) : 'Urban';
$population_density = isset($data['population_density']) && $data['population_density'] !== '' ? (int)$data['population_density'] : null;
$acres = isset($data['acres']) && $data['acres'] !== '' ? (int)$data['acres'] : null;
$topography = isset($data['topography']) ? trim($data['topography']) : null;

if ($area_id <= 0 || empty($state) || empty($city) || empty($area_name) || empty($location_type)) {
    echo json_encode(['success' => false, 'message' => 'area_id, state, city, area_name, and location_type are required.']);
    exit();
}

try {
    $stmt = $pdo->prepare("UPDATE areas SET 
                            state = :state, 
                            city = :city, 
                            area_name = :area_name, 
                            location_type = :location_type, 
                            population_density = :pop_density, 
                            acres = :acres, 
                            topography = :topography 
                           WHERE area_id = :area_id");
    
    $stmt->execute([
        ':state' => $state,
        ':city' => $city,
        ':area_name' => $area_name,
        ':location_type' => $location_type,
        ':pop_density' => $population_density,
        ':acres' => $acres,
        ':topography' => $topography,
        ':area_id' => $area_id
    ]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Area updated successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'No changes made or area not found.']);
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
