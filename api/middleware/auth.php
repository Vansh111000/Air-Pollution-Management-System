<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Checks if the user is authenticated in the session.
 * Outputs JSON and exits if not authenticated when calling from an API,
 * Or redirects to login.php if called from a standard frontend script.
 */
function requireLogin($isApi = false) {
    if (!isset($_SESSION['user_id'])) {
        if ($isApi) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized. Please log in.']);
            exit();
        } else {
            header('Location: /College/Air-Pollution-Management-System-main/login.php');
            exit();
        }
    }
}

/**
 * Validates if the currently logged in user is an admin.
 */
function requireAdmin($isApi = false) {
    requireLogin($isApi);
    if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
        if ($isApi) {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Forbidden. Admin access required.']);
            exit();
        } else {
            header('Location: /College/Air-Pollution-Management-System-main/public_dashboard.php');
            exit();
        }
    }
}

/**
 * Validates if the currently logged in user is a station worker.
 */
function requireStationAccess($isApi = false) {
    requireLogin($isApi);
    if (!isset($_SESSION['user_type']) || ($_SESSION['user_type'] !== 'station_worker' && $_SESSION['user_type'] !== 'admin')) {
        if ($isApi) {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Forbidden. Station worker access required.']);
            exit();
        } else {
            header('Location: /College/Air-Pollution-Management-System-main/public_dashboard.php');
            exit();
        }
    }
    
    // For station workers (non-admin), ensure they actually have a station tied to them
    if ($_SESSION['user_type'] === 'station_worker' && empty($_SESSION['station_id'])) {
        if ($isApi) {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Forbidden. No station assigned to your profile.']);
            exit();
        } else {
            // Unassigned station workers should only see the public board
            header('Location: /College/Air-Pollution-Management-System-main/public_dashboard.php');
            exit();
        }
    }
}
?>
