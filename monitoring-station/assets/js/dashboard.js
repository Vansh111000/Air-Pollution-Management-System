/**
 * Dashboard Page Logic
 * Fetches analytics data, renders charts, and updates status cards
 */

class DashboardManager {
    constructor() {
        this.analytics = null;
        this.sensors = null;
        this.charts = {};
        this.init();
    }

    async init() {
        await this.loadData();
        this.renderAnalytics();
        this.renderCharts();
        this.renderRecentSensors();
    }

    async loadData() {
        try {
            // Show loading state
            document.getElementById('recent-sensors-container').innerHTML = 
                '<div class="empty-state"><div class="spinner"></div><p>Loading data...</p></div>';

            // Fetch data
            const [analyticsRes, sensorsRes] = await Promise.all([
                apiService.getAnalytics(),
                apiService.getSensors()
            ]);

            if (analyticsRes.success) {
                this.analytics = analyticsRes.data;
            }

            if (sensorsRes.success) {
                this.sensors = sensorsRes.data;
            }
        } catch (error) {
            console.error('Error loading dashboard data:', error);
            showNotification('Failed to load dashboard data', 'error');
        }
    }

    renderAnalytics() {
        if (!this.analytics) return;

        const summary = this.analytics.summary;

        // AQI Card
        const aqiCard = document.getElementById('aqi-card-container');
        if (aqiCard) {
            aqiCard.innerHTML = createAqiCard(Math.round(summary.avgAqi), 'Average AQI');
        }

        // Temperature Card
        const tempCard = document.getElementById('temp-card-container');
        if (tempCard) {
            tempCard.innerHTML = `
                <div class="card" style="padding: 2rem; text-align: center;">
                    <div style="font-family: 'Teko', sans-serif; font-size: 2.5rem; color: var(--color-accent); font-weight: 700; margin-bottom: 0.5rem;">
                        ${summary.avgTemperature.toFixed(1)}°C
                    </div>
                    <div style="font-family: 'Rajdhani', sans-serif; font-weight: 600; color: var(--color-text-secondary);">
                        Average Temperature
                    </div>
                </div>
            `;
        }

        // Humidity Card
        const humidityCard = document.getElementById('humidity-card-container');
        if (humidityCard) {
            humidityCard.innerHTML = `
                <div class="card" style="padding: 2rem; text-align: center;">
                    <div style="font-family: 'Teko', sans-serif; font-size: 2.5rem; color: var(--color-accent); font-weight: 700; margin-bottom: 0.5rem;">
                        ${summary.avgHumidity.toFixed(1)}%
                    </div>
                    <div style="font-family: 'Rajdhani', sans-serif; font-weight: 600; color: var(--color-text-secondary);">
                        Average Humidity
                    </div>
                </div>
            `;
        }

        // Status counters
        document.getElementById('active-count').textContent = summary.activeCount;
        document.getElementById('maintenance-count').textContent = summary.maintenanceCount;
        document.getElementById('offline-count').textContent = summary.offlineCount;
    }

    renderCharts() {
        if (!this.analytics) return;

        this.renderHourlyChart();
        this.renderDailyChart();
    }

    renderHourlyChart() {
        const canvas = document.getElementById('hourly-chart');
        if (!canvas) return;

        const ctx = canvas.getContext('2d');
        const data = this.analytics.hourlyAqi;

        // Clear previous chart if exists
        if (this.charts.hourly) {
            this.charts.hourly.destroy();
        }

        // Prepare data
        const labels = data.map(d => d.hour);
        const values = data.map(d => d.value);
        const colors = data.map(d => {
            const colorMap = {
                'good': '#00c853',
                'moderate': '#ffd600',
                'poor': '#ff9100',
                'severe': '#d50000'
            };
            return colorMap[d.status] || '#00bcd4';
        });

        // Draw simple chart using canvas
        this.drawLineChart(ctx, labels, values, colors, 'Hourly AQI');
    }

    renderDailyChart() {
        const canvas = document.getElementById('daily-chart');
        if (!canvas) return;

        const ctx = canvas.getContext('2d');
        const data = this.analytics.dailyAqi;

        // Clear previous chart if exists
        if (this.charts.daily) {
            this.charts.daily.destroy();
        }

        // Prepare data
        const labels = data.map(d => d.day);
        const values = data.map(d => d.value);
        const colors = data.map(d => {
            const colorMap = {
                'good': '#00c853',
                'moderate': '#ffd600',
                'poor': '#ff9100',
                'severe': '#d50000'
            };
            return colorMap[d.status] || '#00bcd4';
        });

        this.drawBarChart(ctx, labels, values, colors, 'Daily AQI');
    }

    resizeCanvas(canvas) {
        // Native Container matching
        const rect = canvas.parentElement.getBoundingClientRect();
        canvas.width = rect.width;
        canvas.height = 250; // Fixed visual height
        const ctx = canvas.getContext('2d');
        ctx.scale(1, 1);
    }

    drawLineChart(ctx, labels, values, colors, title) {
        this.resizeCanvas(ctx.canvas);
        const padding = 40;
        const width = ctx.canvas.width - 2 * padding;
        const height = ctx.canvas.height - 2 * padding;

        const maxValue = Math.max(...values, 200);
        const minValue = 0;

        // Background
        ctx.fillStyle = 'transparent';
        ctx.fillRect(0, 0, ctx.canvas.width, ctx.canvas.height);

        // Draw grid
        ctx.strokeStyle = 'rgba(0, 0, 0, 0.05)';
        ctx.lineWidth = 1;
        for (let i = 0; i <= 5; i++) {
            const y = padding + (height / 5) * i;
            ctx.beginPath();
            ctx.moveTo(padding, y);
            ctx.lineTo(ctx.canvas.width - padding, y);
            ctx.stroke();
        }

        // Draw axis labels
        ctx.fillStyle = 'var(--color-text-secondary)';
        ctx.font = '12px Rajdhani';
        ctx.textAlign = 'right';
        for (let i = 0; i <= 5; i++) {
            const value = Math.round(maxValue - (maxValue / 5) * i);
            const y = padding + (height / 5) * i + 4;
            ctx.fillText(value, padding - 10, y);
        }

        // Draw data points and lines
        ctx.lineWidth = 2;
        ctx.strokeStyle = 'var(--color-accent)';

        // Calculate points
        const points = values.map((val, idx) => {
            const denom = Math.max(1, values.length - 1);
            const x = padding + (width / denom) * idx;
            const y = padding + height - (val / maxValue) * height;
            return { x, y, val, idx };
        });

        // Draw line
        ctx.beginPath();
        points.forEach((point, idx) => {
            if (idx === 0) ctx.moveTo(point.x, point.y);
            else ctx.lineTo(point.x, point.y);
        });
        ctx.stroke();

        // Draw points
        points.forEach((point, idx) => {
            ctx.fillStyle = colors[idx] || 'var(--color-accent)';
            ctx.beginPath();
            ctx.arc(point.x, point.y, 4, 0, Math.PI * 2);
            ctx.fill();

            // Draw time label
            ctx.fillStyle = 'var(--color-text-secondary)';
            ctx.font = '11px Rajdhani';
            ctx.textAlign = 'center';
            if (idx % 4 === 0) {
                ctx.fillText(labels[idx], point.x, ctx.canvas.height - 10);
            }
        });
    }

    drawBarChart(ctx, labels, values, colors, title) {
        this.resizeCanvas(ctx.canvas);
        const padding = 40;
        const width = ctx.canvas.width - 2 * padding;
        const height = ctx.canvas.height - 2 * padding;
        const barWidth = width / labels.length * 0.7;
        const barSpacing = width / labels.length;

        const maxValue = Math.max(...values, 200);

        // Background
        ctx.fillStyle = 'transparent';
        ctx.fillRect(0, 0, ctx.canvas.width, ctx.canvas.height);

        // Draw grid
        ctx.strokeStyle = 'rgba(0, 0, 0, 0.05)';
        ctx.lineWidth = 1;
        for (let i = 0; i <= 5; i++) {
            const y = padding + (height / 5) * i;
            ctx.beginPath();
            ctx.moveTo(padding, y);
            ctx.lineTo(ctx.canvas.width - padding, y);
            ctx.stroke();
        }

        // Draw axis labels
        ctx.fillStyle = 'var(--color-text-secondary)';
        ctx.font = '12px Rajdhani';
        ctx.textAlign = 'right';
        for (let i = 0; i <= 5; i++) {
            const value = Math.round(maxValue - (maxValue / 5) * i);
            const y = padding + (height / 5) * i + 4;
            ctx.fillText(value, padding - 10, y);
        }

        // Draw bars
        values.forEach((val, idx) => {
            const barHeight = (val / maxValue) * height;
            const denom = Math.max(1, labels.length);
            const barWidthFixed = width / denom * 0.7;
            const barSpacingFixed = width / denom;
            const x = padding + barSpacingFixed * idx + (barSpacingFixed - barWidthFixed) / 2;
            const y = padding + height - barHeight;

            ctx.fillStyle = colors[idx] || 'var(--color-accent)';
            ctx.fillRect(x, y, barWidth, barHeight);

            // Label
            ctx.fillStyle = 'var(--color-text-secondary)';
            ctx.font = '12px Rajdhani';
            ctx.textAlign = 'center';
            ctx.fillText(labels[idx], x + barWidth / 2, ctx.canvas.height - 10);
        });
    }

    renderRecentSensors() {
        const container = document.getElementById('recent-sensors-container');
        if (!container || !this.sensors) return;

        if (this.sensors.length === 0) {
            container.innerHTML = '<div class="empty-state"><p>No sensors available</p></div>';
            return;
        }

        // Get 5 most recent sensors
        const recent = this.sensors.slice(0, 5);

        const html = recent.map(sensor => {
            const reading = sensor.lastReading;
            const statusClass = ApiService.getStatusIndicatorClass(sensor.status);
            const healthClass = ApiService.getHealthStatusClass(sensor.health);

            return `
                <div class="card-row" onclick="navigateTo('sensor-detail&id=${sensor.id}')">
                    <div class="card-row-content">
                        <div class="card-row-item">
                            <div class="card-row-label">Sensor ID</div>
                            <div class="card-row-value">${sensor.id}</div>
                        </div>
                        <div class="card-row-item">
                            <div class="card-row-label">Area</div>
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
                        ${reading ? `
                        <div class="card-row-item">
                            <div class="card-row-label">AQI</div>
                            <div class="card-row-value">${reading.aqi || 'N/A'}</div>
                        </div>
                        ` : ''}
                    </div>
                    <div class="card-row-actions">
                        <span class="badge ${healthClass}">${sensor.health.replace(/_/g, ' ')}</span>
                    </div>
                </div>
            `;
        }).join('');

        container.innerHTML = html;
    }
}

// Initialize dashboard when page loads
document.addEventListener('DOMContentLoaded', () => {
    new DashboardManager();
});
