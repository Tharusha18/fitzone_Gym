<?php
// Start the session at the very beginning
session_start();

// Database configuration
$host = "localhost";
$user = "root";
$password = ""; // Default password for XAMPP
$dbname = "fitzone";

// Create database connection
$conn = new mysqli($host, $user, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize messages to prevent undefined variable warnings
$error_message = "";
$success_message = "";

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Determine if the request is for login or registration
    if (isset($_POST['login'])) {
        // **Login Process**
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);

        if (empty($email) || empty($password)) {
            $error_message = "Please fill in all required fields.";
        } else {
            $stmt = $conn->prepare("SELECT user_id, email, password, name, role FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $stmt->bind_result($db_id, $db_email, $db_password, $db_name, $db_role);
                $stmt->fetch();

                if (password_verify($password, $db_password)) {
                    // Set session variables
                    $_SESSION['user_id'] = $db_id;
                    $_SESSION['user_email'] = $db_email;
                    $_SESSION['user_name'] = $db_name;
                    $_SESSION['role'] = $db_role;

                    // Show login success message before redirect
                    $success_message = "Login successful! Redirecting...";

                    // Small delay before redirect so user can see the message
                    echo "<script>
                        setTimeout(function() {
                            window.location.href = '" . ($db_role === 'admin' ? 'admin_dashboard.php' : ($db_role === 'staff' ? 'staff_dashboard.php' : 'customer_dashboard.php')) . "';
                        }, 1500);
                    </script>";
                } else {
                    $error_message = "Invalid email or password.";
                }
            } else {
                $error_message = "Invalid email or password.";
            }
            $stmt->close();
        }
    } elseif (isset($_POST['register'])) {
        // **Registration Process**
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);

        if (empty($name) || empty($email) || empty($password)) {
            $error_message = "Please fill in all required fields.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error_message = "Please enter a valid email address.";
        } else {
            $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $error_message = "An account with this email already exists.";
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $role = 'customer';

                $stmt_insert = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
                $stmt_insert->bind_param("ssss", $name, $email, $hashed_password, $role);

                if ($stmt_insert->execute()) {
                    $success_message = "Registration successful! You can now log in.";
                } else {
                    $error_message = "Registration failed. Please try again later.";
                }
                $stmt_insert->close();
            }
            $stmt->close();
        }
    }
}
$conn->close();
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>FitZone — Login & Register</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Font Awesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

  <style>
    :root{
      --accent-1: #ff7a18;
      --accent-2: #ffb703;
      --glass: rgba(255,255,255,0.08);
      --card-bg: rgba(255,255,255,0.95);
    }

    html,body{
      height:100%;
    }

    body{
      background: linear-gradient(135deg, #0f172a 0%, #071430 50%, #041025 100%);
      font-family: "Inter", system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
      -webkit-font-smoothing:antialiased;
      -moz-osx-font-smoothing:grayscale;
      display:flex;
      align-items:center;
      justify-content:center;
      padding:40px 20px;
      color:#102134;
    }

    .auth-wrap{
      width:100%;
      max-width:1100px;
      border-radius:18px;
      overflow:hidden;
      box-shadow: 0 10px 40px rgba(2,6,23,0.7);
      display:grid;
      grid-template-columns: 1fr 460px;
      background: linear-gradient(180deg, rgba(255,255,255,0.03), rgba(255,255,255,0.02));
    }

    /* Left visual panel */
    .visual-panel{
      padding:40px;
      background:
        linear-gradient(135deg, rgba(255,122,24,0.06), rgba(255,183,3,0.03)),
        url('https://images.unsplash.com/photo-1554284126-aa88f22d8d2c?q=80&w=1600&auto=format&fit=crop&ixlib=rb-4.0.3&s=1d49b9fb2a60fcd8b6b99d1516f64d47');
      background-size:cover;
      background-position:center;
      color: #fff;
      display:flex;
      flex-direction:column;
      justify-content:center;
    }

    .brand{
      display:flex;
      align-items:center;
      gap:12px;
      margin-bottom:24px;
    }

    .brand .logo{
      width:64px;
      height:64px;
      border-radius:14px;
      background:linear-gradient(135deg, var(--accent-1), var(--accent-2));
      display:flex;
      align-items:center;
      justify-content:center;
      font-weight:700;
      box-shadow: 0 6px 20px rgba(255,120,20,0.18);
      color:#fff;
      font-size:22px;
    }

    .visual-panel h2{
      font-size:28px;
      line-height:1.1;
      margin-bottom:12px;
      text-shadow:0 6px 30px rgba(0,0,0,0.4);
    }
    .visual-panel p{
      opacity:0.95;
      max-width:520px;
    }

    /* Right form panel */
    .form-panel{
      background: var(--card-bg);
      padding:28px 32px;
      display:flex;
      align-items:center;
      justify-content:center;
      flex-direction:column;
    }

    .card-ui{
      width:100%;
      max-width:420px;
    }

    .form-header{
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:12px;
      margin-bottom:18px;
    }

    .form-title{
      font-weight:700;
      font-size:20px;
      color:#0b1e2f;
      display:flex;
      align-items:center;
      gap:10px;
    }

    .toggle-tabs{
      display:flex;
      background:transparent;
      padding:6px;
      border-radius:999px;
      gap:6px;
    }

    .toggle-tabs button{
      border:none;
      padding:8px 14px;
      border-radius:999px;
      cursor:pointer;
      font-weight:600;
      color: #6b7280;
      background:transparent;
      transition: all .18s ease;
    }

    .toggle-tabs button.active{
      background: linear-gradient(90deg,var(--accent-1),var(--accent-2));
      color:#fff;
      box-shadow: 0 6px 18px rgba(255,120,20,0.16);
    }

    .form-inner{
      padding:8px 0 0 0;
      width:100%;
    }

    .form-control:focus{
      box-shadow: 0 6px 20px rgba(255,140,0,0.12) !important;
      border-color: var(--accent-1) !important;
    }

    .submit-btn{
      border-radius:10px;
      padding:10px 14px;
      font-weight:700;
      box-shadow: 0 8px 30px rgba(2,6,23,0.08);
    }

    .small-muted{
      font-size:13px;
      color:#6b7280;
    }

    .divider{
      display:flex;
      align-items:center;
      gap:12px;
      margin:16px 0;
    }

    .divider .line{
      flex:1;
      height:1px;
      background:linear-gradient(90deg, rgba(2,6,23,0.06), rgba(2,6,23,0.02));
    }

    .password-toggle{
      cursor:pointer;
      color: #6b7280;
    }

    .alert{
      border-radius:10px;
      box-shadow:none;
    }

    @media (max-width: 980px){
      .auth-wrap{
        grid-template-columns: 1fr;
      }
      .visual-panel{
        display:none;
      }
      body{ padding: 20px; }
    }
  </style>
</head>
<body>
  <div class="auth-wrap">
    <!-- Visual / marketing panel -->
    <div class="visual-panel">
      <div class="brand">
        <div class="logo">FZ</div>
        <div>
          <div style="font-size:18px;font-weight:700">FitZone</div>
          <div style="font-size:12px;opacity:.9">Fitness & Membership Management</div>
        </div>
      </div>

      <h2>Train smarter. Manage easier.</h2>
      <p>Welcome to FitZone — the administrative & member portal. Log in to manage memberships, schedules and track progress. New here? Create your account in seconds.</p>

      <div style="margin-top:24px;display:flex;gap:12px;">
        <div style="background:rgba(255,255,255,0.06);padding:12px;border-radius:10px;">
          <div style="font-weight:700">Secure</div>
          <div style="font-size:13px;opacity:.85">Password hashing & session control</div>
        </div>
        <div style="background:rgba(255,255,255,0.06);padding:12px;border-radius:10px;">
          <div style="font-weight:700">Fast</div>
          <div style="font-size:13px;opacity:.85">Optimized for quick workflows</div>
        </div>
      </div>
    </div>

    <!-- Forms panel -->
    <div class="form-panel">
      <div class="card-ui">

        <div class="form-header">
          <div class="form-title"><i class="fa-solid fa-user-check" style="color:var(--accent-1)"></i> Access Portal</div>

          <div class="toggle-tabs" role="tablist">
            <button id="tab-login" class="active" type="button" onclick="showTab('login')">Login</button>
            <button id="tab-register" type="button" onclick="showTab('register')">Register</button>
          </div>
        </div>

        <!-- server alerts -->
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success" role="alert">
                <?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>

        <div class="form-inner">
          <!-- LOGIN -->
          <div id="login-panel">
            <form id="loginForm" method="POST" action="" novalidate>
              <div class="mb-3">
                <label for="login-email" class="form-label small-muted">Email</label>
                <input type="email" id="login-email" name="email" class="form-control" placeholder="you@domain.com" required>
                <div class="invalid-feedback">Please enter a valid email.</div>
              </div>

              <div class="mb-3 position-relative">
                <label for="login-password" class="form-label small-muted">Password</label>
                <div class="input-group">
                  <input type="password" id="login-password" name="password" class="form-control" placeholder="Your password" required>
                  <span class="input-group-text password-toggle" id="login-pass-toggle" onclick="togglePassword('login-password','login-pass-toggle')">
                    <i class="fa-regular fa-eye"></i>
                  </span>
                  <div class="invalid-feedback">Please enter your password.</div>
                </div>
              </div>

              <input type="hidden" name="login" value="1">
              <div class="d-grid mb-2">
                <button type="submit" class="btn btn-primary submit-btn" style="background:linear-gradient(90deg,var(--accent-1),var(--accent-2));border:none;">
                  <i class="fa-solid fa-right-to-bracket me-2"></i> Login
                </button>
              </div>

            </form>
          </div>

          <!-- REGISTER -->
          <div id="register-panel" style="display:none;">
            <form id="registerForm" method="POST" action="" novalidate>
              <div class="mb-3">
                <label for="register-name" class="form-label small-muted">Full name</label>
                <input type="text" id="register-name" name="name" class="form-control" placeholder="Jane Doe" required>
                <div class="invalid-feedback">Please enter your name.</div>
              </div>

              <div class="mb-3">
                <label for="register-email" class="form-label small-muted">Email</label>
                <input type="email" id="register-email" name="email" class="form-control" placeholder="you@domain.com" required>
                <div class="invalid-feedback">Please enter a valid email.</div>
              </div>

              <div class="mb-3 position-relative">
                <label for="register-password" class="form-label small-muted">Password</label>
                <div class="input-group">
                  <input type="password" id="register-password" name="password" class="form-control" placeholder="Create a password" required minlength="6">
                  <span class="input-group-text password-toggle" id="register-pass-toggle" onclick="togglePassword('register-password','register-pass-toggle')">
                    <i class="fa-regular fa-eye"></i>
                  </span>
                  <div class="invalid-feedback">Password must be at least 6 characters.</div>
                </div>
              </div>

              <input type="hidden" name="register" value="1">
              <div class="d-grid">
                <button type="submit" class="btn btn-success submit-btn" style="background:linear-gradient(90deg,#08a045,#31c48d);border:none;">
                  <i class="fa-solid fa-user-plus me-2"></i> Create account
                </button>
              </div>

              <div class="divider">
                <div class="line"></div>
                <div class="small-muted">Or continue with</div>
                <div class="line"></div>
              </div>
        </div> <!-- form-inner -->
      </div> <!-- card-ui -->

      <div style="margin-top:18px;color:#6b7280;font-size:13px;text-align:center;">
        By continuing you agree to our <a href="#" class="link-primary">Terms</a> & <a href="#" class="link-primary">Privacy</a>.
      </div>

    </div> <!-- form-panel -->
  </div> <!-- auth-wrap -->

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    // Show tab (login/register)
    function showTab(tab) {
      var loginPanel = document.getElementById('login-panel');
      var registerPanel = document.getElementById('register-panel');
      var tabLogin = document.getElementById('tab-login');
      var tabRegister = document.getElementById('tab-register');

      if (tab === 'login') {
        loginPanel.style.display = 'block';
        registerPanel.style.display = 'none';
        tabLogin.classList.add('active');
        tabRegister.classList.remove('active');
      } else {
        loginPanel.style.display = 'none';
        registerPanel.style.display = 'block';
        tabLogin.classList.remove('active');
        tabRegister.classList.add('active');
      }

      // scroll a little for mobile when switching
      window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    // Toggle password visibility
    function togglePassword(inputId, toggleId) {
      var input = document.getElementById(inputId);
      var toggle = document.getElementById(toggleId);
      var icon = toggle.querySelector('i');

      if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
      } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
      }
    }

    // Client-side validation before submit
    (function () {
      'use strict'

      // Bootstrap custom validation
      var forms = document.querySelectorAll('form[novalidate]')
      Array.prototype.slice.call(forms).forEach(function (form) {
        form.addEventListener('submit', function (event) {
          if (!form.checkValidity()) {
            event.preventDefault()
            event.stopPropagation()
          }
          form.classList.add('was-validated')
        }, false)
      })
    })()

    // If server returned an error on register attempt, open register tab
    window.addEventListener('load', function(){
      <?php if (isset($_POST['register']) && $error_message != "") { ?>
        showTab('register');
      <?php } elseif (isset($_POST['login']) && $error_message != "") { ?>
        showTab('login');
      <?php } ?>
    });
  </script>
</body>
</html>
