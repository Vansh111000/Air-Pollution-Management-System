<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>APMS - Air Pollution Monitoring System</title>
    <!-- Modern Typography: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/apms/assets/css/style.css">
    <script src="/apms/assets/js/theme.js" defer></script>
</head>
<body>
    <nav class="navbar glassmorphism">
        <div class="nav-brand">
            <h1>APMS</h1>
        </div>
        <div class="nav-links">
            <a href="/apms/index.php" class="nav-link">Home</a>
            <a href="/apms/public_dashboard.php" class="nav-link">Air Quality</a>
            <a href="/apms/catalogue.php" class="nav-link">Catalogue</a>
            <a href="/apms/login.php" class="nav-link">Login</a>
            <a href="/apms/signup.php" class="btn btn-primary" style="padding: 0.4rem 1rem; border-radius: 6px;">Sign Up</a>
            <button class="btn theme-btn" onclick="toggleTheme()" aria-label="Toggle Theme">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>
            </button>
        </div>
    </nav>