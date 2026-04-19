<?php
session_start();
if (isset($_COOKIE['remember_token'])) {
    require_once 'api/db.php';
    try {
        $stmt = $pdo->prepare("UPDATE users SET remember_token = NULL WHERE remember_token = :token");
        $stmt->execute([':token' => $_COOKIE['remember_token']]);
    } catch (PDOException $e) {}
    setcookie('remember_token', '', time() - 3600, '/college/Air-Pollution-Management-System-main');
}
$_SESSION = [];
session_destroy();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>VayuDarpan – Logout</title>
  <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@400;500;600;700&family=Noto+Sans:wght@300;400;600&family=Teko:wght@400;500;600&display=swap" rel="stylesheet"/>
  <style>
    :root{--nav-bg:#0d2137;--accent:#00bcd4;--error:#e53935;--success:#00c853;--text:#1a2a3a;}
    *{margin:0;padding:0;box-sizing:border-box;}
    body{
      font-family:'Noto Sans',sans-serif;color:var(--text);min-height:100vh;
      background:radial-gradient(ellipse 900px 500px at 15% 20%,rgba(0,188,212,0.08) 0%,transparent 70%),
                 radial-gradient(ellipse 700px 400px at 85% 80%,rgba(0,200,83,0.07) 0%,transparent 60%),
                 linear-gradient(160deg,#e8f4fd 0%,#f5faff 50%,#edf7ee 100%);
    }
    nav{background:var(--nav-bg);display:flex;align-items:center;justify-content:space-between;
      padding:0 32px;height:56px;position:sticky;top:0;z-index:1000;box-shadow:0 2px 12px rgba(0,0,0,0.3);}
    .nav-logo{font-family:'Teko',sans-serif;font-size:1.6rem;color:var(--accent);letter-spacing:2px;}
    .nav-logo span{color:#fff;}
    .nav-links{display:flex;gap:6px;}
    .nav-links a{color:#cde8f5;text-decoration:none;font-family:'Rajdhani',sans-serif;font-weight:600;
      font-size:0.95rem;letter-spacing:1px;padding:7px 16px;border-radius:4px;transition:background 0.2s;text-transform:uppercase;}
    .nav-links a:hover{background:var(--accent);color:#fff;}

    .page-wrap{min-height:calc(100vh - 56px);display:flex;align-items:center;justify-content:center;padding:40px 20px;}

    .logout-card{
      background:#fff;border-radius:20px;
      box-shadow:0 8px 48px rgba(13,33,55,0.13);
      width:100%;max-width:460px;overflow:hidden;
      animation:slideUp 0.5s ease;
      text-align:center;
    }
    @keyframes slideUp{from{opacity:0;transform:translateY(30px);}to{opacity:1;transform:translateY(0);}}

    .card-top{
      background:linear-gradient(135deg,var(--nav-bg),#1a3a5c);
      padding:36px 40px 28px;
    }
    .logout-icon{font-size:3.5rem;margin-bottom:12px;display:block;}
    .card-top h1{font-family:'Teko',sans-serif;font-size:1.9rem;color:#fff;letter-spacing:2px;}
    .card-top p{color:#7cb8d4;font-size:0.83rem;margin-top:6px;}

    .card-body{padding:32px 40px 36px;}
    .card-body p{color:#555;font-size:0.88rem;line-height:1.7;margin-bottom:24px;}
    .card-body b{color:var(--nav-bg);}

    .btn-row{display:flex;gap:12px;justify-content:center;flex-wrap:wrap;}
    .btn{
      padding:11px 28px;border-radius:10px;border:none;cursor:pointer;
      font-family:'Rajdhani',sans-serif;font-weight:700;font-size:0.95rem;
      letter-spacing:1px;text-transform:uppercase;transition:all 0.25s;
      text-decoration:none;display:inline-flex;align-items:center;gap:6px;
    }
    .btn-confirm{
      background:linear-gradient(135deg,var(--error),#c62828);color:#fff;
    }
    .btn-confirm:hover{transform:translateY(-2px);box-shadow:0 6px 18px rgba(229,57,53,0.35);}
    .btn-cancel{
      background:#f0f4f8;color:var(--nav-bg);border:2px solid #dde4ea;
    }
    .btn-cancel:hover{background:var(--nav-bg);color:#fff;border-color:var(--nav-bg);}

    /* countdown */
    .countdown-wrap{margin-top:20px;}
    .countdown-bar-bg{height:5px;background:#eee;border-radius:3px;overflow:hidden;margin:8px 0;}
    .countdown-bar{height:100%;background:linear-gradient(90deg,var(--accent),var(--nav-bg));border-radius:3px;width:100%;transition:width 0.1s linear;}
    .countdown-txt{font-size:0.75rem;color:#aaa;}

    /* logged out state */
    .logged-out{display:none;flex-direction:column;align-items:center;padding:48px 40px;}
    .logged-out.show{display:flex;}
    .lo-icon{font-size:3.5rem;animation:pop 0.4s ease;margin-bottom:12px;}
    @keyframes pop{0%{transform:scale(0);}70%{transform:scale(1.2);}100%{transform:scale(1);}}
    .logged-out h2{font-family:'Teko',sans-serif;font-size:1.7rem;color:var(--nav-bg);margin-bottom:8px;}
    .logged-out p{color:#666;font-size:0.83rem;margin-bottom:20px;}
    .progress-ring{font-size:0.78rem;color:#aaa;}
  </style>
</head>
<body>
<nav>
  <div class="nav-logo">Vayu<span>Darpan</span></div>
  <div class="nav-links">
    <a href="index.php">Home</a>
    <a href="login.php">Login</a>
    <a href="signup.php">Sign Up</a>
  </div>
</nav>

<div class="page-wrap">
<div class="logout-card">

  <!-- CONFIRM STATE -->
  <div id="confirmState">
    <div class="card-top">
      <span class="logout-icon">🔐</span>
      <h1>Logout</h1>
      <p>You are about to end your VayuDarpan session</p>
    </div>
    <div class="card-body">
      <p>Are you sure you want to <b>logout</b>?<br>
      Your session data will be cleared and you will be redirected to the login page.<br>
      Any unsaved changes will be lost.</p>
      <div class="btn-row">
        <button class="btn btn-confirm" onclick="confirmLogout()">🚪 Yes, Logout</button>
        <a href="index.php" class="btn btn-cancel">← Stay Logged In</a>
      </div>
      <div class="countdown-wrap" id="countdownWrap" style="display:none;">
        <span class="countdown-txt">Auto-logging out in <b id="cdNum">10</b> seconds…</span>
        <div class="countdown-bar-bg"><div class="countdown-bar" id="cdBar"></div></div>
      </div>
    </div>
  </div>

  <!-- LOGGED OUT STATE -->
  <div class="logged-out" id="loggedOutState">
    <div class="lo-icon">✅</div>
    <h2>Logged Out Successfully</h2>
    <p>Thank you for using VayuDarpan.<br>Your session has been securely ended.</p>
    <a href="login.php" class="btn btn-confirm" style="background:linear-gradient(135deg,var(--nav-bg),#1a3a5c);">🔑 Login Again</a>
    <div class="progress-ring" style="margin-top:16px;">Redirecting to home in <span id="redirNum">5</span>s…</div>
  </div>

</div>
</div>

<script>
// Auto-countdown after 10s
let autoTimer, cdInterval, redirTimer;

// Start auto-countdown
window.addEventListener('load', () => {
  const wrap = document.getElementById('countdownWrap');
  wrap.style.display = 'block';
  let remaining = 10;
  document.getElementById('cdNum').textContent = remaining;
  const bar = document.getElementById('cdBar');

  cdInterval = setInterval(() => {
    remaining--;
    document.getElementById('cdNum').textContent = remaining;
    bar.style.width = (remaining / 10 * 100) + '%';
    if(remaining <= 0){ clearInterval(cdInterval); confirmLogout(); }
  }, 1000);
});

function confirmLogout() {
  clearInterval(cdInterval);
  document.getElementById('confirmState').style.display = 'none';
  const lo = document.getElementById('loggedOutState');
  lo.classList.add('show');

  // Redirect countdown
  let r = 5;
  document.getElementById('redirNum').textContent = r;
  redirTimer = setInterval(() => {
    r--;
    document.getElementById('redirNum').textContent = r;
    if(r <= 0){ clearInterval(redirTimer); window.location.href = 'index.php'; }
  }, 1000);
}
</script>
</body>
</html>
