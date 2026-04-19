<?php
header("Content-Type: text/html; charset=UTF-8");
session_start();
require_once '../api/db.php';

if (!isset($pdo)) {
    die("Database connection failed.");
}

// ── ADMIN PROTECTION ──
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$message      = '';
$message_type = '';

// ══════════════════════════════════════
// CRUD OPERATIONS ON monitoring_stations
// ══════════════════════════════════════

if(isset($_POST['action'])) {
    if($_POST['action'] === 'create') {
        try {
            $stmt = $pdo->prepare("INSERT INTO monitoring_stations (name, description, area_id) VALUES (:name, :description, :area_id)");
            $stmt->execute([
                ':name'        => $_POST['name'],
                ':description' => $_POST['description'],
                ':area_id'     => empty($_POST['area_id']) ? null : $_POST['area_id']
            ]);
            $message      = "✅ Station '{$_POST['name']}' created successfully!";
            $message_type = 'success';
        } catch(PDOException $e) {
            $message      = "❌ Error: " . $e->getMessage();
            $message_type = 'error';
        }
    }
    elseif($_POST['action'] === 'update') {
        try {
            $stmt = $pdo->prepare("UPDATE monitoring_stations SET name=:name, description=:description, area_id=:area_id WHERE station_id=:station_id");
            $stmt->execute([
                ':name'        => $_POST['name'],
                ':description' => $_POST['description'],
                ':area_id'     => empty($_POST['area_id']) ? null : $_POST['area_id'],
                ':station_id'  => $_POST['station_id']
            ]);
            $message      = "✅ Station updated successfully!";
            $message_type = 'success';
        } catch(PDOException $e) {
            $message      = "❌ Error: " . $e->getMessage();
            $message_type = 'error';
        }
    }
    elseif($_POST['action'] === 'delete') {
        try {
            $stmt = $pdo->prepare("DELETE FROM monitoring_stations WHERE station_id=:station_id");
            $stmt->execute([':station_id' => $_POST['station_id']]);
            $message      = "✅ Station deleted successfully!";
            $message_type = 'success';
        } catch(PDOException $e) {
            $message      = "❌ Error: " . $e->getMessage();
            $message_type = 'error';
        }
    }
}

// ── FETCH DATA ──
$stations = $pdo->query("SELECT * FROM monitoring_stations ORDER BY station_id DESC")->fetchAll(PDO::FETCH_ASSOC);

$workers = $pdo->query("
    SELECT u.name, u.email, m.name as station_name
    FROM users u
    JOIN monitoring_stations m ON u.station_id = m.station_id
    WHERE u.user_type = 'station_worker'
")->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>VayuDarpan – Manage Stations</title>
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
    
    .msg{padding:13px 18px;border-radius:8px;margin-bottom:20px;font-size:0.88rem;font-weight:600;}
    .msg.success{background:#e8f5e9;border:1px solid #a5d6a7;color:#1b5e20;}
    .msg.error  {background:#ffebee;border:1px solid #ef9a9a;color:#b71c1c;}

    .sec-title{
      font-family:'Teko',sans-serif;font-size:1.6rem;color:var(--nav-bg);
      border-left:5px solid var(--accent);padding-left:12px;
      margin:28px 0 16px;letter-spacing:1px;
    }

    /* CRUD LAYOUT */
    .crud-row{display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:24px;}
    .card{background:#fff;border-radius:14px;padding:22px;box-shadow:0 3px 16px rgba(0,0,0,0.08);}
    .card h3{font-family:'Rajdhani',sans-serif;font-weight:700;font-size:0.95rem;margin-bottom:14px;}
    
    .field{margin-bottom:11px;}
    .field label{display:block;font-size:0.75rem;font-weight:600;color:#445;margin-bottom:4px;}
    .field input, .field textarea{width:100%;padding:9px 12px;border:2px solid var(--border);border-radius:7px;}
    
    .btn{width:100%;padding:10px;border:none;border-radius:8px;font-weight:700;cursor:pointer;color:#fff;}
    .btn-green{background:linear-gradient(135deg,#1b5e20,#2e7d32);}
    .btn-orange{background:linear-gradient(135deg,#e65100,#f57c00);}
    .btn-del{background:#ffebee;color:#b71c1c;border:1px solid #ef9a9a;padding:4px 12px;border-radius:6px;cursor:pointer;}

    table{width:100%;border-collapse:collapse;background:#fff;border-radius:14px;overflow:hidden;box-shadow:0 3px 16px rgba(0,0,0,0.08);margin-bottom:24px;}
    thead{background:var(--nav-bg);}
    thead th{padding:11px 16px;text-align:left;color:#fff;font-size:0.85rem;}
    tbody tr{border-bottom:1px solid var(--border);}
    tbody td{padding:11px 16px;font-size:0.85rem;}

    /* RESPONSIVE */
    @media (max-width: 768px) {
      .crud-row { grid-template-columns: 1fr; }
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
      <a href="admin_dashboard.php">Back to Dashboard</a>
      <a href="manage_users.php">Manage Users</a>
      <a href="../logout.php" class="logout">Logout</a>
    </div>
  </div>
</nav>

<div class="wrapper">

  <?php if($message): ?>
  <div class="msg <?= $message_type ?>"><?= $message ?></div>
  <?php endif; ?>

  <div class="sec-title">🏭 Manage Monitoring Stations</div>

  <div class="crud-row">
    <!-- CREATE -->
    <div class="card">
      <h3 style="color:#1b5e20;">➕ Create New Station</h3>
      <form method="POST">
        <input type="hidden" name="action" value="create"/>
        <div class="field">
          <label>Station Name *</label>
          <input type="text" name="name" required/>
        </div>
        <div class="field">
          <label>Description</label>
          <textarea name="description" rows="2"></textarea>
        </div>
        <div class="field">
          <label>Pincode (Area ID)</label>
          <input type="number" name="area_id" placeholder="Optional numeric binding"/>
        </div>
        <button type="submit" class="btn btn-green">Create Station</button>
      </form>
    </div>

    <!-- UPDATE -->
    <div class="card">
      <h3 style="color:#e65100;">✏️ Update Existing Station</h3>
      <form method="POST">
        <input type="hidden" name="action" value="update"/>
        <div class="field">
          <label>Station ID (Auto-filled) *</label>
          <input type="number" name="station_id" id="upId" readonly required/>
        </div>
        <div class="field">
          <label>Station Name *</label>
          <input type="text" name="name" id="upName" required/>
        </div>
        <div class="field">
          <label>Description</label>
          <textarea name="description" id="upDesc" rows="2"></textarea>
        </div>
        <div class="field">
          <label>Pincode (Area ID)</label>
          <input type="number" name="area_id" id="upArea"/>
        </div>
        <button type="submit" class="btn btn-orange">Update Station</button>
      </form>
    </div>
  </div>

  <!-- TABLE -->
  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Description</th>
        <th>Pincode (Area ID)</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($stations as $s): ?>
      <tr>
        <td><?= $s['station_id'] ?></td>
        <td><b><?= htmlspecialchars($s['name']) ?></b></td>
        <td><?= htmlspecialchars($s['description'] ?? '') ?></td>
        <td><?= htmlspecialchars($s['area_id'] ?? 'Not Bound') ?></td>
        <td>
          <button style="background:#e3f2fd;color:#1565c0;border:none;padding:4px 8px;cursor:pointer;border-radius:4px" 
            onclick="fillUp(<?= $s['station_id'] ?>, '<?= htmlspecialchars(addslashes($s['name'])) ?>', '<?= htmlspecialchars(addslashes($s['description']??'')) ?>', '<?= $s['area_id'] ?>')">
            ✏️ Edit
          </button>
          <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this station?');">
            <input type="hidden" name="action" value="delete"/>
            <input type="hidden" name="station_id" value="<?= $s['station_id'] ?>"/>
            <button type="submit" class="btn-del">🗑️</button>
          </form>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <!-- STAFF JOIN LIST -->
  <div class="sec-title">👥 Station Workers Assigned</div>
  <table>
    <thead>
      <tr>
        <th>Worker Name</th>
        <th>Worker Email</th>
        <th>Assigned Station Name</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($workers as $w): ?>
      <tr>
        <td><b><?= htmlspecialchars($w['name']) ?></b></td>
        <td><?= htmlspecialchars($w['email']) ?></td>
        <td><?= htmlspecialchars($w['station_name']) ?></td>
      </tr>
      <?php endforeach; ?>
      <?php if(empty($workers)): ?>
      <tr>
        <td colspan="3" style="text-align:center;color:#666">No station workers currently assigned.</td>
      </tr>
      <?php endif; ?>
    </tbody>
  </table>

</div>

<script>
function fillUp(id, name, desc, area) {
  document.getElementById('upId').value = id;
  document.getElementById('upName').value = name;
  document.getElementById('upDesc').value = desc;
  document.getElementById('upArea').value = area;
}
</script>
</body>
</html>