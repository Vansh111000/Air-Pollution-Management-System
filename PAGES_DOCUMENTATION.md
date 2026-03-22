# APMS Admin Pages - Documentation

## Overview
This document describes the new admin pages created for the Air Pollution Management System with proper validation, UI, and dark mode support.

## Pages Created

### 1. **Login Page** (`login.html`)
**Location:** Root directory
**Features:**
- Email validation
- Password validation (minimum 6 characters)
- Remember me functionality
- Password field masking
- Forgot password link (placeholder)
- Dark/Light theme toggle
- Responsive design
- Error messaging with icons
- LocalStorage for theme preference

**Validation Rules:**
- Email must be in valid format (xxx@xxx.xxx)
- Password must be at least 6 characters
- Both fields are required

---

### 2. **Sign Up Page** (`signup.html`)
**Location:** Root directory
**Features:**
- First name validation
- Last name validation
- Email validation
- Organization field
- Password strength checker
- Confirm password field
- Terms & conditions checkbox
- Real-time password requirements display
- Theme toggle
- Responsive grid layout

**Validation Rules:**
- All fields required
- Email must be valid format
- Password must be 8+ characters with:
  - At least one uppercase letter
  - At least one number
  - At least one special character (!@#$%^&*)
- Password confirmation must match
- Terms must be agreed to

**Password Requirements Display:**
- Visual indicators for each requirement
- Real-time validation as user types
- Green checkmarks when requirements are met

---

### 3. **Admin Dashboard** (`admin/dashboard.html`)
**Location:** admin/dashboard.html
**Features:**
- Interactive sidebar navigation
- Key performance metrics cards
- Bar chart for sensor activity
- Doughnut chart for station status
- Interactive map with Leaflet
- Station markers with status
- Recent alerts section
- Theme toggle button
- Search functionality

**Charts:**
- Sensor Activity Chart (Bar chart)
- Station Status Distribution (Doughnut chart)

**External Libraries:**
- Chart.js for data visualization
- Leaflet for mapping

---

### 4. **Alerts Management** (`admin/alerts.html`)
**Location:** admin/alerts.html
**Features:**
- Searchable alerts list
- Filter buttons (All, Critical, Warning, Info)
- Alert severity badges with color coding
- Status indicators (Pending, Investigating, Resolved)
- View and Resolve/Reopen actions
- Summary statistics
- Real-time search and filtering
- Responsive alert rows

**Alert Severity Levels:**
- 🔴 Critical (Red)
- ⚠️ Warning (Yellow)
- ℹ️ Info (Blue)

**Status Types:**
- Pending
- Investigating
- Resolved

---

### 5. **Stations Management** (`admin/stations.html`)
**Location:** admin/stations.html
**Features:**
- Station cards grid layout
- Station ID and location display
- Admin assignment tracking
- Battery level indicators
- Status pills with live indicators
- Sensor count display
- Quick actions (View, Edit, Delete)
- Search functionality
- Summary statistics
- Add new station button

**Card Information:**
- Station name and ID
- Region/Location
- Assigned admin
- Sensor count
- Battery percentage with visual bar
- Quick action buttons

**Status Types:**
- Active (Green with blinking indicator)
- Maintenance (Yellow)
- Offline (Red)

---

### 6. **Station Details** (`admin/station-details.html`)
**Location:** admin/station-details.html
**Features:**
- Breadcrumb navigation
- Detailed station information
- Key metrics cards
- Sensor activity line chart
- Air quality trends chart
- Active sensors list with readings
- Sensor status display
- Responsive data grid
- Edit and Log buttons
- Theme toggle

**Metrics Displayed:**
- Total Sensors
- Online Sensors
- Offline Sensors
- Battery Level

**Sensor Information:**
- Sensor name and type
- Current reading with units
- Online/Warning status with indicator
- Real-time data display

---

### 7. **Settings Page** (`admin/settings.html`)
**Location:** admin/settings.html
**Features:**
- General settings section
- Notification preferences
- Monitoring configuration
- Security settings
- Danger zone for destructive actions
- Toggle switches for preferences
- Dropdown selectors
- Input fields for thresholds
- Two-factor authentication
- API key management
- Session management

**Settings Sections:**

**General:**
- System name
- Time zone selection
- Data retention period

**Notifications:**
- Email alerts
- SMS alerts
- System logs
- Maintenance alerts

**Monitoring:**
- PM2.5 threshold
- CO2 threshold
- Real-time monitoring toggle
- Machine learning predictions

**Security:**
- Two-factor authentication
- API keys management
- Active sessions

**Danger Zone:**
- Reset all settings
- Delete all data (with confirmation)

---

## Shared CSS (`assets/css/admin.css`)

### Features:
- Dark mode (default) and light mode support
- CSS variables for theming
- Modern glassmorphism effects
- Responsive grid layouts
- Consistent button styling
- Form input styling with focus states
- Modal support
- Alert styling
- Badge and status pill components
- Chart container styling
- Mobile responsive design

### Color Scheme:

**Dark Mode (Default):**
- Background: `#020617`
- Card: `rgba(30, 41, 59, 0.4)`
- Text Primary: `#f8fafc`
- Text Secondary: `#94a3b8`
- Primary: `#38bdf8` (Cyan)
- Success: `#10b981` (Green)
- Warning: `#facc15` (Yellow)
- Danger: `#ef4444` (Red)

**Light Mode:**
- Background: `#f8fafc`
- Card: `rgba(255, 255, 255, 0.95)`
- Text Primary: `#0f172a`
- Text Secondary: `#475569`
- Primary: `#0284c7` (Blue)
- Success: `#10b981` (Green)
- Warning: `#facc15` (Yellow)
- Danger: `#ef4444` (Red)

---

## Theme Implementation

### How Theme Toggle Works:
1. Toggle button changes `body` class between empty and `light`
2. CSS variables automatically switch via `:root` and `body.light` selectors
3. Theme preference saved in `localStorage` under `adminTheme` key
4. On page load, saved theme is automatically applied

### Theme Persistence:
```javascript
localStorage.setItem("adminTheme", "light"); // or "dark"
localStorage.getItem("adminTheme");
```

---

## Form Validation

All forms include:
- Real-time error display
- Field-specific error messages
- Visual error states (red borders)
- Error icons (✗)
- Success messages where applicable
- Input field focus states with highlight

---

## Navigation Structure

```
login.html
   ↓
/admin
├── dashboard.html
├── alerts.html
├── stations.html
├── station-details.html
└── settings.html

signup.html (Registration)
```

Each admin page includes:
- Fixed sidebar with navigation links
- Theme toggle in top-right corner
- Logout link in sidebar (bottom)
- Breadcrumb navigation where applicable
- Search functionality where applicable

---

## Responsive Design

All pages are optimized for:
- **Desktop (1024px+):** Full layout with all features
- **Tablet (768px-1024px):** Adjusted grid layouts
- **Mobile (< 768px):** Single column layouts, collapsible sidebar

---

## Key Features Summary

✅ **Dark/Light Mode Support** - All pages support theme switching
✅ **Form Validation** - Email, password, required fields
✅ **Interactive Charts** - Chart.js integration
✅ **Real-time Search** - Filter alerts and stations
✅ **Status Indicators** - Visual badges for different statuses
✅ **Responsive Design** - Works on all screen sizes
✅ **LocalStorage** - Persists user preferences
✅ **Accessibility** - ARIA labels and semantic HTML
✅ **Error Handling** - User-friendly error messages
✅ **Modern UI** - Glassmorphism, gradients, animations

---

## File Locations

```
Air-Pollution-Management-System/
├── login.html (NEW)
├── signup.html (UPDATED)
├── admin/
│   ├── dashboard.html (NEW)
│   ├── alerts.html (NEW)
│   ├── stations.html (NEW)
│   ├── station-details.html (NEW)
│   └── settings.html (NEW)
├── assets/
│   └── css/
│       └── admin.css (NEW)
```

---

## Browser Compatibility

- Chrome/Edge 90+
- Firefox 88+
- Safari 14+
- Mobile browsers (iOS Safari, Chrome Mobile)

---

## Future Enhancements

- [ ] Backend API integration
- [ ] Real-time WebSocket updates
- [ ] Advanced analytics dashboard
- [ ] Export/Import functionality
- [ ] Multi-language support
- [ ] Custom themes
- [ ] Advanced filtering options
- [ ] Data export (CSV, PDF)
- [ ] Mobile app integration
- [ ] Notification bell with real-time updates

---

## Usage Notes

1. **Theme Persistence:** Theme choice is saved automatically
2. **Form Data:** Form submissions are logged to console (ready for backend integration)
3. **Charts:** Charts are responsive and update when theme changes
4. **Navigation:** Use sidebar links to navigate between pages
5. **Mobile:** Sidebar can be hidden on mobile view for more space

---

## Support

For issues or questions about these pages, refer to the individual JavaScript sections within each HTML file for implementation details.
