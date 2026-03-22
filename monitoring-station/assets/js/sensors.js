/**
 * Sensors Page Logic
 * Handles sensor listing, filtering, and CRUD operations
 */

class SensorsManager {
    constructor() {
        this.sensors = [];
        this.filteredSensors = [];
        this.filters = {
            area: '',
            type: '',
            status: ''
        };
        this.init();
    }

    async init() {
        await this.loadSensors();
        this.renderSensors();
        this.populateFilterOptions();
    }

    async loadSensors() {
        try {
            const response = await apiService.getSensors();
            if (response.success) {
                this.sensors = response.data || [];
                this.filteredSensors = [...this.sensors];
            }
        } catch (error) {
            console.error('Error loading sensors:', error);
            showNotification('Failed to load sensors', 'error');
        }
    }

    populateFilterOptions() {
        // Populate area filter
        const areas = [...new Set(this.sensors.map(s => s.area))];
        const areaFilter = document.getElementById('area-filter');
        if (areaFilter) {
            areas.forEach(area => {
                const option = document.createElement('option');
                option.value = area;
                option.textContent = area;
                areaFilter.appendChild(option);
            });
        }
    }

    renderSensors() {
        const container = document.getElementById('sensors-container');
        if (!container) return;

        if (this.filteredSensors.length === 0) {
            container.innerHTML = `
                <div class="empty-state">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <circle cx="12" cy="12" r="10" fill="none" stroke="currentColor" stroke-width="2"/>
                        <circle cx="12" cy="12" r="3" fill="currentColor"/>
                        <path d="M12 6V4M12 20v-2M6 12H4m16 0h-2M7 7L5.6 5.6M18.4 18.4L17 17M7 17L5.6 18.4M18.4 5.6L17 7"/>
                    </svg>
                    <h3>No sensors found</h3>
                    <p>Try adjusting your filters or add a new sensor</p>
                </div>
            `;
            return;
        }

        const html = this.filteredSensors.map(sensor => {
            const statusClass = ApiService.getStatusIndicatorClass(sensor.status);
            const healthClass = ApiService.getHealthStatusClass(sensor.health);
            const reading = sensor.lastReading;

            return `
                <div class="card-row" style="margin-bottom: 1rem;">
                    <div class="card-row-content" onclick="openSensorDetail('${sensor.id}')" style="cursor: pointer;">
                        <div class="card-row-item">
                            <div class="card-row-label">Sensor ID</div>
                            <div class="card-row-value">${sensor.id}</div>
                        </div>
                        <div class="card-row-item">
                            <div class="card-row-label">Location</div>
                            <div class="card-row-value">${sensor.area}</div>
                        </div>
                        <div class="card-row-item">
                            <div class="card-row-label">Type</div>
                            <div class="card-row-value">${sensor.type}</div>
                        </div>
                        <div class="card-row-item">
                            <div class="card-row-label">Status</div>
                            <div class="card-row-value">
                                <span class="status-indicator ${statusClass}"></span>
                                ${sensor.status.replace(/_/g, ' ').toUpperCase()}
                            </div>
                        </div>
                        ${reading && reading.aqi ? `
                        <div class="card-row-item">
                            <div class="card-row-label">Current AQI</div>
                            <div class="card-row-value" style="color: ${this.getAqiColor(reading.aqi)};">
                                ${reading.aqi}
                            </div>
                        </div>
                        ` : ''}
                    </div>
                    <div class="card-row-actions">
                        <span class="badge ${healthClass}">${sensor.health.replace(/_/g, ' ')}</span>
                        <button class="btn btn-sm btn-secondary" onclick="editSensor('${sensor.id}')" title="Edit">
                            <svg viewBox="0 0 24 24" fill="currentColor" style="width: 16px; height: 16px;">
                                <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25z"/>
                                <path d="M20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
                            </svg>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="deleteSensor('${sensor.id}')" title="Delete">
                            <svg viewBox="0 0 24 24" fill="currentColor" style="width: 16px; height: 16px;">
                                <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-9l-1 1H5v2h14V4z"/>
                            </svg>
                        </button>
                    </div>
                </div>
            `;
        }).join('');

        container.innerHTML = html;
    }

    getAqiColor(value) {
        if (value <= 50) return 'var(--color-aqi-good)';
        if (value <= 100) return 'var(--color-aqi-moderate)';
        if (value <= 200) return 'var(--color-aqi-poor)';
        return 'var(--color-aqi-severe)';
    }

    applyFilters() {
        this.filteredSensors = this.sensors.filter(sensor => {
            if (this.filters.area && sensor.area !== this.filters.area) return false;
            if (this.filters.type && sensor.type !== this.filters.type) return false;
            if (this.filters.status && sensor.status !== this.filters.status) return false;
            return true;
        });

        this.renderSensors();
    }

    resetFilters() {
        this.filters = { area: '', type: '', status: '' };
        document.getElementById('area-filter').value = '';
        document.getElementById('type-filter').value = '';
        document.getElementById('status-filter').value = '';
        this.filteredSensors = [...this.sensors];
        this.renderSensors();
    }
}

// Initialize sensors manager
let sensorsManager = null;

document.addEventListener('DOMContentLoaded', () => {
    sensorsManager = new SensorsManager();
});

/**
 * Apply filter changes
 */
function applySensorFilters() {
    if (!sensorsManager) return;

    sensorsManager.filters.area = document.getElementById('area-filter').value;
    sensorsManager.filters.type = document.getElementById('type-filter').value;
    sensorsManager.filters.status = document.getElementById('status-filter').value;

    sensorsManager.applyFilters();
}

/**
 * Reset all filters
 */
function resetSensorFilters() {
    if (!sensorsManager) return;
    sensorsManager.resetFilters();
}

/**
 * Open "Add Sensor" modal
 */
function openAddSensorModal() {
    showModal('Add New Sensor', `
        <div class="form-group">
            <label>Sensor ID *</label>
            <input type="text" class="form-input" id="new-sensor-id" placeholder="e.g., SEN-009">
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Type *</label>
                <select class="form-select" id="new-sensor-type">
                    <option value="">Select Type</option>
                    <option value="AQI">AQI</option>
                    <option value="Temperature">Temperature</option>
                    <option value="Humidity">Humidity</option>
                </select>
            </div>
            <div class="form-group">
                <label>Status *</label>
                <select class="form-select" id="new-sensor-status">
                    <option value="monitoring">Monitoring</option>
                    <option value="maintenance">Maintenance</option>
                    <option value="on_hold">On Hold</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label>Area *</label>
            <input type="text" class="form-input" id="new-sensor-area" placeholder="e.g., Mumbai">
        </div>
        <div class="form-group">
            <label>Specific Location *</label>
            <input type="text" class="form-input" id="new-sensor-location" placeholder="e.g., Sector 1, MIDC">
        </div>
        <div class="form-group">
            <label>Health Status</label>
            <select class="form-select" id="new-sensor-health">
                <option value="good">Good</option>
                <option value="needs_attention">Needs Attention</option>
                <option value="offline">Offline</option>
            </select>
        </div>
        <div class="form-group">
            <label>Images</label>
            <input type="file" id="sensor-images" class="form-input" multiple accept="image/*">
            <small style="color: var(--color-text-light); display: block; margin-top: 0.5rem;">
                Upload multiple images for reference
            </small>
        </div>
    `, [
        {
            text: 'Add Sensor',
            action: addNewSensor
        },
        {
            text: 'Cancel',
            action: closeModal
        }
    ]);
}

/**
 * Add new sensor
 */
async function addNewSensor() {
    const sensorData = {
        id: document.getElementById('new-sensor-id').value,
        type: document.getElementById('new-sensor-type').value,
        status: document.getElementById('new-sensor-status').value,
        area: document.getElementById('new-sensor-area').value,
        location: document.getElementById('new-sensor-location').value,
        health: document.getElementById('new-sensor-health').value,
        images: []
    };

    // Validate required fields
    const validationErrors = validateForm(sensorData, {
        id: { required: true },
        type: { required: true },
        status: { required: true },
        area: { required: true },
        location: { required: true }
    });

    if (Object.keys(validationErrors).length > 0) {
        showValidationErrors(validationErrors);
        return;
    }

    // Check if sensor ID already exists
    if (sensorsManager.sensors.find(s => s.id === sensorData.id)) {
        showNotification('Sensor ID already exists', 'error');
        return;
    }

    try {
        const response = await apiService.addSensor(sensorData);
        if (response.success) {
            // Handle image upload if any
            const imageInput = document.getElementById('sensor-images');
            if (imageInput && imageInput.files.length > 0) {
                const formData = new FormData();
                formData.append('sensor_id', sensorData.id);
                for (let i = 0; i < imageInput.files.length; i++) {
                    formData.append('images[]', imageInput.files[i]);
                }
                const uploadRes = await apiService.uploadImages(formData);
                if (!uploadRes.success) {
                    showNotification('Sensor added but images failed to upload', 'warning');
                }
            }

            closeModal();
            showNotification('Sensor added successfully', 'success');
            await sensorsManager.loadSensors();
            sensorsManager.renderSensors();
        } else {
            showNotification(response.error || 'Failed to add sensor', 'error');
        }
    } catch (error) {
        console.error('Error adding sensor:', error);
        showNotification('Failed to add sensor', 'error');
    }
}

/**
 * Edit sensor
 */
function editSensor(sensorId) {
    const sensor = sensorsManager.sensors.find(s => s.id === sensorId);
    if (!sensor) return;

    showModal(`Edit Sensor: ${sensor.id}`, `
        <div class="form-group">
            <label>Sensor ID</label>
            <input type="text" class="form-input" value="${sensor.id}" disabled>
        </div>
        <div class="form-group">
            <label>Status</label>
            <select class="form-select" id="edit-sensor-status">
                <option value="monitoring" ${sensor.status === 'monitoring' ? 'selected' : ''}>Monitoring</option>
                <option value="maintenance" ${sensor.status === 'maintenance' ? 'selected' : ''}>Maintenance</option>
                <option value="on_hold" ${sensor.status === 'on_hold' ? 'selected' : ''}>On Hold</option>
            </select>
        </div>
        <div class="form-group">
            <label>Area</label>
            <input type="text" class="form-input" id="edit-sensor-area" value="${sensor.area}">
        </div>
        <div class="form-group">
            <label>Location</label>
            <input type="text" class="form-input" id="edit-sensor-location" value="${sensor.location}">
        </div>
        <div class="form-group">
            <label>Health Status</label>
            <select class="form-select" id="edit-sensor-health">
                <option value="good" ${sensor.health === 'good' ? 'selected' : ''}>Good</option>
                <option value="needs_attention" ${sensor.health === 'needs_attention' ? 'selected' : ''}>Needs Attention</option>
                <option value="offline" ${sensor.health === 'offline' ? 'selected' : ''}>Offline</option>
            </select>
        </div>
    `, [
        {
            text: 'Save Changes',
            action: async () => {
                const updates = {
                    status: document.getElementById('edit-sensor-status').value,
                    area: document.getElementById('edit-sensor-area').value,
                    location: document.getElementById('edit-sensor-location').value,
                    health: document.getElementById('edit-sensor-health').value
                };

                try {
                    // console.log("updates ", updates);
                    const response = await apiService.updateSensor(sensorId, updates);
                    if (response.success) {
                        closeModal();
                        showNotification('Sensor updated successfully', 'success');
                        await sensorsManager.loadSensors();
                        sensorsManager.renderSensors();
                    }
                } catch (error) {
                    showNotification('Failed to update sensor', 'error');
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
 * Delete sensor
 */
async function deleteSensor(sensorId) {
    const sensor = sensorsManager.sensors.find(s => s.id === sensorId);
    if (!sensor) return;

    showModal('Confirm Delete', `
        <p>Are you sure you want to delete sensor <strong>${sensor.id}</strong>?</p>
        <p style="color: var(--color-danger); margin-top: 1rem;">This action cannot be undone.</p>
    `, [
        {
            text: 'Delete',
            action: async () => {
                try {
                    const response = await apiService.deleteSensor(sensorId);
                    if (response.success) {
                        closeModal();
                        showNotification('Sensor deleted successfully', 'success');
                        await sensorsManager.loadSensors();
                        sensorsManager.renderSensors();
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
 * Open sensor detail page
 */
function openSensorDetail(sensorId) {
    navigateTo(`sensor-detail&id=${sensorId}`);
}
