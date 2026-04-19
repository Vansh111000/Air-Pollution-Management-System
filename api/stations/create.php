<?php
// api/stations/create.php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

require_once '../db.php';
require_once '../middleware/auth.php';

// Allow preflight
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
    exit();
}

// Ensure the caller is an active admin
requireAdmin(true);

$data = json_decode(file_get_contents("php://input"), true);
if (!$data) {
    echo json_encode(['success' => false, 'message' => 'No data provided']);
    exit();
}

$name = isset($data['name']) ? trim($data['name']) : '';
$description = isset($data['description']) ? trim($data['description']) : null;
$area_id = isset($data['area_id']) ? (int)$data['area_id'] : 0;
$emails = isset($data['emails']) && is_array($data['emails']) ? $data['emails'] : [];

if (empty($name) || empty($area_id)) {
    echo json_encode(['success' => false, 'message' => 'Station Name and Area ID are required.']);
    exit();
}

try {
    $pdo->beginTransaction();

    // 1. Create the station
    $stmt = $pdo->prepare("INSERT INTO monitoring_stations (area_id, name, description) VALUES (:area_id, :name, :description)");
    $stmt->execute([
        ':area_id' => $area_id,
        ':name' => $name,
        ':description' => $description
    ]);
    
    $station_id = $pdo->lastInsertId();
    $added_users = 0;
    $updated_users = 0;

    // 2. Link users via emails
    if (!empty($emails)) {
        // Prepare statements outside loop for efficiency
        $checkStmt = $pdo->prepare("SELECT user_id FROM users WHERE email = :email");
        $updateStmt = $pdo->prepare("UPDATE users SET user_type = 'station_worker', station_id = :station_id WHERE user_id = :user_id");
        $insertStmt = $pdo->prepare("INSERT INTO users (name, email, password, user_type, station_id) VALUES (:name, :email, :password, 'station_worker', :station_id)");
        
        $defaultPassword = password_hash('password123', PASSWORD_DEFAULT);

        foreach ($emails as $email) {
            $email = trim($email);
            if (empty($email)) continue;

            $checkStmt->execute([':email' => $email]);
            $existing_user = $checkStmt->fetch(PDO::FETCH_ASSOC);

            if ($existing_user) {
                // User exists -> Upgrade to station worker
                $updateStmt->execute([
                    ':station_id' => $station_id,
                    ':user_id' => $existing_user['user_id']
                ]);
                $updated_users++;
            } else {
                // Create user profile
                // Best effort logic: split email prefix to get a basic name
                $parts = explode('@', $email);
                $userNameStr = ucfirst($parts[0] ?? 'Worker');
                
                $insertStmt->execute([
                    ':name' => $userNameStr,
                    ':email' => $email,
                    ':password' => $defaultPassword,
                    ':station_id' => $station_id
                ]);
                $added_users++;
            }
        }
    }

    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Monitoring station created successfully.',
        'data' => [
            'station_id' => $station_id,
            'name' => $name,
            'workers_added' => $added_users,
            'workers_updated' => $updated_users
        ]
    ]);

} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error',
        'error' => $e->getMessage()
    ]);
}
?>
