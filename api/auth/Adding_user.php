<?php
// api/auth/Adding_user.php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

require_once '../db.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($method != 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);
if (!$data) {
    $data = $_POST;
}

if (empty($data)) {
    echo json_encode(['success' => false, 'message' => 'No data provided']);
    exit();
}

$role = $data['role'] ?? 'user';

// Extract common fields
$firstName = isset($data['firstName']) ? trim($data['firstName']) : '';
$lastName = isset($data['lastName']) ? trim($data['lastName']) : '';
$email = isset($data['email']) ? trim($data['email']) : '';
$password = isset($data['password']) ? trim($data['password']) : '';
$dob = isset($data['dob']) ? trim($data['dob']) : null;
$gender = isset($data['gender']) ? trim($data['gender']) : null;
$mobile = isset($data['mobile']) ? trim($data['mobile']) : null;
$city = isset($data['city']) ? trim($data['city']) : null;

$name = trim($firstName . ' ' . $lastName);

if (empty($firstName) || empty($lastName) || empty($email) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit();
}

// Map role to user_type enum ('admin', 'station_worker', 'end_user')
$user_type = 'end_user';
if ($role === 'monitor') {
    $user_type = 'station_worker';
} elseif ($role === 'admin') {
    $user_type = 'admin';
}

// Handle role-specific fields (for location building or if missing column, just ignored out of DB scope)
$location = $city;
if ($role === 'monitor' && !empty($data['stateZone'])) {
    $location .= ', ' . trim($data['stateZone']);
}

// We ignore 'stationId' for DB insert because the schema maps station_id to INT 
// whereas the form provides values like "MH-MUM-001" which are varchar.
$station_id = null;
$area_id = null;

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

try {
    // Check if email already exists
    $checkStmt = $pdo->prepare("SELECT user_id FROM users WHERE email = ?");
    $checkStmt->execute([$email]);
    if ($checkStmt->rowCount() > 0) {
        echo json_encode(['success' => false, 'message' => 'Email already registered']);
        exit();
    }

    $query = "INSERT INTO users (name, email, password, user_type, station_id, area_id, location, DOB, gender, phone_no) 
              VALUES (:name, :email, :password, :user_type, :station_id, :area_id, :location, :dob, :gender, :phone_no)";
    
    $stmt = $pdo->prepare($query);
    
    $result = $stmt->execute([
        ':name' => $name,
        ':email' => $email,
        ':password' => $hashedPassword,
        ':user_type' => $user_type,
        ':station_id' => $station_id,
        ':area_id' => $area_id,
        ':location' => $location,
        ':dob' => empty($dob) ? null : $dob,
        ':gender' => empty($gender) ? null : $gender,
        ':phone_no' => empty($mobile) ? null : $mobile
    ]);

    if ($result) {
        echo json_encode([
            'success' => true, 
            'message' => 'User registered successfully',
            'user_id' => $pdo->lastInsertId()
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to register user']);
    }

} catch (PDOException $e) {
    echo json_encode([
        'success' => false, 
        'message' => 'Database error', 
        'error' => $e->getMessage()
    ]);
}
?>
