<!-- This is signup.php -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>VayuDarpan – Sign Up</title>
  <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@400;500;600;700&family=Noto+Sans:wght@300;400;600&family=Teko:wght@400;500;600&display=swap" rel="stylesheet"/>
  <style>
    :root {
      --nav-bg:#0d2137; --accent:#00bcd4; --good:#00c853;
      --error:#e53935; --success:#00c853; --warn:#ffc107;
      --text:#1a2a3a; --border:#dde4ea; --card:#ffffff;
    }
    *{margin:0;padding:0;box-sizing:border-box;}
    body{
      font-family:'Noto Sans',sans-serif; color:var(--text);
      min-height:100vh;
      background: radial-gradient(ellipse 900px 500px at 15% 20%,rgba(0,188,212,0.08) 0%,transparent 70%),
                  radial-gradient(ellipse 700px 400px at 85% 80%,rgba(0,200,83,0.07) 0%,transparent 60%),
                  linear-gradient(160deg,#e8f4fd 0%,#f5faff 50%,#edf7ee 100%);
    }
    /* NAV */
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

    /* PAGE WRAPPER */
    .page-wrap{
      min-height:calc(100vh - 56px);
      display:flex;align-items:center;justify-content:center;
      padding:40px 20px;
    }
    .signup-container{
      width:100%;max-width:700px;
      background:#fff;border-radius:20px;
      box-shadow:0 8px 48px rgba(13,33,55,0.13);
      overflow:hidden;
      animation:slideUp 0.5s ease;
    }
    @keyframes slideUp{from{opacity:0;transform:translateY(30px);}to{opacity:1;transform:translateY(0);}}

    /* HEADER */
    .form-header{
      background:linear-gradient(135deg,var(--nav-bg) 0%,#1a3a5c 100%);
      padding:32px 40px 24px;
      position:relative;overflow:hidden;
    }
    .form-header::after{
      content:'';position:absolute;right:-40px;top:-40px;
      width:180px;height:180px;border-radius:50%;
      background:rgba(0,188,212,0.1);
    }
    .form-header h1{font-family:'Teko',sans-serif;font-size:2rem;color:#fff;letter-spacing:2px;}
    .form-header h1 span{color:var(--accent);}
    .form-header p{color:#7cb8d4;font-size:0.83rem;margin-top:4px;}

    /* ROLE SELECTOR */
    .role-selector{
      display:grid;grid-template-columns:repeat(3,1fr);gap:12px;
      padding:24px 40px 0;
    }
    .role-card{
      border:2px solid var(--border);border-radius:12px;
      padding:14px 10px;text-align:center;cursor:pointer;
      transition:all 0.25s;background:#f8fbff;
    }
    .role-card:hover{border-color:var(--accent);background:#e0f7fa;}
    .role-card.selected{border-color:var(--accent);background:linear-gradient(135deg,#e0f7fa,#e8f5e9);box-shadow:0 2px 12px rgba(0,188,212,0.2);}
    .role-icon{font-size:1.8rem;margin-bottom:6px;}
    .role-title{font-family:'Rajdhani',sans-serif;font-weight:700;font-size:0.85rem;color:var(--nav-bg);letter-spacing:0.5px;}
    .role-desc{font-size:0.68rem;color:#888;margin-top:3px;line-height:1.4;}
    .role-card input[type=radio]{display:none;}

    /* FORM BODY */
    .form-body{padding:24px 40px 36px;}

    .form-section-title{
      font-family:'Rajdhani',sans-serif;font-weight:700;
      font-size:0.78rem;letter-spacing:2px;color:var(--accent);
      text-transform:uppercase;margin:20px 0 14px;
      display:flex;align-items:center;gap:8px;
    }
    .form-section-title::after{content:'';flex:1;height:1px;background:var(--border);}

    .form-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px;}
    .form-grid.full{grid-template-columns:1fr;}

    /* FIELD */
    .field{display:flex;flex-direction:column;gap:5px;position:relative;}
    .field label{
      font-family:'Rajdhani',sans-serif;font-weight:600;
      font-size:0.8rem;color:#445;letter-spacing:0.5px;
    }
    .field label .req{color:var(--error);margin-left:2px;}
    .field-wrap{position:relative;}
    .field input,.field select,.field textarea{
      width:100%;padding:10px 38px 10px 14px;
      border:2px solid var(--border);border-radius:8px;
      font-size:0.88rem;font-family:'Noto Sans',sans-serif;
      color:var(--text);background:#fff;
      transition:border-color 0.25s,box-shadow 0.25s,background 0.2s;
      outline:none;
    }
    .field textarea{resize:vertical;min-height:80px;padding:10px 14px;}
    .field select{appearance:none;cursor:pointer;}
    .field input:focus,.field select:focus,.field textarea:focus{
      border-color:var(--accent);
      box-shadow:0 0 0 3px rgba(0,188,212,0.12);
      background:#f7fdfe;
    }
    /* validation states */
    .field input.valid,.field select.valid{border-color:var(--success);background:#f0fff4;}
    .field input.invalid,.field select.invalid{border-color:var(--error);background:#fff5f5;}
    .field input.valid:focus,.field select.valid:focus{box-shadow:0 0 0 3px rgba(0,200,83,0.12);}
    .field input.invalid:focus,.field select.invalid:focus{box-shadow:0 0 0 3px rgba(229,57,53,0.12);}

    /* validation icon */
    .v-icon{
      position:absolute;right:12px;top:50%;transform:translateY(-50%);
      font-size:0.85rem;pointer-events:none;transition:opacity 0.2s;
    }

    /* error msg */
    .err-msg{
      font-size:0.72rem;color:var(--error);
      display:flex;align-items:center;gap:4px;
      opacity:0;transform:translateY(-4px);
      transition:opacity 0.25s,transform 0.25s;
      min-height:16px;
    }
    .err-msg.show{opacity:1;transform:translateY(0);}

    /* password strength */
    .pw-strength{margin-top:4px;}
    .pw-bars{display:flex;gap:3px;height:4px;margin-bottom:3px;}
    .pw-bar{flex:1;border-radius:2px;background:#eee;transition:background 0.3s;}
    .pw-label{font-size:0.68rem;color:#888;}

    /* checkbox */
    .check-field{display:flex;align-items:flex-start;gap:10px;margin-top:8px;}
    .check-field input[type=checkbox]{
      width:18px;height:18px;margin-top:2px;cursor:pointer;
      accent-color:var(--accent);flex-shrink:0;
    }
    .check-field label{font-size:0.78rem;color:#555;line-height:1.5;cursor:pointer;}
    .check-field a{color:var(--accent);text-decoration:none;}

    /* extra fields per role — hidden by default */
    .role-fields{display:none;}
    .role-fields.show{display:block;}

    /* SUBMIT */
    .submit-btn{
      width:100%;margin-top:24px;padding:14px;
      background:linear-gradient(135deg,var(--nav-bg),#1a3a5c);
      color:#fff;border:none;border-radius:10px;
      font-family:'Rajdhani',sans-serif;font-weight:700;
      font-size:1.1rem;letter-spacing:2px;text-transform:uppercase;
      cursor:pointer;transition:all 0.3s;
      display:flex;align-items:center;justify-content:center;gap:10px;
    }
    .submit-btn:hover:not(:disabled){background:linear-gradient(135deg,#1a3a5c,var(--accent));transform:translateY(-1px);box-shadow:0 6px 20px rgba(0,188,212,0.3);}
    .submit-btn:disabled{opacity:0.5;cursor:not-allowed;transform:none;}

    /* spinner */
    .spinner{
      width:18px;height:18px;border:3px solid rgba(255,255,255,0.3);
      border-top-color:#fff;border-radius:50%;
      animation:spin 0.7s linear infinite;display:none;
    }
    @keyframes spin{to{transform:rotate(360deg);}}

    /* login link */
    .alt-link{
      text-align:center;margin-top:18px;
      font-size:0.83rem;color:#666;
    }
    .alt-link a{color:var(--accent);font-weight:600;text-decoration:none;}
    .alt-link a:hover{text-decoration:underline;}

    /* alert banner */
    .alert-banner{
      display:none;padding:12px 16px;border-radius:8px;
      margin-bottom:16px;font-size:0.82rem;
      align-items:center;gap:10px;
    }
    .alert-banner.show{display:flex;}
    .alert-banner.error{background:#ffebee;border:1px solid #ef9a9a;color:#b71c1c;}
    .alert-banner.success{background:#e8f5e9;border:1px solid #a5d6a7;color:#1b5e20;}

    /* success overlay */
    .success-overlay{
      display:none;flex-direction:column;align-items:center;justify-content:center;
      padding:60px 40px;text-align:center;
    }
    .success-overlay.show{display:flex;}
    .success-icon{font-size:4rem;margin-bottom:16px;animation:pop 0.4s ease;}
    @keyframes pop{0%{transform:scale(0);}70%{transform:scale(1.2);}100%{transform:scale(1);}}
    .success-overlay h2{font-family:'Teko',sans-serif;font-size:1.8rem;color:var(--nav-bg);}
    .success-overlay p{color:#555;font-size:0.85rem;margin-top:8px;}
  </style>
</head>
<body>
<nav>
  <div class="nav-logo">Vayu<span>Darpan</span></div>
  <div class="nav-links">
    <a href="index.php">Home</a>
    <a href="catalogue.php">Catalogue</a>
    <a href="public_dashboard.php">Public Dashboard</a>
    <a href="signup.php" class="active">Sign Up</a>
    <a href="login.php">Login</a>
    <a href="logout.php" class="logout">Logout</a>
  </div>
</nav>

<div class="page-wrap">
<div class="signup-container">
  <div class="form-header">
    <h1>Create <span>Account</span></h1>
    <p>Join VayuDarpan — Select your account type to get started</p>
  </div>

  <!-- SUCCESS OVERLAY -->
  <div class="success-overlay" id="successOverlay">
    <div class="success-icon">✅</div>
    <h2>Registration Successful!</h2>
    <p id="successMsg">Your account has been created. Redirecting to login…</p>
  </div>

  <!-- FORM -->
  <div id="formWrap">
    <!-- ROLE SELECTOR REMOVED FOR SECURITY (Admin assigned only) -->

    <div class="form-body">
      <div class="alert-banner" id="alertBanner"></div>

      <!-- PERSONAL INFO -->
      <div class="form-section-title">Personal Information</div>
      <div class="form-grid">
        <div class="field">
          <label>First Name <span class="req">*</span></label>
          <div class="field-wrap">
            <input type="text" id="firstName" placeholder="e.g. Rahul" autocomplete="off"/>
            <span class="v-icon" id="vi-firstName"></span>
          </div>
          <span class="err-msg" id="e-firstName">⚠ Please enter your first name (letters only)</span>
        </div>
        <div class="field">
          <label>Last Name <span class="req">*</span></label>
          <div class="field-wrap">
            <input type="text" id="lastName" placeholder="e.g. Sharma" autocomplete="off"/>
            <span class="v-icon" id="vi-lastName"></span>
          </div>
          <span class="err-msg" id="e-lastName">⚠ Please enter your last name (letters only)</span>
        </div>
        <div class="field">
          <label>Date of Birth <span class="req">*</span></label>
          <div class="field-wrap">
            <input type="date" id="dob" max=""/>
            <span class="v-icon" id="vi-dob"></span>
          </div>
          <span class="err-msg" id="e-dob">⚠ Please enter a valid date of birth (must be 18+)</span>
        </div>
        <div class="field">
          <label>Gender <span class="req">*</span></label>
          <div class="field-wrap">
            <select id="gender">
              <option value="">— Select Gender —</option>
              <option>Male</option><option>Female</option><option>Other</option><option>Prefer not to say</option>
            </select>
            <span class="v-icon" id="vi-gender"></span>
          </div>
          <span class="err-msg" id="e-gender">⚠ Please select a gender</span>
        </div>
        <div class="field">
          <label>Mobile Number <span class="req">*</span></label>
          <div class="field-wrap">
            <input type="text" id="mobile" placeholder="10-digit mobile number" maxlength="10"/>
            <span class="v-icon" id="vi-mobile"></span>
          </div>
          <span class="err-msg" id="e-mobile">⚠ Enter a valid 10-digit Indian mobile number</span>
        </div>
        <div class="field">
          <label>City / Location <span class="req">*</span></label>
          <div class="field-wrap">
            <input type="text" id="city" placeholder="e.g. Mumbai"/>
            <span class="v-icon" id="vi-city"></span>
          </div>
          <span class="err-msg" id="e-city">⚠ Please enter your city name</span>
        </div>
      </div>

      <!-- ACCOUNT INFO -->
      <div class="form-section-title">Account Information</div>
      <div class="form-grid full">
        <div class="field">
          <label>Email Address <span class="req">*</span></label>
          <div class="field-wrap">
            <input type="text" id="email" placeholder="example@email.com"/>
            <span class="v-icon" id="vi-email"></span>
          </div>
          <span class="err-msg" id="e-email">⚠ Please enter a valid email address</span>
        </div>
      </div>
      <div class="form-grid">
        <div class="field">
          <label>Password <span class="req">*</span></label>
          <div class="field-wrap">
            <input type="password" id="password" placeholder="Min 8 chars, 1 number, 1 special"/>
            <span class="v-icon" id="vi-password"></span>
          </div>
          <div class="pw-strength">
            <div class="pw-bars"><div class="pw-bar" id="pb1"></div><div class="pw-bar" id="pb2"></div><div class="pw-bar" id="pb3"></div><div class="pw-bar" id="pb4"></div></div>
            <span class="pw-label" id="pwLabel">Enter a password</span>
          </div>
          <span class="err-msg" id="e-password">⚠ Password must be 8+ chars, include a number & special char</span>
        </div>
        <div class="field">
          <label>Confirm Password <span class="req">*</span></label>
          <div class="field-wrap">
            <input type="password" id="confirmPw" placeholder="Re-enter your password"/>
            <span class="v-icon" id="vi-confirmPw"></span>
          </div>
          <span class="err-msg" id="e-confirmPw">⚠ Passwords do not match</span>
        </div>
      </div>



      <!-- TERMS -->
      <div class="form-section-title">Consent</div>
      <div class="check-field">
        <input type="checkbox" id="terms"/>
        <label for="terms">I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a> of VayuDarpan Portal <span class="req">*</span></label>
      </div>
      <span class="err-msg" id="e-terms">⚠ You must accept the terms to register</span>
      <div class="check-field">
        <input type="checkbox" id="alerts"/>
        <label for="alerts">Subscribe to air quality alerts and pollution advisories for my city</label>
      </div>

      <button class="submit-btn" id="submitBtn" onclick="submitForm()" disabled>
        <div class="spinner" id="spinner"></div>
        <span id="btnText">CREATE ACCOUNT</span>
      </button>
      <div class="alt-link">Already have an account? <a href="login.php">Login here →</a></div>
    </div>
  </div>
</div>
</div>

<script>


const today = new Date().toISOString().split('T')[0];
document.getElementById('dob').max = today;

// ══ VALIDATION HELPERS ══
function setValid(id) {
  const el = document.getElementById(id);
  if(!el) return;
  el.classList.add('valid'); el.classList.remove('invalid');
  const vi = document.getElementById('vi-'+id);
  if(vi){vi.textContent='✓'; vi.style.color='var(--success)';}
  const err = document.getElementById('e-'+id);
  if(err) err.classList.remove('show');
}
function setInvalid(id, msg) {
  const el = document.getElementById(id);
  if(!el) return;
  el.classList.add('invalid'); el.classList.remove('valid');
  const vi = document.getElementById('vi-'+id);
  if(vi){vi.textContent='✗'; vi.style.color='var(--error)';}
  const err = document.getElementById('e-'+id);
  if(err){if(msg) err.textContent='⚠ '+msg; err.classList.add('show');}
}
function clearState(id) {
  const el = document.getElementById(id);
  if(!el) return;
  el.classList.remove('valid','invalid');
  const vi = document.getElementById('vi-'+id);
  if(vi) vi.textContent='';
  const err = document.getElementById('e-'+id);
  if(err) err.classList.remove('show');
}
function val(id){ const el=document.getElementById(id); return el?el.value.trim():''; }

// ══ FIELD VALIDATORS ══
const validators = {
  firstName:   v => /^[A-Za-z\s]{2,}$/.test(v),
  lastName:    v => /^[A-Za-z\s]{2,}$/.test(v),
  dob: v => {
    if(!v) return false;
    const d=new Date(v), now=new Date();
    const age = (now-d)/(1000*60*60*24*365.25);
    return age>=18 && d<=now;
  },
  gender:  v => v !== '',
  mobile:  v => /^[6-9]\d{9}$/.test(v),
  city:    v => /^[A-Za-z\s]{2,}$/.test(v),
  email:   v => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v),
  password: v => /^(?=.*[0-9])(?=.*[!@#$%^&*]).{8,}$/.test(v),
  confirmPw: v => v === val('password') && v!=='',
  stationId: v => /^[A-Z0-9\-]{3,}$/.test(v),
  stationName: v => v.length>=3,
  empId: v => /^\d+$/.test(v),
  joinDate: v => v!=='',
  aqiThresh: v => v==='' || (/^\d+$/.test(v) && parseInt(v)>=50 && parseInt(v)<=500),
  stateZone: v => v!=='',
  adminCode: v => v==='ADMIN2025',
  department: v => v!=='',
  designation: v => v.length>=3,
  officialId: v => /^\d+$/.test(v),
};

const errMsgs = {
  firstName:'First name must contain only letters (min 2 chars)',
  lastName:'Last name must contain only letters (min 2 chars)',
  dob:'Enter a valid date of birth. You must be 18 or older.',
  gender:'Please select a gender',
  mobile:'Enter a valid 10-digit mobile number starting with 6–9',
  city:'City name must contain only letters',
  email:'Enter a valid email address (e.g. user@domain.com)',
  password:'Password needs 8+ characters, at least 1 number and 1 special character',
  confirmPw:'Passwords do not match',
  stationId:'Station ID format: uppercase letters, numbers and hyphens (e.g. MH-MUM-001)',
  stationName:'Station name must be at least 3 characters',
  empId:'Employee ID must contain numbers only',
  joinDate:'Please select a valid joining date',
  aqiThresh:'Must be a number between 50 and 500',
  stateZone:'Please select your state',
  adminCode:'Invalid admin code. Contact your system administrator.',
  department:'Please select your department',
  designation:'Designation must be at least 3 characters',
  officialId:'Official ID must contain numbers only',
};

// ══ LIVE VALIDATION ══
const allFields = ['firstName','lastName','dob','gender','mobile','city','email','password','confirmPw'];

allFields.forEach(id => {
  const el = document.getElementById(id);
  if(!el) return;
  el.addEventListener('input', () => { validateField(id); checkSubmitReady(); });
  el.addEventListener('change', () => { validateField(id); checkSubmitReady(); });
  el.addEventListener('blur', () => { validateField(id); });
});

function validateField(id) {
  const v = val(id);
  if(v==='' && !['aqiThresh'].includes(id)) { clearState(id); return false; }
  const ok = validators[id] ? validators[id](v) : true;
  ok ? setValid(id) : setInvalid(id, errMsgs[id]);
  return ok;
}

// ══ PASSWORD STRENGTH ══
document.getElementById('password').addEventListener('input', function() {
  const v = this.value;
  const bars = [document.getElementById('pb1'),document.getElementById('pb2'),document.getElementById('pb3'),document.getElementById('pb4')];
  const lbl = document.getElementById('pwLabel');
  let score=0;
  if(v.length>=8) score++;
  if(/[A-Z]/.test(v)) score++;
  if(/[0-9]/.test(v)) score++;
  if(/[!@#$%^&*]/.test(v)) score++;
  const colors=['#eee','#e53935','#ff6d00','#ffc107','#00c853'];
  const labels=['Enter a password','Weak','Fair','Good','Strong'];
  bars.forEach((b,i) => b.style.background = i<score ? colors[score] : '#eee');
  lbl.textContent=labels[score]; lbl.style.color=colors[score];
});

// ══ TERMS CHECKBOX ══
document.getElementById('terms').addEventListener('change', function() {
  if(this.checked){ setValid('terms'); } else { setInvalid('terms',''); }
  checkSubmitReady();
});

// ══ SUBMIT ENABLE/DISABLE ══
function getRequiredFields() {
  const base = ['firstName','lastName','dob','gender','mobile','city','email','password','confirmPw'];
  return base;
}

function checkSubmitReady() {
  const fields = getRequiredFields();
  const allOk = fields.every(id => validators[id] && validators[id](val(id)));
  const termsOk = document.getElementById('terms').checked;
  document.getElementById('submitBtn').disabled = !(allOk && termsOk);
}

// ══ SUBMIT ══
function submitForm() {
  const fields = getRequiredFields();
  let firstErr = null;
  let hasErr = false;

  fields.forEach(id => {
    const ok = validators[id] && validators[id](val(id));
    if(!ok){ setInvalid(id, errMsgs[id]); if(!firstErr) firstErr=document.getElementById(id); hasErr=true; }
    else setValid(id);
  });

  if(!document.getElementById('terms').checked){
    setInvalid('terms',''); hasErr=true;
    if(!firstErr) firstErr=document.getElementById('terms');
  }

  if(hasErr){
    showAlert('error','⚠️ Please fix the highlighted errors before submitting.');
    firstErr && firstErr.scrollIntoView({behavior:'smooth',block:'center'});
    return;
  }

  // Show spinner, disable button
  document.getElementById('spinner').style.display='block';
  document.getElementById('btnText').textContent='REGISTERING…';
  document.getElementById('submitBtn').disabled=true;

  // Real API call
  const payload = {};
  fields.forEach(id => payload[id] = val(id));

  // The backend now securely maps: name = firstName + lastName
  // We send only what the strict api accepts.
  const securePayload = {
     name: payload.firstName + " " + payload.lastName,
     email: payload.email,
     password: payload.password,
     confirm_password: payload.confirmPw
  };

  fetch('api/auth/register.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(securePayload)
  })
  .then(res => res.json())
  .then(data => {
    if(data.success) {
      document.getElementById('formWrap').style.display='none';
      const ov = document.getElementById('successOverlay');
      ov.classList.add('show');
      document.getElementById('successMsg').textContent =
        `Welcome! Your account has been created. Redirecting to login…`;
      setTimeout(()=>{ window.location.href='login.php'; }, 3000);
    } else {
      document.getElementById('spinner').style.display='none';
      document.getElementById('btnText').textContent='CREATE ACCOUNT';
      document.getElementById('submitBtn').disabled=false;
      showAlert('error', '⚠️ ' + (data.message || 'Registration failed'));
    }
  })
  .catch(err => {
    document.getElementById('spinner').style.display='none';
    document.getElementById('btnText').textContent='CREATE ACCOUNT';
    document.getElementById('submitBtn').disabled=false;
    showAlert('error', '⚠️ Network error occurred. Please try again.');
  });
}

function showAlert(type, msg){
  const b = document.getElementById('alertBanner');
  b.className='alert-banner show '+type;
  b.textContent=msg;
  setTimeout(()=>b.classList.remove('show'),4000);
}
</script>
</body>
</html>
