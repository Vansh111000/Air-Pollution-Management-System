<?php
session_start();
require_once 'api/middleware/auth.php';
requireLogin(); // Restrict to logged-in users only
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <meta name="theme-color" content="#0d2137"/>
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

    body {
      font-family:'Noto Sans',sans-serif;
      background:#f0f4f8;
      color:var(--text);
      min-height:100vh;
      line-height:1.5;
    }

    /* Cloudy backdrop — matches catalogue / landing */
    body::before {
      content:'';
      position:fixed; inset:0; z-index:-1;
      background:
        radial-gradient(ellipse 800px 300px at 10% 20%, rgba(255,255,255,0.95) 0%, transparent 70%),
        radial-gradient(ellipse 600px 250px at 80% 10%, rgba(224,242,254,0.9) 0%, transparent 60%),
        radial-gradient(ellipse 700px 200px at 50% 80%, rgba(232,245,233,0.8) 0%, transparent 60%),
        linear-gradient(160deg, #e8f4fd 0%, #f5faff 40%, #edf7ee 100%);
    }

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
    .nav-links { display:flex; gap:6px; flex-wrap:wrap; align-items:center; justify-content:flex-end; }
    .nav-links a {
      color:#cde8f5; text-decoration:none;
      font-family:'Rajdhani',sans-serif; font-weight:600; font-size:0.95rem;
      letter-spacing:1px; padding:7px 16px; border-radius:4px;
      transition:background 0.2s,color 0.2s; text-transform:uppercase;
    }
    .nav-links a:hover { background:var(--accent); color:#fff; }
    .nav-links a:focus-visible { outline:2px solid var(--accent); outline-offset:2px; }
    .nav-links a.active { background:var(--accent); color:#fff; }
    .nav-links a.logout { border:1px solid #e53935; color:#ff7070; }
    .nav-links a.logout:hover { background:#e53935; color:#fff; }

    /* ── PAGE HEADER ── */
    .page-header {
      position:relative;
      overflow:hidden;
      background:linear-gradient(135deg, var(--nav-bg) 0%, #153a56 55%, #1a3a5c 100%);
      padding:28px 40px 32px;
      display:flex; align-items:center; justify-content:space-between;
      gap:24px;
      flex-wrap:wrap;
      border-bottom:3px solid rgba(0,188,212,0.35);
      box-shadow:0 8px 32px rgba(13,33,55,0.25);
    }
    .page-header::after {
      content:'';
      position:absolute; right:-80px; top:-40%;
      width:320px; height:320px;
      border-radius:50%;
      background:radial-gradient(circle, rgba(0,188,212,0.12) 0%, transparent 70%);
      pointer-events:none;
    }
    .page-header-inner { position:relative; z-index:1; max-width:720px; }
    .page-header-kicker {
      font-family:'Rajdhani',sans-serif;
      font-size:0.72rem;
      font-weight:700;
      letter-spacing:3px;
      text-transform:uppercase;
      color:rgba(124,184,212,0.95);
      margin-bottom:6px;
    }
    .page-header h1 {
      font-family:'Teko',sans-serif; font-size:clamp(1.75rem, 4vw, 2.35rem);
      color:#fff; letter-spacing:2px; line-height:1.1;
    }
    .page-header h1 span { color:var(--accent); }
    .page-header p { color:#9ec9dc; font-size:0.9rem; margin-top:8px; max-width:520px; line-height:1.55; }
    .header-actions { position:relative; z-index:1; display:flex; flex-direction:column; align-items:flex-end; gap:10px; }
    .live-badge {
      display:flex; align-items:center; gap:10px;
      background:rgba(0,200,83,0.12);
      border:1px solid rgba(0,200,83,0.55);
      border-radius:999px; padding:8px 18px;
      box-shadow:0 4px 14px rgba(0,0,0,0.12);
    }
    .live-dot {
      width:10px; height:10px; border-radius:50%;
      background:var(--good);
      box-shadow:0 0 0 3px rgba(0,200,83,0.25);
      animation:pulse 1.5s ease-in-out infinite;
    }
    @keyframes pulse {
      0%,100% { opacity:1; transform:scale(1); }
      50%      { opacity:0.55; transform:scale(1.15); }
    }
    @media (prefers-reduced-motion: reduce) {
      .live-dot { animation:none; }
    }
    .live-badge span { color:#b9f6ca; font-family:'Rajdhani',sans-serif; font-weight:700; font-size:0.82rem; letter-spacing:2px; }
    .header-meta {
      font-family:'Rajdhani',sans-serif;
      font-size:0.75rem;
      color:rgba(158,201,220,0.85);
      letter-spacing:0.5px;
    }

    /* ── WRAPPER ── */
    .dashboard-wrapper { max-width:1320px; margin:32px auto 48px; padding:0 28px; }

    /* ── SECTION HEADERS (catalogue-style) ── */
    .section-block { margin-bottom:36px; }
    .section-header { margin-bottom:20px; }
    .section-header h2 {
      font-family:'Teko',sans-serif; font-size:clamp(1.45rem, 3vw, 1.95rem);
      color:var(--nav-bg); letter-spacing:1px;
    }
    .section-header h2 span { color:var(--accent); }
    .section-header p { color:#555; font-size:0.88rem; margin-top:6px; line-height:1.55; max-width:640px; }
    .section-divider {
      height:3px; width:56px;
      background:linear-gradient(90deg, var(--accent), transparent);
      margin:10px 0 0; border-radius:2px;
    }

    /* ══════════════════════════════
       SECTION 1: MAP + CITY INFO
    ══════════════════════════════ */
    .map-section {
      display:flex; gap:22px; align-items:stretch;
    }

    /* city info panel (left) */
    .city-info-panel {
      width:280px; flex-shrink:0;
      display:flex; flex-direction:column; gap:14px;
    }
    .city-card {
      background:#fff; border-radius:16px;
      box-shadow:0 4px 24px rgba(13,33,55,0.08);
      border:1px solid rgba(221,228,234,0.9);
      overflow:hidden;
      transition:box-shadow 0.25s ease, transform 0.25s ease;
    }
    .city-card:hover { box-shadow:0 8px 32px rgba(13,33,55,0.12); }
    .city-card-header {
      padding:16px 18px 12px;
      background:linear-gradient(135deg, #e3f2fd 0%, #e8f5e9 100%);
      border-bottom:1px solid var(--border);
    }
    .city-card-header h3 {
      font-family:'Rajdhani',sans-serif; font-weight:700;
      font-size:1.08rem; color:var(--nav-bg);
    }
    .city-card-header small { font-size:0.72rem; color:#6b7b8a; }
    .city-card-body { padding:16px 18px; min-height:120px; }

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
      background:var(--card); border-radius:10px;
      padding:10px 10px; text-align:center;
      border:1px solid rgba(221,228,234,0.6);
      transition:background 0.2s, border-color 0.2s;
    }
    .stat-box:hover { background:#fff; border-color:rgba(0,188,212,0.35); }
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
      text-align:center; padding:28px 14px 32px;
      color:#7a8a99; font-size:0.86rem; line-height:1.75;
    }
    .placeholder-icon {
      width:72px; height:72px; margin:0 auto 14px;
      border-radius:50%;
      background:linear-gradient(145deg, rgba(0,188,212,0.12), rgba(13,33,55,0.06));
      border:2px dashed rgba(0,188,212,0.35);
      display:flex; align-items:center; justify-content:center;
      font-size:1.85rem;
    }
    .placeholder-msg strong { display:block; color:var(--nav-bg); font-family:'Rajdhani',sans-serif; font-size:0.95rem; margin-top:4px; }

    /* map */
    .map-container {
      flex:1; border-radius:16px; overflow:hidden;
      box-shadow:0 8px 40px rgba(13,33,55,0.12);
      border:1px solid rgba(221,228,234,0.95);
      min-height:460px;
      position:relative;
    }
    .map-container::before {
      content:'';
      position:absolute; inset:0; pointer-events:none; z-index:2;
      border-radius:16px;
      box-shadow:inset 0 0 0 1px rgba(255,255,255,0.4);
    }
    #indiaMap { width:100%; height:460px; z-index: 1; }

    /* ══════════════════════════════
       SECTION 2: PIE CHART + ALERTS
    ══════════════════════════════ */
    .analytics-section { display:flex; gap:22px; align-items:stretch; }

    .pie-card {
      flex:1.2; background:#fff; border-radius:16px;
      padding:24px 24px 26px;
      box-shadow:0 4px 24px rgba(13,33,55,0.08);
      border:1px solid rgba(221,228,234,0.9);
      position:relative;
      overflow:hidden;
    }
    .pie-card::before {
      content:'';
      position:absolute; top:0; left:0; right:0; height:4px;
      background:linear-gradient(90deg, var(--accent), #4dd0e1 50%, transparent);
      opacity:0.9;
    }
    .pie-card h3 {
      font-family:'Rajdhani',sans-serif; font-weight:700;
      font-size:0.95rem; color:var(--nav-bg); letter-spacing:1.2px;
      text-transform:uppercase; margin-bottom:18px;
      padding-top:4px;
    }
    .pie-wrap {
      display:flex; align-items:center; gap:24px; flex-wrap:wrap;
    }
    .pie-wrap canvas { max-width:220px; max-height:220px; }
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
      border-radius:14px; padding:18px 20px;
      display:flex; gap:14px; align-items:flex-start;
      box-shadow:0 4px 18px rgba(13,33,55,0.07);
      border:1px solid rgba(0,0,0,0.04);
      transition:transform 0.2s ease, box-shadow 0.2s ease;
    }
    .alert-card:hover { transform:translateY(-2px); box-shadow:0 8px 28px rgba(13,33,55,0.1); }
    .alert-card.safe   { background:linear-gradient(135deg,#e8f5e9,#f1f8e9); border-left:5px solid var(--good); }
    .alert-card.danger { background:linear-gradient(135deg,#ffebee,#fff3e0); border-left:5px solid var(--severe); }
    .alert-card.alert-general {
      background:linear-gradient(135deg,#e3f2fd,#f0f8ff);
      border-left:5px solid var(--accent);
    }
    .alert-card.alert-general .alert-content h4 { color:#0d47a1; }
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
    .alert-status.status-general { background:var(--accent); color:#fff; }
    .alert-status.status-severe { background:var(--severe); color:#fff; }
    .alert-status.status-moderate { background:var(--moderate); color:#3d2800; }
    .alert-status.status-good-aqi { background:var(--good); color:#fff; }

    /* ══════════════════════════════
       SECTION 3: CATEGORIES
    ══════════════════════════════ */
    .categories-section { margin-bottom:40px; }

    .cat-tabs {
      display:flex; gap:10px; flex-wrap:wrap; margin-bottom:22px;
    }
    .cat-tab {
      padding:9px 18px; border-radius:999px; border:2px solid var(--border);
      background:#fff; font-family:'Rajdhani',sans-serif; font-weight:600;
      font-size:0.86rem; letter-spacing:0.5px; cursor:pointer;
      transition:all 0.2s; color:var(--text);
      box-shadow:0 2px 8px rgba(13,33,55,0.04);
    }
    .cat-tab:hover { border-color:var(--accent); color:var(--accent); transform:translateY(-1px); }
    .cat-tab:focus-visible { outline:2px solid var(--accent); outline-offset:2px; }
    .cat-tab.active {
      background:linear-gradient(135deg, var(--nav-bg), #1a3a5c);
      color:#fff; border-color:transparent;
      box-shadow:0 4px 14px rgba(13,33,55,0.2);
    }

    .cat-content { display:none; }
    .cat-content.active { display:block; }

    .table-scroll {
      background:#fff; border-radius:14px;
      box-shadow:0 4px 24px rgba(13,33,55,0.07);
      border:1px solid rgba(221,228,234,0.9);
      overflow:auto;
      max-height:min(70vh, 640px);
    }
    .city-table-wrap { min-width:720px; }
    table { width:100%; border-collapse:collapse; }
    thead tr { background:linear-gradient(180deg, #0d2137 0%, #0a1929 100%); }
    thead th {
      padding:14px 16px; text-align:left;
      font-family:'Rajdhani',sans-serif; font-weight:700;
      font-size:0.82rem; color:#cde8f5; letter-spacing:1px;
      text-transform:uppercase;
      position:sticky; top:0; z-index:1;
      box-shadow:0 1px 0 rgba(255,255,255,0.06);
    }
    tbody tr { border-bottom:1px solid var(--border); transition:background 0.15s; }
    tbody tr:nth-child(even) { background:rgba(244,248,251,0.5); }
    tbody tr:hover { background:#e8f7fc; }
    tbody td { padding:12px 16px; font-size:0.83rem; vertical-align:top; }
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
      text-align:center; padding:20px 16px 24px;
      font-size:0.8rem; letter-spacing:0.5px;
      border-top:3px solid rgba(0,188,212,0.25);
    }
    footer a { color:var(--accent); text-decoration:none; }
    footer a:hover { text-decoration:underline; }

    /* Responsive */
    @media (max-width: 1100px) {
      .map-section { flex-direction:column; }
      .city-info-panel { width:100%; }
    }
    @media (max-width: 900px) {
      .analytics-section { flex-direction:column; }
      .pie-wrap { justify-content:center; }
      .pie-legend { flex:1; min-width:200px; }
    }
    @media (max-width: 640px) {
      nav { padding:0 16px; height:auto; min-height:56px; flex-wrap:wrap; padding-top:10px; padding-bottom:10px; }
      .nav-links { width:100%; justify-content:flex-start; gap:4px; }
      .nav-links a { font-size:0.82rem; padding:6px 10px; }
      .page-header { padding:22px 20px; }
      .header-actions { align-items:flex-start; width:100%; }
      .dashboard-wrapper { padding:0 16px; margin:24px auto 40px; }
      .cat-tabs { flex-wrap:nowrap; overflow-x:auto; padding-bottom:6px; -webkit-overflow-scrolling:touch; gap:8px; }
      .cat-tabs::-webkit-scrollbar { height:4px; }
      .cat-tabs::-webkit-scrollbar-thumb { background:var(--accent); border-radius:4px; }
    }

    /* Feedback form */
    .feedback-section {
      margin-top:8px;
      padding:28px 24px 32px;
      text-align:center;
      background:linear-gradient(135deg, rgba(227,242,253,0.6) 0%, rgba(232,245,233,0.45) 100%);
      border:1px solid rgba(221,228,234,0.95);
      border-radius:16px;
      box-shadow:0 4px 24px rgba(13,33,55,0.06);
    }
    .feedback-section h3 {
      font-family:'Teko',sans-serif; font-size:1.5rem; color:var(--nav-bg); letter-spacing:1px;
      margin-bottom:6px;
    }
    .feedback-section > p { font-size:0.88rem; color:#5a6a78; margin-bottom:18px; max-width:480px; margin-left:auto; margin-right:auto; }
    .feedback-btn {
      background:linear-gradient(135deg, var(--accent), #26c6da);
      color:#fff; border:none; padding:12px 28px;
      font-family:'Rajdhani',sans-serif; font-weight:700; font-size:1rem;
      border-radius:999px; cursor:pointer; letter-spacing:1.5px;
      text-transform:uppercase;
      transition:transform 0.2s, box-shadow 0.2s;
      box-shadow:0 6px 20px rgba(0,188,212,0.35);
    }
    .feedback-btn:hover { transform:translateY(-2px); box-shadow:0 10px 28px rgba(0,188,212,0.4); }
    .feedback-btn:focus-visible { outline:2px solid var(--nav-bg); outline-offset:3px; }
    .feedback-modal {
      display:none; position:fixed; top:0; left:0; width:100%; height:100%;
      background:rgba(13,33,55,0.55);
      backdrop-filter:blur(4px);
      -webkit-backdrop-filter:blur(4px);
      z-index:3000; align-items:center; justify-content:center;
      padding:20px;
    }
    .feedback-modal.active { display:flex; }
    .feedback-card {
      background:#fff; width:100%; max-width:420px; border-radius:16px;
      padding:26px 26px 24px; box-shadow:0 20px 50px rgba(0,0,0,0.22); position:relative;
      text-align:left;
      border:1px solid var(--border);
    }
    .feedback-card h3 { font-family:'Teko',sans-serif; font-size:1.5rem; color:var(--nav-bg); margin-bottom:10px; }
    .feedback-close { position:absolute; top:16px; right:16px; cursor:pointer; font-size:1.2rem; color:#888; }
    .feedback-card label { display:block; font-size:0.85rem; font-weight:600; color:#555; margin-bottom:6px; margin-top:12px; }
    .feedback-card textarea { width:100%; padding:10px; border:2px solid var(--border); border-radius:8px; font-family:'Noto Sans',sans-serif; font-size:0.9rem; resize:vertical; min-height:80px; }
    .feedback-card select { width:100%; padding:10px; border:2px solid var(--border); border-radius:8px; font-family:'Noto Sans',sans-serif; font-size:0.9rem; }
    .feedback-card button { width:100%; margin-top:16px; background:var(--nav-bg); color:#fff; border:none; padding:12px; border-radius:8px; font-family:'Rajdhani',sans-serif; font-weight:700; cursor:pointer; font-size:1rem; text-transform:uppercase; transition:background 0.2s;}
    .feedback-card button:hover { background:var(--accent); }

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
    <?php if ($_SESSION['user_type'] === 'admin'): ?>
      <a href="admin/admin_dashboard.php">Admin Panel</a>
    <?php elseif ($_SESSION['user_type'] === 'station_worker'): ?>
      <a href="monitoring-station/index.php">Station Panel</a>
    <?php endif; ?>
    <a href="logout.php" class="logout">Logout</a>
  </div>
</nav>

<!-- PAGE HEADER -->
<div class="page-header">
  <div class="page-header-inner">
    <p class="page-header-kicker">National overview</p>
    <h1>Public <span>Air Quality</span> Dashboard</h1>
    <p>Explore live AQI readings across major Indian cities. Select a pin on the map to update pollutant mix, health advisories, and the city table context.</p>
  </div>
  <div class="header-actions">
    <div class="live-badge">
      <div class="live-dot" aria-hidden="true"></div>
      <span>LIVE DATA</span>
    </div>
    <span class="header-meta">Map &amp; charts update when you pick a city</span>
  </div>
</div>

<div class="dashboard-wrapper">

  <!-- ══ SECTION 1: MAP + CITY INFO ══ -->
  <section class="section-block" aria-labelledby="sec-map-heading">
    <div class="section-header">
      <h2 id="sec-map-heading">India <span>AQI</span> map</h2>
      <p>Pan and zoom the map, then tap a numbered marker to load that city in the side panel and charts.</p>
      <div class="section-divider"></div>
    </div>
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
            <div class="placeholder-icon" aria-hidden="true">🗺️</div>
            <strong>Choose a city</strong>
            Click any numbered pin on the map to load AQI, weather, and pollutant details here.
          </div>
        </div>
      </div>
    </div>

    <!-- Leaflet Map -->
    <div class="map-container">
      <div id="indiaMap" role="application" aria-label="India air quality map"></div>
    </div>
  </div>
  </section>

  <!-- ══ SECTION 2: PIE CHART + SAFETY ALERTS ══ -->
  <section class="section-block" aria-labelledby="sec-analytics-heading">
    <div class="section-header">
      <h2 id="sec-analytics-heading">Pollutant mix &amp; <span>health</span> alerts</h2>
      <p>The doughnut chart reflects either the national average or your selected city. Alerts adapt to the current AQI band.</p>
      <div class="section-divider"></div>
    </div>
  <div class="analytics-section">

    <!-- Pie Chart -->
    <div class="pie-card">
      <h3>Pollutant contribution — <span id="pieCity">India average</span></h3>
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
      <div class="alert-card alert-general" id="generalAlert">
        <div class="alert-icon">🏃</div>
        <div class="alert-content">
          <h4>General public</h4>
          <p id="generalMsg">Safety advisory for the general public will appear here once you select a city.</p>
          <span class="alert-status status-general" id="generalStatus">—</span>
        </div>
      </div>
    </div>
  </div>
  </section>

  <!-- ══ SECTION 3: CATEGORIES ══ -->
  <section class="categories-section section-block" aria-labelledby="sec-table-heading">
    <div class="section-header">
      <h2 id="sec-table-heading">Cities by <span>pollutant</span> emphasis</h2>
      <p>Sort and compare cities by overall AQI or by the dominant pollutant for planning and awareness.</p>
      <div class="section-divider"></div>
    </div>
    <div class="cat-tabs">
      <button class="cat-tab active" onclick="switchTab('all',this)">🏙️ All Cities</button>
      <button class="cat-tab" onclick="switchTab('pm25',this)">🔬 PM2.5</button>
      <button class="cat-tab" onclick="switchTab('pm10',this)">💨 PM10</button>
      <button class="cat-tab" onclick="switchTab('no2',this)">🏭 NO₂</button>
      <button class="cat-tab" onclick="switchTab('so2',this)">⚗️ SO₂</button>
      <button class="cat-tab" onclick="switchTab('co',this)">🚗 CO</button>
      <button class="cat-tab" onclick="switchTab('ozone',this)">🌐 Ozone</button>
    </div>
    <div class="table-scroll">
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
  </section>

  <!-- ══ SECTION 4: FEEDBACK ══ -->
  <div class="feedback-section">
    <h3>We value your input</h3>
    <p>Spot an issue or have an idea? Tell us what works and what we should improve next.</p>
    <button type="button" class="feedback-btn" onclick="document.getElementById('feedbackModal').classList.add('active')">Share feedback</button>
  </div>
  
  <div class="feedback-modal" id="feedbackModal">
    <div class="feedback-card">
      <div class="feedback-close" onclick="document.getElementById('feedbackModal').classList.remove('active')">✖</div>
      <h3>Help Us Improve!</h3>
      <p style="font-size:0.8rem; color:#666; margin-bottom:10px;">Please share your experience with the dashboard.</p>
      
      <form id="feedbackForm" onsubmit="submitFeedback(event)">
        <label>Your Rating (Out of 5)</label>
        <select id="fbRating" required>
          <option value="5">⭐⭐⭐⭐⭐ Outstanding</option>
          <option value="4">⭐⭐⭐⭐ Good</option>
          <option value="3">⭐⭐⭐ Neutral</option>
          <option value="2">⭐⭐ Poor</option>
          <option value="1">⭐ Terrible</option>
        </select>

        <label>Message</label>
        <textarea id="fbMessage" placeholder="Any suggestions or issues?" required></textarea>

        <button type="submit" id="fbSubmitBtn">Submit Feedback</button>
      </form>
      <div id="fbResultMsg" style="margin-top:10px; font-size:0.85rem; font-weight:600; text-align:center;"></div>
    </div>
  </div>

</div><!-- end wrapper -->

<footer>
  © 2026 VayuDarpan Developed under guidence Bharathi ma'am by Vansh Kataria, Atharva kamath
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
  renderPie(data, 'India average');
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
    gStat.textContent='🚨 Severe — wear mask outdoors';
    gStat.className = 'alert-status status-severe';
  } else if(aqi > 100) {
    gMsg.textContent=`Moderate AQI of ${aqi}. Healthy adults can go outdoors with precaution. Sensitive individuals should reduce prolonged outdoor exertion.`;
    gStat.textContent='⚠️ Moderate — take precaution';
    gStat.className = 'alert-status status-moderate';
  } else {
    gMsg.textContent=`Air quality is good today with AQI of ${aqi}. Safe for all outdoor activities. Great day for a walk or exercise!`;
    gStat.textContent='✅ Good — enjoy outdoors';
    gStat.className = 'alert-status status-good-aqi';
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
    const vals = { 'PM2.5':city.pm25, 'PM10':city.pm10, 'NO₂':city.no2, 'SO₂':city.so2, 'CO':city.co*10, 'Ozone':city.ozone };
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

// ══════════════════════════════════════
// FEEDBACK SUBMISSION
// ══════════════════════════════════════
async function submitFeedback(e) {
  e.preventDefault();
  const btn = document.getElementById('fbSubmitBtn');
  const resMsg = document.getElementById('fbResultMsg');
  const message = document.getElementById('fbMessage').value;
  const rating = document.getElementById('fbRating').value;

  btn.disabled = true;
  btn.textContent = "Submitting...";
  resMsg.textContent = "";

  try {
    const res = await fetch('api/feedback/submit.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      credentials: 'same-origin',
      body: JSON.stringify({ message, rating })
    });
    const data = await res.json();
    if(res.ok && data.success) {
      resMsg.style.color = 'green';
      resMsg.textContent = data.message;
      document.getElementById('fbMessage').value = '';
      setTimeout(() => { document.getElementById('feedbackModal').classList.remove('active'); resMsg.textContent=''; }, 2000);
    } else {
      resMsg.style.color = 'red';
      resMsg.textContent = data.message || "Something went wrong.";
    }
  } catch(err) {
    resMsg.style.color = 'red';
    resMsg.textContent = "Network error. Try again.";
  } finally {
    btn.disabled = false;
    btn.textContent = "Submit Feedback";
  }
}
</script>
</body>
</html>