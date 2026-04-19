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

<!-- User Feedback Section -->
<div class="card" style="margin-top: 2rem;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem;">
        <h3 style="margin: 0;">User Feedback</h3>
        <select id="fbFilter" onchange="loadWorkerFeedback()" style="padding:0.5rem; border-radius:4px; border:1px solid #ccc; font-family:'Noto Sans',sans-serif;">
            <option value="recent">Most Recent First</option>
            <option value="oldest">Oldest First</option>
            <option value="rating_high">Highest Rating</option>
            <option value="rating_low">Lowest Rating</option>
        </select>
    </div>
    <div id="workerFeedbackContainer" style="max-height:300px; overflow-y:auto; border-top:1px solid #eee; padding-top:1rem;">
        <div style="color:var(--color-text-secondary); text-align:center;">Loading feedback...</div>
    </div>
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

/* Feedback Styling */
.fb-item { padding:1rem; border-bottom:1px solid #eee; }
.fb-item:last-child { border-bottom:none; }
.fb-header { display:flex; justify-content:space-between; margin-bottom:0.5rem; }
.fb-name { font-weight:600; color:var(--color-text); }
.fb-date { font-size:0.8rem; color:var(--color-text-secondary); }
.fb-rating { color:#ffc107; margin-bottom:0.5rem; }
.fb-msg { font-size:0.9rem; color:#444; }
</style>

<script src="/assets/js/dashboard.js"></script>
<script>
function loadWorkerFeedback() {
    const filter = document.getElementById('fbFilter').value;
    const container = document.getElementById('workerFeedbackContainer');
    container.innerHTML = '<div style="color:var(--color-text-secondary); text-align:center;">Loading feedback...</div>';

    fetch(`../api/feedback/list.php?sort=${filter}`)
        .then(r => r.json())
        .then(data => {
            if(data.success) {
                if(data.data.length === 0) {
                    container.innerHTML = '<div style="color:var(--color-text-secondary); text-align:center;">No feedback submitted yet.</div>';
                    return;
                }
                container.innerHTML = data.data.map(fb => {
                    const stars = '⭐'.repeat(fb.rating || 0) + '☆'.repeat(5 - (fb.rating || 0));
                    return `
                    <div class="fb-item">
                        <div class="fb-header">
                            <span class="fb-name">👤 ${fb.user_name || 'Anonymous User'}</span>
                            <span class="fb-date">🕒 ${new Date(fb.created_at).toLocaleString()}</span>
                        </div>
                        <div class="fb-rating" title="${fb.rating}/5 Rating">${stars}</div>
                        <div class="fb-msg">${fb.message.replace(/</g, "&lt;")}</div>
                    </div>`;
                }).join('');
            } else {
                container.innerHTML = `<div style="color:red; text-align:center;">Error: ${data.message}</div>`;
            }
        })
        .catch(err => {
            container.innerHTML = `<div style="color:red; text-align:center;">Failed to load feedback.</div>`;
        });
}
document.addEventListener('DOMContentLoaded', loadWorkerFeedback);
</script>
