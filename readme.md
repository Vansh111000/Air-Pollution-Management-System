# Vayu - Air Pollution Monitoring System (APMS)

Welcome to **Vayu**, a comprehensive web-based platform designed for monitoring, managing, and analyzing air quality and pollution levels. Vayu provides user-friendly dashboards for citizens, administrators, and monitoring stations to ensure data-driven environmental management.

---

## 🌟 Key Features

### 1. **Role-Based Dashboards & Access**
- **Public/Citizen View:** View real-time Air Quality Index (AQI), weather data, plant recommendations, and general alerts.
- **Monitoring Station View:** A dedicated dashboard for monitoring stations to report data and oversee station-specific sensors.
- **Admin View:** Full operational control over users, stations, sensors, and system-wide alerts.

### 2. **Sensor & Data Management**
- Complete CRUD (Create, Read, Update, Delete) operations for air quality sensors via RESTful APIs.
- Real-time fetching and processing of sensor readings.
- Interactive charts visualizing hourly and daily AQI trends.

### 3. **Authentication & Authorization**
- Secure multi-role user sign-up and login functions (Admin, User, Monitoring Station).
- Robust session tracking and API authentication.

### 4. **Alerts System**
- Station-specific and global warnings regarding high pollution levels or critical sensor states.

---

## 🛠️ Technology Stack

- **Frontend:** HTML5, CSS3, JavaScript (Vanilla JS/ES6+)
- **Backend API:** PHP (RESTful Architecture)
- **Database:** MySQL (using PHP Data Objects - PDO for secure interactions)
- **Environment:** Local Development via XAMPP (Apache + MySQL)

---

## 🚀 Installation & Setup (Local Development)

To run Vayu locally, follow these steps:

### Prerequisites:
- Install [XAMPP](https://www.apachefriends.org/index.html) or any similar LAMP/WAMP stack.
- PHP >= 7.4
- MySQL

### Steps:
1. **Clone the Project**
   Clone or copy the project files to the `htdocs` directory of your XAMPP installation:
   ```bash
   cd C:/xampp/htdocs/
   git clone <repository_url> vayu
   # or simply place the extracted `vayu` folder here
   ```

2. **Database Configuration**
   - Open the XAMPP Control Panel and start **Apache** and **MySQL**.
   - Open your browser and go to `http://localhost/phpmyadmin`.
   - Create a new database named `apms_db`.
   - *(If a `.sql` file is provided in the repository, import it into `apms_db` to seed your tables.)*

3. **Verify Database Connection**
   - Ensure the database configuration in `api/db.php` matches your local MySQL setup:
     ```php
     $host = '127.0.0.1'; 
     $db_name = 'apms_db';
     $username = 'root'; // default xampp user
     $password = ''; // default xampp password (empty)
     ```

4. **Run the Application**
   - Visit `http://localhost/vayu/` in your web browser.
   - You can access the different modules depending on the landing routing (e.g., `index.php`, `login.php`).

---

## 📂 Project Structure

```text
vayu/
├── admin/                  # Admin dashboard and management views
├── api/                    # PHP backend API endpoints
│   ├── auth/               # User authentication APIs
│   ├── health/             # Health check endpoints
│   ├── readings/           # API handlers for sensor readings
│   ├── sensors/            # API handlers for sensor configurations
│   └── db.php              # Database connection logic
├── assets/                 # CSS styles, JS scripts, and images
├── monitoring-station/     # Station-specific pages
├── index.php               # Main Entry / Landing view
├── catalogue.php           # Feature or Plant catalogue view
├── public_dashboard.php    # General citizen dashboard
├── login.php               # Login interface
└── signup.php              # Registration interface
```

---

## 🛡️ Best Practices & Security
- **API CORS:** Handled securely natively in the `db.php` header configurations.
- **SQL Injection Prevention:** Uses PDO Prepared Statements to ensure data sanitization.
- **Error Handling:** Built-in dev and prod error reporting modes internally configured in the API endpoints.

---

*Thank you for exploring Vayu! Let's build a cleaner, greener future.*