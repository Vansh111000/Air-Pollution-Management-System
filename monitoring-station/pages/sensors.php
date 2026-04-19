<?php
/**
 * Sensors Management Page
 * Lists all sensors with filtering and management options
 */
?>

<div class="page-header" style="display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h1>Sensors</h1>
        <p style="color: var(--color-text-secondary); margin-top: 0.5rem;">Manage all monitoring sensors across locations</p>
    </div>
    <button class="btn btn-primary" onclick="openAddSensorModal()">
        <svg viewBox="0 0 24 24" fill="currentColor" style="width: 18px; height: 18px;">
            <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
        </svg>
        Add Sensor
    </button>
</div>

<!-- Filters Section -->
<div class="card" style="margin-bottom: 1.5rem;">
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
        <div class="form-group" style="margin-bottom: 0;">
            <label>Filter by Area</label>
            <select class="form-select" id="area-filter" onchange="applySensorFilters()">
                <option value="">All Areas</option>
            </select>
        </div>
        <div class="form-group" style="margin-bottom: 0;">
            <label>Filter by Type</label>
            <select class="form-select" id="type-filter" onchange="applySensorFilters()">
                <option value="">All Types</option>
                <option value="AQI">AQI</option>
                <option value="Temperature">Temperature</option>
                <option value="Humidity">Humidity</option>
            </select>
        </div>
        <div class="form-group" style="margin-bottom: 0;">
            <label>Filter by Status</label>
            <select class="form-select" id="status-filter" onchange="applySensorFilters()">
                <option value="">All Status</option>
                <option value="monitoring">Monitoring</option>
                <option value="maintenance">Maintenance</option>
                <option value="on_hold">On Hold</option>
            </select>
        </div>
        <div class="form-group" style="margin-bottom: 0; display: flex; align-items: flex-end;">
            <button class="btn btn-secondary" onclick="resetSensorFilters()" style="width: 100%;">
                Reset Filters
            </button>
        </div>
    </div>
</div>

<!-- Sensors List -->
<div id="sensors-container" style="margin-bottom: 2rem;">
    <div class="empty-state">
        <div class="spinner"></div>
        <p>Loading sensors...</p>
    </div>
</div>

<!-- Sensor Detail Modal (Handled by JS) -->
