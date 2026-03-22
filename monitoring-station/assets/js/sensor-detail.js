/**
 * Sensor Detail Page Logic
 * Displays complete sensor information and handles detail operations
 */

class SensorDetailManager {
    constructor() {
        this.sensor = null;
        this.sensorId = this.getSensorIdFromUrl();
        this.init();
    }

    getSensorIdFromUrl() {
        const params = new URLSearchParams(window.location.search);
        return params.get('id');
    }

    async init() {
        if (!this.sensorId) {
            showNotification('No sensor selected', 'error');
            setTimeout(() => navigateTo('sensors'), 2000);
            return;
        }

        await this.loadSensor();
        this.renderSensorDetails();
    }

    async loadSensor() {
        try {
            const response = await apiService.getSensorById(this.sensorId);
            if (response.success && response.data) {
                this.sensor = response.data;
            } else {
                showNotification('Sensor not found', 'error');
                setTimeout(() => navigateTo('sensors'), 2000);
            }
        } catch (error) {
            console.error('Error loading sensor:', error);
            showNotification('Failed to load sensor details', 'error');
        }
    }

    renderSensorDetails() {
        if (!this.sensor) return;

        const sensor = this.sensor;

        // Update title
        const titleContainer = document.getElementById('sensor-title-container');
        if (titleContainer) {
            titleContainer.innerHTML = `
                <div>
                    <h1>${sensor.id}</h1>
                    <p style="color: var(--color-text-secondary); margin-top: 0.5rem;">${sensor.name || sensor.location}</p>
                </div>
            `;
        }

        // Basic Information
        document.getElementById('sensor-id').textContent = sensor.id;
        document.getElementById('sensor-type').textContent = sensor.type;
        document.getElementById('sensor-area').textContent = sensor.area;
        document.getElementById('sensor-location').textContent = sensor.location;

        // Status badges
        const statusClass = ApiService.getStatusIndicatorClass(sensor.status);
        const healthClass = ApiService.getHealthStatusClass(sensor.health);

        document.getElementById('sensor-status').innerHTML = `
            <span class="status-indicator ${statusClass}"></span>
            ${sensor.status.replace(/_/g, ' ').toUpperCase()}
        `;

        document.getElementById('sensor-health').innerHTML = `
            <span class="badge ${healthClass}">${sensor.health.replace(/_/g, ' ')}</span>
        `;

        // Current Readings
        this.renderReadings();

        // Images
        this.renderImages();

        // Statistics
        this.renderStatistics();
    }

    renderReadings() {
        const container = document.getElementById('sensor-readings');
        if (!this.sensor.lastReading) {
            container.innerHTML = '<p style="color: var(--color-text-light); grid-column: 1/-1;">No readings available</p>';
            return;
        }

        const reading = this.sensor.lastReading;
        const html = [];

        if (reading.aqi !== undefined) {
            const aqiStatus = ApiService.getAqiStatus(reading.aqi);
            const statusColor = {
                'good': '#00c853',
                'moderate': '#ffd600',
                'poor': '#ff9100',
                'severe': '#d50000'
            }[aqiStatus] || '#00bcd4';

            html.push(`
                <div style="text-align: center; padding: 1rem; background: var(--color-surface-alt); border-radius: var(--radius-md);">
                    <div style="font-size: 0.85rem; color: var(--color-text-light); margin-bottom: 0.5rem;">AQI</div>
                    <div style="font-size: 2rem; font-weight: 700; color: ${statusColor};">${reading.aqi}</div>
                    <div style="font-size: 0.8rem; color: var(--color-text-secondary);">${aqiStatus.toUpperCase()}</div>
                </div>
            `);
        }

        if (reading.pm25 !== undefined) {
            html.push(`
                <div style="text-align: center; padding: 1rem; background: var(--color-surface-alt); border-radius: var(--radius-md);">
                    <div style="font-size: 0.85rem; color: var(--color-text-light); margin-bottom: 0.5rem;">PM2.5</div>
                    <div style="font-size: 2rem; font-weight: 700; color: var(--color-accent);">${reading.pm25}</div>
                    <div style="font-size: 0.8rem; color: var(--color-text-secondary);">μg/m³</div>
                </div>
            `);
        }

        if (reading.pm10 !== undefined) {
            html.push(`
                <div style="text-align: center; padding: 1rem; background: var(--color-surface-alt); border-radius: var(--radius-md);">
                    <div style="font-size: 0.85rem; color: var(--color-text-light); margin-bottom: 0.5rem;">PM10</div>
                    <div style="font-size: 2rem; font-weight: 700; color: var(--color-accent);">${reading.pm10}</div>
                    <div style="font-size: 0.8rem; color: var(--color-text-secondary);">μg/m³</div>
                </div>
            `);
        }

        if (reading.temperature !== undefined) {
            html.push(`
                <div style="text-align: center; padding: 1rem; background: var(--color-surface-alt); border-radius: var(--radius-md);">
                    <div style="font-size: 0.85rem; color: var(--color-text-light); margin-bottom: 0.5rem;">Temperature</div>
                    <div style="font-size: 2rem; font-weight: 700; color: var(--color-accent);">${reading.temperature}</div>
                    <div style="font-size: 0.8rem; color: var(--color-text-secondary);">°C</div>
                </div>
            `);
        }

        if (reading.humidity !== undefined) {
            html.push(`
                <div style="text-align: center; padding: 1rem; background: var(--color-surface-alt); border-radius: var(--radius-md);">
                    <div style="font-size: 0.85rem; color: var(--color-text-light); margin-bottom: 0.5rem;">Humidity</div>
                    <div style="font-size: 2rem; font-weight: 700; color: var(--color-accent);">${reading.humidity}</div>
                    <div style="font-size: 0.8rem; color: var(--color-text-secondary);">%</div>
                </div>
            `);
        }

        container.innerHTML = html.join('');
    }

    renderImages() {
        const container = document.getElementById('sensor-images');
        const images = this.sensor.images || [];

        if (images.length === 0) {
            container.innerHTML = `
                <div style="padding: 2rem; background: var(--color-surface-alt); border-radius: var(--radius-md); text-align: center; color: var(--color-text-light); grid-column: 1/-1;">
                    No images uploaded
                </div>
            `;
            return;
        }

        const html = images.map((img, idx) => `
            <div style="position: relative; border-radius: var(--radius-md); overflow: hidden; aspect-ratio: 1; background: var(--color-surface-alt); cursor: pointer;" onclick="viewImage('${img}')">
                <img src="/api/images/get.php?image_id=${img}" alt="Sensor image ${idx + 1}" style="width: 100%; height: 100%; object-fit: cover;">
                <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0); transition: background 0.3s;" onmouseover="this.style.background = 'rgba(0,0,0,0.3)'" onmouseout="this.style.background = 'rgba(0,0,0,0)';">
                    <svg viewBox="0 0 24 24" fill="white" style="width: 30px; height: 30px; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); opacity: 0; transition: opacity 0.3s;" onmouseover="this.style.opacity = '1'" onmouseout="this.style.opacity = '0'">
                        <path d="M12 4.5C7.31 4.5 3.31 7.61 2 12c1.31 4.39 5.31 7.5 10 7.5s8.69-3.11 10-7.5c-1.31-4.39-5.31-7.5-10-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>
                    </svg>
                </div>
            </div>
        `).join('');

        container.innerHTML = html;
    }

    renderStatistics() {
        document.getElementById('sensor-total-readings').textContent = this.sensor.totalReadings || '0';
        document.getElementById('sensor-uptime').textContent = this.sensor.uptime + '%';

        if (this.sensor.lastReading && this.sensor.lastReading.timestamp) {
            document.getElementById('sensor-last-reading').textContent = formatDate(this.sensor.lastReading.timestamp);
        } else {
            document.getElementById('sensor-last-reading').textContent = 'Never';
        }
    }
}

// Initialize
let sensorDetailManager = null;

document.addEventListener('DOMContentLoaded', () => {
    sensorDetailManager = new SensorDetailManager();
});

/**
 * Edit current sensor
 */
function editCurrentSensor() {
    if (!sensorDetailManager || !sensorDetailManager.sensor) return;

    const sensor = sensorDetailManager.sensor;

    showModal(`Edit Sensor: ${sensor.id}`, `
        <div class="form-group">
            <label>Status</label>
            <select class="form-select" id="edit-status">
                <option value="monitoring" ${sensor.status === 'monitoring' ? 'selected' : ''}>Monitoring</option>
                <option value="maintenance" ${sensor.status === 'maintenance' ? 'selected' : ''}>Maintenance</option>
                <option value="on_hold" ${sensor.status === 'on_hold' ? 'selected' : ''}>On Hold</option>
            </select>
        </div>
        <div class="form-group">
            <label>Area</label>
            <input type="text" class="form-input" id="edit-area" value="${sensor.area}">
        </div>
        <div class="form-group">
            <label>Location</label>
            <input type="text" class="form-input" id="edit-location" value="${sensor.location}">
        </div>
        <div class="form-group">
            <label>Health Status</label>
            <select class="form-select" id="edit-health">
                <option value="good" ${sensor.health === 'good' ? 'selected' : ''}>Good</option>
                <option value="needs_attention" ${sensor.health === 'needs_attention' ? 'selected' : ''}>Needs Attention</option>
                <option value="offline" ${sensor.health === 'offline' ? 'selected' : ''}>Offline</option>
            </select>
        </div>
    `, [
        {
            text: 'Save',
            action: saveSensorEdits
        },
        {
            text: 'Cancel',
            action: closeModal
        }
    ]);
}

/**
 * Save sensor edits
 */
async function saveSensorEdits() {
    if (!sensorDetailManager) return;

    const updates = {
        status: document.getElementById('edit-status').value,
        area: document.getElementById('edit-area').value,
        location: document.getElementById('edit-location').value,
        health: document.getElementById('edit-health').value
    };

    try {
        const response = await apiService.updateSensor(sensorDetailManager.sensorId, updates);
        if (response.success) {
            closeModal();
            showNotification('Sensor updated successfully', 'success');
            await sensorDetailManager.loadSensor();
            sensorDetailManager.renderSensorDetails();
        }
    } catch (error) {
        showNotification('Failed to update sensor', 'error');
    }
}

/**
 * Delete current sensor
 */
function deleteCurrentSensor() {
    if (!sensorDetailManager || !sensorDetailManager.sensor) return;

    showModal('Confirm Delete', `
        <p>Are you sure you want to delete sensor <strong>${sensorDetailManager.sensor.id}</strong>?</p>
        <p style="color: var(--color-danger); margin-top: 1rem;">This action cannot be undone.</p>
    `, [
        {
            text: 'Delete',
            action: async () => {
                try {
                    const response = await apiService.deleteSensor(sensorDetailManager.sensorId);
                    if (response.success) {
                        closeModal();
                        showNotification('Sensor deleted successfully', 'success');
                        setTimeout(() => navigateTo('sensors'), 1500);
                    }
                } catch (error) {
                    showNotification('Failed to delete sensor', 'error');
                }
            }
        },
        {
            text: 'Cancel',
            action: closeModal
        }
    ]);
}

/**
 * Quick actions
 */
function downloadSensorData() {
    showNotification('Download feature coming soon', 'info');
}

function viewSensorHistory() {
    showNotification('History view coming soon', 'info');
}

function generateReport() {
    showNotification('Report generation coming soon', 'info');
}

function viewImage(imageName) {
    showNotification('Image viewer coming soon', 'info');
}
