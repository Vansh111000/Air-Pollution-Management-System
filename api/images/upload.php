<?php
// api/images/upload.php
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
require_once dirname(__DIR__) . '/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'data' => null, 'error' => 'Method not allowed']);
    exit;
}

$sensor_id = $_POST['sensor_id'] ?? null;

if (!$sensor_id) {
    http_response_code(400);
    echo json_encode(['success' => false, 'data' => null, 'error' => 'sensor_id is required']);
    exit;
}

if (!isset($_FILES['images'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'data' => null, 'error' => 'No images uploaded']);
    exit;
}

$allowed_types = ['image/jpeg', 'image/png'];
$max_size = 5 * 1024 * 1024; // 5 MB

$files = $_FILES['images'];
$uploaded_count = 0;
$errors = [];

try {
    $pdo->beginTransaction();
    $sql = "INSERT INTO sensor_images (sensor_id, image_path, image_data) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql);

    // Normalize array
    $file_count = is_array($files['name']) ? count($files['name']) : 1;
    
    for ($i = 0; $i < $file_count; $i++) {
        $name = is_array($files['name']) ? $files['name'][$i] : $files['name'];
        $type = is_array($files['type']) ? $files['type'][$i] : $files['type'];
        $tmp_name = is_array($files['tmp_name']) ? $files['tmp_name'][$i] : $files['tmp_name'];
        $error = is_array($files['error']) ? $files['error'][$i] : $files['error'];
        $size = is_array($files['size']) ? $files['size'][$i] : $files['size'];

        if ($error !== UPLOAD_ERR_OK) {
            $errors[] = "Error uploading file $name";
            continue;
        }

        if (!in_array($type, $allowed_types)) {
            $errors[] = "Invalid file type for $name. Only JPG and PNG are allowed.";
            continue;
        }

        if ($size > $max_size) {
            $errors[] = "File $name exceeds 5MB limit.";
            continue;
        }

        $image_data = file_get_contents($tmp_name);
        $image_path = $name; 
        
        $stmt->execute([$sensor_id, $image_path, $image_data]);
        $uploaded_count++;
    }

    $pdo->commit();

    if ($uploaded_count === 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'data' => null, 'error' => implode(', ', $errors)]);
    } else {
        echo json_encode([
            'success' => true,
            'data' => [
                'uploaded_count' => $uploaded_count,
                'errors' => empty($errors) ? null : $errors
            ],
            'error' => null
        ]);
    }
} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500);
    echo json_encode(['success' => false, 'data' => null, 'error' => 'Database error']);
}
