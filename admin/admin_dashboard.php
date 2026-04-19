<!-- admin/admin_dashboard.php -->
<?php
header("Content-Type: text/html; charset=UTF-8");
session_start();
require_once '../api/db.php';

if (!isset($pdo)) {
    die("Database connection failed.");
}

// ── ADMIN PROTECTION ──
// Only allow users with role = 'admin'
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Log admin login into database
$admin_id   = $_SESSION['user_id'];
$admin_name = $_SESSION['name'] ?? 'Admin';

$message      = '';
$message_type = '';

// ══════════════════════════════════════
// CRUD OPERATIONS ON pollution_data
// ══════════════════════════════════════

// ── CREATE — Add new city/area ──
if(isset($_POST['action']) && $_POST['action'] === 'create') {
    if (empty($_POST['station_id'])) {
        $message = "❌ Error: Station is required";
        $message_type = 'error';
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO pollution_data 
                (station_id, area_id, aqi, pm25, humidity, temperature, recorded_at)
                VALUES (:station_id, :area_id, :aqi, :pm25, :humidity, :temperature, NOW())");
            $stmt->execute([
                ':station_id'  => $_POST['station_id'],
                ':area_id'     => $_POST['area_id'],
                ':aqi'         => $_POST['aqi'],
                ':pm25'        => $_POST['pm25'],
                ':humidity'    => $_POST['humidity'],
                ':temperature' => $_POST['temperature'],
            ]);
            $message      = "✅ Pincode '{$_POST['area_id']}' added successfully to the dashboard!";
            $message_type = 'success';
        } catch(PDOException $e) {
            if(strpos($e->getMessage(), 'duplicate') !== false || strpos($e->getMessage(), 'unique') !== false) {
                $message = "⚠️ Pincode {$_POST['area_id']} already exists. Use a different pincode.";
            } else {
                $message = "❌ Error: " . $e->getMessage();
            }
            $message_type = 'error';
        }
    }
}

// ── UPDATE — Edit existing area ──
if(isset($_POST['action']) && $_POST['action'] === 'update') {
    if (empty($_POST['station_id'])) {
        $message = "❌ Error: Station is required";
        $message_type = 'error';
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE pollution_data 
                SET station_id=:station_id, aqi=:aqi, pm25=:pm25, humidity=:humidity, temperature=:temperature
                WHERE area_id=:area_id");
            $stmt->execute([
                ':station_id'  => $_POST['station_id'],
                ':area_id'     => $_POST['area_id'],
                ':aqi'         => $_POST['aqi'],
                ':pm25'        => $_POST['pm25'],
                ':humidity'    => $_POST['humidity'],
                ':temperature' => $_POST['temperature']
            ]);
            $message      = "✅ Pincode updated successfully!";
            $message_type = 'success';
        } catch(PDOException $e) {
            $message      = "❌ Error: " . $e->getMessage();
            $message_type = 'error';
        }
    }
}

// ── DELETE — Remove area ──
if(isset($_POST['action']) && $_POST['action'] === 'delete') {
    try {
        $stmt = $pdo->prepare("DELETE FROM pollution_data WHERE area_id=:area_id");
        $stmt->execute([':area_id' => $_POST['area_id']]);
        $message      = "✅ Pincode '{$_POST['area_id']}' deleted from dashboard successfully!";
        $message_type = 'success';
    } catch(PDOException $e) {
        $message      = "❌ Error: " . $e->getMessage();
        $message_type = 'error';
    }
}

// ── READ — Fetch all areas ──
$search = isset($_GET['search']) ? $_GET['search'] : '';
if($search !== '') {
    $stmt = $pdo->prepare("SELECT * FROM pollution_data WHERE area_id LIKE :s ORDER BY aqi DESC");
    $stmt->execute([':s' => '%'.$search.'%']);
} else {
    $stmt = $pdo->query("SELECT * FROM pollution_data ORDER BY aqi DESC");
}
$areas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ── FETCH ALL ADMINS who have logged in ──
$admins = $pdo->query("SELECT user_id, name, email FROM users WHERE user_type='admin' ORDER BY user_id ASC")->fetchAll(PDO::FETCH_ASSOC);

// ── FETCH ALL STATIONS ──
$stations = $pdo->query("SELECT station_id, name FROM monitoring_stations ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

// ── FETCH ALL AREAS for dropdown ──
$db_areas = $pdo->query("SELECT area_id, area_name, city FROM areas ORDER BY area_name ASC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>VayuDarpan – Admin Dashboard</title>
  <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@400;600;700&family=Noto+Sans:wght@300;400;600&family=Teko:wght@400;500;600&display=swap" rel="stylesheet"/>
  <style>
    :root{
      --nav-bg:#0d2137; --accent:#00bcd4; --good:#00c853;
      --error:#e53935; --warn:#ffc107; --text:#1a2a3a;
      --border:#dde4ea; --poor:#ff6d00;
    }
    *{margin:0;padding:0;box-sizing:border-box;}
    body{
      font-family:'Noto Sans',sans-serif;color:var(--text);min-height:100vh;
      background:radial-gradient(ellipse 900px 500px at 15% 20%,rgba(0,188,212,0.08) 0%,transparent 70%),
                 linear-gradient(160deg,#e8f4fd 0%,#f5faff 50%,#edf7ee 100%);
    }

    /* NAV */
    nav{background:var(--nav-bg);display:flex;align-items:center;justify-content:space-between;
      padding:0 32px;height:56px;position:sticky;top:0;z-index:1000;
      box-shadow:0 2px 12px rgba(0,0,0,0.3);}
    .nav-logo{font-family:'Teko',sans-serif;font-size:1.6rem;color:var(--accent);letter-spacing:2px;}
    .nav-logo span{color:#fff;}
    .nav-right{display:flex;align-items:center;gap:16px;}
    .admin-badge{
      background:rgba(0,188,212,0.15);border:1px solid var(--accent);
      border-radius:20px;padding:5px 14px;
      font-family:'Rajdhani',sans-serif;font-weight:700;
      font-size:0.82rem;color:var(--accent);
    }
    .nav-links{display:flex;gap:6px;}
    .nav-links a{color:#cde8f5;text-decoration:none;font-family:'Rajdhani',sans-serif;
      font-weight:600;font-size:0.9rem;letter-spacing:1px;padding:7px 14px;
      border-radius:4px;transition:background 0.2s;text-transform:uppercase;}
    .nav-links a:hover{background:var(--accent);color:#fff;}
    .nav-links a.logout{border:1px solid var(--error);color:#ff7070;}
    .nav-links a.logout:hover{background:var(--error);color:#fff;}

    /* ADMIN HEADER */
    .admin-header{
      background:linear-gradient(135deg,var(--nav-bg) 0%,#1a3a5c 100%);
      padding:24px 40px;
      display:flex;align-items:center;justify-content:space-between;
    }
    .admin-header h1{font-family:'Teko',sans-serif;font-size:2rem;color:#fff;letter-spacing:2px;}
    .admin-header h1 span{color:var(--accent);}
    .admin-header p{color:#7cb8d4;font-size:0.83rem;margin-top:4px;}
    .session-info{
      background:rgba(0,188,212,0.1);border:1px solid rgba(0,188,212,0.3);
      border-radius:10px;padding:10px 18px;
      font-family:'Rajdhani',sans-serif;font-size:0.82rem;
      color:#cde8f5;line-height:1.8;text-align:right;
    }
    .session-info b{color:var(--accent);}

    /* WRAPPER */
    .wrapper{max-width:1200px;margin:28px auto;padding:0 24px;}

    /* MESSAGE */
    .msg{padding:13px 18px;border-radius:8px;margin-bottom:20px;font-size:0.88rem;font-weight:600;}
    .msg.success{background:#e8f5e9;border:1px solid #a5d6a7;color:#1b5e20;}
    .msg.error  {background:#ffebee;border:1px solid #ef9a9a;color:#b71c1c;}
    .msg.warn   {background:#fff8e1;border:1px solid #ffe082;color:#e65100;}

    /* SECTION TITLE */
    .sec-title{
      font-family:'Teko',sans-serif;font-size:1.6rem;color:var(--nav-bg);
      border-left:5px solid var(--accent);padding-left:12px;
      margin:28px 0 16px;letter-spacing:1px;
    }

    /* ADMIN LIST */
    .admin-list{
      display:flex;gap:14px;flex-wrap:wrap;margin-bottom:28px;
    }
    .admin-card{
      background:#fff;border-radius:10px;padding:14px 18px;
      box-shadow:0 2px 10px rgba(0,0,0,0.07);
      border-left:4px solid var(--accent);
      display:flex;align-items:center;gap:12px;
    }
    .admin-avatar{
      width:40px;height:40px;border-radius:50%;
      background:linear-gradient(135deg,var(--nav-bg),var(--accent));
      display:flex;align-items:center;justify-content:center;
      font-family:'Teko',sans-serif;font-size:1.1rem;color:#fff;
      flex-shrink:0;
    }
    .admin-name{font-family:'Rajdhani',sans-serif;font-weight:700;font-size:0.92rem;color:var(--nav-bg);}
    .admin-email{font-size:0.75rem;color:#888;}
    .you-badge{
      background:var(--accent);color:#fff;font-size:0.65rem;
      font-weight:700;padding:2px 8px;border-radius:8px;
      font-family:'Rajdhani',sans-serif;letter-spacing:1px;margin-left:6px;
    }

    /* CRUD FORMS */
    .crud-row{display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:24px;}
    .card{background:#fff;border-radius:14px;padding:22px;box-shadow:0 3px 16px rgba(0,0,0,0.08);}
    .card h3{
      font-family:'Rajdhani',sans-serif;font-weight:700;
      font-size:0.95rem;letter-spacing:1px;text-transform:uppercase;
      margin-bottom:14px;display:flex;align-items:center;gap:8px;
    }
    .c-create h3{color:#1b5e20;}
    .c-update h3{color:#e65100;}
    .c-delete h3{color:#b71c1c;}

    /* FIELDS */
    .field{margin-bottom:11px;}
    .field label{
      display:block;font-family:'Rajdhani',sans-serif;font-weight:600;
      font-size:0.75rem;color:#445;letter-spacing:0.5px;
      text-transform:uppercase;margin-bottom:4px;
    }
    .field input{
      width:100%;padding:9px 12px;border:2px solid var(--border);
      border-radius:7px;font-size:0.86rem;font-family:'Noto Sans',sans-serif;
      color:var(--text);outline:none;transition:border-color 0.2s;
    }
    .field input:focus{border-color:var(--accent);box-shadow:0 0 0 3px rgba(0,188,212,0.1);}
    .field-row{display:grid;grid-template-columns:1fr 1fr;gap:10px;}

    /* BUTTONS */
    .btn{
      width:100%;padding:10px;border:none;border-radius:8px;
      font-family:'Rajdhani',sans-serif;font-weight:700;
      font-size:0.92rem;letter-spacing:1px;text-transform:uppercase;
      cursor:pointer;transition:all 0.2s;margin-top:4px;
    }
    .btn-green{background:linear-gradient(135deg,#1b5e20,#2e7d32);color:#fff;}
    .btn-green:hover{filter:brightness(1.1);transform:translateY(-1px);}
    .btn-orange{background:linear-gradient(135deg,#e65100,#f57c00);color:#fff;}
    .btn-orange:hover{filter:brightness(1.1);transform:translateY(-1px);}
    .btn-red{background:linear-gradient(135deg,#b71c1c,#c62828);color:#fff;}
    .btn-red:hover{filter:brightness(1.1);transform:translateY(-1px);}
    .btn-navy{background:linear-gradient(135deg,var(--nav-bg),#1a3a5c);color:#fff;}
    .btn-navy:hover{filter:brightness(1.1);transform:translateY(-1px);}

    /* SEARCH */
    .search-bar{
      display:flex;gap:10px;margin-bottom:16px;
    }
    .search-bar input{
      flex:1;padding:10px 14px;border:2px solid var(--border);
      border-radius:8px;font-size:0.88rem;outline:none;
      transition:border-color 0.2s;
    }
    .search-bar input:focus{border-color:var(--accent);}
    .search-bar button{
      padding:10px 24px;background:var(--nav-bg);color:#fff;
      border:none;border-radius:8px;font-family:'Rajdhani',sans-serif;
      font-weight:700;font-size:0.9rem;letter-spacing:1px;
      cursor:pointer;text-transform:uppercase;transition:background 0.2s;
    }
    .search-bar button:hover{background:var(--accent);}
    .search-bar a{
      padding:10px 18px;background:#f0f4f8;color:var(--text);
      border:2px solid var(--border);border-radius:8px;
      font-family:'Rajdhani',sans-serif;font-weight:700;
      font-size:0.85rem;text-decoration:none;display:flex;
      align-items:center;transition:all 0.2s;
    }
    .search-bar a:hover{background:var(--nav-bg);color:#fff;border-color:var(--nav-bg);}

    /* TABLE */
    .table-card{background:#fff;border-radius:14px;overflow:hidden;box-shadow:0 3px 16px rgba(0,0,0,0.08);}
    .table-head{
      background:var(--nav-bg);padding:16px 22px;
      display:flex;align-items:center;justify-content:space-between;
    }
    .table-head h3{font-family:'Rajdhani',sans-serif;font-weight:700;
      font-size:1rem;color:#cde8f5;letter-spacing:1px;text-transform:uppercase;}
    .rec-count{background:var(--accent);color:#fff;font-size:0.75rem;
      font-weight:700;padding:3px 12px;border-radius:12px;font-family:'Rajdhani',sans-serif;}
    table{width:100%;border-collapse:collapse;}
    thead tr{background:#f0f4f8;}
    thead th{padding:11px 16px;text-align:left;font-family:'Rajdhani',sans-serif;
      font-weight:700;font-size:0.78rem;color:var(--nav-bg);letter-spacing:1px;
      text-transform:uppercase;border-bottom:2px solid var(--border);}
    tbody tr{border-bottom:1px solid var(--border);transition:background 0.15s;}
    tbody tr:hover{background:#f7fdfe;}
    tbody td{padding:11px 16px;font-size:0.85rem;}

    /* AQI CHIPS */
    .aqi-chip{display:inline-block;padding:3px 12px;border-radius:12px;
      font-weight:700;font-size:0.78rem;color:#fff;}
    .chip-good    {background:var(--good);}
    .chip-moderate{background:var(--warn);color:#3d2800;}
    .chip-poor    {background:var(--poor);}
    .chip-severe  {background:var(--error);}

    /* ACTION BUTTONS IN TABLE */
    .btn-edit{background:#e3f2fd;color:#1565c0;border:1px solid #90caf9;
      padding:4px 12px;border-radius:6px;font-size:0.75rem;font-weight:700;
      cursor:pointer;font-family:'Rajdhani',sans-serif;transition:all 0.2s;margin-right:4px;}
    .btn-edit:hover{background:#1565c0;color:#fff;}
    .btn-del{background:#ffebee;color:#b71c1c;border:1px solid #ef9a9a;
      padding:4px 12px;border-radius:6px;font-size:0.75rem;font-weight:700;
      cursor:pointer;font-family:'Rajdhani',sans-serif;transition:all 0.2s;}
    .btn-del:hover{background:#b71c1c;color:#fff;}

    .empty{text-align:center;padding:32px;color:#aaa;font-size:0.88rem;}
    
    /* FEEDBACK UI */
    .fb-card { background:#fff; border-radius:12px; padding:16px; margin-bottom:12px; border-left:4px solid var(--accent); box-shadow:0 2px 8px rgba(0,0,0,0.05); }
    .fb-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:8px; border-bottom:1px solid var(--border); padding-bottom:8px; }
    .fb-user { font-family:'Rajdhani',sans-serif; font-weight:700; font-size:0.9rem; color:var(--nav-bg); }
    .fb-date { font-size:0.75rem; color:#888; }
    .fb-rating { color:#ffb300; font-size:1.1rem; letter-spacing:1px; margin-bottom:6px; }
    .fb-msg { font-size:0.85rem; color:#444; line-height:1.5; }
    .fb-filter { margin-bottom:16px; padding:8px 12px; border:2px solid var(--border); border-radius:8px; outline:none; }

    footer{background:var(--nav-bg);color:#7cb8d4;
      text-align:center;padding:14px;font-size:0.78rem;margin-top:32px;}
      
    /* RESPONSIVE FLUID RULES */
    @media (max-width: 768px) {
      .crud-row {
        grid-template-columns: 1fr;
      }
      .field-row {
        grid-template-columns: 1fr;
      }
      nav {
        flex-direction: column;
        height: auto;
        padding: 10px;
        align-items: center;
      }
      .nav-right {
          flex-direction: column;
          margin-top: 10px;
      }
      .admin-header {
        flex-direction: column;
        align-items: flex-start;
      }
      table {
        font-size: 12px;
        display: block;
        overflow-x: auto;
      }
      .table-head {
          flex-direction: column;
          align-items: flex-start;
          gap: 10px;
      }
      .admin-list {
          flex-direction: column;
      }
      .btn {
          width: 100%;
      }
      .search-bar {
          flex-direction: column;
      }
    }
  </style>
</head>
<body>

<!-- NAV -->
<nav>
  <div class="nav-logo">Vayu<span>Darpan</span></div>
  <div class="nav-right">
    <div class="admin-badge">🛡️ ADMIN PANEL</div>
    <div class="nav-links">
      <a href="../index.php">Home</a>
      <a href="../public_dashboard.php">Dashboard</a>
      <a href="manage_users.php">Manage Users</a>
      <a href="stations.php">Manage Stations</a>
      <a href="../logout.php" class="logout">Logout</a>
    </div>
  </div>
</nav>

<!-- ADMIN HEADER -->
<div class="admin-header">
  <div>
    <h1>Admin <span>Control Panel</span></h1>
    <p>Manage pollution data — changes reflect instantly on the Public Dashboard</p>
  </div>
  <div class="session-info">
    👤 Logged in as: <b><?= htmlspecialchars($admin_name) ?></b><br>
    🔑 Session ID: <b style="font-size:0.72rem;color:#7cb8d4;"><?= session_id() ?></b>
  </div>
</div>

<div class="wrapper">

  <!-- MESSAGE -->
  <?php if($message): ?>
  <div class="msg <?= $message_type ?>"><?= $message ?></div>
  <?php endif; ?>

  <!-- ADMIN USERS LIST -->
  <div class="sec-title">🛡️ Registered Administrators</div>
  <div class="admin-list">
    <?php foreach($admins as $admin): ?>
    <div class="admin-card">
      <div class="admin-avatar">
        <?= strtoupper(substr($admin['name'],0,1)) ?>
      </div>
      <div>
        <div class="admin-name">
          <?= htmlspecialchars($admin['name']) ?>
          <?php if($admin['user_id'] == $admin_id): ?>
          <span class="you-badge">YOU</span>
          <?php endif; ?>
        </div>
        <div class="admin-email"><?= htmlspecialchars($admin['email']) ?></div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>

  <div class="crud-row">
    <!-- CREATE FORM -->
    <div class="card c-create">
      <h3>➕ Add New Data</h3>
      
      <?php if(empty($stations)): ?>
        <div class="msg warn">⚠️ Please create a monitoring station first before adding pollution data. <br><br><a href="stations.php" class="btn btn-navy" style="text-decoration:none; display:inline-block; text-align:center;">Manage Stations</a></div>
      <?php else: ?>
        <form method="POST">
          <input type="hidden" name="action" value="create"/>
          
          <div class="field">
            <label>Monitoring Station</label>
            <select name="station_id" style="width:100%;padding:9px 12px;border:2px solid var(--border);border-radius:7px;margin-bottom:11px;" required>
              <option value="">Select Station</option>
              <?php foreach($stations as $station): ?>
                <option value="<?= $station['station_id'] ?>">
                  <?= htmlspecialchars($station['name']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="field">
            <label>Pincode / Area ID</label>
            <select name="area_id" style="width:100%;padding:9px 12px;border:2px solid var(--border);border-radius:7px;margin-bottom:11px;" required>
              <option value="">Select Area / Pincode</option>
              <?php foreach($db_areas as $ar): ?>
                <option value="<?= $ar['area_id'] ?>">
                  <?= htmlspecialchars($ar['area_id']) ?> - <?= htmlspecialchars($ar['area_name']) ?> (<?= htmlspecialchars($ar['city']) ?>)
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="field-row">
            <div class="field">
              <label>AQI</label>
              <input type="number" name="aqi" placeholder="0 - 500" required/>
            </div>
            <div class="field">
              <label>PM2.5</label>
              <input type="number" step="0.1" name="pm25" placeholder="µg/m³" required/>
            </div>
          </div>
          <div class="field-row">
            <div class="field">
              <label>Humidity %</label>
              <input type="number" step="0.1" name="humidity" placeholder="%" required/>
            </div>
            <div class="field">
              <label>Temp °C</label>
              <input type="number" step="0.1" name="temperature" placeholder="°C" required/>
            </div>
          </div>
          <button type="submit" class="btn btn-green">Add Data</button>
        </form>
      <?php endif; ?>
    </div>

    <!-- UPDATE FORM -->
    <div class="card c-update">
      <h3>✏️ Update Data</h3>
      <?php if(empty($stations)): ?>
        <div class="msg warn">⚠️ Please create a monitoring station first.</div>
      <?php else: ?>
        <form method="POST">
          <input type="hidden" name="action" value="update"/>
          <input type="hidden" name="area_id" id="upAreaId" required/>
          
          <div class="field">
            <label>Monitoring Station</label>
            <select name="station_id" id="upStation" style="width:100%;padding:9px 12px;border:2px solid var(--border);border-radius:7px;margin-bottom:11px;" required>
              <option value="">Select Station</option>
              <?php foreach($stations as $station): ?>
                <option value="<?= $station['station_id'] ?>">
                  <?= htmlspecialchars($station['name']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="field-row">
            <div class="field">
              <label>AQI</label>
              <input type="number" name="aqi" id="upAqi" placeholder="0 - 500" required/>
            </div>
            <div class="field">
              <label>PM2.5</label>
              <input type="number" step="0.1" name="pm25" id="upPm" placeholder="µg/m³" required/>
            </div>
          </div>
          <div class="field-row">
            <div class="field">
              <label>Humidity %</label>
              <input type="number" step="0.1" name="humidity" id="upHum" placeholder="%" required/>
            </div>
            <div class="field">
              <label>Temp °C</label>
              <input type="number" step="0.1" name="temperature" id="upTemp" placeholder="°C" required/>
            </div>
          </div>
          <button type="submit" class="btn btn-orange">Update Data</button>
        </form>
      <?php endif; ?>
    </div>
  </div>

  <!-- MANAGE AREAS DYNAMIC SECTION -->
  <div class="sec-title">🗺️ Manage Areas (Locations)</div>
  <div class="crud-row" id="area-management-section">
    <!-- CREATE AREA -->
    <div class="card c-create">
      <h3>➕ Add New Area</h3>
      <form id="createAreaForm">
        <div class="field-row">
          <div class="field"><label>Pincode (Area ID)</label><input type="number" id="ca_id" required/></div>
          <div class="field"><label>Area Name</label><input type="text" id="ca_name" required/></div>
        </div>
        <div class="field-row">
          <div class="field"><label>City</label><input type="text" id="ca_city" required/></div>
          <div class="field"><label>State</label><input type="text" id="ca_state" required/></div>
        </div>
        <div class="field-row">
          <div class="field"><label>Type</label>
             <select id="ca_type" style="width:100%;padding:9px 12px;border:2px solid var(--border);border-radius:7px;">
                <option value="Urban">Urban</option>
                <option value="Rural">Rural</option>
                <option value="Suburban">Suburban</option>
             </select>
          </div>
          <div class="field"><label>Pop. Density</label><input type="number" id="ca_pop" placeholder="Optional"/></div>
        </div>
        <div class="field-row">
          <div class="field"><label>Acres</label><input type="number" id="ca_acres" placeholder="Optional"/></div>
          <div class="field"><label>Topography</label><input type="text" id="ca_topo" placeholder="Optional"/></div>
        </div>
        <button type="submit" class="btn btn-green">Create Area</button>
      </form>
    </div>

    <!-- UPDATE AREA -->
    <div class="card c-update">
      <h3>✏️ Update Area</h3>
      <form id="updateAreaForm">
        <div class="field-row">
          <div class="field"><label>Pincode (Area ID)</label><input type="number" id="ua_id" required readonly style="background:#f0f4f8;"/></div>
          <div class="field"><label>Area Name</label><input type="text" id="ua_name" required/></div>
        </div>
        <div class="field-row">
          <div class="field"><label>City</label><input type="text" id="ua_city" required/></div>
          <div class="field"><label>State</label><input type="text" id="ua_state" required/></div>
        </div>
        <div class="field-row">
          <div class="field"><label>Type</label>
             <select id="ua_type" style="width:100%;padding:9px 12px;border:2px solid var(--border);border-radius:7px;">
                <option value="Urban">Urban</option>
                <option value="Rural">Rural</option>
                <option value="Suburban">Suburban</option>
             </select>
          </div>
        </div>
        <div class="field-row">
          <div class="field"><label>Pop. Density</label><input type="number" id="ua_pop"/></div>
          <div class="field"><label>Acres</label><input type="number" id="ua_acres"/></div>
        </div>
        <div class="field">
          <label>Topography</label><input type="text" id="ua_topo"/>
        </div>
        <button type="submit" class="btn btn-orange">Update Area</button>
      </form>
    </div>
  </div>

  <div class="table-card" style="margin-bottom: 28px;">
    <div class="table-head">
      <h3>📋 Registered Areas (Locations)</h3>
      <button onclick="loadAreas()" style="background:var(--accent);color:#fff;border:none;padding:5px 12px;border-radius:8px;cursor:pointer;font-weight:bold;">↻ Refresh API</button>
    </div>
    <div style="max-height: 400px; overflow-y: auto;">
      <table id="areasTable">
        <thead style="position: sticky; top: 0; z-index: 2;">
          <tr style="background:#f0f4f8;">
            <th>ID</th>
            <th>Name</th>
            <th>City</th>
            <th>State</th>
            <th>Type</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody id="areasTbody">
          <!-- Populated by JS -->
        </tbody>
      </table>
    </div>
  </div>

  <!-- READ TABLE -->
  <div class="sec-title">📋 All Areas on Public Dashboard</div>
  <form method="GET" class="search-bar">
    <input type="text" name="search" placeholder="🔍 Search by Pincode..."
      value="<?= htmlspecialchars($search) ?>"/>
    <button type="submit">Search</button>
    <?php if($search): ?>
    <a href="admin_dashboard.php">✕ Clear</a>
    <?php endif; ?>
  </form>

  <div class="table-card">
    <div class="table-head">
      <h3>📋 Pollution Data — Public Dashboard Areas</h3>
      <span class="rec-count"><?= count($areas) ?> Records</span>
    </div>
    <?php if(count($areas) > 0): ?>
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Pincode</th>
          <th>AQI</th>
          <th>PM2.5 µg/m³</th>
          <th>Humidity %</th>
          <th>Temp °C</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($areas as $i => $row):
          $aqi = $row['aqi'];
          if($aqi <= 100)     { $cls='chip-good';     $label='Good'; }
          elseif($aqi <= 200) { $cls='chip-moderate'; $label='Moderate'; }
          elseif($aqi <= 300) { $cls='chip-poor';     $label='Poor'; }
          else                { $cls='chip-severe';   $label='Severe'; }
        ?>
        <tr>
          <td><?= $i+1 ?></td>
          <td><b><?= htmlspecialchars($row['area_id']) ?></b></td>
          <td><span class="aqi-chip <?= $cls ?>"><?= $aqi ?></span></td>
          <td><?= $row['pm25'] ?></td>
          <td><?= $row['humidity'] ?>%</td>
          <td><?= $row['temperature'] ?>°C</td>
          <td><span class="aqi-chip <?= $cls ?>"><?= $label ?></span></td>
          <td>
            <button class="btn-edit" onclick="fillUpdate(
              '<?= htmlspecialchars($row['area_id']) ?>',
              '<?= htmlspecialchars($row['station_id'] ?? '') ?>',
              <?= $aqi ?>,
              <?= $row['pm25'] ?>,
              <?= $row['humidity'] ?>,
              <?= $row['temperature'] ?>)">
              ✏️ Edit
            </button>
            <form method="POST" style="display:inline;"
              onsubmit="return confirm('Delete <?= htmlspecialchars($row['area_id']) ?> from dashboard?')">
              <input type="hidden" name="action" value="delete"/>
              <input type="hidden" name="area_id" value="<?= $row['area_id'] ?>"/>
              <button type="submit" class="btn-del">🗑️ Del</button>
            </form>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <?php else: ?>
    <div class="empty">📭 No areas found<?= $search ? ' for "'.$search.'"' : '' ?>.</div>
    <?php endif; ?>
  </div>

  <!-- USER FEEDBACK SECTION -->
  <div class="sec-title">💬 User Feedback</div>
  <div class="table-card" style="padding:22px; margin-bottom:28px;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
        <h3 style="margin:0; font-family:'Rajdhani',sans-serif; font-weight:700;">Recent Feedback</h3>
        <select class="fb-filter" id="fbFilter" onchange="loadFeedback()">
            <option value="recent">Most Recent First</option>
            <option value="oldest">Oldest First</option>
            <option value="rating_high">Highest Rating</option>
            <option value="rating_low">Lowest Rating</option>
        </select>
    </div>
    <div id="feedbackContainer" style="max-height:400px; overflow-y:auto; padding-right:10px;">
        <div class="empty">Loading feedback...</div>
    </div>
  </div>

</div><!-- end wrapper -->

<footer>
  © 2025 VayuDarpan – Admin Control Panel &nbsp;|&nbsp;
  Logged in as <?= htmlspecialchars($admin_name) ?> &nbsp;|&nbsp;
  Session: <?= session_id() ?>
</footer>

<script>
function fillUpdate(area_id, station_id, aqi, pm25, hum, temp) {
  document.querySelector('.c-update').scrollIntoView({behavior:'smooth', block:'center'});
  document.getElementById('upAreaId').value  = area_id;
  if(document.getElementById('upStation')) {
      document.getElementById('upStation').value = station_id;
  }
  document.getElementById('upAqi').value     = aqi;
  document.getElementById('upPm').value      = pm25;
  document.getElementById('upHum').value     = hum;
  document.getElementById('upTemp').value    = temp;
}

// ==========================================
// AREA API LOGIC
// ==========================================

const API_BASE = '../api/areas';

function loadAreas() {
  fetch(`${API_BASE}/fetch_all.php`)
    .then(r => r.json())
    .then(data => {
      const tbody = document.getElementById('areasTbody');
      tbody.innerHTML = '';
      if(data.success && data.data.length > 0) {
        data.data.forEach(a => {
          const tr = document.createElement('tr');
          tr.innerHTML = `
            <td><b>${a.area_id}</b></td>
            <td>${a.area_name}</td>
            <td>${a.city}</td>
            <td>${a.state}</td>
            <td>${a.location_type}</td>
            <td>
              <button class="btn-edit" onclick='editArea(${JSON.stringify(a).replace(/'/g, "&#39;")})'>✏️ Edit</button>
              <button class="btn-del" onclick="deleteArea(${a.area_id})">🗑️ Del</button>
            </td>
          `;
          tbody.appendChild(tr);
        });
      } else {
        tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;">No areas found via API.</td></tr>';
      }
    })
    .catch(e => console.error("Error loading areas:", e));
}

function editArea(area) {
  document.getElementById('ua_id').value = area.area_id;
  document.getElementById('ua_name').value = area.area_name;
  document.getElementById('ua_city').value = area.city;
  document.getElementById('ua_state').value = area.state;
  document.getElementById('ua_type').value = area.location_type;
  document.getElementById('ua_pop').value = area.population_density || '';
  document.getElementById('ua_acres').value = area.acres || '';
  document.getElementById('ua_topo').value = area.topography || '';
  document.querySelector('#updateAreaForm').scrollIntoView({behavior:'smooth', block:'center'});
}

function deleteArea(id) {
  if(!confirm('Are you sure you want to delete this area?')) return;
  fetch(`${API_BASE}/delete.php`, {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({area_id: id})
  })
  .then(r => r.json())
  .then(data => {
    alert(data.message);
    if(data.success) loadAreas();
  });
}

document.getElementById('createAreaForm').addEventListener('submit', function(e) {
  e.preventDefault();
  const payload = {
    area_id: document.getElementById('ca_id').value,
    area_name: document.getElementById('ca_name').value,
    city: document.getElementById('ca_city').value,
    state: document.getElementById('ca_state').value,
    location_type: document.getElementById('ca_type').value,
    population_density: document.getElementById('ca_pop').value,
    acres: document.getElementById('ca_acres').value,
    topography: document.getElementById('ca_topo').value
  };
  
  fetch(`${API_BASE}/create.php`, {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify(payload)
  })
  .then(r => r.json())
  .then(data => {
    alert(data.message);
    if(data.success) {
      this.reset();
      loadAreas();
    }
  });
});

document.getElementById('updateAreaForm').addEventListener('submit', function(e) {
  e.preventDefault();
  const payload = {
    area_id: document.getElementById('ua_id').value,
    area_name: document.getElementById('ua_name').value,
    city: document.getElementById('ua_city').value,
    state: document.getElementById('ua_state').value,
    location_type: document.getElementById('ua_type').value,
    population_density: document.getElementById('ua_pop').value,
    acres: document.getElementById('ua_acres').value,
    topography: document.getElementById('ua_topo').value
  };
  
  fetch(`${API_BASE}/update.php`, {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify(payload)
  })
  .then(r => r.json())
  .then(data => {
    alert(data.message);
    if(data.success) {
      loadAreas();
    }
  });
});

// Load areas on startup
document.addEventListener('DOMContentLoaded', loadAreas);
</script>
</body>
</html>
