<?php include("includes/navbar.php"); ?>

<div class="hero">
    <div class="hero-content">
        <h2 class="animate-fade-in-up">Air Pollution Monitoring System</h2>
        <p class="animate-fade-in-up delay-1">
            Real-time environmental monitoring platform that tracks AQI, 
            pollutants, and sensor data from multiple monitoring stations seamlessly.
        </p>
        <div class="hero-actions animate-fade-in-up delay-2">
            <a href="public_dashboard.php" class="btn btn-outline">Explore Data</a>
            <a href="login.php" class="btn btn-primary">Login to Dashboard <span>&rarr;</span></a>
        </div>
    </div>
</div>

<main class="main-container">
    <div class="section-header">
        <h2>Environmental Overview</h2>
        <p>Live metrics from the central monitoring station.</p>
    </div>

    <div class="grid cards-grid">
        <div class="card stat-card glass-card hover-lift">
            <div class="card-icon aqi-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            </div>
            <div class="card-content">
                <div class="card-title">Air Quality Index (AQI)</div>
                <div class="card-value text-red">142</div>
                <div class="card-trend text-red">Unhealthy for Sensitive Groups</div>
            </div>
        </div>

        <div class="card stat-card glass-card hover-lift">
            <div class="card-icon temp-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 14.76V3.5a2.5 2.5 0 0 0-5 0v11.26a4.5 4.5 0 1 0 5 0z"/></svg>
            </div>
            <div class="card-content">
                <div class="card-title">Temperature</div>
                <div class="card-value">29°C</div>
                <div class="card-trend text-neutral">Normal Range</div>
            </div>
        </div>

        <div class="card stat-card glass-card hover-lift">
            <div class="card-icon pm-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 8v8"/><path d="M8 12h8"/></svg>
            </div>
            <div class="card-content">
                <div class="card-title">PM2.5</div>
                <div class="card-value text-yellow">82 <span class="unit">µg/m³</span></div>
                <div class="card-trend text-yellow">Moderate</div>
            </div>
        </div>

        <div class="card stat-card glass-card hover-lift">
            <div class="card-icon drop-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z"/></svg>
            </div>
            <div class="card-content">
                <div class="card-title">Humidity</div>
                <div class="card-value text-blue">61%</div>
                <div class="card-trend text-neutral">Optimal</div>
            </div>
        </div>
    </div>
</main>

<?php include("includes/footer.php"); ?>