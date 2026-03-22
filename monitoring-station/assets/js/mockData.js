/**
 * Mock Data Service
 * Provides realistic test data for the monitoring station
 * Structure mirrors actual backend API responses
 */

const MockDataService = {
    /**
     * Mock Sensors Data
     */
    sensors: [
        {
            id: 'SEN-001',
            name: 'Sector 1 - Industrial',
            area: 'Mumbai',
            location: 'Sector 1, MIDC Industrial Area',
            type: 'AQI',
            status: 'monitoring',
            health: 'good',
            lastReading: {
                aqi: 62,
                pm25: 35,
                pm10: 45,
                temperature: 32,
                humidity: 65,
                timestamp: new Date(Date.now() - 3600000).toISOString()
            },
            uptime: 99.8,
            images: ['img1.jpg', 'img2.jpg'],
            totalReadings: 2880
        },
        {
            id: 'SEN-002',
            name: 'Downtown Metro Station',
            area: 'Delhi',
            location: 'Central Delhi Metro Station',
            type: 'AQI',
            status: 'monitoring',
            health: 'good',
            lastReading: {
                aqi: 85,
                pm25: 48,
                pm10: 72,
                temperature: 28,
                humidity: 58,
                timestamp: new Date(Date.now() - 1800000).toISOString()
            },
            uptime: 99.5,
            images: ['img3.jpg'],
            totalReadings: 2880
        },
        {
            id: 'SEN-003',
            name: 'Park & Green Zone',
            area: 'Bangalore',
            location: 'Lalbagh Garden Area',
            type: 'AQI',
            status: 'monitoring',
            health: 'good',
            lastReading: {
                aqi: 45,
                pm25: 22,
                pm10: 32,
                temperature: 26,
                humidity: 72,
                timestamp: new Date(Date.now() - 2400000).toISOString()
            },
            uptime: 99.9,
            images: [],
            totalReadings: 2880
        },
        {
            id: 'SEN-004',
            name: 'Highway Traffic Zone',
            area: 'Mumbai',
            location: 'Western Express Highway',
            type: 'AQI',
            status: 'maintenance',
            health: 'needs_attention',
            lastReading: {
                aqi: 156,
                pm25: 89,
                pm10: 145,
                temperature: 34,
                humidity: 45,
                timestamp: new Date(Date.now() - 86400000).toISOString()
            },
            uptime: 85.3,
            images: ['img4.jpg', 'img5.jpg'],
            totalReadings: 2700
        },
        {
            id: 'SEN-005',
            name: 'Residential Area',
            area: 'Pune',
            location: 'Koregaon Park',
            type: 'Temperature',
            status: 'monitoring',
            health: 'good',
            lastReading: {
                temperature: 29,
                humidity: 62,
                timestamp: new Date(Date.now() - 1200000).toISOString()
            },
            uptime: 98.2,
            images: [],
            totalReadings: 2880
        },
        {
            id: 'SEN-006',
            name: 'Port Area Sensor',
            area: 'Mumbai',
            location: 'Mumbai Port Trust Area',
            type: 'AQI',
            status: 'on_hold',
            health: 'offline',
            lastReading: null,
            uptime: 0,
            images: ['img6.jpg'],
            totalReadings: 1200
        },
        {
            id: 'SEN-007',
            name: 'IT Park Monitoring',
            area: 'Bangalore',
            location: 'Whitefield Tech Park',
            type: 'AQI',
            status: 'monitoring',
            health: 'good',
            lastReading: {
                aqi: 52,
                pm25: 28,
                pm10: 38,
                temperature: 24,
                humidity: 68,
                timestamp: new Date(Date.now() - 600000).toISOString()
            },
            uptime: 99.7,
            images: [],
            totalReadings: 2880
        },
        {
            id: 'SEN-008',
            name: 'Coastal Area Sensor',
            area: 'Chennai',
            location: 'Marina Beach',
            type: 'AQI',
            status: 'monitoring',
            health: 'good',
            lastReading: {
                aqi: 68,
                pm25: 38,
                pm10: 52,
                temperature: 31,
                humidity: 78,
                timestamp: new Date(Date.now() - 900000).toISOString()
            },
            uptime: 99.6,
            images: [],
            totalReadings: 2880
        }
    ],

    /**
     * Mock Health Logs
     */
    healthLogs: [
        {
            id: 'HEALTH-001',
            sensorId: 'SEN-001',
            date: new Date(Date.now() - 172800000).toISOString().split('T')[0],
            status: 'good',
            note: 'Regular maintenance completed'
        },
        {
            id: 'HEALTH-002',
            sensorId: 'SEN-004',
            date: new Date(Date.now() - 86400000).toISOString().split('T')[0],
            status: 'maintenance',
            note: 'Sensor calibration required, scheduled for next week'
        },
        {
            id: 'HEALTH-003',
            sensorId: 'SEN-002',
            date: new Date(Date.now() - 259200000).toISOString().split('T')[0],
            status: 'good',
            note: 'Replaced battery pack'
        },
        {
            id: 'HEALTH-004',
            sensorId: 'SEN-006',
            date: new Date(Date.now() - 345600000).toISOString().split('T')[0],
            status: 'offline',
            note: 'Sensor offline for 15 days, pending repair'
        },
        {
            id: 'HEALTH-005',
            sensorId: 'SEN-005',
            date: new Date().toISOString().split('T')[0],
            status: 'good',
            note: 'Temperature sensor reading stable'
        }
    ],

    /**
     * Mock Dashboard Analytics
     */
    analytics: {
        hourlyAqi: [
            { hour: '00:00', value: 65, status: 'moderate' },
            { hour: '01:00', value: 62, status: 'moderate' },
            { hour: '02:00', value: 58, status: 'moderate' },
            { hour: '03:00', value: 55, status: 'moderate' },
            { hour: '04:00', value: 52, status: 'moderate' },
            { hour: '05:00', value: 48, status: 'good' },
            { hour: '06:00', value: 52, status: 'moderate' },
            { hour: '07:00', value: 68, status: 'moderate' },
            { hour: '08:00', value: 85, status: 'moderate' },
            { hour: '09:00', value: 92, status: 'moderate' },
            { hour: '10:00', value: 98, status: 'moderate' },
            { hour: '11:00', value: 105, status: 'moderate' },
            { hour: '12:00', value: 112, status: 'poor' },
            { hour: '13:00', value: 118, status: 'poor' },
            { hour: '14:00', value: 115, status: 'poor' },
            { hour: '15:00', value: 108, status: 'moderate' },
            { hour: '16:00', value: 102, status: 'moderate' },
            { hour: '17:00', value: 98, status: 'moderate' },
            { hour: '18:00', value: 95, status: 'moderate' },
            { hour: '19:00', value: 92, status: 'moderate' },
            { hour: '20:00', value: 85, status: 'moderate' },
            { hour: '21:00', value: 78, status: 'moderate' },
            { hour: '22:00', value: 72, status: 'moderate' },
            { hour: '23:00', value: 68, status: 'moderate' }
        ],
        
        dailyAqi: [
            { day: 'Mon', value: 78, status: 'moderate' },
            { day: 'Tue', value: 82, status: 'moderate' },
            { day: 'Wed', value: 75, status: 'moderate' },
            { day: 'Thu', value: 88, status: 'moderate' },
            { day: 'Fri', value: 92, status: 'moderate' },
            { day: 'Sat', value: 68, status: 'moderate' },
            { day: 'Sun', value: 62, status: 'moderate' }
        ],

        summary: {
            avgAqi: 81,
            avgTemperature: 45.5,
            avgHumidity: 64.3,
            activeCount: 7,
            maintenanceCount: 1,
            offlineCount: 1
        }
    },

    /**
     * Get all sensors
     */
    getSensors() {
        return Promise.resolve({
            success: true,
            data: this.sensors
        });
    },

    /**
     * Get single sensor by ID
     */
    getSensorById(id) {
        const sensor = this.sensors.find(s => s.id === id);
        return Promise.resolve({
            success: !!sensor,
            data: sensor || null
        });
    },

    /**
     * Add new sensor
     */
    addSensor(sensorData) {
        // Validate unique sensor_id
        if (this.sensors.find(s => s.id === sensorData.id)) {
            return Promise.resolve({
                success: false,
                error: 'Sensor ID already exists'
            });
        }

        const newSensor = {
            id: sensorData.id,
            name: sensorData.name || sensorData.id,
            area: sensorData.area,
            location: sensorData.location,
            type: sensorData.type,
            status: sensorData.status || 'monitoring',
            health: sensorData.health || 'good',
            lastReading: null,
            uptime: 0,
            images: sensorData.images || [],
            totalReadings: 0
        };

        this.sensors.push(newSensor);
        return Promise.resolve({
            success: true,
            data: newSensor
        });
    },

    /**
     * Update sensor
     */
    updateSensor(id, updates) {
        const index = this.sensors.findIndex(s => s.id === id);
        if (index === -1) {
            return Promise.resolve({
                success: false,
                error: 'Sensor not found'
            });
        }

        this.sensors[index] = { ...this.sensors[index], ...updates };
        return Promise.resolve({
            success: true,
            data: this.sensors[index]
        });
    },

    /**
     * Delete sensor
     */
    deleteSensor(id) {
        const index = this.sensors.findIndex(s => s.id === id);
        if (index === -1) {
            return Promise.resolve({
                success: false,
                error: 'Sensor not found'
            });
        }

        const deleted = this.sensors.splice(index, 1);
        return Promise.resolve({
            success: true,
            data: deleted[0]
        });
    },

    /**
     * Get dashboard analytics
     */
    getAnalytics() {
        return Promise.resolve({
            success: true,
            data: this.analytics
        });
    },

    /**
     * Get health logs
     */
    getHealthLogs() {
        return Promise.resolve({
            success: true,
            data: this.healthLogs
        });
    },

    /**
     * Add health log
     */
    addHealthLog(sensorId, status, note) {
        const log = {
            id: `HEALTH-${Date.now()}`,
            sensorId,
            date: new Date().toISOString().split('T')[0],
            status,
            note
        };

        this.healthLogs.push(log);
        return Promise.resolve({
            success: true,
            data: log
        });
    }
};
