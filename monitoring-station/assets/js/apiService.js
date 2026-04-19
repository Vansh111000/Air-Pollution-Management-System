const API_BASE = "../api";

class ApiService {
    constructor() {
        this.baseUrl = API_BASE;
    }

    async _fetch(url, options = {}) {
        console.log(`[ApiService] Fetching: ${url}`);
        try {
            const response = await fetch(url, options);
            if (!response.ok) {
                console.error(`[ApiService] HTTP Error: ${response.status}`);
            }
            
            const text = await response.text();
            let data;
            try {
                data = JSON.parse(text);
            } catch (e) {
                console.error('[ApiService] JSON Parse Error. Response text:', text);
                throw new Error("Invalid format from server");
            }
            
            console.log(`[ApiService] Response for ${url}:`, data);
            
            if (!data.success) {
                throw new Error(data.error || 'API Error');
            }
            return data;
        } catch (error) {
            console.error('[ApiService] Fetch Error:', error);
            return {
                success: false,
                error: error.message
            };
        }
    }

    _mapSensor(s) {
        if (!s) return null;
        return {
            id: s.sensor_id,
            name: s.name || s.sensor_id,
            area: s.area_name || s.area_id || 'Unknown',
            location: s.location || '',
            type: s.type || 'AQI',
            status: s.status || 'monitoring',
            health: s.health || 'good',
            lastReading: s.latest_recorded_at ? {
                aqi: s.latest_aqi !== null ? s.latest_aqi : undefined,
                pm25: s.latest_pm25 !== null ? s.latest_pm25 : undefined,
                pm10: s.latest_pm10 !== null ? s.latest_pm10 : undefined,
                temperature: s.latest_temperature !== null ? s.latest_temperature : undefined,
                humidity: s.latest_humidity !== null ? s.latest_humidity : undefined,
                timestamp: s.latest_recorded_at
            } : null,
            uptime: parseFloat(s.uptime) || 0,
            images: s.image_ids ? s.image_ids.split(',') : [],
            totalReadings: parseInt(s.computed_total_readings) || 0
        };
    }

    async getSensors(filters = {}) {
        const query = new URLSearchParams(filters).toString();
        const url = `${this.baseUrl}/sensors/get_all.php${query ? '?' + query : ''}`;
        const data = await this._fetch(url);
        
        if (data && data.success) {
            return {
                success: true,
                data: (data.data || []).map(this._mapSensor)
            };
        }
        return data;
    }

    async getSensorById(id) {
        const url = `${this.baseUrl}/sensors/get_one.php?sensor_id=${encodeURIComponent(id)}`;
        const data = await this._fetch(url);
        if (data && data.success) {
            return {
                success: true,
                data: this._mapSensor(data.data)
            };
        }
        return data;
    }

    async addSensor(sensorData) {
        const payload = {
            sensor_id: sensorData.id,
            name: sensorData.name,
            location: sensorData.location,
            type: sensorData.type,
            status: sensorData.status,
            health: sensorData.health
        };

        const url = `${this.baseUrl}/sensors/add.php`;
        return await this._fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });
    }

    async updateSensor(id, updates) {
        const payload = {
            sensor_id: id,
            ...updates
        };

        const url = `${this.baseUrl}/sensors/update.php`;
        return await this._fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });
    }

    async deleteSensor(id) {
        const url = `${this.baseUrl}/sensors/delete.php`;
        return await this._fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ sensor_id: id })
        });
    }

    async getAnalytics() {
        const sensorsData = await this.getSensors();
        if (!sensorsData || !sensorsData.success) {
            return { success: false, error: 'Failed to fetch analytics' };
        }

        const sensors = sensorsData.data;
        let activeCount = 0;
        let maintenanceCount = 0;
        let offlineCount = 0;
        let totalAqi = 0, aqiCount = 0;
        let totalTemp = 0, tempCount = 0;
        let totalHum = 0, humCount = 0;

        sensors.forEach(s => {
            if (s.status === 'monitoring') activeCount++;
            else if (s.status === 'maintenance') maintenanceCount++;
            else offlineCount++;

            if (s.lastReading) {
                if (s.lastReading.aqi) { totalAqi += parseInt(s.lastReading.aqi); aqiCount++; }
                if (s.lastReading.temperature) { totalTemp += parseFloat(s.lastReading.temperature); tempCount++; }
                if (s.lastReading.humidity) { totalHum += parseFloat(s.lastReading.humidity); humCount++; }
            }
        });

        let hourlyAqi = [];
        let dailyAqi = [];
        if (sensors.length > 0) {
            const firstSensor = sensors[0];
            const readingsHourlyReq = await this._fetch(`${this.baseUrl}/readings/get_by_sensor.php?sensor_id=${firstSensor.id}&aggregation=hourly`);
            if (readingsHourlyReq && readingsHourlyReq.success && readingsHourlyReq.data.length > 0) {
                hourlyAqi = readingsHourlyReq.data.map(r => ({
                    hour: r.recorded_hour.split(' ')[1].substring(0, 5),
                    value: r.aqi,
                    status: ApiService.getAqiStatus(r.aqi)
                })).slice(-24);
            }
            
            const readingsDailyReq = await this._fetch(`${this.baseUrl}/readings/get_by_sensor.php?sensor_id=${firstSensor.id}&aggregation=daily`);
            if (readingsDailyReq && readingsDailyReq.success && readingsDailyReq.data.length > 0) {
                const days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
                dailyAqi = readingsDailyReq.data.map(r => {
                    const d = new Date(r.recorded_date);
                    return {
                        day: days[d.getDay()],
                        value: r.aqi,
                        status: ApiService.getAqiStatus(r.aqi)
                    };
                }).slice(-7);
            }
        }

        if (hourlyAqi.length === 0) {
            for(let i=0; i<24; i++) hourlyAqi.push({hour: `${i.toString().padStart(2, '0')}:00`, value: 50, status: 'good'});
        }
        if (dailyAqi.length === 0) {
            ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'].forEach(d => dailyAqi.push({day: d, value: 50, status: 'good'}));
        }

        return {
            success: true,
            data: {
                hourlyAqi,
                dailyAqi,
                summary: {
                    avgAqi: aqiCount > 0 ? totalAqi / aqiCount : 0,
                    avgTemperature: tempCount > 0 ? totalTemp / tempCount : 0,
                    avgHumidity: humCount > 0 ? totalHum / humCount : 0,
                    activeCount,
                    maintenanceCount,
                    offlineCount
                }
            }
        };
    }

    async getHealthLogs(filters = {}) {
        const query = new URLSearchParams(filters).toString();
        const url = `${this.baseUrl}/health/get.php${query ? '?' + query : ''}`;
        const data = await this._fetch(url);
        
        if (data && data.success) {
            const flatLogs = [];
            for (const [area, logs] of Object.entries(data.data || {})) {
                logs.forEach(l => {
                    flatLogs.push({
                        id: l.log_id,
                        sensorId: l.sensor_id,
                        date: l.log_date,
                        status: l.status,
                        note: l.note
                    });
                });
            }
            return {
                success: true,
                data: flatLogs
            };
        }
        return data;
    }

    async addHealthLog(sensorId, status, note) {
        const url = `${this.baseUrl}/health/add.php`;
        return await this._fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ sensor_id: sensorId, status, note })
        });
    }

    async uploadImages(formData) {
        const url = `${this.baseUrl}/images/upload.php`;
        try {
            const response = await fetch(url, {
                method: 'POST',
                body: formData
            });
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Image upload failed:', error);
            return { success: false, error: error.message };
        }
    }

    static getAqiStatus(value) {
        if (!value) return 'good';
        if (value <= 50) return 'good';
        if (value <= 100) return 'moderate';
        if (value <= 200) return 'poor';
        return 'severe';
    }

    static getHealthStatusClass(status) {
        const statusMap = {
            'good': 'badge-success',
            'needs_attention': 'badge-warning',
            'offline': 'badge-danger'
        };
        return statusMap[status] || 'badge-info';
    }

    static getStatusIndicatorClass(status) {
        const statusMap = {
            'monitoring': 'active',
            'maintenance': 'idle',
            'on_hold': 'offline'
        };
        return statusMap[status] || 'active';
    }
}

const apiService = new ApiService();
