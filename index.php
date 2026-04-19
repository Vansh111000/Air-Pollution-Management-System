<!-- index.php (root directory) -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>VayuDarpan – India Air Quality Monitor</title>
  <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@400;500;600;700&family=Noto+Sans:wght@300;400;600&family=Teko:wght@400;500;600&display=swap" rel="stylesheet"/>
  <style>
    :root {
      --aqi-good: #00c853;
      --aqi-moderate: #ffd600;
      --aqi-severe: #d50000;
      --nav-bg: #0d2137;
      --accent: #00bcd4;
      --white: #ffffff;
      --text-dark: #1a2a3a;
      --card-bg: #f4f8fb;
    }

    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
      font-family: 'Noto Sans', sans-serif;
      background: #ffffff;
      color: var(--text-dark);
    }

    /* ── NAV ── */
    nav {
      background: var(--nav-bg);
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 32px;
      height: 56px;
      position: sticky;
      top: 0;
      z-index: 1000;
      box-shadow: 0 2px 12px rgba(0,0,0,0.3);
    }
    .nav-logo {
      font-family: 'Teko', sans-serif;
      font-size: 1.6rem;
      color: var(--accent);
      letter-spacing: 2px;
    }
    .nav-logo span { color: #fff; }
    .nav-links { display: flex; gap: 6px; }
    .nav-links a {
      color: #cde8f5;
      text-decoration: none;
      font-family: 'Rajdhani', sans-serif;
      font-weight: 600;
      font-size: 0.95rem;
      letter-spacing: 1px;
      padding: 7px 16px;
      border-radius: 4px;
      transition: background 0.2s, color 0.2s;
      text-transform: uppercase;
    }
    .nav-links a:hover { background: var(--accent); color: #fff; }
    .nav-links a.active { background: var(--accent); color: #fff; }
    .nav-links a.logout { border: 1px solid #e53935; color: #ff7070; }
    .nav-links a.logout:hover { background: #e53935; color: #fff; }

    /* ── TICKER ── */
    .ticker-wrap {
      background: linear-gradient(90deg, #e8f5e9, #e3f2fd);
      border-bottom: 2px solid var(--accent);
      overflow: hidden;
      height: 36px;
      display: flex;
      align-items: center;
    }
    .ticker-label {
      background: var(--accent);
      color: #fff;
      font-family: 'Rajdhani', sans-serif;
      font-weight: 700;
      font-size: 0.8rem;
      padding: 0 14px;
      height: 100%;
      display: flex;
      align-items: center;
      white-space: nowrap;
      letter-spacing: 1px;
    }
    .ticker-inner {
      display: flex;
      animation: ticker 40s linear infinite;
      white-space: nowrap;
    }
    .ticker-inner span {
      font-family: 'Rajdhani', sans-serif;
      font-size: 0.88rem;
      font-weight: 500;
      color: #1a3a2a;
      padding: 0 40px;
    }
    .ticker-inner span::after {
      content: "●";
      color: var(--accent);
      margin-left: 40px;
    }
    @keyframes ticker {
      0% { transform: translateX(0); }
      100% { transform: translateX(-50%); }
    }

    /* ── MINISTER STRIP ── */
    .minister-strip {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 24px 48px;
      background: linear-gradient(135deg, #f0f9ff 0%, #e8f5e9 50%, #fff8e1 100%);
      border-bottom: 1px solid #dde;
      gap: 24px;
    }
    .minister-card {
      display: flex;
      align-items: center;
      gap: 14px;
    }
    .minister-photo {
    width: 110px;
    height: 130px;
    border-radius: 8px;
    object-fit: cover;
    object-position: center top;
    border: 3px solid var(--accent);
    box-shadow: 0 4px 16px rgba(0,0,0,0.15);
    flex-shrink: 0;
    overflow: hidden;
    display: block;
    }
    .minister-info small {
      font-size: 0.7rem;
      color: #888;
      font-weight: 600;
      letter-spacing: 1px;
      text-transform: uppercase;
    }
    .minister-info p {
      font-family: 'Rajdhani', sans-serif;
      font-weight: 700;
      font-size: 1rem;
      color: var(--text-dark);
      margin-top: 2px;
    }
    .minister-info span {
      font-size: 0.78rem;
      color: #555;
    }
    .slogan-center {
      flex: 1;
      text-align: center;
      padding: 0 24px;
    }
    .slogan-center h2 {
      font-family: 'Teko', sans-serif;
      font-size: 2rem;
      color: var(--nav-bg);
      line-height: 1.15;
      letter-spacing: 1px;
    }
    .slogan-center p {
      font-size: 0.85rem;
      color: #555;
      margin-top: 6px;
      font-style: italic;
    }
    .tricolor-bar {
      display: flex;
      height: 4px;
      width: 80%;
      margin: 8px auto 0;
      border-radius: 2px;
      overflow: hidden;
    }
    .tricolor-bar div { flex: 1; }
    .tc-orange { background: #FF9933; }
    .tc-white { background: #fff; border-top: 1px solid #ddd; border-bottom: 1px solid #ddd; }
    .tc-green { background: #138808; }

    /* ── SEARCH ── */
    .search-section {
      background: var(--nav-bg);
      padding: 22px 32px;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 12px;
    }
    .search-section label {
      color: #cde8f5;
      font-family: 'Rajdhani', sans-serif;
      font-weight: 600;
      font-size: 1rem;
      letter-spacing: 1px;
    }
    .search-box {
      display: flex;
      gap: 0;
      border-radius: 6px;
      overflow: hidden;
      box-shadow: 0 2px 12px rgba(0,0,0,0.25);
    }
    .search-box input {
      width: 320px;
      padding: 11px 18px;
      border: none;
      font-size: 0.95rem;
      font-family: 'Noto Sans', sans-serif;
      outline: none;
      background: #fff;
      color: #1a2a3a;
    }
    .search-box button {
      background: var(--accent);
      border: none;
      padding: 0 22px;
      color: #fff;
      font-family: 'Rajdhani', sans-serif;
      font-weight: 700;
      font-size: 1rem;
      letter-spacing: 1px;
      cursor: pointer;
      transition: background 0.2s;
    }
    .search-box button:hover { background: #0097a7; }

    /* ── MAIN CONTENT ── */
    .main-content {
      max-width: 1200px;
      margin: 32px auto;
      padding: 0 24px;
    }

    /* ── AQI BLOCK ── */
    .aqi-section {
      display: flex;
      gap: 24px;
      margin-bottom: 18px;
    }
    .aqi-main {
      flex: 2;
      border-radius: 14px;
      padding: 28px;
      transition: background 1s;
      box-shadow: 0 4px 24px rgba(0,0,0,0.10);
      position: relative;
      overflow: hidden;
      min-height: 320px;
    }
    .aqi-main.good    { background: linear-gradient(135deg, #00c853 0%, #b9f6ca 100%); color: #003300; }
    .aqi-main.moderate{ background: linear-gradient(135deg, #ffd600 0%, #fff9c4 100%); color: #3d3000; }
    .aqi-main.severe  { background: linear-gradient(135deg, #d50000 0%, #ffcdd2 100%); color: #2a0000; }
    .aqi-city {
      font-family: 'Teko', sans-serif;
      font-size: 2rem;
      letter-spacing: 1px;
    }
    .aqi-number {
      font-family: 'Teko', sans-serif;
      font-size: 5.5rem;
      line-height: 1;
      font-weight: 600;
    }
    .aqi-label {
      font-family: 'Rajdhani', sans-serif;
      font-weight: 700;
      font-size: 1.2rem;
      letter-spacing: 2px;
      text-transform: uppercase;
      margin-bottom: 12px;
    }
    .aqi-status-badge {
      display: inline-block;
      padding: 4px 18px;
      border-radius: 20px;
      font-size: 0.85rem;
      font-weight: 600;
      background: rgba(255,255,255,0.5);
      margin-bottom: 16px;
    }
    .aqi-updated {
      font-size: 0.75rem;
      opacity: 0.7;
      margin-top: 8px;
    }
    /* mini chart inside aqi-main */
    .aqi-week-chart {
      margin-top: 18px;
    }
    .aqi-week-chart h4 {
      font-family: 'Rajdhani', sans-serif;
      font-weight: 600;
      font-size: 0.85rem;
      letter-spacing: 1px;
      margin-bottom: 10px;
      opacity: 0.8;
    }
    .bar-chart {
      display: flex;
      align-items: flex-end;
      gap: 8px;
      height: 120px;
      margin-top: 10px;
    }
    .bar-group {
      display: flex;
      flex-direction: column;
      align-items: center;
      flex: 1;
      gap: 4px;
    }
    .bar {
      width: 100%;
      border-radius: 4px 4px 0 0;
      transition: height 0.6s ease;
      min-width: 28px;
      background: rgba(255,255,255,0.55);
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }
    .bar-day {
      font-size: 0.68rem;
      font-weight: 600;
      letter-spacing: 0.5px;
      opacity: 0.75;
    }
    .bar-val {
      font-size: 0.65rem;
      font-weight: 700;
      opacity: 0.8;
    }

    /* AQI legend panel */
    .aqi-legend {
      flex: 1;
      display: flex;
      flex-direction: column;
      gap: 12px;
    }
    .legend-card {
      border-radius: 10px;
      padding: 14px 16px;
      flex: 1;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }
    .legend-card.good-card     { background: linear-gradient(135deg,#e8f5e9,#c8e6c9); border-left: 4px solid #00c853; }
    .legend-card.moderate-card { background: linear-gradient(135deg,#fffde7,#fff9c4); border-left: 4px solid #ffd600; }
    .legend-card.severe-card   { background: linear-gradient(135deg,#ffebee,#ffcdd2); border-left: 4px solid #d50000; }
    .legend-title {
      font-family: 'Rajdhani', sans-serif;
      font-weight: 700;
      font-size: 0.9rem;
      letter-spacing: 1px;
      text-transform: uppercase;
      color: #1a2a3a;
    }
    .legend-range {
      font-size: 0.7rem;
      color: #555;
      margin: 2px 0;
    }
    .pollutant-row {
      display: flex;
      gap: 10px;
      margin-top: 6px;
      flex-wrap: wrap;
    }
    .pollutant-tag {
      background: rgba(0,0,0,0.07);
      border-radius: 4px;
      padding: 2px 8px;
      font-size: 0.68rem;
      color: #333;
      font-weight: 600;
    }
    .pollutant-tag b { color: var(--nav-bg); }

    /* ── TEMP BLOCK ── */
    .temp-block {
      background: linear-gradient(135deg, #e3f2fd, #bbdefb);
      border-radius: 12px;
      padding: 18px 28px;
      display: flex;
      align-items: center;
      gap: 28px;
      margin-bottom: 18px;
      box-shadow: 0 2px 12px rgba(0,0,0,0.07);
    }
    .temp-icon { font-size: 2.8rem; }
    .temp-val {
      font-family: 'Teko', sans-serif;
      font-size: 3rem;
      color: #0d47a1;
      line-height: 1;
    }
    .temp-details { flex: 1; }
    .temp-details p { font-size: 0.82rem; color: #1a2a3a; line-height: 1.8; }
    .temp-details span { font-weight: 700; color: #0d47a1; }

    /* ── WEATHER FORECAST ── */
    .forecast-block {
      background: #f8fbff;
      border-radius: 12px;
      padding: 20px 28px;
      margin-bottom: 32px;
      box-shadow: 0 2px 12px rgba(0,0,0,0.07);
    }
    .forecast-block h3 {
      font-family: 'Rajdhani', sans-serif;
      font-weight: 700;
      font-size: 1.05rem;
      letter-spacing: 1px;
      color: var(--nav-bg);
      margin-bottom: 14px;
      text-transform: uppercase;
    }
    .forecast-row {
      display: flex;
      gap: 10px;
      overflow-x: auto;
    }
    .forecast-card {
      background: linear-gradient(160deg, #e3f2fd 0%, #f1f8e9 100%);
      border-radius: 10px;
      padding: 12px 16px;
      min-width: 100px;
      text-align: center;
      flex: 1;
      border: 1px solid #dde;
    }
    .forecast-day {
      font-family: 'Rajdhani', sans-serif;
      font-weight: 700;
      font-size: 0.78rem;
      color: #555;
      letter-spacing: 1px;
      text-transform: uppercase;
    }
    .forecast-icon { font-size: 1.8rem; margin: 4px 0; }
    .forecast-temp {
      font-family: 'Teko', sans-serif;
      font-size: 1.3rem;
      color: var(--nav-bg);
    }
    .forecast-aqi-dot {
      display: inline-block;
      width: 10px; height: 10px;
      border-radius: 50%;
      margin-right: 4px;
    }
    .forecast-desc { font-size: 0.68rem; color: #555; margin-top: 3px; }

    /* ── GUIDELINES ── */
    .guidelines-section {
      margin-bottom: 32px;
    }
    .section-title {
      font-family: 'Teko', sans-serif;
      font-size: 1.8rem;
      color: var(--nav-bg);
      border-left: 5px solid var(--accent);
      padding-left: 14px;
      margin-bottom: 18px;
      letter-spacing: 1px;
    }
    .guideline-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
      gap: 16px;
    }
    .guideline-card {
      background: var(--card-bg);
      border-radius: 10px;
      padding: 18px;
      border-top: 3px solid var(--accent);
      box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    .guideline-card .g-icon { font-size: 1.8rem; margin-bottom: 8px; }
    .guideline-card h4 {
      font-family: 'Rajdhani', sans-serif;
      font-weight: 700;
      font-size: 0.95rem;
      color: var(--nav-bg);
      margin-bottom: 6px;
    }
    .guideline-card p { font-size: 0.8rem; color: #555; line-height: 1.6; }

    /* ── FAQ ── */
    .faq-section { margin-bottom: 40px; }
    .faq-item {
      border-bottom: 1px solid #e0e0e0;
      padding: 14px 0;
    }
    .faq-q {
      font-family: 'Rajdhani', sans-serif;
      font-weight: 700;
      font-size: 1rem;
      color: var(--nav-bg);
      cursor: pointer;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .faq-q .arrow { transition: transform 0.3s; color: var(--accent); }
    .faq-a {
      font-size: 0.82rem;
      color: #555;
      line-height: 1.7;
      max-height: 0;
      overflow: hidden;
      transition: max-height 0.4s ease, padding 0.3s;
    }
    .faq-a.open { max-height: 200px; padding-top: 8px; }

    /* ── FOOTER ── */
    footer {
      background: var(--nav-bg);
      color: #7cb8d4;
      text-align: center;
      padding: 18px;
      font-size: 0.78rem;
      letter-spacing: 0.5px;
    }
    footer a { color: var(--accent); text-decoration: none; }

    /* scrollbar */
    ::-webkit-scrollbar { width: 6px; height: 6px; }
    ::-webkit-scrollbar-track { background: #f1f1f1; }
    ::-webkit-scrollbar-thumb { background: var(--accent); border-radius: 3px; }
  </style>
</head>
<body>

<!-- ── NAV ── -->
<nav>
  <div class="nav-logo">Vayu<span>Darpan</span></div>
  <div class="nav-links">
    <a href="catalogue.php">Catalogue</a>
    <a href="public_dashboard.php">Public Dashboard</a>
    <a href="signup.php">Sign Up</a>
    <a href="login.php" class="active">Login</a>
    <a href="logout.php" class="logout">Logout</a>
  </div>
</nav>

<!-- ── TICKER ── -->
<div class="ticker-wrap">
  <div class="ticker-label">📢 LIVE NEWS</div>
  <div style="overflow:hidden;flex:1;display:flex;align-items:center;">
    <div class="ticker-inner">
      <span>Delhi AQI touches 310 – severe category, authorities issue health advisory</span>
      <span>Mumbai records moderate AQI of 98 – marine winds provide relief</span>
      <span>CPCB deploys 10 new air monitoring stations across Tier-2 cities</span>
      <span>Govt bans crop burning in Punjab & Haryana ahead of winter season</span>
      <span>India ranks 8th globally in cities with worst air quality – WHO 2025 report</span>
      <span>Green Diwali campaign: 35% reduction in firecracker-related pollution recorded</span>
      <span>NCAP targets 40% reduction in PM10 & PM2.5 by 2026</span>
      <!-- duplicate for seamless loop -->
      <span>Delhi AQI touches 310 – severe category, authorities issue health advisory</span>
      <span>Mumbai records moderate AQI of 98 – marine winds provide relief</span>
      <span>CPCB deploys 10 new air monitoring stations across Tier-2 cities</span>
      <span>Govt bans crop burning in Punjab & Haryana ahead of winter season</span>
      <span>India ranks 8th globally in cities with worst air quality – WHO 2025 report</span>
      <span>Green Diwali campaign: 35% reduction in firecracker-related pollution recorded</span>
      <span>NCAP targets 40% reduction in PM10 & PM2.5 by 2026</span>
    </div>
  </div>
</div>

<!-- ── MINISTER STRIP ── -->
<div class="minister-strip">
  <!-- Bhupendra Yadav (left) -->
  <div class="minister-card">
    <img class="minister-photo" src="https://static.pib.gov.in/WriteReadData/PressNote/2025/Jul/I20250704385.JPEG" alt="Bhupendra Yadav" />
    <div class="minister-info">
      <small>Minister of Environment</small>
      <p>Shri Bhupender Yadav</p>
      <span>Ministry of Environment, Forest<br>& Climate Change, Govt. of India</span>
    </div>
  </div>

  <!-- Slogan centre -->
  <div class="slogan-center">
    <h2>"Clean Air is Every Citizen's Right —<br>Together We Breathe, Together We Thrive."</h2>
    <div class="tricolor-bar"><div class="tc-orange"></div><div class="tc-white"></div><div class="tc-green"></div></div>
    <p>Under the visionary leadership of Hon. Prime Minister Shri Narendra Modi<br>National Clean Air Programme (NCAP) — Building a Greener, Healthier Bharat</p>
  </div>

  <!-- PM Modi (right) -->
  <div class="minister-card" style="flex-direction:row-reverse;text-align:right;">
    <img class="minister-photo" src="https://www.imageshine.in/uploads/gallery/PNG_Images_of_Narendra_Modi.png" alt="Narendra Modi" />
    <div class="minister-info" style="text-align:right;">
      <small>Prime Minister of India</small>
      <p>Shri Narendra Modi</p>
      <span>Government of India<br>New Delhi — 110 001</span>
    </div>
  </div>
</div>

<!-- ── SEARCH ── -->
<div class="search-section">
  <label>🔍 Search AQI for Your City:</label>
  <div class="search-box">
    <input type="text" id="cityInput" placeholder="Enter city name e.g. Mumbai, Delhi…" />
    <button onclick="searchCity()">CHECK AQI</button>
  </div>
</div>

<!-- ── MAIN ── -->
<div class="main-content">

  <!-- AQI BLOCK -->
  <div class="aqi-section">
    <!-- Left: live AQI + week chart -->
    <div class="aqi-main moderate" id="aqiMain">
      <div class="aqi-city" id="aqiCity">📍 Mumbai, Maharashtra</div>
      <div class="aqi-number" id="aqiNumber">142</div>
      <div class="aqi-label">Air Quality Index</div>
      <div class="aqi-status-badge" id="aqiBadge">⚠️ Moderate — Sensitive groups should limit outdoor exposure</div>
      <div class="aqi-updated">Last updated: <span id="aqiTime"></span></div>

      <!-- Week bar chart -->
      <div class="aqi-week-chart">
        <h4>📊 PAST 7-DAY AQI TREND</h4>
        <div class="bar-chart" id="barChart"></div>
      </div>
    </div>

    <!-- Right: legend -->
    <div class="aqi-legend">
      <div class="legend-card good-card">
        <div class="legend-title">🟢 Pleasant / Good</div>
        <div class="legend-range">AQI 0–100 &nbsp;|&nbsp; Safe for all activities</div>
        <div class="pollutant-row">
          <div class="pollutant-tag">PM2.5 <b>&lt;30µg/m³</b></div>
          <div class="pollutant-tag">PM10 <b>&lt;50µg/m³</b></div>
          <div class="pollutant-tag">Dust <b>&lt;15%</b></div>
          <div class="pollutant-tag">NO₂ <b>&lt;40µg/m³</b></div>
        </div>
      </div>
      <div class="legend-card moderate-card">
        <div class="legend-title">🟡 Moderate</div>
        <div class="legend-range">AQI 101–200 &nbsp;|&nbsp; Sensitive groups at risk</div>
        <div class="pollutant-row">
          <div class="pollutant-tag">PM2.5 <b>30–60µg/m³</b></div>
          <div class="pollutant-tag">PM10 <b>50–100µg/m³</b></div>
          <div class="pollutant-tag">Dust <b>15–35%</b></div>
          <div class="pollutant-tag">SO₂ <b>40–80µg/m³</b></div>
        </div>
      </div>
      <div class="legend-card severe-card">
        <div class="legend-title">🔴 Severe / Hazardous</div>
        <div class="legend-range">AQI 201+ &nbsp;|&nbsp; Avoid all outdoor activity</div>
        <div class="pollutant-row">
          <div class="pollutant-tag">PM2.5 <b>&gt;60µg/m³</b></div>
          <div class="pollutant-tag">PM10 <b>&gt;100µg/m³</b></div>
          <div class="pollutant-tag">Dust <b>&gt;35%</b></div>
          <div class="pollutant-tag">CO <b>&gt;2mg/m³</b></div>
        </div>
      </div>
    </div>
  </div>

  <!-- TEMP BLOCK -->
  <div class="temp-block">
    <div class="temp-icon">🌡️</div>
    <div class="temp-val" id="tempVal">34°C</div>
    <div class="temp-details">
      <p>Feels like <span id="tempFeels">37°C</span> &nbsp;|&nbsp; Humidity: <span id="tempHum">72%</span> &nbsp;|&nbsp; Wind: <span id="tempWind">18 km/h SW</span></p>
      <p>Visibility: <span id="tempVis">6 km</span> &nbsp;|&nbsp; Pressure: <span id="tempPres">1012 hPa</span></p>
    </div>
    <div style="font-size:2rem;" id="weatherIcon">☀️</div>
    <div style="font-family:'Rajdhani',sans-serif;font-size:1rem;color:#1a2a3a;font-weight:600;"><span id="weatherDesc">Partly Sunny</span></div>
  </div>

  <!-- FORECAST -->
  <div class="forecast-block">
    <h3>🗓 7-Day Weather & AQI Forecast</h3>
    <div class="forecast-row" id="forecastRow"></div>
  </div>

  <!-- GUIDELINES -->
  <div class="guidelines-section">
    <div class="section-title">Health & Safety Guidelines</div>
    <div class="guideline-grid">
      <div class="guideline-card">
        <div class="g-icon">😷</div>
        <h4>Wear N95 Masks</h4>
        <p>On days with AQI above 150, always wear a certified N95/FFP2 mask when stepping outdoors, especially in high-traffic zones.</p>
      </div>
      <div class="guideline-card">
        <div class="g-icon">🚗</div>
        <h4>Reduce Vehicle Use</h4>
        <p>Opt for public transport, carpooling, or cycling on high-pollution days. Avoid idling engines for more than 30 seconds.</p>
      </div>
      <div class="guideline-card">
        <div class="g-icon">🌳</div>
        <h4>Plant Trees</h4>
        <p>Trees absorb CO₂ and particulate matter. Participate in local plantation drives and maintain green cover around your home.</p>
      </div>
      <div class="guideline-card">
        <div class="g-icon">🏭</div>
        <h4>Report Industrial Pollution</h4>
        <p>Use the CPCB Sameer App or call 14422 to report illegal industrial emissions and burning of waste in your locality.</p>
      </div>
      <div class="guideline-card">
        <div class="g-icon">🏠</div>
        <h4>Indoor Air Quality</h4>
        <p>Use air purifiers with HEPA filters indoors. Keep windows closed on severe AQI days. Avoid burning agarbatti or candles excessively.</p>
      </div>
      <div class="guideline-card">
        <div class="g-icon">🧒</div>
        <h4>Protect Children & Elderly</h4>
        <p>Children, elderly and those with respiratory conditions should avoid morning outdoor activity when pollution peaks are highest.</p>
      </div>
    </div>
  </div>

  <!-- FAQs -->
  <div class="faq-section">
    <div class="section-title">Frequently Asked Questions</div>

    <div class="faq-item">
      <div class="faq-q" onclick="toggleFaq(this)">What is AQI and how is it calculated? <span class="arrow">▼</span></div>
      <div class="faq-a">AQI stands for Air Quality Index. It is calculated based on the concentration of key pollutants — PM2.5, PM10, SO₂, NO₂, CO, O₃ — measured by CPCB monitoring stations. Each pollutant is converted to a sub-index and the highest sub-index becomes the overall AQI.</div>
    </div>
    <div class="faq-item">
      <div class="faq-q" onclick="toggleFaq(this)">What is a safe AQI level? <span class="arrow">▼</span></div>
      <div class="faq-a">An AQI of 0–50 is "Good", 51–100 is "Satisfactory", 101–200 is "Moderate", 201–300 is "Poor", 301–400 is "Very Poor", and 401–500 is "Severe". For healthy adults, AQI below 100 is generally considered safe for outdoor activities.</div>
    </div>
    <div class="faq-item">
      <div class="faq-q" onclick="toggleFaq(this)">Which cities in India have the worst air quality? <span class="arrow">▼</span></div>
      <div class="faq-a">Historically, cities in the Indo-Gangetic Plain — Delhi, Kanpur, Lucknow, Patna, and Varanasi — record the highest AQI levels, particularly during winter months (October–January) due to crop stubble burning, cold air trapping pollutants, and vehicular emissions.</div>
    </div>
    <div class="faq-item">
      <div class="faq-q" onclick="toggleFaq(this)">How can I check real-time AQI for my city? <span class="arrow">▼</span></div>
      <div class="faq-a">You can use this VayuDarpan portal or the official CPCB Sameer App (available on Android & iOS) to check real-time AQI for over 200 cities across India.</div>
    </div>
    <div class="faq-item">
      <div class="faq-q" onclick="toggleFaq(this)">What is PM2.5 and why is it dangerous? <span class="arrow">▼</span></div>
      <div class="faq-a">PM2.5 refers to fine particulate matter with a diameter of 2.5 micrometres or less. These tiny particles can penetrate deep into the lungs and even enter the bloodstream, causing cardiovascular and respiratory diseases, lung cancer, and premature death with prolonged exposure.</div>
    </div>
  </div>

</div><!-- end main-content -->

<footer>
  © 2026 VayuDarpan Developed under guidence Bharathi ma'am by Vansh Kataria, Atharva kamath
</footer>

<script>
  // ── TIME ──
  const aqiTime = document.getElementById('aqiTime');
  function updateTime() {
    aqiTime.textContent = new Date().toLocaleTimeString('en-IN', {hour:'2-digit',minute:'2-digit'}) + ', ' +
      new Date().toLocaleDateString('en-IN',{day:'2-digit',month:'short',year:'numeric'});
  }
  updateTime(); setInterval(updateTime, 60000);

  // ── BAR CHART ──
  const weekData = [
    {day:'Mon', val:188, cls:'severe'},
    {day:'Tue', val:165, cls:'severe'},
    {day:'Wed', val:130, cls:'moderate'},
    {day:'Thu', val:110, cls:'moderate'},
    {day:'Fri', val:95,  cls:'good'},
    {day:'Sat', val:142, cls:'moderate'},
    {day:'Sun', val:158, cls:'moderate'},
  ];
  const maxVal = Math.max(...weekData.map(d=>d.val));
  const barChart = document.getElementById('barChart');
  weekData.forEach(d => {
    const h = Math.round((d.val / maxVal) * 70);
    barChart.innerHTML += `
      <div class="bar-group">
        <div class="bar-val">${d.val}</div>
        <div class="bar" style="height:${h}px;"></div>
        <div class="bar-day">${d.day}</div>
      </div>`;
  });

  // ── FORECAST ──
  const forecastData = [
    {day:'Today', icon:'⛅', temp:'34°C', aqi:142, color:'#ffd600', desc:'Moderate'},
    {day:'Tue',   icon:'🌤',  temp:'33°C', aqi:98,  color:'#00c853', desc:'Good'},
    {day:'Wed',   icon:'☀️',  temp:'35°C', aqi:88,  color:'#00c853', desc:'Good'},
    {day:'Thu',   icon:'🌧',  temp:'29°C', aqi:55,  color:'#00c853', desc:'Satisfactory'},
    {day:'Fri',   icon:'🌦',  temp:'30°C', aqi:120, color:'#ffd600', desc:'Moderate'},
    {day:'Sat',   icon:'⛅', temp:'32°C', aqi:160, color:'#ff6d00', desc:'Poor'},
    {day:'Sun',   icon:'🌤',  temp:'33°C', aqi:135, color:'#ffd600', desc:'Moderate'},
  ];
  const fr = document.getElementById('forecastRow');
  forecastData.forEach(f => {
    fr.innerHTML += `
      <div class="forecast-card">
        <div class="forecast-day">${f.day}</div>
        <div class="forecast-icon">${f.icon}</div>
        <div class="forecast-temp">${f.temp}</div>
        <div class="forecast-desc">
          <span class="forecast-aqi-dot" style="background:${f.color}"></span>
          AQI ${f.aqi} – ${f.desc}
        </div>
      </div>`;
  });

  // ── CITY SEARCH ──
  const WEATHER_API_KEY = "ad7929500ad79921beaed9fe2c862c53";

  async function searchCity(defaultCity = null) {
    const city = defaultCity || document.getElementById('cityInput').value.trim();
    if (!city) return alert('Please enter a city name.');
    
    try {
      // 1. Fetch Weather
      const weatherUrl = `https://api.openweathermap.org/data/2.5/weather?q=${encodeURIComponent(city)}&appid=${WEATHER_API_KEY}&units=metric`;
      const weatherRes = await fetch(weatherUrl);
      if(!weatherRes.ok) throw new Error("City not found or API error");
      const weatherData = await weatherRes.json();
      
      // Update Weather UI
      document.getElementById('tempVal').textContent = Math.round(weatherData.main.temp) + '°C';
      document.getElementById('tempFeels').textContent = Math.round(weatherData.main.feels_like) + '°C';
      document.getElementById('tempHum').textContent = weatherData.main.humidity + '%';
      document.getElementById('tempWind').textContent = (weatherData.wind.speed * 3.6).toFixed(1) + ' km/h';
      document.getElementById('tempVis').textContent = (weatherData.visibility / 1000).toFixed(1) + ' km';
      document.getElementById('tempPres').textContent = weatherData.main.pressure + ' hPa';
      document.getElementById('weatherDesc').textContent = weatherData.weather[0].description.toUpperCase();
      
      const { lat, lon } = weatherData.coord;
      
      // 2. Fetch Air Pollution
      const aqiUrl = `https://api.openweathermap.org/data/2.5/air_pollution?lat=${lat}&lon=${lon}&appid=${WEATHER_API_KEY}`;
      const aqiRes = await fetch(aqiUrl);
      const aqiData = await aqiRes.json();
      
      const pollution = aqiData.list[0];
      const owmAqi = pollution.main.aqi; 
      
      // Calculate realistic Indian AQI using PM2.5 roughly
      let pm25 = pollution.components.pm2_5; // Fix: was pm25 instead of pm2_5
      let aqiVal = Math.round(pm25 * 3.5); 
      if (aqiVal < 20) aqiVal = 25; // Base minimum
      if (isNaN(aqiVal)) aqiVal = 50; // Fallback
      
      let aqiClass = 'good';
      let aqiBadgeMsg = '✅ Pleasant — Air quality is satisfactory';
      
      if(aqiVal <= 100) { aqiClass = 'good'; }
      else if(aqiVal <= 200) { aqiClass = 'moderate'; aqiBadgeMsg = '⚠️ Moderate — Sensitive groups should limit outdoor exposure'; }
      else { aqiClass = 'severe'; aqiBadgeMsg = '🚨 Severe — Avoid outdoor activities, wear N95 mask'; }

      // Update AQI UI
      document.getElementById('aqiCity').textContent = '📍 ' + weatherData.name + ', ' + weatherData.sys.country;
      document.getElementById('aqiNumber').textContent = aqiVal; 
      document.getElementById('aqiMain').className = 'aqi-main ' + aqiClass;
      document.getElementById('aqiBadge').textContent = aqiBadgeMsg;
      
    } catch(e) {
      alert("Error: " + e.message);
    }
  }

  document.getElementById('cityInput').addEventListener('keydown', e => {
    if (e.key === 'Enter') searchCity();
  });

  // Call on load with default city
  window.addEventListener('load', () => {
    searchCity('New Delhi');
  });

  // ── FAQ TOGGLE ──
  function toggleFaq(el) {
    const ans = el.nextElementSibling;
    const arrow = el.querySelector('.arrow');
    const isOpen = ans.classList.toggle('open');
    arrow.style.transform = isOpen ? 'rotate(180deg)' : '';
  }
</script>
</body>
</html>