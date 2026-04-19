<?php
session_start();
require_once 'api/db.php';

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    if ($_SESSION['user_type'] === 'admin') header("Location: admin/admin_dashboard.php");
    elseif ($_SESSION['user_type'] === 'station_worker') header("Location: monitoring-station/");
    else header("Location: public_dashboard.php");
    exit;
}

if (isset($_COOKIE['remember_token']) && !isset($_SESSION['logged_in'])) {
    $token = $_COOKIE['remember_token'];
    try {
        $stmt = $pdo->prepare("SELECT user_id, name, user_type, station_id FROM users WHERE remember_token = :token LIMIT 1");
        $stmt->execute([':token' => $token]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            session_regenerate_id(true);
            $_SESSION['logged_in'] = true;
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_type'] = $user['user_type'];
            $_SESSION['station_id'] = $user['station_id'];
            $_SESSION['user_role'] = $user['user_type'];

            if ($user['user_type'] === 'admin') header("Location: admin/admin_dashboard.php");
            elseif ($user['user_type'] === 'station_worker') header("Location: monitoring-station/");
            else header("Location: public_dashboard.php");
            exit;
        }
    } catch (PDOException $e) {}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>VayuDarpan – Login</title>
  <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@500;600;700&family=Noto+Sans:wght@400;500;600&family=Teko:wght@500;600&display=swap" rel="stylesheet"/>
  <style>
    :root {
      --primary: #00bcd4;
      --primary-dark: #0097a7;
      --bg-color: #f4f7f6;
      --surface: #ffffff;
      --text: #1a2a3a;
      --text-muted: #64748b;
      --border: #e2e8f0;
      --error: #ef4444;
      --error-bg: #fef2f2;
      --error-border: #f87171;
    }

    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
      font-family: 'Noto Sans', sans-serif;
      color: var(--text);
      background-color: var(--bg-color);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      /* Modern soft gradient mesh background */
      background-image: 
        radial-gradient(at 10% 20%, rgba(0, 188, 212, 0.08) 0px, transparent 50%),
        radial-gradient(at 90% 80%, rgba(0, 200, 83, 0.05) 0px, transparent 50%);
    }

    /* CENTERED CARD LAYOUT */
    .login-wrapper {
      width: 100%;
      max-width: 420px;
      padding: 24px;
    }

    .login-card {
      background: var(--surface);
      border-radius: 16px;
      box-shadow: 0 10px 40px -10px rgba(0,0,0,0.08), 0 1px 3px rgba(0,0,0,0.05);
      padding: 40px 32px;
      width: 100%;
    }

    /* HEADER */
    .brand {
      text-align: center;
      margin-bottom: 32px;
    }
    .brand-logo {
      font-family: 'Teko', sans-serif;
      font-size: 2.5rem;
      color: #0d2137;
      letter-spacing: 1px;
      line-height: 1;
    }
    .brand-logo span { color: var(--primary); }
    .brand-subtitle {
      font-size: 0.9rem;
      color: var(--text-muted);
      margin-top: 8px;
    }

    /* INLINE ERROR DISPLAY */
    .error-banner {
      display: none;
      background: var(--error-bg);
      border: 1px solid var(--error-border);
      color: var(--error);
      padding: 12px 16px;
      border-radius: 8px;
      margin-bottom: 24px;
      font-size: 0.85rem;
      align-items: center;
      gap: 12px;
    }
    .error-banner.show { display: flex; animation: shake 0.4s ease-in-out; }
    .error-icon { font-size: 1.1rem; }

    @keyframes shake {
      0%, 100% { transform: translateX(0); }
      25% { transform: translateX(-4px); }
      75% { transform: translateX(4px); }
    }

    /* FORM ELEMENTS */
    .form-group {
      margin-bottom: 20px;
    }
    
    .form-group label {
      display: block;
      font-size: 0.85rem;
      font-weight: 500;
      color: var(--text);
      margin-bottom: 8px;
    }

    .input-wrapper {
      position: relative;
    }

    .form-control {
      width: 100%;
      padding: 12px 16px;
      border: 1.5px solid var(--border);
      border-radius: 8px;
      font-size: 0.95rem;
      font-family: inherit;
      color: var(--text);
      background-color: #fff;
      transition: all 0.2s ease;
      outline: none;
    }

    .form-control::placeholder { color: #a0aec0; }
    
    .form-control:focus {
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(0, 188, 212, 0.15);
    }
    .form-control.is-invalid {
      border-color: var(--error);
      background-color: #fffafaf;
    }

    .password-toggle {
      position: absolute;
      right: 12px;
      top: 50%;
      transform: translateY(-50%);
      background: none;
      border: none;
      cursor: pointer;
      color: var(--text-muted);
      font-size: 1rem;
      padding: 4px;
    }
    .password-toggle:hover { color: var(--primary); }

    .forgot-link {
      text-align: right;
      margin-top: -8px;
      margin-bottom: 24px;
    }
    .forgot-link a {
      font-size: 0.8rem;
      color: var(--primary);
      text-decoration: none;
      font-weight: 500;
    }
    .forgot-link a:hover { text-decoration: underline; }

    /* ACTION BUTTON */
    .btn-submit {
      width: 100%;
      height: 48px;
      background: var(--primary);
      color: #fff;
      border: none;
      border-radius: 8px;
      font-size: 1rem;
      font-weight: 600;
      font-family: 'Rajdhani', sans-serif;
      letter-spacing: 1px;
      cursor: pointer;
      transition: all 0.2s;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
    }

    .btn-submit:hover:not(:disabled) {
      background: var(--primary-dark);
      transform: translateY(-1px);
      box-shadow: 0 4px 12px rgba(0, 188, 212, 0.25);
    }

    .btn-submit:disabled {
      opacity: 0.7;
      cursor: not-allowed;
    }

    /* SPINNER */
    .spinner {
      width: 20px;
      height: 20px;
      border: 3px solid rgba(255,255,255,0.3);
      border-radius: 50%;
      border-top-color: #fff;
      animation: spin 0.8s linear infinite;
      display: none;
    }
    @keyframes spin { to { transform: rotate(360deg); } }

    /* FOOTER */
    .auth-footer {
      text-align: center;
      margin-top: 24px;
      font-size: 0.85rem;
      color: var(--text-muted);
    }
    .auth-footer a {
      color: var(--primary);
      font-weight: 600;
      text-decoration: none;
    }
    .auth-footer a:hover { text-decoration: underline; }

  </style>
</head>
<body>

<div class="login-wrapper">
  <div class="login-card">
    
    <div class="brand">
      <div class="brand-logo">Vayu<span>Darpan</span></div>
      <div class="brand-subtitle">Sign in to your account</div>
    </div>

    <!-- INLINE ERROR BANNER -->
    <div class="error-banner" id="errorBanner">
      <div class="error-icon">⚠️</div>
      <span id="errorText">Invalid email or password.</span>
    </div>

    <form id="loginForm" onsubmit="event.preventDefault(); submitLogin();">
      
      <div class="form-group">
        <label for="email">Email Address</label>
        <div class="input-wrapper">
          <input type="email" id="email" class="form-control" placeholder="you@example.com" autocomplete="email" required />
        </div>
      </div>

      <div class="form-group">
        <label for="password">Password</label>
        <div class="input-wrapper">
          <input type="password" id="password" class="form-control" placeholder="••••••••" required />
          <button type="button" class="password-toggle" id="pwToggle" onclick="togglePassword()" title="Toggle password visibility">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
              <circle cx="12" cy="12" r="3"></circle>
            </svg>
          </button>
        </div>
      </div>

      <div style="display:flex; justify-content:space-between; align-items:center; margin-top:-8px; margin-bottom:24px;">
        <label style="display:flex; align-items:center; font-size:0.85rem; color:var(--text); cursor:pointer; font-weight:500;">
          <input type="checkbox" id="remember" style="margin-right:8px; cursor:pointer; width:16px; height:16px;" /> Remember Me
        </label>
        <div class="forgot-link" style="margin:0;">
          <a href="forgot_password.php">Forgot password?</a>
        </div>
      </div>

      <button type="submit" class="btn-submit" id="submitBtn">
        <div class="spinner" id="spinner"></div>
        <span id="btnText">Sign In</span>
      </button>

    </form>

    <div class="auth-footer">
      Don't have an account? <a href="signup.php">Create one</a>
    </div>

  </div>
</div>

<script>
// --- UI INTERACTIONS ---
function togglePassword() {
  const inp = document.getElementById('password');
  const svgOpen = `<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>`;
  const svgClosed = `<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>`;
  
  if (inp.type === 'password') {
    inp.type = 'text';
    document.getElementById('pwToggle').innerHTML = `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">${svgClosed}</svg>`;
  } else {
    inp.type = 'password';
    document.getElementById('pwToggle').innerHTML = `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">${svgOpen}</svg>`;
  }
}

function showError(msg) {
  const banner = document.getElementById('errorBanner');
  document.getElementById('errorText').textContent = msg;
  banner.classList.remove('show');
  void banner.offsetWidth; // trigger reflow
  banner.classList.add('show');
  
  // Highlight inputs
  document.getElementById('email').classList.add('is-invalid');
  document.getElementById('password').classList.add('is-invalid');
}

function clearError() {
  document.getElementById('errorBanner').classList.remove('show');
  document.getElementById('email').classList.remove('is-invalid');
  document.getElementById('password').classList.remove('is-invalid');
}

// Clear error state on typing
document.getElementById('email').addEventListener('input', clearError);
document.getElementById('password').addEventListener('input', clearError);


// --- API INTEGRATION ---
async function submitLogin() {
  const email = document.getElementById('email').value.trim();
  const password = document.getElementById('password').value;
  
  if (!email || !password) return;

  clearError();

  // Loading state
  const btn = document.getElementById('submitBtn');
  const spinner = document.getElementById('spinner');
  const btnText = document.getElementById('btnText');
  
  btn.disabled = true;
  spinner.style.display = 'block';
  btnText.textContent = 'Signing in...';

  const remember = document.getElementById('remember').checked;

  // 1. DYNAMIC BASE URL - Solves 404 Error
  const BASE_URL = window.location.origin + '/college/Air-Pollution-Management-System-main';

  try {
    const res = await fetch(`${BASE_URL}/api/auth/login.php`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ email, password, remember })
    });

    // 2. HTTP Error Handling
    if (!res.ok) {
        let errMsg = "An unexpected error occurred.";
        try {
            const errData = await res.json();
            errMsg = errData.message || errMsg;
        } catch(e) {
            // Unparseable error (500 internal server error etc)
            if(res.status === 404) errMsg = "Authentication endpoint not found.";
        }
        throw new Error(errMsg);
    }

    const data = await res.json();

    // 3. Routing Control
    if (data.success && data.redirect) {
      console.log(data.redirect);
      window.location.href = data.redirect;
    } else {
      throw new Error(data.message || "Invalid response from server.");
    }

  } catch (err) {
    // 4. Network and Custom Error Display
    let displayMsg = err.message;
    if (err.name === 'TypeError') {
        displayMsg = "Network error. Please check your connection.";
    }
    showError(displayMsg);
    
    // Reset button
    btn.disabled = false;
    spinner.style.display = 'none';
    btnText.textContent = 'Sign In';
  }
}
</script>
</body>
</html>
