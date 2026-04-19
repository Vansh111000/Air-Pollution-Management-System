<?php
require_once dirname(__DIR__) . '/db.php';

$image_id = $_GET['image_id'] ?? null;

if (!$image_id) {
    header('Content-Type: application/json');
    http_response_code(400);
    echo json_encode(['success' => false, 'data' => null, 'error' => 'image_id is required']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT image_data, image_path FROM sensor_images WHERE image_id = ?");
    $stmt->execute([$image_id]);
    $image = $stmt->fetch();

    if ($image && $image['image_data']) {
        // Output using header as required
        $ext = strtolower(pathinfo($image['image_path'], PATHINFO_EXTENSION));
        $content_type = ($ext === 'png') ? 'image/png' : 'image/jpeg';
        
        header("Content-Type: $content_type");
        echo $image['image_data'];
    } else {
        header('Content-Type: application/json');
        http_response_code(404);
        echo json_encode(['success' => false, 'data' => null, 'error' => 'Image not found']);
    }
} catch (PDOException $e) {
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode(['success' => false, 'data' => null, 'error' => 'Database error']);
}
