<?php
session_start();
include 'db.php';

// ── ADMIN PROTECTION ──
// Only allow users with role = 'admin'
if(!isset($_SESSION['logged_in']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Log admin login into database
$admin_id   = $_SESSION['user_id'];
$admin_name = $_SESSION['user_name'];

$message      = '';
$message_type = '';

// ══════════════════════════════════════
// CRUD OPERATIONS ON pollution_data
// ══════════════════════════════════════

// ── CREATE — Add new city/area ──
if(isset($_POST['action']) && $_POST['action'] === 'create') {
    try {
        $stmt = $pdo->prepare("INSERT INTO public.pollution_data 
            (area_id, pincode, aqi, pm, humitdity, temperature)
            VALUES (:area_id, :pincode, :aqi, :pm, :humitdity, :temperature)");
        $stmt->execute([
            ':area_id'     => $_POST['area_id'],
            ':pincode'     => $_POST['pincode'],
            ':aqi'         => $_POST['aqi'],
            ':pm'          => $_POST['pm'],
            ':humitdity'   => $_POST['humitdity'],
            ':temperature' => $_POST['temperature'],
        ]);
        $message      = "✅ Area '{$_POST['area_id']}' added successfully to the dashboard!";
        $message_type = 'success';
    } catch(PDOException $e) {
        if(strpos($e->getMessage(), 'duplicate') !== false || strpos($e->getMessage(), 'unique') !== false) {
            $message = "⚠️ Pincode {$_POST['pincode']} already exists. Use a different pincode.";
        } else {
            $message = "❌ Error: " . $e->getMessage();
        }
        $message_type = 'error';
    }
}

// ── UPDATE — Edit existing area ──
if(isset($_POST['action']) && $_POST['action'] === 'update') {
    try {
        $stmt = $pdo->prepare("UPDATE public.pollution_data 
            SET area_id=:area_id, aqi=:aqi, pm=:pm, humitdity=:humitdity, temperature=:temperature
            WHERE pincode=:pincode");
        $stmt->execute([
            ':area_id'     => $_POST['area_id'],
            ':aqi'         => $_POST['aqi'],
            ':pm'          => $_POST['pm'],
            ':humitdity'   => $_POST['humitdity'],
            ':temperature' => $_POST['temperature'],
            ':pincode'     => $_POST['pincode'],
        ]);
        $message      = "✅ Area updated successfully!";
        $message_type = 'success';
    } catch(PDOException $e) {
        $message      = "❌ Error: " . $e->getMessage();
        $message_type = 'error';
    }
}

// ── DELETE — Remove area ──
if(isset($_POST['action']) && $_POST['action'] === 'delete') {
    try {
        $area = $pdo->query("SELECT area_id FROM public.pollution_data WHERE pincode={$_POST['pincode']}")->fetch();
        $stmt = $pdo->prepare("DELETE FROM public.pollution_data WHERE pincode=:pincode");
        $stmt->execute([':pincode' => $_POST['pincode']]);
        $message      = "✅ Area '{$area['area_id']}' deleted from dashboard successfully!";
        $message_type = 'success';
    } catch(PDOException $e) {
        $message      = "❌ Error: " . $e->getMessage();
        $message_type = 'error';
    }
}

// ── READ — Fetch all areas ──
$search = isset($_GET['search']) ? $_GET['search'] : '';
if($search !== '') {
    $stmt = $pdo->prepare("SELECT * FROM public.pollution_data WHERE area_id ILIKE :s ORDER BY aqi DESC");
    $stmt->execute([':s' => '%'.$search.'%']);
} else {
    $stmt = $pdo->query("SELECT * FROM public.pollution_data ORDER BY aqi DESC");
}
$areas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ── FETCH ALL ADMINS who have logged in ──
$admins = $pdo->query("SELECT id, first_name, last_name, email, city FROM public.users WHERE role='admin' ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
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

    footer{background:var(--nav-bg);color:#7cb8d4;
      text-align:center;padding:14px;font-size:0.78rem;margin-top:32px;}
  </style>
</head>
<body>

<!-- NAV -->
<nav>
  <div class="nav-logo">Vayu<span>Darpan</span></div>
  <div class="nav-right">
    <div class="admin-badge">🛡️ ADMIN PANEL</div>
    <div class="nav-links">
      <a href="index.php">Home</a>
      <a href="public_dashboard.php">Dashboard</a>
      <a href="logout.php" class="logout">Logout</a>
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
    📧 <b><?= htmlspecialchars($_SESSION['user_email']) ?></b><br>
    ⏰ Login time: <b><?= htmlspecialchars($_SESSION['login_time']) ?></b><br>
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
        <?= strtoupper(substr($admin['first_name'],0,1)) ?>
      </div>
      <div>
        <div class="admin-name">
          <?= htmlspecialchars($admin['first_name'].' '.$admin['last_name']) ?>
          <?php if($admin['id'] == $admin_id): ?>
          <span class="you-badge">YOU</span>
          <?php endif; ?>
        </div>
        <div class="admin-email"><?= htmlspecialchars($admin['email']) ?></div>
        <div class="admin-email">📍 <?= htmlspecialchars($admin['city']) ?></div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>

  <!-- CRUD FORMS -->
  <div class="sec-title">🏙️ Manage Cities / Areas on Public Dashboard</div>

  <div class="crud-row">

    <!-- ADD CITY -->
    <div class="card c-create">
      <h3>➕ Add New City / Area</h3>
      <form method="POST">
        <input type="hidden" name="action" value="create"/>
        <div class="field-row">
          <div class="field">
            <label>Area Name *</label>
            <input type="text" name="area_id" placeholder="e.g. Powai" required/>
          </div>
          <div class="field">
            <label>Pincode *</label>
            <input type="number" name="pincode" placeholder="e.g. 400076" required/>
          </div>
        </div>
        <div class="field-row">
          <div class="field">
            <label>AQI Value *</label>
            <input type="number" name="aqi" placeholder="0–500" min="0" max="500" required/>
          </div>
          <div class="field">
            <label>PM (µg/m³) *</label>
            <input type="number" name="pm" placeholder="e.g. 60" required/>
          </div>
        </div>
        <div class="field-row">
          <div class="field">
            <label>Humidity (%) *</label>
            <input type="number" name="humitdity" placeholder="e.g. 72" required/>
          </div>
          <div class="field">
            <label>Temperature (°C) *</label>
            <input type="number" name="temperature" placeholder="e.g. 34" required/>
          </div>
        </div>
        <button type="submit" class="btn btn-green">➕ Add to Dashboard</button>
      </form>
    </div>

    <!-- UPDATE CITY -->
    <div class="card c-update">
      <h3>✏️ Update Existing Area</h3>
      <form method="POST" id="updateForm">
        <input type="hidden" name="action" value="update"/>
        <div class="field-row">
          <div class="field">
            <label>Pincode (identify record) *</label>
            <input type="number" name="pincode" id="upPincode" placeholder="Auto-filled on Edit click" required/>
          </div>
          <div class="field">
            <label>Area Name *</label>
            <input type="text" name="area_id" id="upAreaId" placeholder="Area name" required/>
          </div>
        </div>
        <div class="field-row">
          <div class="field">
            <label>New AQI *</label>
            <input type="number" name="aqi" id="upAqi" placeholder="0–500" min="0" max="500" required/>
          </div>
          <div class="field">
            <label>New PM (µg/m³) *</label>
            <input type="number" name="pm" id="upPm" placeholder="e.g. 75" required/>
          </div>
        </div>
        <div class="field-row">
          <div class="field">
            <label>New Humidity (%) *</label>
            <input type="number" name="humitdity" id="upHum" placeholder="e.g. 65" required/>
          </div>
          <div class="field">
            <label>New Temperature (°C) *</label>
            <input type="number" name="temperature" id="upTemp" placeholder="e.g. 32" required/>
          </div>
        </div>
        <button type="submit" class="btn btn-orange">✏️ Update Area</button>
      </form>
    </div>

  </div>

  <!-- DELETE CITY (standalone) -->
  <div class="card c-delete" style="margin-bottom:24px;">
    <h3>🗑️ Delete Area from Dashboard</h3>
    <p style="font-size:0.8rem;color:#b71c1c;margin-bottom:12px;">
      ⚠️ Deleting an area removes it permanently from the Public Dashboard. Click the 🗑️ button in the table below for quick delete.
    </p>
    <form method="POST" style="display:flex;gap:12px;align-items:flex-end;">
      <input type="hidden" name="action" value="delete"/>
      <div class="field" style="flex:1;margin:0;">
        <label>Pincode of area to delete *</label>
        <input type="number" name="pincode" placeholder="Enter pincode" required/>
      </div>
      <button type="submit" class="btn btn-red" style="width:auto;padding:10px 24px;"
        onclick="return confirm('Are you sure you want to delete this area from the dashboard?')">
        🗑️ Delete Area
      </button>
    </form>
  </div>

  <!-- READ TABLE -->
  <div class="sec-title">📋 All Areas on Public Dashboard</div>

  <form method="GET" class="search-bar">
    <input type="text" name="search" placeholder="🔍 Search by area name..."
      value="<?= htmlspecialchars($search) ?>"/>
    <button type="submit">Search</button>
    <?php if($search): ?>
    <a href="admin_dashboard.php">✕ Clear</a>
    <?php endif; ?>
  </form>

  <div class="table-card">
    <div class="table-head">
      <h3>📋 Pollution Data — Public Dashboard Areas</h3>
      <span class="rec-count"><?= count($areas) ?> Areas</span>
    </div>
    <?php if(count($areas) > 0): ?>
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Area Name</th>
          <th>Pincode</th>
          <th>AQI</th>
          <th>PM µg/m³</th>
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
          <td><?= $row['pincode'] ?></td>
          <td><span class="aqi-chip <?= $cls ?>"><?= $aqi ?></span></td>
          <td><?= $row['pm'] ?></td>
          <td><?= $row['humitdity'] ?>%</td>
          <td><?= $row['temperature'] ?>°C</td>
          <td><span class="aqi-chip <?= $cls ?>"><?= $label ?></span></td>
          <td>
            <button class="btn-edit" onclick="fillUpdate(
              <?= $row['pincode'] ?>,
              '<?= htmlspecialchars($row['area_id']) ?>',
              <?= $aqi ?>,
              <?= $row['pm'] ?>,
              <?= $row['humitdity'] ?>,
              <?= $row['temperature'] ?>)">
              ✏️ Edit
            </button>
            <form method="POST" style="display:inline;"
              onsubmit="return confirm('Delete <?= htmlspecialchars($row['area_id']) ?> from dashboard?')">
              <input type="hidden" name="action" value="delete"/>
              <input type="hidden" name="pincode" value="<?= $row['pincode'] ?>"/>
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

</div><!-- end wrapper -->

<footer>
  © 2025 VayuDarpan – Admin Control Panel &nbsp;|&nbsp;
  Logged in as <?= htmlspecialchars($admin_name) ?> &nbsp;|&nbsp;
  Session: <?= session_id() ?>
</footer>

<script>
function fillUpdate(pincode, area, aqi, pm, hum, temp) {
  document.querySelector('.c-update').scrollIntoView({behavior:'smooth', block:'center'});
  document.getElementById('upPincode').value = pincode;
  document.getElementById('upAreaId').value  = area;
  document.getElementById('upAqi').value     = aqi;
  document.getElementById('upPm').value      = pm;
  document.getElementById('upHum').value     = hum;
  document.getElementById('upTemp').value    = temp;
}
</script>
</body>
</html>
