<?php
/**
 * Health Management Page
 * Tracks sensor health, maintenance status, and health logs
 */
?>

<div class="page-header" style="display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h1>Sensor Health</h1>
        <p style="color: var(--color-text-secondary); margin-top: 0.5rem;">Monitor sensor status and health metrics</p>
    </div>
    <button class="btn btn-primary" onclick="openAddHealthLogModal()">
        <svg viewBox="0 0 24 24" fill="currentColor" style="width: 18px; height: 18px;">
            <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
        </svg>
        Add Health Log
    </button>
</div>

<!-- Health Overview Cards -->
<div class="status-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
    <div class="card">
        <div style="text-align: center;">
            <div style="font-size: 2rem; font-weight: 700; color: var(--color-success); margin-bottom: 0.5rem;" id="good-count">0</div>
            <div style="font-family: 'Rajdhani', sans-serif; font-weight: 600; color: var(--color-text-secondary);">Healthy Sensors</div>
            <div style="font-size: 0.85rem; color: var(--color-text-light); margin-top: 0.5rem;">Operational and monitored</div>
        </div>
    </div>
    <div class="card">
        <div style="text-align: center;">
            <div style="font-size: 2rem; font-weight: 700; color: var(--color-warning); margin-bottom: 0.5rem;" id="maintenance-count">0</div>
            <div style="font-family: 'Rajdhani', sans-serif; font-weight: 600; color: var(--color-text-secondary);">Under Maintenance</div>
            <div style="font-size: 0.85rem; color: var(--color-text-light); margin-top: 0.5rem;">Scheduled service</div>
        </div>
    </div>
    <div class="card">
        <div style="text-align: center;">
            <div style="font-size: 2rem; font-weight: 700; color: var(--color-danger); margin-bottom: 0.5rem;" id="offline-count">0</div>
            <div style="font-family: 'Rajdhani', sans-serif; font-weight: 600; color: var(--color-text-secondary);">Offline Sensors</div>
            <div style="font-size: 0.85rem; color: var(--color-text-light); margin-top: 0.5rem;">Requires attention</div>
        </div>
    </div>
</div>

<!-- Filter Section -->
<div class="card" style="margin-bottom: 1.5rem;">
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
        <div class="form-group" style="margin-bottom: 0;">
            <label>Filter by Area</label>
            <select class="form-select" id="health-area-filter" onchange="applyHealthFilters()">
                <option value="">All Areas</option>
            </select>
        </div>
        <div class="form-group" style="margin-bottom: 0;">
            <label>Filter by Health</label>
            <select class="form-select" id="health-status-filter" onchange="applyHealthFilters()">
                <option value="">All Status</option>
                <option value="good">Good</option>
                <option value="needs_attention">Needs Attention</option>
                <option value="offline">Offline</option>
            </select>
        </div>
        <div class="form-group" style="margin-bottom: 0; display: flex; align-items: flex-end;">
            <button class="btn btn-secondary" onclick="resetHealthFilters()" style="width: 100%;">
                Reset Filters
            </button>
        </div>
    </div>
</div>

<!-- Health Logs Table -->
<div class="card">
    <h3 style="margin-top: 0; margin-bottom: 1rem;">Health Logs by Area</h3>
    <div id="health-logs-container">
        <div class="empty-state">
            <div class="spinner"></div>
            <p>Loading health data...</p>
        </div>
    </div>
</div>
