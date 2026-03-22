<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>VayuDarpan – Catalogue</title>
  <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@400;500;600;700&family=Noto+Sans:wght@300;400;600&family=Teko:wght@400;500;600&display=swap" rel="stylesheet"/>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    :root {
      --nav-bg: #0d2137;
      --accent: #00bcd4;
      --good: #00c853;
      --moderate: #ffc107;
      --poor: #ff6d00;
      --severe: #e53935;
      --white: #ffffff;
      --text: #1a2a3a;
      --border: #dde4ea;
      --card: #f4f8fb;
      --sidebar-w: 230px;
    }
    * { margin:0; padding:0; box-sizing:border-box; }

    body {
      font-family:'Noto Sans',sans-serif;
      background: #f0f4f8;
      color: var(--text);
      min-height: 100vh;
    }

    /* cloudy background */
    body::before {
      content:'';
      position:fixed; inset:0; z-index:-1;
      background:
        radial-gradient(ellipse 800px 300px at 10% 20%, rgba(255,255,255,0.95) 0%, transparent 70%),
        radial-gradient(ellipse 600px 250px at 80% 10%, rgba(224,242,254,0.9) 0%, transparent 60%),
        radial-gradient(ellipse 700px 200px at 50% 80%, rgba(232,245,233,0.8) 0%, transparent 60%),
        linear-gradient(160deg, #e8f4fd 0%, #f5faff 40%, #edf7ee 100%);
    }

    /* ── TOP NAV ── */
    nav {
      background: var(--nav-bg);
      display:flex; align-items:center; justify-content:space-between;
      padding:0 32px; height:56px;
      position:sticky; top:0; z-index:3000;
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

    /* ── LAYOUT ── */
    .layout { display:flex; min-height:calc(100vh - 56px); }

    /* ── SIDEBAR ── */
    .sidebar {
      width: var(--sidebar-w);
      background: var(--nav-bg);
      position: sticky; top:56px;
      height: calc(100vh - 56px);
      overflow-y: auto;
      flex-shrink: 0;
      box-shadow: 3px 0 16px rgba(0,0,0,0.15);
      display:flex; flex-direction:column;
    }
    .sidebar-header {
      padding:22px 20px 16px;
      border-bottom:1px solid rgba(255,255,255,0.1);
    }
    .sidebar-header h2 {
      font-family:'Teko',sans-serif; font-size:1.5rem;
      color:var(--accent); letter-spacing:2px;
    }
    .sidebar-header p { color:#7cb8d4; font-size:0.72rem; margin-top:2px; }

    .sidebar-nav { padding:12px 0; flex:1; }
    .sidebar-item {
      display:flex; align-items:center; gap:12px;
      padding:12px 20px; cursor:pointer;
      color:#a8c8e0; font-family:'Rajdhani',sans-serif;
      font-weight:600; font-size:0.92rem; letter-spacing:0.5px;
      transition:all 0.2s; border-left:3px solid transparent;
      text-decoration:none;
    }
    .sidebar-item:hover { background:rgba(0,188,212,0.1); color:#fff; }
    .sidebar-item.active {
      background:rgba(0,188,212,0.15);
      color:var(--accent); border-left-color:var(--accent);
    }
    .sidebar-item .s-icon { font-size:1.2rem; width:24px; text-align:center; flex-shrink:0; }
    .sidebar-item .s-num {
      margin-left:auto; background:rgba(255,255,255,0.1);
      color:#7cb8d4; font-size:0.65rem; padding:2px 7px;
      border-radius:10px; font-weight:700;
    }

    /* ── MAIN CONTENT ── */
    .main {
      flex:1; padding:32px 36px;
      overflow-y:auto;
    }

    /* section (each catalogue page) */
    .cat-section { display:none; }
    .cat-section.active { display:block; }

    .section-header { margin-bottom:28px; }
    .section-header h1 {
      font-family:'Teko',sans-serif; font-size:2.2rem;
      color:var(--nav-bg); letter-spacing:1px;
    }
    .section-header h1 span { color:var(--accent); }
    .section-header p { color:#555; font-size:0.88rem; margin-top:4px; line-height:1.6; }
    .section-divider {
      height:3px; width:60px;
      background:linear-gradient(90deg,var(--accent),transparent);
      margin:10px 0 0; border-radius:2px;
    }

    /* ── POLLUTANT CARDS ── */
    .pollutant-grid {
      display:grid;
      grid-template-columns:repeat(auto-fill, minmax(300px,1fr));
      gap:20px;
    }
    .pollutant-card {
      background:#fff; border-radius:14px;
      overflow:hidden; box-shadow:0 3px 16px rgba(0,0,0,0.08);
      transition:transform 0.2s, box-shadow 0.2s;
    }
    .pollutant-card:hover { transform:translateY(-4px); box-shadow:0 8px 28px rgba(0,0,0,0.13); }
    .p-card-top {
      padding:20px; position:relative; min-height:110px;
      display:flex; flex-direction:column; justify-content:flex-end;
    }
    .p-card-top h2 {
      font-family:'Teko',sans-serif; font-size:2.2rem;
      color:#fff; line-height:1; letter-spacing:1px;
    }
    .p-card-top h2 sub { font-size:1rem; }
    .p-card-top p { color:rgba(255,255,255,0.85); font-size:0.78rem; margin-top:3px; }
    .p-card-icon {
      position:absolute; right:16px; top:14px;
      font-size:2.4rem; opacity:0.35;
    }
    .p-card-body { padding:18px; }
    .p-info-row { display:flex; flex-direction:column; gap:10px; }
    .p-info-item { display:flex; gap:10px; align-items:flex-start; }
    .p-info-label {
      font-family:'Rajdhani',sans-serif; font-weight:700;
      font-size:0.7rem; letter-spacing:1px; color:#888;
      text-transform:uppercase; min-width:80px; padding-top:2px;
    }
    .p-info-val { font-size:0.8rem; color:#333; line-height:1.5; }
    .danger-badge {
      display:inline-flex; align-items:center; gap:5px;
      padding:5px 14px; border-radius:20px; margin-top:12px;
      font-family:'Rajdhani',sans-serif; font-weight:700;
      font-size:0.78rem; letter-spacing:1px; color:#fff;
    }
    .safe-limit {
      background:var(--card); border-radius:8px;
      padding:8px 12px; margin-top:10px;
      font-size:0.75rem; color:#444;
      border-left:3px solid var(--accent);
    }
    .safe-limit b { color:var(--nav-bg); }

    /* colors per pollutant */
    .pm25-top  { background:linear-gradient(135deg,#b71c1c,#e53935); }
    .pm10-top  { background:linear-gradient(135deg,#e65100,#ff6d00); }
    .no2-top   { background:linear-gradient(135deg,#f57f17,#fbc02d); }
    .so2-top   { background:linear-gradient(135deg,#1565c0,#1e88e5); }
    .co-top    { background:linear-gradient(135deg,#4a148c,#7b1fa2); }
    .ozone-top { background:linear-gradient(135deg,#1b5e20,#388e3c); }

    /* ── DISEASE SECTION ── */
    .disease-grid {
      display:grid;
      grid-template-columns:repeat(auto-fill,minmax(280px,1fr));
      gap:20px;
    }
    .disease-card {
      background:#fff; border-radius:14px;
      padding:22px; box-shadow:0 3px 14px rgba(0,0,0,0.07);
      border-top:4px solid var(--accent);
      transition:transform 0.2s;
    }
    .disease-card:hover { transform:translateY(-3px); }
    .disease-card .d-icon { font-size:2rem; margin-bottom:10px; }
    .disease-card h3 {
      font-family:'Rajdhani',sans-serif; font-weight:700;
      font-size:1.05rem; color:var(--nav-bg); margin-bottom:6px;
    }
    .disease-card p { font-size:0.78rem; color:#555; line-height:1.6; margin-bottom:10px; }
    .pollutant-tags { display:flex; flex-wrap:wrap; gap:5px; margin-bottom:10px; }
    .ptag {
      padding:2px 10px; border-radius:10px; font-size:0.68rem;
      font-weight:700; color:#fff;
    }
    .risk-row { display:flex; gap:6px; flex-wrap:wrap; }
    .risk-chip {
      display:flex; align-items:center; gap:4px;
      background:var(--card); border-radius:6px;
      padding:3px 9px; font-size:0.68rem; color:#333; font-weight:600;
    }
    .risk-dot { width:8px; height:8px; border-radius:50%; }

    /* ── SOURCE SECTION ── */
    .source-grid {
      display:grid;
      grid-template-columns:repeat(auto-fill,minmax(260px,1fr));
      gap:20px; margin-bottom:28px;
    }
    .source-card {
      background:#fff; border-radius:14px;
      overflow:hidden; box-shadow:0 3px 14px rgba(0,0,0,0.07);
      transition:transform 0.2s;
    }
    .source-card:hover { transform:translateY(-3px); }
    .source-top {
      padding:20px; text-align:center;
      background:linear-gradient(135deg,#e3f2fd,#e8f5e9);
    }
    .source-top .src-icon { font-size:2.8rem; }
    .source-top h3 {
      font-family:'Rajdhani',sans-serif; font-weight:700;
      font-size:1rem; color:var(--nav-bg); margin-top:8px;
    }
    .source-body { padding:16px; }
    .contrib-bar-wrap { margin:10px 0; }
    .contrib-bar-wrap label {
      font-size:0.7rem; color:#888; font-weight:700;
      letter-spacing:0.5px; text-transform:uppercase;
    }
    .contrib-bar-bg {
      height:10px; background:#eee; border-radius:5px; margin-top:5px; overflow:hidden;
    }
    .contrib-bar-fill {
      height:100%; border-radius:5px;
      background:linear-gradient(90deg,var(--accent),var(--nav-bg));
      transition:width 1s ease;
    }
    .contrib-pct {
      font-family:'Teko',sans-serif; font-size:1.4rem;
      color:var(--nav-bg); margin-top:4px;
    }
    .source-body p { font-size:0.77rem; color:#555; line-height:1.6; }

    .pie-chart-wrap {
      background:#fff; border-radius:14px; padding:24px;
      box-shadow:0 3px 14px rgba(0,0,0,0.07);
      display:flex; align-items:center; gap:32px;
      flex-wrap:wrap;
    }
    .pie-chart-wrap h3 {
      font-family:'Rajdhani',sans-serif; font-weight:700;
      font-size:1rem; color:var(--nav-bg); margin-bottom:16px;
      letter-spacing:1px; text-transform:uppercase;
      width:100%;
    }
    .pie-chart-wrap canvas { max-width:220px; }
    .src-legend { flex:1; min-width:180px; }
    .src-leg-item {
      display:flex; align-items:center; gap:10px;
      padding:6px 0; border-bottom:1px solid #f0f0f0;
      font-size:0.8rem;
    }
    .src-leg-dot { width:12px; height:12px; border-radius:3px; flex-shrink:0; }
    .src-leg-item span { flex:1; color:#333; }
    .src-leg-item b { color:var(--nav-bg); }

    /* ── CITY HISTORY ── */
    .city-select-row {
      display:flex; gap:10px; flex-wrap:wrap; margin-bottom:20px;
    }
    .city-btn {
      padding:7px 18px; border-radius:20px; border:2px solid var(--border);
      background:#fff; font-family:'Rajdhani',sans-serif; font-weight:600;
      font-size:0.85rem; cursor:pointer; transition:all 0.2s; color:var(--text);
    }
    .city-btn:hover { border-color:var(--accent); color:var(--accent); }
    .city-btn.active { background:var(--nav-bg); color:#fff; border-color:var(--nav-bg); }
    .history-chart-card {
      background:#fff; border-radius:14px; padding:24px;
      box-shadow:0 3px 14px rgba(0,0,0,0.07); margin-bottom:20px;
    }
    .history-chart-card h3 {
      font-family:'Rajdhani',sans-serif; font-weight:700;
      font-size:1rem; color:var(--nav-bg); margin-bottom:16px;
      letter-spacing:1px; text-transform:uppercase;
    }
    .best-worst-row { display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-top:16px; }
    .bw-card {
      border-radius:10px; padding:16px;
      display:flex; align-items:center; gap:14px;
    }
    .bw-card.best { background:linear-gradient(135deg,#e8f5e9,#f1f8e9); border-left:4px solid var(--good); }
    .bw-card.worst { background:linear-gradient(135deg,#ffebee,#fff3e0); border-left:4px solid var(--severe); }
    .bw-icon { font-size:2rem; }
    .bw-val { font-family:'Teko',sans-serif; font-size:1.6rem; }
    .bw-card.best  .bw-val { color:#1b5e20; }
    .bw-card.worst .bw-val { color:#b71c1c; }
    .bw-label { font-size:0.72rem; color:#666; }

    /* ── SCHEMES ── */
    .schemes-grid {
      display:grid; grid-template-columns:repeat(auto-fill,minmax(300px,1fr));
      gap:20px;
    }
    .scheme-card {
      background:#fff; border-radius:14px; padding:22px;
      box-shadow:0 3px 14px rgba(0,0,0,0.07);
      border-top:4px solid var(--nav-bg);
      transition:transform 0.2s;
    }
    .scheme-card:hover { transform:translateY(-3px); }
    .scheme-card .sc-icon { font-size:1.8rem; margin-bottom:10px; }
    .scheme-card h3 {
      font-family:'Rajdhani',sans-serif; font-weight:700;
      font-size:1rem; color:var(--nav-bg); margin-bottom:4px;
    }
    .scheme-card .sc-full { font-size:0.72rem; color:#888; margin-bottom:10px; }
    .scheme-card p { font-size:0.78rem; color:#555; line-height:1.6; margin-bottom:10px; }
    .status-pill {
      display:inline-block; padding:3px 12px; border-radius:12px;
      font-size:0.68rem; font-weight:700; letter-spacing:1px;
    }
    .status-active   { background:#e8f5e9; color:#1b5e20; border:1px solid var(--good); }
    .status-ongoing  { background:#fff8e1; color:#f57f17; border:1px solid var(--moderate); }
    .status-enforced { background:#e3f2fd; color:#1565c0; border:1px solid #1e88e5; }
    .scheme-card ul {
      margin:8px 0 10px 16px; font-size:0.77rem; color:#444; line-height:1.8;
    }

    /* ── DEVICES ── */
    .devices-grid {
      display:grid; grid-template-columns:repeat(auto-fill,minmax(280px,1fr));
      gap:20px;
    }
    .device-card {
      background:#fff; border-radius:14px; overflow:hidden;
      box-shadow:0 3px 14px rgba(0,0,0,0.07);
      transition:transform 0.2s;
    }
    .device-card:hover { transform:translateY(-3px); }
    .device-top {
      padding:22px; text-align:center;
      background:linear-gradient(135deg,#e8eaf6,#e3f2fd);
    }
    .device-top .dv-icon { font-size:2.8rem; }
    .device-top h3 {
      font-family:'Rajdhani',sans-serif; font-weight:700;
      font-size:1rem; color:var(--nav-bg); margin-top:8px;
    }
    .device-body { padding:16px; }
    .device-body p { font-size:0.78rem; color:#555; line-height:1.6; margin-bottom:10px; }
    .effectiveness-wrap { margin:10px 0; }
    .effectiveness-wrap label {
      font-size:0.7rem; color:#888; font-weight:700;
      letter-spacing:0.5px; text-transform:uppercase;
      display:flex; justify-content:space-between;
    }
    .eff-bar-bg {
      height:8px; background:#eee; border-radius:4px; margin-top:5px; overflow:hidden;
    }
    .eff-bar-fill {
      height:100%; border-radius:4px;
      background:linear-gradient(90deg,var(--good),#00acc1);
    }
    .device-tags { display:flex; flex-wrap:wrap; gap:5px; margin-top:10px; }
    .device-tag {
      background:var(--card); border-radius:4px;
      padding:2px 8px; font-size:0.68rem; color:#445; font-weight:600;
    }

    /* scrollbar */
    ::-webkit-scrollbar { width:5px; }
    ::-webkit-scrollbar-track { background:#f1f1f1; }
    ::-webkit-scrollbar-thumb { background:var(--accent); border-radius:3px; }

    /* footer */
    footer {
      background:var(--nav-bg); color:#7cb8d4;
      text-align:center; padding:14px;
      font-size:0.78rem; margin-top:0;
    }
    footer a { color:var(--accent); text-decoration:none; }
  </style>
</head>
<body>

<!-- TOP NAV -->
<nav>
  <div class="nav-logo">Vayu<span>Darpan</span></div>
  <div class="nav-links">
    <a href="index.php">Home</a>
    <a href="catalogue.php" class="active">Catalogue</a>
    <a href="public_dashboard.php">Public Dashboard</a>
    <a href="signup.php">Sign Up</a>
    <a href="login.php">Login</a>
    <a href="logout.php" class="logout">Logout</a>
  </div>
</nav>

<!-- LAYOUT -->
<div class="layout">

  <!-- SIDEBAR -->
  <div class="sidebar">
    <div class="sidebar-header">
      <h2>CATALOGUE</h2>
      <p>Air Pollution Reference Library</p>
    </div>
    <div class="sidebar-nav">
      <a class="sidebar-item active" onclick="showSection('pollutants',this)">
        <span class="s-icon">🔬</span> Pollutant Encyclopedia <span class="s-num">6</span>
      </a>
      <a class="sidebar-item" onclick="showSection('diseases',this)">
        <span class="s-icon">🫁</span> Health Impact <span class="s-num">4</span>
      </a>
      <a class="sidebar-item" onclick="showSection('sources',this)">
        <span class="s-icon">🏭</span> Pollution Sources <span class="s-num">5</span>
      </a>
      <a class="sidebar-item" onclick="showSection('history',this)">
        <span class="s-icon">📈</span> City AQI History <span class="s-num">6</span>
      </a>
      <a class="sidebar-item" onclick="showSection('schemes',this)">
        <span class="s-icon">🏛️</span> Govt. Schemes & Laws <span class="s-num">5</span>
      </a>
      <a class="sidebar-item" onclick="showSection('devices',this)">
        <span class="s-icon">⚙️</span> Control Devices <span class="s-num">5</span>
      </a>
    </div>
  </div>

  <!-- MAIN CONTENT -->
  <div class="main">

    <!-- ══ 1. POLLUTANT ENCYCLOPEDIA ══ -->
    <div class="cat-section active" id="sec-pollutants">
      <div class="section-header">
        <h1>Pollutant <span>Encyclopedia</span></h1>
        <p>Detailed reference for all major air pollutants — their sources, health effects, and safe exposure limits.</p>
        <div class="section-divider"></div>
      </div>
      <div class="pollutant-grid">

        <!-- PM2.5 -->
        <div class="pollutant-card">
          <div class="p-card-top pm25-top">
            <span class="p-card-icon">🌫️</span>
            <h2>PM<sub>2.5</sub></h2>
            <p>Fine Particulate Matter (&lt;2.5µm)</p>
          </div>
          <div class="p-card-body">
            <div class="p-info-row">
              <div class="p-info-item"><span class="p-info-label">What it is</span><span class="p-info-val">Microscopic solid or liquid particles small enough to enter deep lung tissue and bloodstream.</span></div>
              <div class="p-info-item"><span class="p-info-label">Sources</span><span class="p-info-val">Vehicle exhaust, crop burning, industrial emissions, construction dust, cooking smoke.</span></div>
              <div class="p-info-item"><span class="p-info-label">Health Effect</span><span class="p-info-val">Respiratory diseases, heart attacks, stroke, premature death with long-term exposure.</span></div>
            </div>
            <div class="safe-limit">WHO Safe Limit: <b>15 µg/m³ (annual)</b> | India NAAQS: <b>40 µg/m³</b></div>
            <div class="danger-badge" style="background:#e53935;">🔴 Very High Danger</div>
          </div>
        </div>

        <!-- PM10 -->
        <div class="pollutant-card">
          <div class="p-card-top pm10-top">
            <span class="p-card-icon">💨</span>
            <h2>PM<sub>10</sub></h2>
            <p>Coarse Particulate Matter (&lt;10µm)</p>
          </div>
          <div class="p-card-body">
            <div class="p-info-row">
              <div class="p-info-item"><span class="p-info-label">What it is</span><span class="p-info-val">Larger inhalable particles that can reach upper airways and trigger respiratory irritation.</span></div>
              <div class="p-info-item"><span class="p-info-label">Sources</span><span class="p-info-val">Road dust, construction sites, mining, unpaved roads, agricultural activities.</span></div>
              <div class="p-info-item"><span class="p-info-label">Health Effect</span><span class="p-info-val">Coughing, sneezing, reduced lung function, aggravation of asthma and bronchitis.</span></div>
            </div>
            <div class="safe-limit">WHO Safe Limit: <b>45 µg/m³ (24-hr)</b> | India NAAQS: <b>100 µg/m³</b></div>
            <div class="danger-badge" style="background:#ff6d00;">🟠 High Danger</div>
          </div>
        </div>

        <!-- NO2 -->
        <div class="pollutant-card">
          <div class="p-card-top no2-top">
            <span class="p-card-icon">🏭</span>
            <h2>NO<sub>2</sub></h2>
            <p>Nitrogen Dioxide</p>
          </div>
          <div class="p-card-body">
            <div class="p-info-row">
              <div class="p-info-item"><span class="p-info-label">What it is</span><span class="p-info-val">A reddish-brown gas with a sharp, biting odor formed during combustion at high temperatures.</span></div>
              <div class="p-info-item"><span class="p-info-label">Sources</span><span class="p-info-val">Diesel & petrol vehicles, thermal power plants, industrial furnaces, gas stoves.</span></div>
              <div class="p-info-item"><span class="p-info-label">Health Effect</span><span class="p-info-val">Inflames airways, worsens asthma, reduces immunity against respiratory infections.</span></div>
            </div>
            <div class="safe-limit">WHO Safe Limit: <b>25 µg/m³ (24-hr)</b> | India NAAQS: <b>80 µg/m³</b></div>
            <div class="danger-badge" style="background:#f9a825;color:#3d2800;">🟡 Moderate Danger</div>
          </div>
        </div>

        <!-- SO2 -->
        <div class="pollutant-card">
          <div class="p-card-top so2-top">
            <span class="p-card-icon">⚗️</span>
            <h2>SO<sub>2</sub></h2>
            <p>Sulfur Dioxide</p>
          </div>
          <div class="p-card-body">
            <div class="p-info-row">
              <div class="p-info-item"><span class="p-info-label">What it is</span><span class="p-info-val">A colorless gas with a pungent smell produced by burning sulfur-containing fuels.</span></div>
              <div class="p-info-item"><span class="p-info-label">Sources</span><span class="p-info-val">Coal-fired power plants, oil refineries, metal smelting, volcanic eruptions.</span></div>
              <div class="p-info-item"><span class="p-info-label">Health Effect</span><span class="p-info-val">Causes acid rain, irritates throat and lungs, triggers asthma attacks, eye irritation.</span></div>
            </div>
            <div class="safe-limit">WHO Safe Limit: <b>40 µg/m³ (24-hr)</b> | India NAAQS: <b>80 µg/m³</b></div>
            <div class="danger-badge" style="background:#1565c0;">🔵 Moderate–High Danger</div>
          </div>
        </div>

        <!-- CO -->
        <div class="pollutant-card">
          <div class="p-card-top co-top">
            <span class="p-card-icon">🚗</span>
            <h2>CO</h2>
            <p>Carbon Monoxide</p>
          </div>
          <div class="p-card-body">
            <div class="p-info-row">
              <div class="p-info-item"><span class="p-info-label">What it is</span><span class="p-info-val">An odourless, colourless toxic gas produced by incomplete combustion of carbon-based fuels.</span></div>
              <div class="p-info-item"><span class="p-info-label">Sources</span><span class="p-info-val">Vehicle exhausts, wood/coal burning, generators, faulty gas appliances indoors.</span></div>
              <div class="p-info-item"><span class="p-info-label">Health Effect</span><span class="p-info-val">Reduces oxygen in blood, causes headaches, dizziness, unconsciousness; fatal at high levels.</span></div>
            </div>
            <div class="safe-limit">WHO Safe Limit: <b>4 mg/m³ (24-hr)</b> | India NAAQS: <b>2 mg/m³ (8-hr)</b></div>
            <div class="danger-badge" style="background:#6a1b9a;">🟣 Severe Indoor Risk</div>
          </div>
        </div>

        <!-- Ozone -->
        <div class="pollutant-card">
          <div class="p-card-top ozone-top">
            <span class="p-card-icon">🌐</span>
            <h2>O<sub>3</sub></h2>
            <p>Ground-Level Ozone</p>
          </div>
          <div class="p-card-body">
            <div class="p-info-row">
              <div class="p-info-item"><span class="p-info-label">What it is</span><span class="p-info-val">A secondary pollutant formed when NOx and VOCs react in sunlight. Not directly emitted.</span></div>
              <div class="p-info-item"><span class="p-info-label">Sources</span><span class="p-info-val">Vehicle & industrial emissions reacting with sunlight; photochemical smog in cities.</span></div>
              <div class="p-info-item"><span class="p-info-label">Health Effect</span><span class="p-info-val">Chest pain, coughing, throat irritation, worsened asthma, reduced lung capacity over time.</span></div>
            </div>
            <div class="safe-limit">WHO Safe Limit: <b>100 µg/m³ (8-hr)</b> | India NAAQS: <b>180 µg/m³</b></div>
            <div class="danger-badge" style="background:#2e7d32;">🟢 Low–Moderate Danger</div>
          </div>
        </div>

      </div>
    </div>

    <!-- ══ 2. HEALTH IMPACT ══ -->
    <div class="cat-section" id="sec-diseases">
      <div class="section-header">
        <h1>Health <span>Impact</span> Catalogue</h1>
        <p>Understand which pollutants cause which diseases and the risk level across different age groups.</p>
        <div class="section-divider"></div>
      </div>
      <div class="disease-grid">
        <div class="disease-card" style="border-top-color:#e53935;">
          <div class="d-icon">😮‍💨</div>
          <h3>Asthma & Bronchitis</h3>
          <p>Inflammation of airways triggered and worsened by pollutant inhalation. India has over 30 million asthma patients, a majority living in high-AQI cities.</p>
          <div class="pollutant-tags">
            <span class="ptag" style="background:#e53935;">PM2.5</span>
            <span class="ptag" style="background:#ff6d00;">PM10</span>
            <span class="ptag" style="background:#f9a825;color:#333;">NO₂</span>
            <span class="ptag" style="background:#1565c0;">SO₂</span>
          </div>
          <div class="risk-row">
            <div class="risk-chip"><div class="risk-dot" style="background:#e53935;"></div>Children: Very High</div>
            <div class="risk-chip"><div class="risk-dot" style="background:#ff6d00;"></div>Elderly: High</div>
            <div class="risk-chip"><div class="risk-dot" style="background:#ffc107;"></div>Adults: Moderate</div>
          </div>
        </div>
        <div class="disease-card" style="border-top-color:#b71c1c;">
          <div class="d-icon">🫀</div>
          <h3>Cardiovascular Disease</h3>
          <p>Fine particles enter the bloodstream causing inflammation, arterial plaque, heart attacks and stroke. Long-term PM2.5 exposure is linked to 30% higher cardiac risk.</p>
          <div class="pollutant-tags">
            <span class="ptag" style="background:#e53935;">PM2.5</span>
            <span class="ptag" style="background:#6a1b9a;">CO</span>
            <span class="ptag" style="background:#f9a825;color:#333;">NO₂</span>
          </div>
          <div class="risk-row">
            <div class="risk-chip"><div class="risk-dot" style="background:#e53935;"></div>Elderly: Very High</div>
            <div class="risk-chip"><div class="risk-dot" style="background:#ffc107;"></div>Adults: Moderate</div>
            <div class="risk-chip"><div class="risk-dot" style="background:#00c853;"></div>Children: Lower</div>
          </div>
        </div>
        <div class="disease-card" style="border-top-color:#4a148c;">
          <div class="d-icon">🫁</div>
          <h3>Lung Cancer</h3>
          <p>Classified as a Group 1 carcinogen by WHO, outdoor air pollution is responsible for ~29% of lung cancer deaths globally. Indoor cooking smoke also contributes significantly.</p>
          <div class="pollutant-tags">
            <span class="ptag" style="background:#e53935;">PM2.5</span>
            <span class="ptag" style="background:#ff6d00;">PM10</span>
            <span class="ptag" style="background:#1565c0;">SO₂</span>
          </div>
          <div class="risk-row">
            <div class="risk-chip"><div class="risk-dot" style="background:#e53935;"></div>Long-term exposure: Critical</div>
            <div class="risk-chip"><div class="risk-dot" style="background:#ff6d00;"></div>Smokers: Amplified risk</div>
          </div>
        </div>
        <div class="disease-card" style="border-top-color:#0288d1;">
          <div class="d-icon">👁️</div>
          <h3>Eye & Skin Irritation</h3>
          <p>Gaseous pollutants and fine particulates cause conjunctivitis, burning eyes, and skin rashes. Ozone exposure during summer smog events is a major trigger.</p>
          <div class="pollutant-tags">
            <span class="ptag" style="background:#2e7d32;">Ozone</span>
            <span class="ptag" style="background:#1565c0;">SO₂</span>
            <span class="ptag" style="background:#f9a825;color:#333;">NO₂</span>
          </div>
          <div class="risk-row">
            <div class="risk-chip"><div class="risk-dot" style="background:#ffc107;"></div>All ages: Moderate</div>
            <div class="risk-chip"><div class="risk-dot" style="background:#ff6d00;"></div>Contact lens users: High</div>
          </div>
        </div>
      </div>
    </div>

    <!-- ══ 3. SOURCES ══ -->
    <div class="cat-section" id="sec-sources">
      <div class="section-header">
        <h1>Pollution <span>Source</span> Catalogue</h1>
        <p>Major contributors to India's air pollution — their share, mechanism, and impact.</p>
        <div class="section-divider"></div>
      </div>
      <div class="source-grid">
        <div class="source-card">
          <div class="source-top"><div class="src-icon">🚗</div><h3>Vehicular Emissions</h3></div>
          <div class="source-body">
            <div class="contrib-pct">28%</div>
            <div class="contrib-bar-wrap">
              <label>Contribution to Urban AQI</label>
              <div class="contrib-bar-bg"><div class="contrib-bar-fill" style="width:28%"></div></div>
            </div>
            <p>India has 300+ million registered vehicles. Diesel engines emit high PM2.5 and NO₂. Two-wheelers contribute significantly to CO levels in metro cities.</p>
          </div>
        </div>
        <div class="source-card">
          <div class="source-top"><div class="src-icon">🏭</div><h3>Industrial Emissions</h3></div>
          <div class="source-body">
            <div class="contrib-pct">24%</div>
            <div class="contrib-bar-wrap">
              <label>Contribution to Urban AQI</label>
              <div class="contrib-bar-bg"><div class="contrib-bar-fill" style="width:24%"></div></div>
            </div>
            <p>Thermal power plants, cement factories, steel mills and chemical industries release SO₂, NOx, and heavy metals into the atmosphere continuously.</p>
          </div>
        </div>
        <div class="source-card">
          <div class="source-top"><div class="src-icon">🌾</div><h3>Crop Burning</h3></div>
          <div class="source-body">
            <div class="contrib-pct">22%</div>
            <div class="contrib-bar-wrap">
              <label>Contribution (Oct–Nov peak)</label>
              <div class="contrib-bar-bg"><div class="contrib-bar-fill" style="width:22%"></div></div>
            </div>
            <p>Stubble burning in Punjab and Haryana post-harvest causes massive seasonal spikes in Delhi's AQI — sometimes contributing over 40% during peak days.</p>
          </div>
        </div>
        <div class="source-card">
          <div class="source-top"><div class="src-icon">🏗️</div><h3>Construction & Dust</h3></div>
          <div class="source-body">
            <div class="contrib-pct">16%</div>
            <div class="contrib-bar-wrap">
              <label>Contribution to PM10</label>
              <div class="contrib-bar-bg"><div class="contrib-bar-fill" style="width:16%"></div></div>
            </div>
            <p>Rapid urbanisation generates enormous amounts of road dust and construction particulates. Unpaved roads and demolition sites are major local contributors.</p>
          </div>
        </div>
        <div class="source-card">
          <div class="source-top"><div class="src-icon">🎆</div><h3>Firecrackers</h3></div>
          <div class="source-body">
            <div class="contrib-pct">10%</div>
            <div class="contrib-bar-wrap">
              <label>Contribution (Diwali peak)</label>
              <div class="contrib-bar-bg"><div class="contrib-bar-fill" style="width:10%"></div></div>
            </div>
            <p>Diwali fireworks can spike PM2.5 by 5–10x within hours. They also release toxic heavy metals — barium, lead, potassium — into the air and water.</p>
          </div>
        </div>
      </div>
      <!-- Source pie chart -->
      <div class="pie-chart-wrap">
        <h3>📊 India Urban Pollution — Source Contribution</h3>
        <canvas id="sourcePie" style="max-width:220px;max-height:220px;"></canvas>
        <div class="src-legend" id="srcLegend"></div>
      </div>
    </div>

    <!-- ══ 4. CITY HISTORY ══ -->
    <div class="cat-section" id="sec-history">
      <div class="section-header">
        <h1>City AQI <span>History</span></h1>
        <p>Year-wise pollution trends for major Indian cities — tracking improvement or decline over the years.</p>
        <div class="section-divider"></div>
      </div>
      <div class="city-select-row" id="cityBtns"></div>
      <div class="history-chart-card">
        <h3 id="historyChartTitle">Delhi — Annual Average AQI (2018–2024)</h3>
        <canvas id="historyChart" style="max-height:260px;"></canvas>
        <div class="best-worst-row" id="bestWorstRow"></div>
      </div>
    </div>

    <!-- ══ 5. SCHEMES ══ -->
    <div class="cat-section" id="sec-schemes">
      <div class="section-header">
        <h1>Government <span>Schemes & Laws</span></h1>
        <p>India's legislative and policy framework to combat air pollution — what each scheme does and its current status.</p>
        <div class="section-divider"></div>
      </div>
      <div class="schemes-grid">
        <div class="scheme-card">
          <div class="sc-icon">🌿</div>
          <h3>NCAP</h3>
          <div class="sc-full">National Clean Air Programme</div>
          <p>India's first national programme with a target to reduce PM2.5 and PM10 levels by 40% by 2026. Covers 131 non-attainment cities with dedicated action plans.</p>
          <ul><li>City-specific action plans</li><li>Real-time monitoring expansion</li><li>₹10,566 crore allocated (2019–26)</li></ul>
          <span class="status-pill status-active">✅ Active</span>
        </div>
        <div class="scheme-card">
          <div class="sc-icon">🚌</div>
          <h3>BS-VI Norms</h3>
          <div class="sc-full">Bharat Stage VI Emission Standards</div>
          <p>Implemented from April 2020, BS-VI drastically reduces allowed NOx (from 0.25 to 0.06 g/km for petrol) and PM from diesel vehicles — equivalent to Euro 6 standards.</p>
          <ul><li>10x cleaner than BS-IV</li><li>Mandatory for all new vehicles</li><li>Ultra-low sulfur fuel mandated</li></ul>
          <span class="status-pill status-enforced">🔵 Enforced</span>
        </div>
        <div class="scheme-card">
          <div class="sc-icon">⚠️</div>
          <h3>GRAP</h3>
          <div class="sc-full">Graded Response Action Plan</div>
          <p>A set of emergency measures activated in Delhi-NCR based on AQI levels. Stages I–IV trigger progressively stricter bans on construction, vehicles, and industries.</p>
          <ul><li>Stage III: Ban on BS-III petrol, BS-IV diesel</li><li>Stage IV: School closures, WFH advisories</li></ul>
          <span class="status-pill status-ongoing">🟡 Seasonal Activation</span>
        </div>
        <div class="scheme-card">
          <div class="sc-icon">⚡</div>
          <h3>FAME India</h3>
          <div class="sc-full">Faster Adoption of EVs in India (Phase II)</div>
          <p>₹10,000 crore scheme to promote electric vehicles — reducing vehicular emissions significantly. Subsidises EV purchases for two-wheelers, buses and taxis.</p>
          <ul><li>7,090 e-buses sanctioned</li><li>Charging infra expansion</li><li>Tax incentives on EV loans</li></ul>
          <span class="status-pill status-active">✅ Active</span>
        </div>
        <div class="scheme-card">
          <div class="sc-icon">🔥</div>
          <h3>Parali Management</h3>
          <div class="sc-full">Crop Residue Management Scheme</div>
          <p>Provides subsidised Happy Seeders and other machinery to farmers to manage paddy straw without burning. Aims to eliminate stubble burning in Punjab, Haryana and UP.</p>
          <ul><li>50% subsidy to individual farmers</li><li>80% subsidy to cooperatives</li></ul>
          <span class="status-pill status-ongoing">🟡 Ongoing</span>
        </div>
      </div>
    </div>

    <!-- ══ 6. DEVICES ══ -->
    <div class="cat-section" id="sec-devices">
      <div class="section-header">
        <h1>Pollution <span>Control Devices</span></h1>
        <p>Technologies and devices used to monitor, filter, and reduce air pollution — how they work and how effective they are.</p>
        <div class="section-divider"></div>
      </div>
      <div class="devices-grid">
        <div class="device-card">
          <div class="device-top"><div class="dv-icon">🌬️</div><h3>HEPA Air Purifier</h3></div>
          <div class="device-body">
            <p>High-Efficiency Particulate Air filters trap 99.97% of particles ≥0.3µm. Essential for indoor PM2.5 protection in high-AQI cities.</p>
            <div class="effectiveness-wrap">
              <label><span>PM2.5 Removal Efficiency</span><span>99.97%</span></label>
              <div class="eff-bar-bg"><div class="eff-bar-fill" style="width:99%"></div></div>
            </div>
            <div class="device-tags"><span class="device-tag">Indoor Use</span><span class="device-tag">PM2.5</span><span class="device-tag">PM10</span><span class="device-tag">Allergens</span></div>
          </div>
        </div>
        <div class="device-card">
          <div class="device-top"><div class="dv-icon">🚗</div><h3>Catalytic Converter</h3></div>
          <div class="device-body">
            <p>Fitted in vehicle exhaust systems, it converts CO, NOx and unburned hydrocarbons into CO₂, N₂ and H₂O through chemical reactions over precious metal catalysts.</p>
            <div class="effectiveness-wrap">
              <label><span>CO & NOx Reduction</span><span>~90%</span></label>
              <div class="eff-bar-bg"><div class="eff-bar-fill" style="width:90%"></div></div>
            </div>
            <div class="device-tags"><span class="device-tag">Vehicles</span><span class="device-tag">CO</span><span class="device-tag">NOx</span><span class="device-tag">HC</span></div>
          </div>
        </div>
        <div class="device-card">
          <div class="device-top"><div class="dv-icon">🏭</div><h3>Electrostatic Precipitator</h3></div>
          <div class="device-body">
            <p>Industrial device that uses electrostatic charge to collect dust and particulate matter from flue gases before they are released from chimneys. Used in power plants and factories.</p>
            <div class="effectiveness-wrap">
              <label><span>Particulate Removal</span><span>99%</span></label>
              <div class="eff-bar-bg"><div class="eff-bar-fill" style="width:99%"></div></div>
            </div>
            <div class="device-tags"><span class="device-tag">Industrial</span><span class="device-tag">PM2.5</span><span class="device-tag">PM10</span><span class="device-tag">Fly Ash</span></div>
          </div>
        </div>
        <div class="device-card">
          <div class="device-top"><div class="dv-icon">💧</div><h3>Wet Scrubber</h3></div>
          <div class="device-body">
            <p>Sprays liquid (usually water) into polluted gas streams to absorb SO₂, HCl and particulates. Widely used in chemical plants, cement factories and coal power stations.</p>
            <div class="effectiveness-wrap">
              <label><span>SO₂ Removal Efficiency</span><span>~95%</span></label>
              <div class="eff-bar-bg"><div class="eff-bar-fill" style="width:95%"></div></div>
            </div>
            <div class="device-tags"><span class="device-tag">Industrial</span><span class="device-tag">SO₂</span><span class="device-tag">HCl</span><span class="device-tag">Dust</span></div>
          </div>
        </div>
        <div class="device-card">
          <div class="device-top"><div class="dv-icon">📡</div><h3>AQI Monitoring Station</h3></div>
          <div class="device-body">
            <p>CPCB-operated Continuous Ambient Air Quality Monitoring Stations (CAAQMS) measure PM2.5, PM10, SO₂, NO₂, CO and Ozone 24×7 and transmit data in real time.</p>
            <div class="effectiveness-wrap">
              <label><span>Data Accuracy</span><span>~97%</span></label>
              <div class="eff-bar-bg"><div class="eff-bar-fill" style="width:97%"></div></div>
            </div>
            <div class="device-tags"><span class="device-tag">Monitoring</span><span class="device-tag">All Pollutants</span><span class="device-tag">Real-time</span></div>
          </div>
        </div>
      </div>
    </div>

  </div><!-- end main -->
</div><!-- end layout -->

<footer>
  © 2025 VayuDarpan – National Air Quality Monitoring Portal &nbsp;|&nbsp;
  <a href="#">CPCB</a> &nbsp;|&nbsp; <a href="#">MoEFCC</a> &nbsp;|&nbsp;
  <a href="#">WHO Guidelines</a>
</footer>

<script>
// ── SIDEBAR NAVIGATION ──
function showSection(id, el) {
  document.querySelectorAll('.cat-section').forEach(s => s.classList.remove('active'));
  document.querySelectorAll('.sidebar-item').forEach(s => s.classList.remove('active'));
  document.getElementById('sec-' + id).classList.add('active');
  el.classList.add('active');

  // lazy-init charts
  if(id === 'sources' && !sourcePieBuilt) buildSourcePie();
  if(id === 'history' && !historyBuilt) buildHistory();
}

// ── SOURCE PIE CHART ──
let sourcePieBuilt = false;
function buildSourcePie() {
  const labels = ['Vehicles','Industry','Crop Burning','Construction','Firecrackers'];
  const data   = [28, 24, 22, 16, 10];
  const colors = ['#00bcd4','#e53935','#ff6d00','#ffc107','#7b1fa2'];

  new Chart(document.getElementById('sourcePie'), {
    type:'doughnut',
    data:{ labels, datasets:[{ data, backgroundColor:colors, borderWidth:2, borderColor:'#fff' }] },
    options:{ plugins:{ legend:{ display:false } }, cutout:'55%', animation:{ duration:800 } }
  });

  const total = data.reduce((a,b)=>a+b,0);
  document.getElementById('srcLegend').innerHTML = labels.map((l,i)=>`
    <div class="src-leg-item">
      <div class="src-leg-dot" style="background:${colors[i]}"></div>
      <span>${l}</span><b>${data[i]}%</b>
    </div>`).join('');
  sourcePieBuilt = true;
}

// ── CITY HISTORY CHART ──
let historyBuilt = false;
let historyChart;
const historyData = {
  Delhi:     { data:[312,298,275,289,248,231,220], best:'Feb (110)', worst:'Nov (480)' },
  Mumbai:    { data:[148,142,128,135,118,108,98],  best:'Jul (55)',  worst:'Dec (198)' },
  Bengaluru: { data:[98, 105,92, 88, 82, 78, 72],  best:'Aug (48)', worst:'Jan (142)' },
  Kolkata:   { data:[210,218,195,202,188,175,165],  best:'Jul (80)', worst:'Nov (310)' },
  Lucknow:   { data:[268,278,255,265,240,228,210],  best:'Feb (95)', worst:'Nov (420)' },
  Chennai:   { data:[88, 92, 80, 85, 75, 70, 65],   best:'Aug (40)', worst:'Jan (125)' },
};
const years = ['2018','2019','2020','2021','2022','2023','2024'];

function buildHistory() {
  // build city buttons
  const btnWrap = document.getElementById('cityBtns');
  Object.keys(historyData).forEach((city, i) => {
    const btn = document.createElement('button');
    btn.className = 'city-btn' + (i===0?' active':'');
    btn.textContent = city;
    btn.onclick = () => { updateHistory(city); document.querySelectorAll('.city-btn').forEach(b=>b.classList.remove('active')); btn.classList.add('active'); };
    btnWrap.appendChild(btn);
  });
  updateHistory('Delhi');
  historyBuilt = true;
}

function updateHistory(city) {
  document.getElementById('historyChartTitle').textContent = city + ' — Annual Average AQI (2018–2024)';
  const d = historyData[city];
  const colors = d.data.map(v => v<=100?'#00c853': v<=200?'#ffc107': v<=300?'#ff6d00':'#e53935');

  if(historyChart) historyChart.destroy();
  historyChart = new Chart(document.getElementById('historyChart'), {
    type:'bar',
    data:{
      labels: years,
      datasets:[{
        label:'Annual Avg AQI',
        data: d.data,
        backgroundColor: colors,
        borderRadius: 6,
        borderSkipped: false,
      }]
    },
    options:{
      plugins:{ legend:{ display:false } },
      scales:{
        y:{ beginAtZero:true, max:500, grid:{ color:'#f0f0f0' } },
        x:{ grid:{ display:false } }
      },
      animation:{ duration:700 }
    }
  });

  document.getElementById('bestWorstRow').innerHTML = `
    <div class="bw-card best">
      <div class="bw-icon">🌿</div>
      <div>
        <div class="bw-val">Best Month</div>
        <div class="bw-label">${d.best} — Lowest recorded AQI</div>
      </div>
    </div>
    <div class="bw-card worst">
      <div class="bw-icon">⚠️</div>
      <div>
        <div class="bw-val">Worst Month</div>
        <div class="bw-label">${d.worst} — Peak pollution recorded</div>
      </div>
    </div>`;
}
</script>
</body>
</html>
