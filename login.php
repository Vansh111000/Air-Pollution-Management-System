<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>VayuDarpan – Login</title>
  <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@400;500;600;700&family=Noto+Sans:wght@300;400;600&family=Teko:wght@400;500;600&display=swap" rel="stylesheet"/>
  <style>
    :root{--nav-bg:#0d2137;--accent:#00bcd4;--error:#e53935;--success:#00c853;--text:#1a2a3a;--border:#dde4ea;}
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
      font-size:0.95rem;letter-spacing:1px;padding:7px 16px;border-radius:4px;transition:background 0.2s,color 0.2s;text-transform:uppercase;}
    .nav-links a:hover{background:var(--accent);color:#fff;}
    .nav-links a.active{background:var(--accent);color:#fff;}
    .nav-links a.logout{border:1px solid #e53935;color:#ff7070;}
    .nav-links a.logout:hover{background:#e53935;color:#fff;}

    .page-wrap{min-height:calc(100vh - 56px);display:flex;align-items:center;justify-content:center;padding:40px 20px;}

    .login-container{
      width:100%;max-width:480px;
      background:#fff;border-radius:20px;
      box-shadow:0 8px 48px rgba(13,33,55,0.13);
      overflow:hidden;animation:slideUp 0.5s ease;
    }
    @keyframes slideUp{from{opacity:0;transform:translateY(30px);}to{opacity:1;transform:translateY(0);}}

    .form-header{
      background:linear-gradient(135deg,var(--nav-bg) 0%,#1a3a5c 100%);
      padding:32px 40px 24px;position:relative;overflow:hidden;
    }
    .form-header::after{content:'';position:absolute;right:-40px;top:-40px;
      width:180px;height:180px;border-radius:50%;background:rgba(0,188,212,0.1);}
    .form-header h1{font-family:'Teko',sans-serif;font-size:2rem;color:#fff;letter-spacing:2px;}
    .form-header h1 span{color:var(--accent);}
    .form-header p{color:#7cb8d4;font-size:0.83rem;margin-top:4px;}

    /* ROLE TABS */
    .role-tabs{display:flex;border-bottom:2px solid var(--border);}
    .role-tab{
      flex:1;padding:12px;text-align:center;cursor:pointer;
      font-family:'Rajdhani',sans-serif;font-weight:700;font-size:0.85rem;
      letter-spacing:0.5px;color:#888;border-bottom:3px solid transparent;
      margin-bottom:-2px;transition:all 0.2s;
    }
    .role-tab:hover{color:var(--accent);}
    .role-tab.active{color:var(--accent);border-bottom-color:var(--accent);background:#f7fdfe;}
    .role-tab .tab-icon{font-size:1.1rem;margin-bottom:3px;display:block;}

    .form-body{padding:28px 40px 36px;}

    .alert-banner{display:none;padding:12px 16px;border-radius:8px;margin-bottom:16px;
      font-size:0.82rem;align-items:center;gap:10px;}
    .alert-banner.show{display:flex;}
    .alert-banner.error{background:#ffebee;border:1px solid #ef9a9a;color:#b71c1c;}
    .alert-banner.success{background:#e8f5e9;border:1px solid #a5d6a7;color:#1b5e20;}
    .alert-banner.warn{background:#fff8e1;border:1px solid #ffe082;color:#e65100;}

    .field{display:flex;flex-direction:column;gap:5px;margin-bottom:16px;}
    .field label{font-family:'Rajdhani',sans-serif;font-weight:600;font-size:0.8rem;color:#445;letter-spacing:0.5px;}
    .field label .req{color:var(--error);margin-left:2px;}
    .field-wrap{position:relative;}
    .field input,.field select{
      width:100%;padding:11px 38px 11px 14px;
      border:2px solid var(--border);border-radius:8px;
      font-size:0.88rem;font-family:'Noto Sans',sans-serif;color:var(--text);
      background:#fff;transition:border-color 0.25s,box-shadow 0.25s,background 0.2s;outline:none;
    }
    .field input:focus,.field select:focus{border-color:var(--accent);box-shadow:0 0 0 3px rgba(0,188,212,0.12);background:#f7fdfe;}
    .field input.valid{border-color:var(--success);background:#f0fff4;}
    .field input.invalid{border-color:var(--error);background:#fff5f5;}
    .field input.invalid:focus{box-shadow:0 0 0 3px rgba(229,57,53,0.12);}
    .v-icon{position:absolute;right:12px;top:50%;transform:translateY(-50%);font-size:0.85rem;pointer-events:none;}
    .err-msg{font-size:0.72rem;color:var(--error);display:flex;align-items:center;gap:4px;
      opacity:0;transform:translateY(-4px);transition:opacity 0.25s,transform 0.25s;min-height:16px;}
    .err-msg.show{opacity:1;transform:translateY(0);}

    /* pw toggle */
    .pw-toggle{position:absolute;right:12px;top:50%;transform:translateY(-50%);
      cursor:pointer;font-size:1rem;color:#aaa;transition:color 0.2s;background:none;border:none;}
    .pw-toggle:hover{color:var(--accent);}

    .forgot-link{text-align:right;margin-top:-10px;margin-bottom:16px;}
    .forgot-link a{font-size:0.78rem;color:var(--accent);text-decoration:none;}
    .forgot-link a:hover{text-decoration:underline;}

    /* attempts warning */
    .attempts-bar{
      height:4px;background:#eee;border-radius:2px;margin-bottom:16px;overflow:hidden;display:none;
    }
    .attempts-fill{height:100%;border-radius:2px;background:var(--error);transition:width 0.4s;}

    .submit-btn{
      width:100%;padding:13px;
      background:linear-gradient(135deg,var(--nav-bg),#1a3a5c);
      color:#fff;border:none;border-radius:10px;
      font-family:'Rajdhani',sans-serif;font-weight:700;
      font-size:1.05rem;letter-spacing:2px;text-transform:uppercase;
      cursor:pointer;transition:all 0.3s;
      display:flex;align-items:center;justify-content:center;gap:10px;
    }
    .submit-btn:hover:not(:disabled){background:linear-gradient(135deg,#1a3a5c,var(--accent));transform:translateY(-1px);box-shadow:0 6px 20px rgba(0,188,212,0.3);}
    .submit-btn:disabled{opacity:0.5;cursor:not-allowed;}
    .spinner{width:18px;height:18px;border:3px solid rgba(255,255,255,0.3);border-top-color:#fff;
      border-radius:50%;animation:spin 0.7s linear infinite;display:none;}
    @keyframes spin{to{transform:rotate(360deg);}}

    .divider{display:flex;align-items:center;gap:12px;margin:20px 0;color:#aaa;font-size:0.78rem;}
    .divider::before,.divider::after{content:'';flex:1;height:1px;background:var(--border);}

    .alt-link{text-align:center;font-size:0.83rem;color:#666;}
    .alt-link a{color:var(--accent);font-weight:600;text-decoration:none;}

    /* success overlay */
    .success-overlay{display:none;flex-direction:column;align-items:center;justify-content:center;padding:60px 40px;text-align:center;}
    .success-overlay.show{display:flex;}
    .success-icon{font-size:4rem;margin-bottom:16px;animation:pop 0.4s ease;}
    @keyframes pop{0%{transform:scale(0);}70%{transform:scale(1.2);}100%{transform:scale(1);}}
    .success-overlay h2{font-family:'Teko',sans-serif;font-size:1.8rem;color:var(--nav-bg);}
    .success-overlay p{color:#555;font-size:0.85rem;margin-top:8px;}

    /* AQI alert */
    .aqi-login-alert{
      background:linear-gradient(135deg,#fff3e0,#ffebee);
      border:1px solid #ffcc80;border-radius:10px;
      padding:12px 16px;margin-bottom:16px;
      display:flex;gap:10px;align-items:flex-start;
    }
    .aqi-login-alert .aqi-icon{font-size:1.2rem;flex-shrink:0;}
    .aqi-login-alert p{font-size:0.78rem;color:#bf360c;line-height:1.5;}
    .aqi-login-alert b{color:#b71c1c;}

    .admin-note{
      background:#e8f0fe;border:1px solid #bbdefb;border-radius:8px;
      padding:10px 14px;margin-bottom:14px;font-size:0.75rem;color:#1565c0;
      display:none;
    }
    .admin-note.show{display:block;}
  </style>
</head>
<body>
<nav>
  <div class="nav-logo">Vayu<span>Darpan</span></div>
  <div class="nav-links">
    <a href="index.php">Home</a>
    <a href="catalogue.php">Catalogue</a>
    <a href="public_dashboard.php">Public Dashboard</a>
    <a href="signup.php">Sign Up</a>
    <a href="login.php" class="active">Login</a>
    <a href="logout.php" class="logout">Logout</a>
  </div>
</nav>

<div class="page-wrap">
<div class="login-container">
  <div class="form-header">
    <h1>Welcome <span>Back</span></h1>
    <p>Login to your VayuDarpan account</p>
  </div>

  <!-- SUCCESS OVERLAY -->
  <div class="success-overlay" id="successOverlay">
    <div class="success-icon" id="successIcon">✅</div>
    <h2 id="successTitle">Login Successful!</h2>
    <p id="successMsg">Redirecting to your dashboard…</p>
  </div>

  <div id="formWrap">
    <!-- ROLE TABS -->
    <div class="role-tabs">
      <div class="role-tab active" id="tab-user" onclick="switchTab('user')">
        <span class="tab-icon">👤</span>General User
      </div>
      <div class="role-tab" id="tab-monitor" onclick="switchTab('monitor')">
        <span class="tab-icon">📡</span>Monitoring
      </div>
      <div class="role-tab" id="tab-admin" onclick="switchTab('admin')">
        <span class="tab-icon">🛡️</span>Admin
      </div>
    </div>

    <div class="form-body">
      <!-- AQI Alert (auto-shown) -->
      <div class="aqi-login-alert">
        <div class="aqi-icon">⚠️</div>
        <p><b>Pollution Alert Active:</b> AQI in Delhi is currently 298 (Severe). Login to manage alerts and view real-time station data.</p>
      </div>

      <div class="admin-note" id="adminNote">
        🛡️ Admin login requires your official credentials and department-issued access. Unauthorised attempts are logged.
      </div>

      <div class="alert-banner" id="alertBanner"></div>

      <!-- ATTEMPTS BAR -->
      <div class="attempts-bar" id="attemptsBar">
        <div class="attempts-fill" id="attemptsFill" style="width:0%"></div>
      </div>

      <!-- EMAIL -->
      <div class="field">
        <label>Email Address <span class="req">*</span></label>
        <div class="field-wrap">
          <input type="text" id="email" placeholder="your@email.com" autocomplete="email"/>
          <span class="v-icon" id="vi-email"></span>
        </div>
        <span class="err-msg" id="e-email">⚠ Enter a valid email address</span>
      </div>

      <!-- PASSWORD -->
      <div class="field">
        <label>Password <span class="req">*</span></label>
        <div class="field-wrap">
          <input type="password" id="password" placeholder="Enter your password"/>
          <button class="pw-toggle" type="button" id="pwToggle" onclick="togglePw()" title="Show/hide password">👁</button>
        </div>
        <span class="err-msg" id="e-password">⚠ Password cannot be empty</span>
      </div>

      <div class="forgot-link"><a href="forgot_password.php">Forgot Password?</a></div>

      <!-- CAPTCHA (simple math) -->
      <div class="field" id="captchaField">
        <label>Security Check <span class="req">*</span> — What is <span id="captchaQ" style="color:var(--nav-bg);font-weight:700;"></span>?</label>
        <div class="field-wrap">
          <input type="text" id="captcha" placeholder="Enter answer" maxlength="4"/>
          <span class="v-icon" id="vi-captcha"></span>
        </div>
        <span class="err-msg" id="e-captcha">⚠ Incorrect answer — please try again</span>
      </div>

      <button class="submit-btn" id="submitBtn" onclick="submitLogin()">
        <div class="spinner" id="spinner"></div>
        <span id="btnText">LOGIN</span>
      </button>

      <div class="divider">or</div>
      <div class="alt-link">Don't have an account? <a href="signup.php">Sign up here →</a></div>
    </div>
  </div>
</div>
</div>

<script>
let loginAttempts = 0;
const MAX_ATTEMPTS = 5;
let captchaAnswer = 0;
let currentRole = 'user';

// Generate captcha
function genCaptcha() {
  const a = Math.floor(Math.random()*10)+1;
  const b = Math.floor(Math.random()*10)+1;
  captchaAnswer = a + b;
  document.getElementById('captchaQ').textContent = a + ' + ' + b;
  document.getElementById('captcha').value = '';
  clearState('captcha');
}
genCaptcha();

// Role tabs
function switchTab(role) {
  currentRole = role;
  ['user','monitor','admin'].forEach(r => {
    document.getElementById('tab-'+r).classList.toggle('active', r===role);
  });
  const note = document.getElementById('adminNote');
  note.classList.toggle('show', role==='admin');
}

// Validation helpers
function setValid(id){
  const el=document.getElementById(id); if(!el)return;
  el.classList.add('valid');el.classList.remove('invalid');
  const vi=document.getElementById('vi-'+id);if(vi){vi.textContent='✓';vi.style.color='var(--success)';}
  const err=document.getElementById('e-'+id);if(err)err.classList.remove('show');
}
function setInvalid(id,msg){
  const el=document.getElementById(id);if(!el)return;
  el.classList.add('invalid');el.classList.remove('valid');
  const vi=document.getElementById('vi-'+id);if(vi){vi.textContent='✗';vi.style.color='var(--error)';}
  const err=document.getElementById('e-'+id);if(err){if(msg)err.textContent='⚠ '+msg;err.classList.add('show');}
}
function clearState(id){
  const el=document.getElementById(id);if(!el)return;
  el.classList.remove('valid','invalid');
  const vi=document.getElementById('vi-'+id);if(vi)vi.textContent='';
  const err=document.getElementById('e-'+id);if(err)err.classList.remove('show');
}
function val(id){const el=document.getElementById(id);return el?el.value.trim():'';}

// Live validation
document.getElementById('email').addEventListener('input',function(){
  const v=this.value.trim();
  if(!v){clearState('email');return;}
  /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v)?setValid('email'):setInvalid('email','Enter a valid email address');
});
document.getElementById('password').addEventListener('input',function(){
  this.value.length>0?setValid('password'):clearState('password');
});
document.getElementById('captcha').addEventListener('input',function(){
  const v=this.value.trim();
  if(!v){clearState('captcha');return;}
  parseInt(v)===captchaAnswer?setValid('captcha'):setInvalid('captcha','Incorrect answer');
});

// Password toggle
function togglePw(){
  const inp=document.getElementById('password');
  const btn=document.getElementById('pwToggle');
  if(inp.type==='password'){inp.type='text';btn.textContent='🙈';}
  else{inp.type='password';btn.textContent='👁';}
}

function showAlert(type,msg){
  const b=document.getElementById('alertBanner');
  b.className='alert-banner show '+type;
  b.innerHTML=msg;
  setTimeout(()=>b.classList.remove('show'),5000);
}

function submitLogin(){
  let hasErr=false;
  const email=val('email');
  const pw=val('password');
  const cap=val('captcha');

  if(!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)){
    setInvalid('email','Enter a valid email address'); hasErr=true;
  } else setValid('email');

  if(!pw){
    setInvalid('password','Password cannot be empty'); hasErr=true;
  } else setValid('password');

  if(parseInt(cap)!==captchaAnswer){
    setInvalid('captcha','Incorrect security answer'); hasErr=true;
  } else setValid('captcha');

  if(hasErr) return;

  // Show spinner
  document.getElementById('spinner').style.display='block';
  document.getElementById('btnText').textContent='VERIFYING…';
  document.getElementById('submitBtn').disabled=true;

  setTimeout(()=>{
    loginAttempts++;
    // Demo: accept any valid email/pw
    const success = pw.length >= 6;
    if(success){
      document.getElementById('formWrap').style.display='none';
      const ov=document.getElementById('successOverlay');
      ov.classList.add('show');
      const roleLabel = currentRole==='admin'?'Admin Dashboard':currentRole==='monitor'?'Monitoring Panel':'Dashboard';
      document.getElementById('successTitle').textContent='Login Successful!';
      document.getElementById('successMsg').textContent=`Welcome back! Redirecting to your ${roleLabel}…`;
      setTimeout(()=>{ window.location.href = currentRole==='admin'?'admin_dashboard.php':currentRole==='monitor'?'monitor_panel.php':'index.php'; },2500);
    } else {
      document.getElementById('spinner').style.display='none';
      document.getElementById('btnText').textContent='LOGIN';
      document.getElementById('submitBtn').disabled=false;
      const remaining = MAX_ATTEMPTS - loginAttempts;
      const bar=document.getElementById('attemptsBar');
      bar.style.display='block';
      document.getElementById('attemptsFill').style.width=(loginAttempts/MAX_ATTEMPTS*100)+'%';
      if(loginAttempts>=MAX_ATTEMPTS){
        document.getElementById('submitBtn').disabled=true;
        showAlert('error','🔒 Too many failed attempts. Account temporarily locked. Please try after 15 minutes.');
      } else {
        showAlert('error',`❌ Invalid credentials. ${remaining} attempt${remaining!==1?'s':''} remaining before lockout.`);
      }
      genCaptcha();
    }
  },1800);
}
</script>
</body>
</html>