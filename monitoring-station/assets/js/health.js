/**
 * Health Page Logic
 * Manages sensor health tracking and health logs
 */

class HealthManager {
    constructor() {
        this.sensors = [];
        this.healthLogs = [];
        this.filteredLogs = [];
        this.filters = {
            area: '',
            health: ''
        };
        this.init();
    }

    async init() {
        await this.loadData();
        this.populateFilterOptions();
        this.renderHealthSummary();
        this.renderHealthLogs();
    }

    async loadData() {
        try {
            const [sensorsRes, healthRes] = await Promise.all([
                apiService.getSensors(),
                apiService.getHealthLogs()
            ]);

            if (sensorsRes.success) {
                this.sensors = sensorsRes.data || [];
            }

            if (healthRes.success) {
                this.healthLogs = healthRes.data || [];
                this.filteredLogs = [...this.healthLogs];
            }
        } catch (error) {
            console.error('Error loading health data:', error);
            showNotification('Failed to load health data', 'error');
        }
    }

    populateFilterOptions() {
        // Populate area filter
        const areas = [...new Set(this.sensors.map(s => s.area))];
        const areaFilter = document.getElementById('health-area-filter');
        if (areaFilter) {
            areas.forEach(area => {
                const option = document.createElement('option');
                option.value = area;
                option.textContent = area;
                areaFilter.appendChild(option);
            });
        }
    }

    renderHealthSummary() {
        // Count sensors by health status
        const goodCount = this.sensors.filter(s => s.health === 'good').length;
        const needsAttention = this.sensors.filter(s => s.health === 'needs_attention').length;
        const offline = this.sensors.filter(s => s.health === 'offline').length;

        document.getElementById('good-count').textContent = goodCount;
        document.getElementById('maintenance-count').textContent = needsAttention;
        document.getElementById('offline-count').textContent = offline;
    }

    renderHealthLogs() {
        const container = document.getElementById('health-logs-container');

        if (this.filteredLogs.length === 0) {
            container.innerHTML = `
                <div class="empty-state" style="padding: 2rem;">
                    <svg viewBox="0 0 24 24" fill="currentColor" style="width: 60px; height: 60px; opacity: 0.3;">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm3.5-9c.83 0 1.5-.67 1.5-1.5S16.33 8 15.5 8 14 8.67 14 9.5s.67 1.5 1.5 1.5zm-7 0c.83 0 1.5-.67 1.5-1.5S9.33 8 8.5 8 7 8.67 7 9.5 7.67 11 8.5 11zm3.5 6.5c2.33 0 4.31-1.46 5.11-3.5H6.89c.8 2.04 2.78 3.5 5.11 3.5z"/>
                    </svg>
                    <h3>No health logs available</h3>
                    <p>Add a health log to track sensor maintenance and status</p>
                </div>
            `;
            return;
        }

        // Group logs by area
        const grouped = {};
        this.filteredLogs.forEach(log => {
            const sensor = this.sensors.find(s => s.id === log.sensorId);
            if (sensor) {
                if (!grouped[sensor.area]) {
                    grouped[sensor.area] = [];
                }
                grouped[sensor.area].push({ ...log, sensorArea: sensor.area });
            }
        });

        const html = Object.entries(grouped).map(([area, logs]) => {
            const logRows = logs.map(log => {
                const sensor = this.sensors.find(s => s.id === log.sensorId);
                const healthClass = ApiService.getHealthStatusClass(log.status);

                return `
                    <tr>
                        <td>${log.sensorId}</td>
                        <td>
                            <span class="badge ${healthClass}">${log.status.replace(/_/g, ' ')}</span>
                        </td>
                        <td>${formatDateOnly(log.date)}</td>
                        <td>${log.note || '-'}</td>
                    </tr>
                `;
            }).join('');

            return `
                <div style="margin-bottom: 1.5rem;">
                    <h4 style="margin-bottom: 1rem; color: var(--color-accent);">${area}</h4>
                    <table class="data-table" style="width: 100%; margin: 0;">
                        <thead>
                            <tr>
                                <th>Sensor ID</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${logRows}
                        </tbody>
                    </table>
                </div>
            `;
        }).join('');

        container.innerHTML = html;
    }

    applyFilters() {
        this.filteredLogs = this.healthLogs.filter(log => {
            const sensor = this.sensors.find(s => s.id === log.sensorId);
            if (!sensor) return false;

            if (this.filters.area && sensor.area !== this.filters.area) return false;
            if (this.filters.health && log.status !== this.filters.health) return false;

            return true;
        });

        this.renderHealthLogs();
    }

    resetFilters() {
        this.filters = { area: '', health: '' };
        document.getElementById('health-area-filter').value = '';
        document.getElementById('health-status-filter').value = '';
        this.filteredLogs = [...this.healthLogs];
        this.renderHealthLogs();
    }
}

// Initialize
let healthManager = null;

document.addEventListener('DOMContentLoaded', () => {
    healthManager = new HealthManager();
});

/**
 * Apply filters
 */
function applyHealthFilters() {
    if (!healthManager) return;

    healthManager.filters.area = document.getElementById('health-area-filter').value;
    healthManager.filters.health = document.getElementById('health-status-filter').value;

    healthManager.applyFilters();
}

/**
 * Reset filters
 */
function resetHealthFilters() {
    if (!healthManager) return;
    healthManager.resetFilters();
}

/**
 * Open add health log modal
 */
function openAddHealthLogModal() {
    if (!healthManager) return;

    // Create sensor options
    const sensorOptions = healthManager.sensors.map(s => 
        `<option value="${s.id}">${s.id} - ${s.area}</option>`
    ).join('');

    showModal('Add Health Log', `
        <div class="form-group">
            <label>Sensor *</label>
            <select class="form-select" id="health-sensor-id">
                <option value="">Select Sensor</option>
                ${sensorOptions}
            </select>
        </div>
        <div class="form-group">
            <label>Health Status *</label>
            <select class="form-select" id="health-status">
                <option value="">Select Status</option>
                <option value="good">Good</option>
                <option value="needs_attention">Needs Attention</option>
                <option value="offline">Offline</option>
            </select>
        </div>
        <div class="form-group">
            <label>Notes</label>
            <textarea class="form-textarea" id="health-note" placeholder="Enter health notes..."></textarea>
        </div>
    `, [
        {
            text: 'Add Log',
            action: addHealthLog
        },
        {
            text: 'Cancel',
            action: closeModal
        }
    ]);
}

/**
 * Add new health log
 */
async function addHealthLog() {
    const sensorId = document.getElementById('health-sensor-id').value;
    const status = document.getElementById('health-status').value;
    const note = document.getElementById('health-note').value;

    // Validate
    if (!sensorId || !status) {
        showNotification('Please select sensor and health status', 'error');
        return;
    }

    try {
        const response = await apiService.addHealthLog(sensorId, status, note);
        if (response.success) {
            closeModal();
            showNotification('Health log added successfully', 'success');
            await healthManager.loadData();
            healthManager.renderHealthSummary();
            healthManager.renderHealthLogs();
        }
    } catch (error) {
        console.error('Error adding health log:', error);
        showNotification('Failed to add health log', 'error');
    }
}
