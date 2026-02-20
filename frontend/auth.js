document.addEventListener('DOMContentLoaded', () => {

    // --- 1. UI Toggle Logic ---
    const loginBtn = document.getElementById('btn-login');
    const registerBtn = document.getElementById('btn-register');
    const loginForm = document.getElementById('login-form');
    const registerForm = document.getElementById('register-form');

    function switchTab(isLogin) {
        if (isLogin) {
            loginForm.classList.add('active-form');
            loginForm.classList.remove('hidden-form');
            registerForm.classList.remove('active-form');
            registerForm.classList.add('hidden-form');
            loginBtn.classList.add('active');
            registerBtn.classList.remove('active');
        } else {
            registerForm.classList.add('active-form');
            registerForm.classList.remove('hidden-form');
            loginForm.classList.remove('active-form');
            loginForm.classList.add('hidden-form');
            registerBtn.classList.add('active');
            loginBtn.classList.remove('active');
        }
    }

    loginBtn.addEventListener('click', () => switchTab(true));
    registerBtn.addEventListener('click', () => switchTab(false));

    // --- 2. Validation Config (Regex) ---
    const patterns = {
        email: /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/,
        phone: /^[0-9]{10}$/,
        // Min 8 chars, 1 upper, 1 lower, 1 number, 1 special
        password: /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/,
        station: /^ST-\d{3,4}$/ // Example: ST-405
    };

    // --- 3. Helper Functions ---
    function showError(input, message) {
        const group = input.parentElement;
        const msgElement = group.querySelector('.error-msg');
        input.classList.add('error');
        input.classList.remove('success');
        msgElement.innerText = message;
    }

    function showSuccess(input) {
        const group = input.parentElement;
        const msgElement = group.querySelector('.error-msg');
        input.classList.remove('error');
        input.classList.add('success');
        msgElement.innerText = "";
    }

    // --- 4. Main Validation Logic (Task 6) ---
    function validateField(input) {
        const name = input.name;
        const value = input.value.trim();

        // Check Empty
        if (value === "") {
            showError(input, "This field is required");
            return false;
        }

        // Specific Checks
        if (name === "email" && !patterns.email.test(value)) {
            showError(input, "Invalid email format (e.g. admin@gov.in)");
            return false;
        }

        if (name === "phone" && !patterns.phone.test(value)) {
            showError(input, "Phone must be 10 digits");
            return false;
        }

        if (name === "stationid" && !patterns.station.test(value)) {
            showError(input, "Format: ST-XXX (e.g., ST-405)");
            return false;
        }

        if (name === "password") {
            // Strength Meter Logic
            const strengthFill = document.getElementById('strength-fill');
            if (patterns.password.test(value)) {
                strengthFill.style.width = "100%";
                strengthFill.style.backgroundColor = "#388e3c"; // Green
            } else if (value.length > 5) {
                strengthFill.style.width = "60%";
                strengthFill.style.backgroundColor = "orange";
                showError(input, "Password weak: Need Upper, Lower, Number & Symbol");
                return false;
            } else {
                strengthFill.style.width = "30%";
                strengthFill.style.backgroundColor = "red";
                showError(input, "Password too short");
                return false;
            }
        }

        if (name === "confirm") {
            const passVal = document.getElementById('reg-pass').value;
            if (value !== passVal) {
                showError(input, "Passwords do not match");
                return false;
            }
        }

        showSuccess(input);
        return true;
    }

    // --- 5. Event Listeners (Tasks 5 & 10) ---
    const inputs = document.querySelectorAll('input');

    inputs.forEach(input => {
        // Real-time validation
        input.addEventListener('keyup', () => validateField(input));
        input.addEventListener('blur', () => validateField(input));
    });

    // Prevent Paste on Confirm Password (Task 10)
    document.getElementById('reg-confirm').addEventListener('paste', e => {
        e.preventDefault();
        alert("For security, please type the password manually.");
    });

    // --- 6. Form Submission & Connecting to Index.html ---

    // Login Handling
    document.getElementById('login-form').addEventListener('submit', (e) => {
        e.preventDefault();
        // In a real app, you would check backend here.
        // For Exp 4, we assume valid inputs mean success.
        const email = document.getElementById('login-email');
        const pass = document.getElementById('login-pass');

        if (validateField(email) && pass.value.length > 0) {
            // Redirect to the Dashboard you created earlier
            window.location.href = "index.html";
        }
    });

    // Register Handling
    document.getElementById('register-form').addEventListener('submit', (e) => {
        e.preventDefault();
        let isValid = true;

        // Validate all fields in register form
        const regInputs = document.querySelectorAll('#register-form input');
        regInputs.forEach(input => {
            if (!validateField(input)) isValid = false;
        });

        if (isValid) {
            alert("Station Registered Successfully! Logging you in...");
            window.location.href = "index.html";
        }
    });
});