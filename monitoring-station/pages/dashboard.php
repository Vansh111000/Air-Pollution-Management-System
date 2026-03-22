<?php
/**
 * Dashboard Page
 * Shows overview with analytics, latest sensor readings, and charts
 */
?>

<div class="page-header">
    <h1>Dashboard</h1>
    <p style="color: var(--color-text-secondary); margin-top: 0.5rem;">Real-time monitoring station overview</p>
</div>

<!-- Analytics Cards -->
<div class="analytics-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
    <div id="aqi-card-container"></div>
    <div id="temp-card-container"></div>
    <div id="humidity-card-container"></div>
</div>

<!-- Status Overview -->
<div class="status-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
    <div class="card">
        <div style="text-align: center;">
            <div style="font-size: 2rem; font-weight: 700; color: var(--color-accent); margin-bottom: 0.5rem;" id="active-count">0</div>
            <div style="font-family: 'Rajdhani', sans-serif; font-weight: 600; color: var(--color-text-secondary);">Active Sensors</div>
        </div>
    </div>
    <div class="card">
        <div style="text-align: center;">
            <div style="font-size: 2rem; font-weight: 700; color: var(--color-warning); margin-bottom: 0.5rem;" id="maintenance-count">0</div>
            <div style="font-family: 'Rajdhani', sans-serif; font-weight: 600; color: var(--color-text-secondary);">Under Maintenance</div>
        </div>
    </div>
    <div class="card">
        <div style="text-align: center;">
            <div style="font-size: 2rem; font-weight: 700; color: var(--color-danger); margin-bottom: 0.5rem;" id="offline-count">0</div>
            <div style="font-family: 'Rajdhani', sans-serif; font-weight: 600; color: var(--color-text-secondary);">Offline</div>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
    <div class="chart-container">
        <div class="chart-title">Hourly AQI Trend</div>
        <canvas id="hourly-chart" style="max-height: 250px;"></canvas>
    </div>
    <div class="chart-container">
        <div class="chart-title">Daily AQI Trend</div>
        <canvas id="daily-chart" style="max-height: 250px;"></canvas>
    </div>
</div>

<!-- Recent Sensors Table -->
<div class="card">
    <h3 style="margin-top: 0;">Recent Sensor Readings</h3>
    <div id="recent-sensors-container" style="margin-top: 1rem;"></div>
</div>

<style>
@media (max-width: 1200px) {
    .analytics-grid {
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)) !important;
    }
    
    [style*="grid-template-columns: 1fr 1fr"] {
        grid-template-columns: 1fr !important;
    }
}

.chart-canvas-wrapper {
    position: relative;
    height: 300px;
}
</style>

<script src="/assets/js/dashboard.js"></script>
