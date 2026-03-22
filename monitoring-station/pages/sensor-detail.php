<?php
/**
 * Sensor Detail Page
 * Shows full sensor information with stats, images, and management options
 */

$sensor_id = $_GET['id'] ?? null;
?>

<div class="page-header" style="display: flex; justify-content: space-between; align-items: center;">
    <div id="sensor-title-container">
        <h1>Sensor Details</h1>
    </div>
    <div style="display: flex; gap: 0.5rem;">
        <button class="btn btn-secondary" onclick="editCurrentSensor()" id="edit-btn">
            <svg viewBox="0 0 24 24" fill="currentColor" style="width: 18px; height: 18px;">
                <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25z"/>
                <path d="M20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
            </svg>
            Edit
        </button>
        <button class="btn btn-danger" onclick="deleteCurrentSensor()">
            <svg viewBox="0 0 24 24" fill="currentColor" style="width: 18px; height: 18px;">
                <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-9l-1 1H5v2h14V4z"/>
            </svg>
            Delete
        </button>
    </div>
</div>

<!-- Main Content -->
<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
    <!-- Left Column: Info & Stats -->
    <div>
        <!-- Sensor Information -->
        <div class="card" style="margin-bottom: 1.5rem;">
            <h3 style="margin-top: 0;">Basic Information</h3>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-top: 1rem;">
                <div>
                    <div style="font-family: 'Rajdhani', sans-serif; font-weight: 600; color: var(--color-text-light); font-size: 0.85rem; margin-bottom: 0.25rem;">SENSOR ID</div>
                    <div id="sensor-id" style="font-size: 1rem; color: var(--color-text-primary); font-weight: 500;">-</div>
                </div>
                <div>
                    <div style="font-family: 'Rajdhani', sans-serif; font-weight: 600; color: var(--color-text-light); font-size: 0.85rem; margin-bottom: 0.25rem;">TYPE</div>
                    <div id="sensor-type" style="font-size: 1rem; color: var(--color-text-primary); font-weight: 500;">-</div>
                </div>
                <div>
                    <div style="font-family: 'Rajdhani', sans-serif; font-weight: 600; color: var(--color-text-light); font-size: 0.85rem; margin-bottom: 0.25rem;">AREA</div>
                    <div id="sensor-area" style="font-size: 1rem; color: var(--color-text-primary); font-weight: 500;">-</div>
                </div>
                <div>
                    <div style="font-family: 'Rajdhani', sans-serif; font-weight: 600; color: var(--color-text-light); font-size: 0.85rem; margin-bottom: 0.25rem;">LOCATION</div>
                    <div id="sensor-location" style="font-size: 1rem; color: var(--color-text-primary); font-weight: 500;">-</div>
                </div>
                <div>
                    <div style="font-family: 'Rajdhani', sans-serif; font-weight: 600; color: var(--color-text-light); font-size: 0.85rem; margin-bottom: 0.25rem;">STATUS</div>
                    <div id="sensor-status" style="font-size: 1rem; color: var(--color-text-primary); font-weight: 500;">-</div>
                </div>
                <div>
                    <div style="font-family: 'Rajdhani', sans-serif; font-weight: 600; color: var(--color-text-light); font-size: 0.85rem; margin-bottom: 0.25rem;">HEALTH</div>
                    <div id="sensor-health" style="font-size: 1rem; color: var(--color-text-primary); font-weight: 500;">-</div>
                </div>
            </div>
        </div>

        <!-- Current Readings -->
        <div class="card" style="margin-bottom: 1.5rem;">
            <h3 style="margin-top: 0;">Current Readings</h3>
            <div id="sensor-readings" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem; margin-top: 1rem;">
                <div style="text-align: center; padding: 1rem; background: var(--color-surface-alt); border-radius: var(--radius-md);">
                    <div style="font-size: 0.85rem; color: var(--color-text-light); margin-bottom: 0.5rem;">No data</div>
                </div>
            </div>
        </div>

        <!-- Images Gallery -->
        <div class="card">
            <h3 style="margin-top: 0;">Images</h3>
            <div id="sensor-images" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 1rem; margin-top: 1rem;">
                <div style="padding: 2rem; background: var(--color-surface-alt); border-radius: var(--radius-md); text-align: center; color: var(--color-text-light);">
                    No images uploaded
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Stats -->
    <div>
        <!-- Stats Card -->
        <div class="card" style="margin-bottom: 1.5rem;">
            <h3 style="margin-top: 0;">Statistics</h3>
            <div style="display: flex; flex-direction: column; gap: 1rem; margin-top: 1rem;">
                <div style="padding: 1rem; background: var(--color-surface-alt); border-radius: var(--radius-md);">
                    <div style="font-family: 'Rajdhani', sans-serif; font-weight: 600; color: var(--color-text-light); font-size: 0.85rem; margin-bottom: 0.5rem;">TOTAL READINGS</div>
                    <div style="font-size: 1.8rem; font-weight: 700; color: var(--color-accent);" id="sensor-total-readings">-</div>
                </div>
                <div style="padding: 1rem; background: var(--color-surface-alt); border-radius: var(--radius-md);">
                    <div style="font-family: 'Rajdhani', sans-serif; font-weight: 600; color: var(--color-text-light); font-size: 0.85rem; margin-bottom: 0.5rem;">UPTIME</div>
                    <div style="font-size: 1.8rem; font-weight: 700; color: var(--color-success);" id="sensor-uptime">-</div>
                </div>
                <div style="padding: 1rem; background: var(--color-surface-alt); border-radius: var(--radius-md);">
                    <div style="font-family: 'Rajdhani', sans-serif; font-weight: 600; color: var(--color-text-light); font-size: 0.85rem; margin-bottom: 0.5rem;">LAST READING</div>
                    <div style="font-size: 0.95rem; color: var(--color-text-primary);" id="sensor-last-reading">-</div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card">
            <h3 style="margin-top: 0;">Quick Actions</h3>
            <div style="display: flex; flex-direction: column; gap: 0.75rem; margin-top: 1rem;">
                <button class="btn btn-secondary" onclick="downloadSensorData()" style="width: 100%; justify-content: center;">
                    <svg viewBox="0 0 24 24" fill="currentColor" style="width: 18px; height: 18px;">
                        <path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2z"/>
                    </svg>
                    Export Data
                </button>
                <button class="btn btn-secondary" onclick="viewSensorHistory()" style="width: 100%; justify-content: center;">
                    <svg viewBox="0 0 24 24" fill="currentColor" style="width: 18px; height: 18px;">
                        <path d="M11.99 5V1h-1v4H8.98v2h3.01v3.71h2V7h3.01V5h-3.01V1h-1v4h-2zm.01 8c-3.87 0-7 3.13-7 7s3.13 7 7 7 7-3.13 7-7-3.13-7-7-7z"/>
                    </svg>
                    View History
                </button>
                <button class="btn btn-secondary" onclick="generateReport()" style="width: 100%; justify-content: center;">
                    <svg viewBox="0 0 24 24" fill="currentColor" style="width: 18px; height: 18px;">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-8-6z"/>
                        <line x1="9" y1="7" x2="15" y2="7" stroke="currentColor" stroke-width="2"/>
                        <line x1="9" y1="11" x2="15" y2="11" stroke="currentColor" stroke-width="2"/>
                        <line x1="9" y1="15" x2="13" y2="15" stroke="currentColor" stroke-width="2"/>
                    </svg>
                    Generate Report
                </button>
            </div>
        </div>
    </div>
</div>
