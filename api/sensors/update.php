<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST, PUT');
header('Access-Control-Allow-Headers: Content-Type');

require_once dirname(__DIR__) . '/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'PUT') {
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

try {
    $stmt = $pdo->prepare("SELECT * FROM sensors WHERE sensor_id = ?");
    $stmt->execute([$sensor_id]);
    if (!$stmt->fetch()) {
        http_response_code(404);
        echo json_encode(['success' => false, 'data' => null, 'error' => 'Sensor not found']);
        exit;
    }

    $updates = [];
    $params = [];
    
    // Update status, area, location, health
    $allowed_fields = ['status', 'location', 'health'];
    foreach ($allowed_fields as $field) {
        if (isset($input[$field])) {
            $updates[] = "$field = ?";
            $params[] = $input[$field];
        }
    }
    
    // Translate area name to ID if provided
    if (isset($input['area']) && !isset($input['area_id'])) {
        $astmt = $pdo->prepare("SELECT area_id FROM areas WHERE area_name = ?");
        $astmt->execute([$input['area']]);
        $fetched_area_id = $astmt->fetchColumn();
        if ($fetched_area_id) {
            $input['area_id'] = $fetched_area_id;
        } else {
            // Insert missing area
            $ins = $pdo->prepare("INSERT INTO areas (area_name) VALUES (?)");
            $ins->execute([trim($input['area'])]);
            $input['area_id'] = $pdo->lastInsertId();
        }
    }

    // Assign area_id safely
    if (isset($input['area_id'])) {
        $updates[] = "area_id = ?";
        $params[] = $input['area_id'];
    }
    if (empty($updates)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'data' => null, 'error' => 'No valid fields to update']);
        exit;
    }

    $params[] = $sensor_id;
    $sql = "UPDATE sensors SET " . implode(', ', $updates) . " WHERE sensor_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    echo json_encode(['success' => true, 'data' => ['sensor_id' => $sensor_id, 'updated' => true], 'error' => null, 'obj' => $input]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'data' => null, 
        'error' => $e->getMessage(),
        'debug_sql' => $sql ?? '',
        'debug_params' => $params ?? [],
        'debug_input' => $input ?? [],
        'input'=>$input
    ]);
}
