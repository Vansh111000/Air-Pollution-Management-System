/**
 * Data Service
 * Abstraction layer for API calls with mock data fallback
 * 
 * USE_MOCK_DATA: toggle between mock data and actual API
 */

const USE_MOCK_DATA = true; // Set to false to use PHP APIs

class DataService {
    constructor() {
        this.baseUrl = '/api';
        this.useMock = USE_MOCK_DATA;
        this.mockService = MockDataService;
    }

    /**
     * Fetch all sensors
     */
    async getSensors(filters = {}) {
        try {
            if (this.useMock) {
                return await this.mockService.getSensors();
            }

            const query = new URLSearchParams(filters).toString();
            const response = await fetch(`${this.baseUrl}/fetch_sensors.php?${query}`);
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Error fetching sensors:', error);
            // Fallback to mock data
            return await this.mockService.getSensors();
        }
    }

    /**
     * Get single sensor by ID
     */
    async getSensorById(id) {
        try {
            if (this.useMock) {
                return await this.mockService.getSensorById(id);
            }

            const response = await fetch(`${this.baseUrl}/fetch_sensors.php?id=${id}`);
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Error fetching sensor:', error);
            return await this.mockService.getSensorById(id);
        }
    }

    /**
     * Add new sensor
     */
    async addSensor(sensorData) {
        try {
            // Client-side validation
            if (!sensorData.id || !sensorData.area) {
                return {
                    success: false,
                    error: 'sensor_id and area are required'
                };
            }

            if (this.useMock) {
                return await this.mockService.addSensor(sensorData);
            }

            const response = await fetch(`${this.baseUrl}/add_sensor.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(sensorData)
            });

            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Error adding sensor:', error);
            return await this.mockService.addSensor(sensorData);
        }
    }

    /**
     * Update sensor
     */
    async updateSensor(id, updates) {
        try {
            if (this.useMock) {
                return await this.mockService.updateSensor(id, updates);
            }

            const response = await fetch(`${this.baseUrl}/update_sensor.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id, ...updates })
            });

            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Error updating sensor:', error);
            return await this.mockService.updateSensor(id, updates);
        }
    }

    /**
     * Delete sensor
     */
    async deleteSensor(id) {
        try {
            if (this.useMock) {
                return await this.mockService.deleteSensor(id);
            }

            const response = await fetch(`${this.baseUrl}/delete_sensor.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id })
            });

            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Error deleting sensor:', error);
            return await this.mockService.deleteSensor(id);
        }
    }

    /**
     * Get analytics
     */
    async getAnalytics() {
        try {
            if (this.useMock) {
                return await this.mockService.getAnalytics();
            }

            const response = await fetch(`${this.baseUrl}/fetch_analytics.php`);
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Error fetching analytics:', error);
            return await this.mockService.getAnalytics();
        }
    }

    /**
     * Get health logs
     */
    async getHealthLogs(filters = {}) {
        try {
            if (this.useMock) {
                return await this.mockService.getHealthLogs();
            }

            const query = new URLSearchParams(filters).toString();
            const response = await fetch(`${this.baseUrl}/fetch_health_logs.php?${query}`);
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Error fetching health logs:', error);
            return await this.mockService.getHealthLogs();
        }
    }

    /**
     * Add health log
     */
    async addHealthLog(sensorId, status, note) {
        try {
            if (this.useMock) {
                return await this.mockService.addHealthLog(sensorId, status, note);
            }

            const response = await fetch(`${this.baseUrl}/add_health_log.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ sensorId, status, note })
            });

            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Error adding health log:', error);
            return await this.mockService.addHealthLog(sensorId, status, note);
        }
    }

    /**
     * Utility: Get AQI status
     */
    static getAqiStatus(value) {
        if (value <= 50) return 'good';
        if (value <= 100) return 'moderate';
        if (value <= 200) return 'poor';
        return 'severe';
    }

    /**
     * Utility: Get health status badge class
     */
    static getHealthStatusClass(status) {
        const statusMap = {
            'good': 'badge-success',
            'needs_attention': 'badge-warning',
            'offline': 'badge-danger'
        };
        return statusMap[status] || 'badge-info';
    }

    /**
     * Utility: Get status indicator class
     */
    static getStatusIndicatorClass(status) {
        const statusMap = {
            'monitoring': 'active',
            'maintenance': 'idle',
            'on_hold': 'offline'
        };
        return statusMap[status] || 'active';
    }
}

// Create global instance
const dataService = new DataService();
