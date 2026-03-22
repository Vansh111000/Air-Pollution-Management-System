<?php
// api/health/get.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once dirname(__DIR__) . '/db.php';

try {
    // Get logs and their corresponding areas
    $sql = "
        SELECT 
            s.area_id,
            a.area_name as area_name,
            hl.*
        FROM health_logs hl
        JOIN sensors s ON hl.sensor_id = s.sensor_id
        LEFT JOIN areas a ON s.area_id = a.area_id
        ORDER BY hl.log_date DESC, hl.created_at DESC
    ";
    
    $stmt = $pdo->query($sql);
    $logs = $stmt->fetchAll();

    // Grouping by area
    $grouped = [];
    foreach ($logs as $log) {
        $area = $log['area_name'] ?? 'Unknown Area';
        if (!isset($grouped[$area])) {
            $grouped[$area] = [];
        }
        $grouped[$area][] = $log;
    }

    echo json_encode(['success' => true, 'data' => $grouped, 'error' => null]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'data' => null, 'error' => 'Database error']);
}
