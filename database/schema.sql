-- database/schema.sql
-- database/schema.sql
-- Vayu APMS Schema (MySQL)

CREATE TABLE areas (
    area_id INT AUTO_INCREMENT PRIMARY KEY,
    state VARCHAR(100) NOT NULL,
    city VARCHAR(100) NOT NULL,
    area_name VARCHAR(100) NOT NULL,
    location_type ENUM('Urban', 'Rural', 'Suburban') NOT NULL,
    population_density INT NULL,
    acres INT NULL,
    topography VARCHAR(100) NULL
);

CREATE TABLE monitoring_stations (
    station_id INT AUTO_INCREMENT PRIMARY KEY,
    area_id INT,
    name VARCHAR(100) NOT NULL,
    description TEXT NULL,
    FOREIGN KEY (area_id) REFERENCES areas(area_id) ON DELETE RESTRICT
);

CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    user_type ENUM('admin', 'station_worker', 'end_user') NOT NULL,
    station_id INT NULL,
    area_id INT NULL,
    location VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (station_id) REFERENCES monitoring_stations(station_id) ON DELETE SET NULL,
    FOREIGN KEY (area_id) REFERENCES areas(area_id) ON DELETE SET NULL
);

CREATE TABLE sensors (
    sensor_id VARCHAR(50) PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    station_id INT,
    area_id INT,
    location VARCHAR(255) NOT NULL,
    type VARCHAR(50) NOT NULL,
    status ENUM('monitoring', 'maintenance', 'on_hold') NOT NULL,
    health ENUM('good', 'maintenance', 'offline') NOT NULL,
    uptime DECIMAL(5,2) NULL,
    total_readings INT DEFAULT 0,
    FOREIGN KEY (station_id) REFERENCES monitoring_stations(station_id) ON DELETE CASCADE,
    FOREIGN KEY (area_id) REFERENCES areas(area_id) ON DELETE RESTRICT
);

CREATE TABLE sensor_readings (
    reading_id INT AUTO_INCREMENT PRIMARY KEY,
    sensor_id VARCHAR(50),
    aqi INT NULL,
    pm25 DECIMAL(6,2) NULL,
    pm10 DECIMAL(6,2) NULL,
    temperature DECIMAL(5,2) NULL,
    humidity DECIMAL(5,2) NULL,
    recorded_at DATETIME NOT NULL,
    FOREIGN KEY (sensor_id) REFERENCES sensors(sensor_id) ON DELETE CASCADE
);

CREATE TABLE pollution_data (
    data_id INT AUTO_INCREMENT,
    station_id INT,
    area_id INT,
    aqi INT NULL,
    pm25 DECIMAL(6,2) NULL,
    pm10 DECIMAL(6,2) NULL,
    temperature DECIMAL(5,2) NULL,
    humidity DECIMAL(5,2) NULL,
    recorded_at DATETIME NOT NULL,
    PRIMARY KEY (data_id, station_id, area_id),
    FOREIGN KEY (station_id) REFERENCES monitoring_stations(station_id) ON DELETE CASCADE,
    FOREIGN KEY (area_id) REFERENCES areas(area_id) ON DELETE CASCADE
);

CREATE TABLE health_logs (
    log_id INT AUTO_INCREMENT,
    sensor_id VARCHAR(50),
    status ENUM('good', 'maintenance', 'offline') NOT NULL,
    note TEXT NULL,
    log_date DATE NOT NULL,
    PRIMARY KEY (log_id, sensor_id),
    FOREIGN KEY (sensor_id) REFERENCES sensors(sensor_id) ON DELETE CASCADE
);

CREATE TABLE feedback (
    feedback_id INT AUTO_INCREMENT,
    user_id INT,
    message TEXT NOT NULL,
    rating INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (feedback_id, user_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

CREATE TABLE alerts (
    alert_id INT AUTO_INCREMENT,
    sensor_id VARCHAR(50),
    message TEXT NOT NULL,
    severity ENUM('low', 'medium', 'high') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (alert_id, sensor_id),
    FOREIGN KEY (sensor_id) REFERENCES sensors(sensor_id) ON DELETE CASCADE
);

CREATE TABLE sensor_images (
    image_id INT AUTO_INCREMENT PRIMARY KEY,
    sensor_id VARCHAR(50),
    image_data LONGBLOB NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sensor_id) REFERENCES sensors(sensor_id) ON DELETE CASCADE
);
