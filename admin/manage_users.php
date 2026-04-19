<?php
header("Content-Type: text/html; charset=UTF-8");
session_start();
require_once '../api/db.php';

// ── ADMIN PROTECTION ──
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Fetch stations for the add user dropdown
$stations = [];
try {
    $stRows = $pdo->query("SELECT station_id, name FROM monitoring_stations ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
    if($stRows) $stations = $stRows;
} catch(PDOException $e) {}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>VayuDarpan – Manage Users</title>
  <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@400;600;700&family=Noto+Sans:wght@300;400;600&family=Teko:wght@400;500;600&display=swap" rel="stylesheet"/>
  <style>
    :root{
      --nav-bg:#0d2137; --accent:#00bcd4; --text:#1a2a3a;
      --border:#dde4ea; --success:#00c853; --error:#e53935;
    }
    *{margin:0;padding:0;box-sizing:border-box;}
    body{
      font-family:'Noto Sans',sans-serif;color:var(--text);min-height:100vh;
      background:linear-gradient(160deg,#e8f4fd 0%,#f5faff 50%,#edf7ee 100%);
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

    /* WRAPPER */
    .wrapper{max-width:1200px;margin:28px auto;padding:0 24px;}
    
    .sec-title{
      font-family:'Teko',sans-serif;font-size:1.6rem;color:var(--nav-bg);
      border-left:5px solid var(--accent);padding-left:12px;
      margin:28px 0 16px;letter-spacing:1px;
    }

    /* STATS */
    .stats-row {
      display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 28px;
    }
    .stat-card {
      background: #fff; padding: 20px; border-radius: 12px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.05); text-align: center;
      border-bottom: 4px solid var(--accent);
    }
    .stat-val { font-size: 2rem; font-weight: bold; color: var(--nav-bg); font-family:'Teko',sans-serif; }
    .stat-label { font-size: 0.9rem; color: #666; font-family:'Rajdhani',sans-serif; font-weight: 600; text-transform: uppercase; }

    /* CARD AND FORM */
    .card{background:#fff;border-radius:14px;padding:22px;box-shadow:0 3px 16px rgba(0,0,0,0.08);margin-bottom:24px;}
    .card h3{font-family:'Rajdhani',sans-serif;font-weight:700;font-size:0.95rem;margin-bottom:14px;color:#1b5e20;}
    
    .field-row { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
    .field{margin-bottom:11px;}
    .field label{display:block;font-size:0.75rem;font-weight:600;color:#445;margin-bottom:4px;text-transform:uppercase;}
    .field input, .field select{width:100%;padding:9px 12px;border:2px solid var(--border);border-radius:7px;outline:none;font-family:inherit;}
    .field input:focus, .field select:focus{border-color:var(--accent);}

    .btn{width:100%;padding:10px;border:none;border-radius:8px;font-weight:700;cursor:pointer;color:#fff;font-family:'Rajdhani',sans-serif;text-transform:uppercase;letter-spacing:1px;transition:0.2s;}
    .btn-green{background:linear-gradient(135deg,#1b5e20,#2e7d32);}
    .btn-green:hover{filter:brightness(1.1);}

    /* TABLES */
    .table-card{background:#fff;border-radius:14px;overflow:hidden;box-shadow:0 3px 16px rgba(0,0,0,0.08);margin-bottom:28px;}
    .table-head{background:var(--nav-bg);color:#fff;padding:12px 20px;}
    .table-head h3{font-family:'Rajdhani',sans-serif;font-size:1rem;margin:0;letter-spacing:1px;text-transform:uppercase;}
    table{width:100%;border-collapse:collapse;background:#fff;}
    thead{background:#f0f4f8;}
    thead th{padding:11px 16px;text-align:left;color:var(--nav-bg);font-size:0.8rem;text-transform:uppercase;font-family:'Rajdhani',sans-serif;}
    tbody tr{border-bottom:1px solid var(--border);transition:0.2s;}
    tbody tr:hover{background:#f8fcfe;}
    tbody td{padding:11px 16px;font-size:0.85rem;}

    /* RESPONSIVE */
    @media (max-width: 768px) {
      .stats-row { grid-template-columns: 1fr; }
      .field-row { grid-template-columns: 1fr; }
      nav { flex-direction: column; height: auto; padding: 10px; }
      .nav-right { flex-direction: column; margin-top: 10px; }
      table { display: block; overflow-x: auto; white-space: nowrap; }
    }
  </style>
</head>
<body>

<nav>
  <div class="nav-logo">Vayu<span>Darpan</span></div>
  <div class="nav-right">
    <div class="admin-badge">🛡️ ADMIN PANEL</div>
    <div class="nav-links">
      <a href="admin_dashboard.php">Dashboard</a>
      <a href="stations.php">Manage Stations</a>
      <a href="../logout.php" class="logout">Logout</a>
    </div>
  </div>
</nav>

<div class="wrapper">
  
  <div class="sec-title">👥 User Statistics</div>
  <div class="stats-row">
    <div class="stat-card">
      <div class="stat-val" id="stat-total">0</div>
      <div class="stat-label">Total Users</div>
    </div>
    <div class="stat-card" style="border-bottom-color: #2e7d32;">
      <div class="stat-val" id="stat-end">0</div>
      <div class="stat-label">End Users</div>
    </div>
    <div class="stat-card" style="border-bottom-color: #e65100;">
      <div class="stat-val" id="stat-worker">0</div>
      <div class="stat-label">Station Workers</div>
    </div>
  </div>

  <div class="sec-title">➕ Add New User</div>
  <div class="card">
    <form id="addUserForm">
      <div class="field-row">
        <div class="field">
          <label>Full Name *</label>
          <input type="text" id="add_name" required/>
        </div>
        <div class="field">
          <label>Email Address *</label>
          <input type="email" id="add_email" required/>
        </div>
      </div>
      <div class="field-row">
        <div class="field">
          <label>Password *</label>
          <input type="password" id="add_password" required/>
        </div>
        <div class="field">
          <label>User Role *</label>
          <select id="add_role" required onchange="toggleStationSelect()">
            <option value="end_user">End User</option>
            <option value="station_worker">Station Worker</option>
          </select>
        </div>
      </div>
      <div class="field" id="stationField" style="display:none; max-width: 50%;">
        <label>Assign to Monitoring Station *</label>
        <select id="add_station">
          <option value="">-- Select a Station --</option>
          <?php foreach($stations as $s): ?>
            <option value="<?= $s['station_id'] ?>"><?= htmlspecialchars($s['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div style="margin-top:16px;">
        <button type="submit" class="btn btn-green">Create User</button>
      </div>
    </form>
  </div>

  <div class="sec-title">🔍 System Users</div>
  
  <!-- Station Workers Section -->
  <div class="table-card">
    <div class="table-head">
      <h3>👷 Station Workers</h3>
    </div>
    <table>
      <thead>
        <tr>
          <th>User ID</th>
          <th>Name</th>
          <th>Email Address</th>
          <th>Assigned Station</th>
          <th>Joined On</th>
        </tr>
      </thead>
      <tbody id="workersTbody">
        <tr><td colspan="5" style="text-align:center;">Loading...</td></tr>
      </tbody>
    </table>
  </div>

  <!-- End Users Section -->
  <div class="table-card" style="border-bottom: 4px solid #00bcd4;">
    <div class="table-head" style="background:#00bcd4;">
      <h3>👤 End Users</h3>
    </div>
    <table>
      <thead>
        <tr>
          <th>User ID</th>
          <th>Name</th>
          <th>Email Address</th>
          <th>Joined On</th>
        </tr>
      </thead>
      <tbody id="endUsersTbody">
        <tr><td colspan="4" style="text-align:center;">Loading...</td></tr>
      </tbody>
    </table>
  </div>

</div>

<script>
function toggleStationSelect() {
  const role = document.getElementById('add_role').value;
  const stationDiv = document.getElementById('stationField');
  if(role === 'station_worker') {
    stationDiv.style.display = 'block';
    document.getElementById('add_station').required = true;
  } else {
    stationDiv.style.display = 'none';
    document.getElementById('add_station').required = false;
  }
}

async function fetchStats() {
  try {
    const res = await fetch('../api/users/fetch_all.php');
    const data = await res.json();
    if(data.success) {
      document.getElementById('stat-total').innerText = data.data.length;
      document.getElementById('stat-end').innerText = data.data.filter(u => u.user_type === 'end_user').length;
      document.getElementById('stat-worker').innerText = data.data.filter(u => u.user_type === 'station_worker').length;
    }
  } catch(e) { console.error("Error fetching stats:", e); }
}

async function fetchWorkers() {
  try {
    const res = await fetch('../api/users/fetch_station_workers.php');
    const data = await res.json();
    const tbody = document.getElementById('workersTbody');
    if(data.success && data.data.length > 0) {
      tbody.innerHTML = data.data.map(u => `
        <tr>
          <td><b>${u.user_id}</b></td>
          <td>${u.name}</td>
          <td>${u.email}</td>
          <td>${u.station_name || 'Unassigned'}</td>
          <td>${new Date(u.created_at).toLocaleDateString()}</td>
        </tr>
      `).join('');
    } else {
      tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;">No station workers found.</td></tr>';
    }
  } catch(e) { console.error("Error fetching workers:", e); }
}

async function fetchEndUsers() {
  try {
    const res = await fetch('../api/users/fetch_end_users.php');
    const data = await res.json();
    const tbody = document.getElementById('endUsersTbody');
    if(data.success && data.data.length > 0) {
      tbody.innerHTML = data.data.map(u => `
        <tr>
          <td><b>${u.user_id}</b></td>
          <td>${u.name}</td>
          <td>${u.email}</td>
          <td>${new Date(u.created_at).toLocaleDateString()}</td>
        </tr>
      `).join('');
    } else {
      tbody.innerHTML = '<tr><td colspan="4" style="text-align:center;">No end users found.</td></tr>';
    }
  } catch(e) { console.error("Error fetching end users:", e); }
}

document.getElementById('addUserForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  
  const payload = {
    name: document.getElementById('add_name').value,
    email: document.getElementById('add_email').value,
    password: document.getElementById('add_password').value,
    user_type: document.getElementById('add_role').value,
    station_id: document.getElementById('add_station').value
  };

  try {
    const res = await fetch('../api/users/add.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    });
    const data = await res.json();
    alert(data.message);
    if(data.success) {
      this.reset();
      toggleStationSelect();
      loadAllData();
    }
  } catch(err) {
    alert("An error occurred while creating user.");
    console.error(err);
  }
});

function loadAllData() {
  fetchStats();
  fetchWorkers();
  fetchEndUsers();
}

document.addEventListener('DOMContentLoaded', loadAllData);
</script>
</body>
</html>
