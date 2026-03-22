<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>VayuDarpan – Public Dashboard</title>

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@400;500;600;700&family=Noto+Sans:wght@300;400;600&family=Teko:wght@400;500;600&display=swap" rel="stylesheet"/>

  <!-- Leaflet CSS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css"/>

  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <style>
    :root {
      --nav-bg: #0d2137;
      --accent: #00bcd4;
      --good: #00c853;
      --moderate: #ffc107;
      --severe: #e53935;
      --poor: #ff6d00;
      --white: #ffffff;
      --card: #f4f8fb;
      --text: #1a2a3a;
      --border: #dde4ea;
    }
    * { margin:0; padding:0; box-sizing:border-box; }
    body { font-family:'Noto Sans',sans-serif; background:#f0f4f8; color:var(--text); }

    /* ── NAV ── */
    nav {
      background:var(--nav-bg);
      display:flex; align-items:center; justify-content:space-between;
      padding:0 32px; height:56px;
      position:sticky; top:0; z-index:2000;
      box-shadow:0 2px 12px rgba(0,0,0,0.3);
    }
    .nav-logo { font-family:'Teko',sans-serif; font-size:1.6rem; color:var(--accent); letter-spacing:2px; }
    .nav-logo span { color:#fff; }
    .nav-links { display:flex; gap:6px; }
    .nav-links a {
      color:#cde8f5; text-decoration:none;
      font-family:'Rajdhani',sans-serif; font-weight:600; font-size:0.95rem;
      letter-spacing:1px; padding:7px 16px; border-radius:4px;
      transition:background 0.2s,color 0.2s; text-transform:uppercase;
    }
    .nav-links a:hover { background:var(--accent); color:#fff; }
    .nav-links a.active { background:var(--accent); color:#fff; }
    .nav-links a.logout { border:1px solid #e53935; color:#ff7070; }
    .nav-links a.logout:hover { background:#e53935; color:#fff; }

    /* ── PAGE HEADER ── */
    .page-header {
      background:linear-gradient(135deg, var(--nav-bg) 0%, #1a3a5c 100%);
      padding:22px 40px;
      display:flex; align-items:center; justify-content:space-between;
    }
    .page-header h1 {
      font-family:'Teko',sans-serif; font-size:2rem;
      color:#fff; letter-spacing:2px;
    }
    .page-header h1 span { color:var(--accent); }
    .page-header p { color:#7cb8d4; font-size:0.82rem; margin-top:3px; }
    .live-badge {
      display:flex; align-items:center; gap:8px;
      background:rgba(0,200,83,0.15); border:1px solid var(--good);
      border-radius:20px; padding:6px 16px;
    }
    .live-dot {
      width:9px; height:9px; border-radius:50%;
      background:var(--good); animation:pulse 1.5s infinite;
    }
    @keyframes pulse {
      0%,100% { opacity:1; transform:scale(1); }
      50%      { opacity:0.4; transform:scale(1.4); }
    }
    .live-badge span { color:var(--good); font-family:'Rajdhani',sans-serif; font-weight:700; font-size:0.85rem; letter-spacing:1px; }

    /* ── WRAPPER ── */
    .dashboard-wrapper { max-width:1300px; margin:28px auto; padding:0 24px; }

    /* ── SECTION TITLE ── */
    .section-title {
      font-family:'Teko',sans-serif; font-size:1.6rem;
      color:var(--nav-bg); border-left:5px solid var(--accent);
      padding-left:12px; margin-bottom:16px; letter-spacing:1px;
    }

    /* ══════════════════════════════
       SECTION 1: MAP + CITY INFO
    ══════════════════════════════ */
    .map-section { display:flex; gap:20px; margin-bottom:28px; align-items:flex-start; }

    /* city info panel (left) */
    .city-info-panel {
      width:260px; flex-shrink:0;
      display:flex; flex-direction:column; gap:14px;
    }
    .city-card {
      background:#fff; border-radius:12px;
      box-shadow:0 2px 12px rgba(0,0,0,0.08);
      overflow:hidden;
    }
    .city-card-header {
      padding:14px 16px 10px;
      background:linear-gradient(135deg,#e3f2fd,#e8f5e9);
      border-bottom:1px solid var(--border);
    }
    .city-card-header h3 {
      font-family:'Rajdhani',sans-serif; font-weight:700;
      font-size:1.1rem; color:var(--nav-bg);
    }
    .city-card-header small { font-size:0.72rem; color:#888; }
    .city-card-body { padding:14px 16px; }

    .aqi-display {
      display:flex; align-items:center; gap:12px; margin-bottom:12px;
    }
    .aqi-circle {
      width:64px; height:64px; border-radius:50%;
      display:flex; flex-direction:column;
      align-items:center; justify-content:center;
      font-family:'Teko',sans-serif;
      box-shadow:0 3px 10px rgba(0,0,0,0.15);
      flex-shrink:0;
    }
    .aqi-circle .num { font-size:1.6rem; line-height:1; }
    .aqi-circle .lbl { font-size:0.6rem; letter-spacing:1px; }
    .aqi-circle.good     { background:var(--good); color:#fff; }
    .aqi-circle.moderate { background:var(--moderate); color:#3d2800; }
    .aqi-circle.poor     { background:var(--poor); color:#fff; }
    .aqi-circle.severe   { background:var(--severe); color:#fff; }

    .aqi-meta h4 { font-family:'Rajdhani',sans-serif; font-weight:700; font-size:0.95rem; }
    .aqi-meta p  { font-size:0.72rem; color:#666; margin-top:2px; line-height:1.5; }

    .stat-row {
      display:grid; grid-template-columns:1fr 1fr;
      gap:8px; margin-top:4px;
    }
    .stat-box {
      background:var(--card); border-radius:8px;
      padding:8px 10px; text-align:center;
    }
    .stat-box .s-val {
      font-family:'Teko',sans-serif; font-size:1.2rem;
      color:var(--nav-bg);
    }
    .stat-box .s-lbl { font-size:0.65rem; color:#888; letter-spacing:0.5px; }

    .aqi-bar-wrap { margin-top:10px; }
    .aqi-bar-wrap label { font-size:0.68rem; color:#888; font-weight:600; letter-spacing:0.5px; }
    .aqi-bar-bg {
      height:8px; border-radius:4px; margin-top:4px;
      background:linear-gradient(90deg,#00c853 0%,#ffc107 40%,#ff6d00 65%,#e53935 100%);
      position:relative;
    }
    .aqi-needle {
      width:3px; height:14px; background:#1a2a3a;
      border-radius:2px; position:absolute; top:-3px;
      transition:left 0.6s ease;
    }

    .placeholder-msg {
      text-align:center; padding:32px 16px;
      color:#aaa; font-size:0.85rem; line-height:1.8;
    }
    .placeholder-msg .big { font-size:2rem; margin-bottom:8px; }

    /* map */
    .map-container {
      flex:1; border-radius:14px; overflow:hidden;
      box-shadow:0 4px 20px rgba(0,0,0,0.12);
      border:2px solid var(--border);
      min-height:460px;
    }
    #indiaMap { width:100%; height:460px; z-index: 1; }

    /* ══════════════════════════════
       SECTION 2: PIE CHART + ALERTS
    ══════════════════════════════ */
    .analytics-section { display:flex; gap:20px; margin-bottom:28px; }

    .pie-card {
      flex:1.2; background:#fff; border-radius:14px;
      padding:22px; box-shadow:0 2px 12px rgba(0,0,0,0.08);
    }
    .pie-card h3 {
      font-family:'Rajdhani',sans-serif; font-weight:700;
      font-size:1rem; color:var(--nav-bg); letter-spacing:1px;
      text-transform:uppercase; margin-bottom:16px;
    }
    .pie-wrap {
      display:flex; align-items:center; gap:20px;
    }
    .pie-wrap canvas { max-width:200px; max-height:200px; }
    .pie-legend { flex:1; }
    .pie-legend-item {
      display:flex; align-items:center; gap:8px;
      margin-bottom:8px; font-size:0.8rem;
    }
    .pie-dot { width:12px; height:12px; border-radius:3px; flex-shrink:0; }
    .pie-legend-item span { color:#555; }
    .pie-legend-item b { color:var(--nav-bg); margin-left:auto; }

    /* alerts panel */
    .alerts-panel {
      flex:1; display:flex; flex-direction:column; gap:14px;
    }
    .alert-card {
      border-radius:12px; padding:18px 20px;
      display:flex; gap:14px; align-items:flex-start;
      box-shadow:0 2px 10px rgba(0,0,0,0.07);
    }
    .alert-card.safe   { background:linear-gradient(135deg,#e8f5e9,#f1f8e9); border-left:5px solid var(--good); }
    .alert-card.danger { background:linear-gradient(135deg,#ffebee,#fff3e0); border-left:5px solid var(--severe); }
    .alert-icon { font-size:2.2rem; flex-shrink:0; }
    .alert-content h4 {
      font-family:'Rajdhani',sans-serif; font-weight:700;
      font-size:1rem; letter-spacing:0.5px;
    }
    .alert-card.safe   .alert-content h4 { color:#1b5e20; }
    .alert-card.danger .alert-content h4 { color:#b71c1c; }
    .alert-content p { font-size:0.78rem; color:#555; margin-top:4px; line-height:1.6; }
    .alert-status {
      display:inline-block; margin-top:8px;
      padding:3px 12px; border-radius:12px;
      font-size:0.7rem; font-weight:700; letter-spacing:1px;
    }
    .safe   .alert-status { background:var(--good);   color:#fff; }
    .danger .alert-status { background:var(--severe); color:#fff; }

    /* ══════════════════════════════
       SECTION 3: CATEGORIES
    ══════════════════════════════ */
    .categories-section { margin-bottom:40px; }

    .cat-tabs {
      display:flex; gap:8px; flex-wrap:wrap; margin-bottom:20px;
    }
    .cat-tab {
      padding:8px 20px; border-radius:20px; border:2px solid var(--border);
      background:#fff; font-family:'Rajdhani',sans-serif; font-weight:600;
      font-size:0.88rem; letter-spacing:0.5px; cursor:pointer;
      transition:all 0.2s; color:var(--text);
    }
    .cat-tab:hover { border-color:var(--accent); color:var(--accent); }
    .cat-tab.active { background:var(--nav-bg); color:#fff; border-color:var(--nav-bg); }

    .cat-content { display:none; }
    .cat-content.active { display:block; }

    .city-table-wrap {
      background:#fff; border-radius:12px;
      box-shadow:0 2px 12px rgba(0,0,0,0.07); overflow:hidden;
    }
    table { width:100%; border-collapse:collapse; }
    thead tr { background:var(--nav-bg); }
    thead th {
      padding:12px 16px; text-align:left;
      font-family:'Rajdhani',sans-serif; font-weight:700;
      font-size:0.85rem; color:#cde8f5; letter-spacing:1px;
      text-transform:uppercase;
    }
    tbody tr { border-bottom:1px solid var(--border); transition:background 0.15s; }
    tbody tr:hover { background:#f0f9ff; }
    tbody td { padding:11px 16px; font-size:0.83rem; }
    .aqi-chip {
      display:inline-block; padding:3px 12px; border-radius:12px;
      font-weight:700; font-size:0.78rem; color:#fff;
    }
    .chip-good     { background:var(--good); }
    .chip-moderate { background:var(--moderate); color:#3d2800; }
    .chip-poor     { background:var(--poor); }
    .chip-severe   { background:var(--severe); }

    .cause-tag {
      display:inline-block; background:#eef2f7; color:#445;
      border-radius:4px; padding:2px 8px; font-size:0.72rem;
      margin:2px; font-weight:600;
    }
    .guideline-mini {
      font-size:0.75rem; color:#555;
      border-left:3px solid var(--accent);
      padding-left:8px; font-style:italic;
    }

    /* scrollbar */
    ::-webkit-scrollbar { width:6px; height:6px; }
    ::-webkit-scrollbar-track { background:#f1f1f1; }
    ::-webkit-scrollbar-thumb { background:var(--accent); border-radius:3px; }

    /* footer */
    footer {
      background:var(--nav-bg); color:#7cb8d4;
      text-align:center; padding:16px;
      font-size:0.78rem; letter-spacing:0.5px;
    }
    footer a { color:var(--accent); text-decoration:none; }

    /* Leaflet popup custom */
    .leaflet-popup-content-wrapper {
      border-radius:10px !important;
      box-shadow:0 4px 20px rgba(0,0,0,0.2) !important;
    }
    .custom-popup h4 {
      font-family:'Rajdhani',sans-serif; font-weight:700;
      font-size:1rem; margin-bottom:6px; color:#0d2137;
    }
    .custom-popup .popup-aqi {
      font-family:'Teko',sans-serif; font-size:1.8rem; line-height:1;
    }
    .custom-popup table td { padding:2px 8px; font-size:0.78rem; }
  </style>
</head>
<body>

<!-- NAV -->
<nav>
  <div class="nav-logo">Vayu<span>Darpan</span></div>
  <div class="nav-links">
    <a href="index.php">Home</a>
    <a href="catalogue.php">Catalogue</a>
    <a href="public_dashboard.php" class="active">Public Dashboard</a>
    <a href="signup.php">Sign Up</a>
    <a href="login.php">Login</a>
    <a href="logout.php" class="logout">Logout</a>
  </div>
</nav>

<!-- PAGE HEADER -->
<div class="page-header">
  <div>
    <h1>Public <span>Air Quality</span> Dashboard</h1>
    <p>Real-time AQI data across India — Click any city pin on the map for details</p>
  </div>
  <div class="live-badge">
    <div class="live-dot"></div>
    <span>LIVE DATA</span>
  </div>
</div>

<div class="dashboard-wrapper">

  <!-- ══ SECTION 1: MAP + CITY INFO ══ -->
  <div class="section-title">India AQI Map</div>
  <div class="map-section">

    <!-- City Info Panel -->
    <div class="city-info-panel">
      <div class="city-card" id="cityCard">
        <div class="city-card-header">
          <h3 id="cityName">Select a City</h3>
          <small id="cityState">Click any pin on the map →</small>
        </div>
        <div class="city-card-body" id="cityCardBody">
          <div class="placeholder-msg">
            <div class="big">🗺️</div>
            Click any city pin<br>on the map to view<br>detailed AQI data
          </div>
        </div>
      </div>
    </div>

    <!-- Leaflet Map -->
    <div class="map-container">
      <div id="indiaMap"></div>
    </div>
  </div>

  <!-- ══ SECTION 2: PIE CHART + SAFETY ALERTS ══ -->
  <div class="section-title">Pollutant Breakdown & Safety Alerts</div>
  <div class="analytics-section">

    <!-- Pie Chart -->
    <div class="pie-card">
      <h3>🧪 Pollutant Contribution — <span id="pieCity">India Average</span></h3>
      <div class="pie-wrap">
        <canvas id="pollutantPie"></canvas>
        <div class="pie-legend" id="pieLegend"></div>
      </div>
    </div>

    <!-- Safety Alerts -->
    <div class="alerts-panel">
      <div class="alert-card" id="elderlyAlert">
        <div class="alert-icon">👴</div>
        <div class="alert-content">
          <h4>Senior Citizens</h4>
          <p id="elderlyMsg">Select a city on the map to see personalised safety advisory for senior citizens.</p>
          <span class="alert-status" id="elderlyStatus">—</span>
        </div>
      </div>
      <div class="alert-card" id="kidsAlert">
        <div class="alert-icon">👧</div>
        <div class="alert-content">
          <h4>Children &amp; Infants</h4>
          <p id="kidsMsg">Select a city on the map to see personalised safety advisory for children and infants.</p>
          <span class="alert-status" id="kidsStatus">—</span>
        </div>
      </div>
      <div class="alert-card" id="generalAlert" style="background:linear-gradient(135deg,#e3f2fd,#f0f8ff);border-left:5px solid var(--accent);">
        <div class="alert-icon">🏃</div>
        <div class="alert-content">
          <h4 style="color:#0d47a1;">General Public</h4>
          <p id="generalMsg">Safety advisory for general public will appear here once you select a city.</p>
          <span class="alert-status" id="generalStatus" style="background:var(--accent);">—</span>
        </div>
      </div>
    </div>
  </div>

  <!-- ══ SECTION 3: CATEGORIES ══ -->
  <div class="categories-section">
    <div class="section-title">City Categories by Pollutant</div>
    <div class="cat-tabs">
      <button class="cat-tab active" onclick="switchTab('all',this)">🏙️ All Cities</button>
      <button class="cat-tab" onclick="switchTab('pm25',this)">🔬 PM2.5</button>
      <button class="cat-tab" onclick="switchTab('pm10',this)">💨 PM10</button>
      <button class="cat-tab" onclick="switchTab('no2',this)">🏭 NO₂</button>
      <button class="cat-tab" onclick="switchTab('so2',this)">⚗️ SO₂</button>
      <button class="cat-tab" onclick="switchTab('co',this)">🚗 CO</button>
      <button class="cat-tab" onclick="switchTab('ozone',this)">🌐 Ozone</button>
    </div>
    <div class="city-table-wrap">
      <table>
        <thead>
          <tr>
            <th>#</th>
            <th>City</th>
            <th>AQI</th>
            <th>Level</th>
            <th>Primary Pollutant</th>
            <th>Main Causes</th>
            <th>Guideline</th>
          </tr>
        </thead>
        <tbody id="cityTableBody"></tbody>
      </table>
    </div>
  </div>

</div><!-- end wrapper -->

<footer>
  © 2025 VayuDarpan – National Air Quality Monitoring Portal &nbsp;|&nbsp;
  <a href="#">CPCB</a> &nbsp;|&nbsp; <a href="#">MoEFCC</a> &nbsp;|&nbsp;
  Data refreshed every 15 minutes
</footer>

<!-- Leaflet JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>

<script>
// ══════════════════════════════════════
// CITY DATA (replace with PHP/MySQL feed)
// ══════════════════════════════════════
const cities = [
  { name:"Delhi",       state:"Delhi",           lat:28.6139, lng:77.2090, aqi:298, temp:33, wind:12, pm25:110, pm10:180, no2:62, so2:28, co:1.8, ozone:44 },
  { name:"Mumbai",      state:"Maharashtra",      lat:19.0760, lng:72.8777, aqi:142, temp:34, wind:18, pm25:55,  pm10:90,  no2:38, so2:14, co:0.9, ozone:32 },
  { name:"Kolkata",     state:"West Bengal",      lat:22.5726, lng:88.3639, aqi:210, temp:31, wind:10, pm25:88,  pm10:140, no2:50, so2:22, co:1.4, ozone:38 },
  { name:"Chennai",     state:"Tamil Nadu",       lat:13.0827, lng:80.2707, aqi:98,  temp:35, wind:22, pm25:32,  pm10:58,  no2:28, so2:10, co:0.6, ozone:28 },
  { name:"Bengaluru",   state:"Karnataka",        lat:12.9716, lng:77.5946, aqi:115, temp:26, wind:14, pm25:42,  pm10:70,  no2:35, so2:12, co:0.7, ozone:30 },
  { name:"Hyderabad",   state:"Telangana",        lat:17.3850, lng:78.4867, aqi:128, temp:30, wind:16, pm25:48,  pm10:82,  no2:33, so2:15, co:0.8, ozone:31 },
  { name:"Pune",        state:"Maharashtra",      lat:18.5204, lng:73.8567, aqi:105, temp:29, wind:20, pm25:38,  pm10:65,  no2:30, so2:11, co:0.6, ozone:29 },
  { name:"Ahmedabad",   state:"Gujarat",          lat:23.0225, lng:72.5714, aqi:175, temp:36, wind:9,  pm25:68,  pm10:110, no2:44, so2:20, co:1.1, ozone:36 },
  { name:"Lucknow",     state:"Uttar Pradesh",    lat:26.8467, lng:80.9462, aqi:265, temp:31, wind:7,  pm25:98,  pm10:162, no2:58, so2:25, co:1.6, ozone:42 },
  { name:"Kanpur",      state:"Uttar Pradesh",    lat:26.4499, lng:80.3319, aqi:310, temp:32, wind:6,  pm25:120, pm10:195, no2:65, so2:32, co:2.1, ozone:48 },
  { name:"Patna",       state:"Bihar",            lat:25.5941, lng:85.1376, aqi:285, temp:30, wind:8,  pm25:108, pm10:172, no2:60, so2:27, co:1.9, ozone:45 },
  { name:"Jaipur",      state:"Rajasthan",        lat:26.9124, lng:75.7873, aqi:158, temp:38, wind:15, pm25:60,  pm10:100, no2:40, so2:18, co:1.0, ozone:35 },
  { name:"Surat",       state:"Gujarat",          lat:21.1702, lng:72.8311, aqi:145, temp:33, wind:17, pm25:55,  pm10:88,  no2:36, so2:16, co:0.9, ozone:33 },
  { name:"Nagpur",      state:"Maharashtra",      lat:21.1458, lng:79.0882, aqi:122, temp:31, wind:13, pm25:45,  pm10:75,  no2:32, so2:13, co:0.7, ozone:30 },
  { name:"Bhopal",      state:"Madhya Pradesh",   lat:23.2599, lng:77.4126, aqi:138, temp:30, wind:11, pm25:52,  pm10:85,  no2:35, so2:15, co:0.8, ozone:32 },
];

// pollutant causes map
const causes = {
  pm25:  ["Vehicle emissions","Crop burning","Industrial smoke","Construction dust"],
  pm10:  ["Road dust","Construction","Mining","Industrial waste"],
  no2:   ["Diesel vehicles","Thermal plants","Industrial combustion"],
  so2:   ["Coal burning","Oil refineries","Thermal power plants"],
  co:    ["Incomplete combustion","Vehicles","Biomass burning"],
  ozone: ["Vehicle exhaust (photochemical)","Industrial VOCs","Sunlight reaction"],
};

const guidelines = {
  pm25:  "Wear N95 mask; avoid morning jogs on high-PM2.5 days.",
  pm10:  "Wet-mop floors; keep windows closed during dusty winds.",
  no2:   "Avoid peak traffic hours; use public transport.",
  so2:   "Stay indoors near industrial zones; use air purifiers.",
  co:    "Never idle your car; ensure proper ventilation indoors.",
  ozone: "Avoid outdoor exercise between 12pm–6pm on sunny days.",
};

// AQI helper
function aqiClass(v) {
  if(v<=100) return 'good';
  if(v<=200) return 'moderate';
  if(v<=300) return 'poor';
  return 'severe';
}
function aqiLabel(v) {
  if(v<=100) return 'Good';
  if(v<=200) return 'Moderate';
  if(v<=300) return 'Poor';
  return 'Severe';
}
function aqiChip(v) {
  const c = aqiClass(v);
  const chipMap = {good:'chip-good',moderate:'chip-moderate',poor:'chip-poor',severe:'chip-severe'};
  return `<span class="aqi-chip ${chipMap[c]}">${v} – ${aqiLabel(v)}</span>`;
}
function pinColor(v) {
  if(v<=100) return '#00c853';
  if(v<=200) return '#ffc107';
  if(v<=300) return '#ff6d00';
  return '#e53935';
}

// ══════════════════════════════════════
// LEAFLET MAP
// ══════════════════════════════════════
const map = L.map('indiaMap', {
  center: [22.5, 82.5],
  zoom: 5,
  minZoom: 4,
  maxZoom: 10,
});

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
  attribution: '© OpenStreetMap contributors'
}).addTo(map);

cities.forEach(city => {
  const color = pinColor(city.aqi);
  const icon = L.divIcon({
    className:'',
    html:`<div style="
      background:${color};color:#fff;
      width:36px;height:36px;border-radius:50%;
      display:flex;align-items:center;justify-content:center;
      font-family:'Teko',sans-serif;font-size:0.85rem;font-weight:600;
      border:2px solid #fff;box-shadow:0 2px 8px rgba(0,0,0,0.3);
      cursor:pointer;
    ">${city.aqi}</div>`,
    iconSize:[36,36], iconAnchor:[18,18],
  });

  const marker = L.marker([city.lat, city.lng], {icon}).addTo(map);
  marker.bindPopup(`
    <div class="custom-popup">
      <h4>📍 ${city.name}, ${city.state}</h4>
      <div class="popup-aqi" style="color:${color}">${city.aqi}</div>
      <div style="font-size:0.75rem;color:#666;margin-bottom:6px;">${aqiLabel(city.aqi)}</div>
      <table>
        <tr><td>🌡 Temp</td><td><b>${city.temp}°C</b></td></tr>
        <tr><td>💨 Wind</td><td><b>${city.wind} km/h</b></td></tr>
        <tr><td>🔬 PM2.5</td><td><b>${city.pm25} µg/m³</b></td></tr>
        <tr><td>💨 PM10</td><td><b>${city.pm10} µg/m³</b></td></tr>
      </table>
    </div>
  `, { maxWidth:220 });

  marker.on('click', () => updateCityPanel(city));
});

// ══════════════════════════════════════
// CITY PANEL UPDATE
// ══════════════════════════════════════
function updateCityPanel(city) {
  const cls = aqiClass(city.aqi);
  const needleLeft = Math.min(Math.round((city.aqi / 500) * 100), 97);

  document.getElementById('cityName').textContent = '📍 ' + city.name;
  document.getElementById('cityState').textContent = city.state + ', India';

  document.getElementById('cityCardBody').innerHTML = `
    <div class="aqi-display">
      <div class="aqi-circle ${cls}">
        <span class="num">${city.aqi}</span>
        <span class="lbl">AQI</span>
      </div>
      <div class="aqi-meta">
        <h4>${aqiLabel(city.aqi)}</h4>
        <p>PM2.5: ${city.pm25} µg/m³<br>PM10: ${city.pm10} µg/m³</p>
      </div>
    </div>
    <div class="stat-row">
      <div class="stat-box"><div class="s-val">${city.temp}°C</div><div class="s-lbl">Temperature</div></div>
      <div class="stat-box"><div class="s-val">${city.wind}</div><div class="s-lbl">Wind km/h</div></div>
      <div class="stat-box"><div class="s-val">${city.no2}</div><div class="s-lbl">NO₂ µg/m³</div></div>
      <div class="stat-box"><div class="s-val">${city.so2}</div><div class="s-lbl">SO₂ µg/m³</div></div>
    </div>
    <div class="aqi-bar-wrap">
      <label>AQI SCALE</label>
      <div class="aqi-bar-bg">
        <div class="aqi-needle" style="left:${needleLeft}%"></div>
      </div>
    </div>
  `;

  // Update pie chart
  updatePie(city);

  // Update alerts
  updateAlerts(city);
}

// ══════════════════════════════════════
// PIE CHART
// ══════════════════════════════════════
const pieColors = ['#e53935','#ff6d00','#ffc107','#00bcd4','#7c4dff','#00c853'];
const pieLabels = ['PM2.5','PM10','NO₂','SO₂','CO','Ozone'];
let pieChart;

function buildDefaultPie() {
  // average across all cities
  const avg = key => Math.round(cities.reduce((s,c)=>s+c[key],0)/cities.length);
  const data = [avg('pm25'),avg('pm10'),avg('no2'),avg('so2'),Math.round(avg('co')*10),avg('ozone')];
  renderPie(data, 'India Average');
}

function updatePie(city) {
  const data = [city.pm25, city.pm10, city.no2, city.so2, Math.round(city.co*10), city.ozone];
  renderPie(data, city.name);
}

function renderPie(data, label) {
  document.getElementById('pieCity').textContent = label;
  if(pieChart) pieChart.destroy();
  pieChart = new Chart(document.getElementById('pollutantPie'), {
    type:'doughnut',
    data:{
      labels: pieLabels,
      datasets:[{ data, backgroundColor:pieColors, borderWidth:2, borderColor:'#fff' }]
    },
    options:{
      plugins:{ legend:{ display:false } },
      cutout:'60%',
      animation:{ animateRotate:true, duration:600 },
    }
  });

  const total = data.reduce((a,b)=>a+b,0);
  const legend = document.getElementById('pieLegend');
  legend.innerHTML = pieLabels.map((l,i)=>`
    <div class="pie-legend-item">
      <div class="pie-dot" style="background:${pieColors[i]}"></div>
      <span>${l}</span>
      <b>${Math.round(data[i]/total*100)}%</b>
    </div>`).join('');
}

buildDefaultPie();

// ══════════════════════════════════════
// SAFETY ALERTS
// ══════════════════════════════════════
function updateAlerts(city) {
  const aqi = city.aqi;

  // Elderly
  const elCard  = document.getElementById('elderlyAlert');
  const elMsg   = document.getElementById('elderlyMsg');
  const elStat  = document.getElementById('elderlyStatus');
  if(aqi > 150) {
    elCard.className='alert-card danger';
    elMsg.textContent=`AQI of ${aqi} is dangerous for senior citizens. Stay indoors, keep windows closed and use air purifier if available. Avoid all outdoor activity.`;
    elStat.textContent='⚠️ HIGH RISK — STAY INDOORS';
  } else {
    elCard.className='alert-card safe';
    elMsg.textContent=`AQI of ${aqi} is relatively safe. Senior citizens may go outdoors but should avoid prolonged strenuous activity and peak traffic zones.`;
    elStat.textContent='✅ LOW RISK — SAFE';
  }

  // Kids
  const kCard = document.getElementById('kidsAlert');
  const kMsg  = document.getElementById('kidsMsg');
  const kStat = document.getElementById('kidsStatus');
  if(aqi > 100) {
    kCard.className='alert-card danger';
    kMsg.textContent=`Children and infants are highly sensitive to PM2.5 (${city.pm25} µg/m³). Avoid outdoor play, school commute should use N95 masks. Keep children away from roads.`;
    kStat.textContent='⚠️ CAUTION — LIMIT OUTDOOR TIME';
  } else {
    kCard.className='alert-card safe';
    kMsg.textContent=`Air quality is suitable for children today. Outdoor play is safe but monitor AQI regularly. Hydration is advised in hot and humid conditions.`;
    kStat.textContent='✅ SAFE FOR CHILDREN';
  }

  // General
  const gMsg  = document.getElementById('generalMsg');
  const gStat = document.getElementById('generalStatus');
  if(aqi > 200) {
    gMsg.textContent=`Severe AQI of ${aqi}. General public should wear N95 masks outdoors. Avoid jogging or cycling. Limit time near busy roads and construction sites.`;
    gStat.textContent='🚨 SEVERE — WEAR MASK OUTDOORS'; gStat.style.background='#e53935';
  } else if(aqi > 100) {
    gMsg.textContent=`Moderate AQI of ${aqi}. Healthy adults can go outdoors with precaution. Sensitive individuals should reduce prolonged outdoor exertion.`;
    gStat.textContent='⚠️ MODERATE — TAKE PRECAUTION'; gStat.style.background='#ffc107'; gStat.style.color='#3d2800';
  } else {
    gMsg.textContent=`Air quality is good today with AQI of ${aqi}. Safe for all outdoor activities. Great day for a walk or exercise!`;
    gStat.textContent='✅ GOOD — ENJOY OUTDOORS'; gStat.style.background='var(--good)'; gStat.style.color='#fff';
  }
}

// ══════════════════════════════════════
// CATEGORY TABLE
// ══════════════════════════════════════
function buildTable(filter) {
  let sorted = [...cities];

  const keyMap = { pm25:'pm25', pm10:'pm10', no2:'no2', so2:'so2', co:'co', ozone:'ozone' };
  if(filter !== 'all') sorted.sort((a,b) => b[keyMap[filter]] - a[keyMap[filter]]);
  else sorted.sort((a,b) => b.aqi - a.aqi);

  const primaryPollutant = (city) => {
    const vals = { PM2.5:city.pm25, PM10:city.pm10, NO₂:city.no2, SO₂:city.so2, CO:city.co*10, Ozone:city.ozone };
    return Object.entries(vals).sort((a,b)=>b[1]-a[1])[0][0];
  };

  const causeKey = (p) => ({
    'PM2.5':'pm25','PM10':'pm10','NO₂':'no2','SO₂':'so2','CO':'co','Ozone':'ozone'
  }[p] || 'pm25');

  const tbody = document.getElementById('cityTableBody');
  tbody.innerHTML = sorted.map((city,i) => {
    const primary = primaryPollutant(city);
    const cKey = causeKey(primary);
    const causeTags = causes[cKey].map(c=>`<span class="cause-tag">${c}</span>`).join('');
    return `<tr>
      <td><b>${i+1}</b></td>
      <td><b>${city.name}</b><br><span style="font-size:0.7rem;color:#888;">${city.state}</span></td>
      <td>${aqiChip(city.aqi)}</td>
      <td>${aqiLabel(city.aqi)}</td>
      <td><b>${primary}</b></td>
      <td>${causeTags}</td>
      <td><span class="guideline-mini">${guidelines[cKey]}</span></td>
    </tr>`;
  }).join('');
}

function switchTab(filter, el) {
  document.querySelectorAll('.cat-tab').forEach(t=>t.classList.remove('active'));
  el.classList.add('active');
  buildTable(filter);
}

buildTable('all');
</script>
</body>
</html>