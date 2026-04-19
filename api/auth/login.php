<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

require_once '../db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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

$email = isset($data['email']) ? trim($data['email']) : '';
$password = isset($data['password']) ? trim($data['password']) : '';
$remember = isset($data['remember']) ? $data['remember'] : false;

// Validation
if (empty($email) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Email and password are required.']);
    exit();
}

try {
    $stmt = $pdo->prepare("SELECT user_id, name, email, password, user_type, station_id FROM users WHERE email = :email LIMIT 1");
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // Check for station worker assignment before initiating session
        if ($user['user_type'] === 'station_worker' && empty($user['station_id'])) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Access denied. No station assigned. Please contact administrator.']);
            exit();
        }

        // HARD RESET SESSION (Fixation & Clean Slate)
        session_unset();
        session_destroy();
        session_start();
        session_regenerate_id(true);

        $_SESSION['logged_in'] = true;
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['user_type'] = $user['user_type'];
        $_SESSION['station_id'] = $user['station_id'];
        
        // Let's alias it for compatibility if any old file uses 'user_role'
        $_SESSION['user_role'] = $user['user_type'];

        if ($remember) {
            $token = bin2hex(random_bytes(32));
            setcookie('remember_token', $token, time() + (86400 * 30), "/college/Air-Pollution-Management-System-main");
            try {
                $updateTokenStmt = $pdo->prepare("UPDATE users SET remember_token = :token WHERE user_id = :id");
                $updateTokenStmt->execute([':token' => $token, ':id' => $user['user_id']]);
            } catch (PDOException $e) {}
        }

        $BASE_PATH = "/college/Air-Pollution-Management-System-main";

        // Determine redirect route based on user_type
        if ($user['user_type'] === 'admin') {
            $redirect = $BASE_PATH . "/admin/admin_dashboard.php";
        } elseif ($user['user_type'] === 'station_worker') {
            $redirect = $BASE_PATH . "/monitoring-station/";
        } else {
            $redirect = $BASE_PATH . "/public_dashboard.php";
        }

        echo json_encode([
            'success' => true,
            'message' => 'Login successful',
            'redirect' => $redirect,
            'user' => [
                'user_id' => $user['user_id'],
                'name' => $user['name'],
                'user_type' => $user['user_type']
            ]
        ]);
        exit();
    } else {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Invalid email or password.']);
        exit();
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
