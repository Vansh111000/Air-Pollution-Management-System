<?php
session_start();
header('Content-Type: application/json');

require_once '../db.php';
require_once '../middleware/auth.php';

// Only logged in users (admin/station_worker) can view feedback. Normal users can't view all feedbacks.
// Wait, actually station workers and admins need it.
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}
if ($_SESSION['user_type'] !== 'admin' && $_SESSION['user_type'] !== 'station_worker') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Forbidden']);
    exit();
}

// Support sort params
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'recent';

$orderClause = "ORDER BY f.created_at DESC";
if ($sort === 'oldest') {
    $orderClause = "ORDER BY f.created_at ASC";
} elseif ($sort === 'rating_high') {
    $orderClause = "ORDER BY f.rating DESC, f.created_at DESC";
} elseif ($sort === 'rating_low') {
    $orderClause = "ORDER BY f.rating ASC, f.created_at DESC";
}

try {
    $query = "
        SELECT 
            f.feedback_id, f.message, f.rating, f.created_at, 
            u.name as user_name, u.email as user_email
        FROM feedback f
        LEFT JOIN users u ON f.user_id = u.user_id 
        $orderClause
    ";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $feedbacks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['success' => true, 'data' => $feedbacks]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
