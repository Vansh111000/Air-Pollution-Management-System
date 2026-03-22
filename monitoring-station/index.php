<?php
/**
 * VayuDarpan - Monitoring Station Module
 * Main Application Entry Point
 * 
 * Router and layout container for all pages
 */

session_start();

// Determine current page
$page = $_GET['page'] ?? 'dashboard';
$allowed_pages = ['dashboard', 'sensors', 'sensor-detail', 'health'];
$current_page = in_array($page, $allowed_pages) ? $page : 'dashboard';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VayuDarpan - Monitoring Station</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Teko:wght@400;500;600;700&family=Rajdhani:wght@400;500;600;700&family=Noto+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="app-container">
        <!-- Navbar -->
        <?php include 'components/navbar.php'; ?>
        
        <div class="app-content">
            <!-- Sidebar -->
            <?php include 'components/sidebar.php'; ?>
            
            <!-- Main Content -->
            <main class="main-content">
                <?php
                    // Route to appropriate page
                    switch($current_page) {
                        case 'dashboard':
                            include 'pages/dashboard.php';
                            break;
                        case 'sensors':
                            include 'pages/sensors.php';
                            break;
                        case 'sensor-detail':
                            include 'pages/sensor-detail.php';
                            break;
                        case 'health':
                            include 'pages/health.php';
                            break;
                        default:
                            include 'pages/dashboard.php';
                    }
                ?>
            </main>
        </div>
    </div>

    <!-- Global Modals Container -->
    <div id="modal-container"></div>

    <!-- Scripts -->
    <script src="assets/js/apiService.js"></script>
    <script src="assets/js/app.js"></script>
    
    <!-- Page-specific scripts -->
    <?php if($current_page === 'dashboard'): ?>
        <script src="assets/js/dashboard.js"></script>
    <?php elseif($current_page === 'sensors'): ?>
        <script src="assets/js/sensors.js"></script>
    <?php elseif($current_page === 'health'): ?>
        <script src="assets/js/health.js"></script>
    <?php elseif($current_page === 'sensor-detail'): ?>
        <script src="assets/js/sensor-detail.js"></script>
    <?php endif; ?>
</body>
</html>
