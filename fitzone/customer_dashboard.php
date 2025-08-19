<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "fitzone";

// Create database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Start session to get the logged-in user's ID
session_start();
if (!isset($_SESSION['user_id'])) {
    // For a registration page, you might want to remove this check or handle it differently.
    // For now, we assume a user must be logged in to see the dashboard.
    // die("User not logged in. Please log in to view your dashboard.");
}
$user_id = $_SESSION['user_id'] ?? 0; // Use null coalescing to avoid errors if not set

// Fetch trainers
$trainers_result = $conn->query("SELECT trainer_id, name, proficiency, image FROM trainers");
$trainers = $trainers_result ? $trainers_result->fetch_all(MYSQLI_ASSOC) : [];

// Fetch memberships
$memberships_result = $conn->query("SELECT * FROM memberships");
$memberships = $memberships_result ? $memberships_result->fetch_all(MYSQLI_ASSOC) : [];

// Fetch classes
$classes_result = $conn->query("SELECT class_id, name AS class_name, description, type FROM classes");
$classes = $classes_result ? $classes_result->fetch_all(MYSQLI_ASSOC) : [];

// Fetch user queries
if ($user_id > 0) {
    $stmt = $conn->prepare("SELECT id, subject, message, status, created_at FROM query WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $queries = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    $stmt->close();
} else {
    $queries = [];
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>FitZone Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<style>
/* = Background = */
body {
    font-family: 'Poppins', sans-serif;
    background: url('https://www.shutterstock.com/image-photo/barbell-fitness-training-gym-sports-600nw-2139742761.jpg') no-repeat center center/cover;
    color: #fff;
    scroll-behavior: smooth;
    position: relative;
    overflow-x: hidden;
}
body::before {
    content: "";
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.55);
    backdrop-filter: blur(3px);
    z-index: -1;
}
/* Navbar */
.navbar {
    background: rgba(30,30,50,0.85);
    backdrop-filter: blur(10px);
    transition: 0.3s;
}
.navbar.scrolled {
    background: rgba(30,30,50,1);
}
.navbar-brand {
    font-weight: 700;
    font-size: 1.6rem;
    background: linear-gradient(90deg, #ff758c, #ff7eb3, #ffcc70);
    -webkit-background-clip: text;
    -moz-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
    -moz-text-fill-color: transparent;
    color: transparent;
}
.nav-link {
    color: #fff !important;
    font-weight: 500;
    transition: 0.3s;
}
.nav-link:hover {
    color: #ffcc70 !important;
}
/* = Section Cards = */
.section-card {
    background: rgba(255,255,255,0.08);
    border-radius: 20px;
    padding: 25px;
    max-width: 700px;
    margin: 50px auto;
    box-shadow: 0 8px 25px rgba(0,0,0,0.3);
    transform: translateY(30px);
    opacity: 0;
    transition: all 0.8s ease;
}
.section-card.show {
    transform: translateY(0);
    opacity: 1;
}
.section-card h2 {
    font-weight: 800;
    font-size: 1.5rem;
    margin-bottom: 20px;
    text-align: center;
    background: linear-gradient(270deg, #ff758c, #ff7eb3, #ffcc70);
    background-size: 600% 600%;
    -webkit-background-clip: text;
    -moz-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
    -moz-text-fill-color: transparent;
    color: transparent;
    animation: gradientAnimation 8s ease infinite;
}
@keyframes gradientAnimation {
    0%{background-position:0% 50%;}
    50%{background-position:100% 50%;}
    100%{background-position:0% 50%;}
}
/* = Forms = */
.form-floating > .form-control {
    border-radius: 10px;
    padding: 20px 12px 8px 12px;
    background: rgba(255,255,255,0.1);
    font-size: 0.95rem;
    border: 1px solid rgba(255,255,255,0.2);
    color: #000000ff; 
    transition: 0.3s;
}
.form-floating > .form-control:focus {
    border-color: #ffcc70;
    box-shadow: 0 0 10px rgba(255,204,112,0.4);
    background: rgba(255,255,255,0.15);
}
.form-floating > label {
    color: #ffffffff;
    font-weight: 500;
}
.form-floating > .form-control:focus ~ label,
.form-floating > .form-control:not(:placeholder-shown) ~ label {
    color: #ff758c;
    font-weight: 600;
}
/* = Buttons = */
.btn-custom {
    background: linear-gradient(90deg, #ff758c, #ffcc70);
    border: none;
    border-radius: 10px;
    padding: 10px 16px;
    font-weight: 600;
    color: #fff;
    transition: 0.3s;
}
.btn-custom:hover {
    background: linear-gradient(90deg, #ffcc70, #ff758c);
    transform: translateY(-2px);
    box-shadow: 0 0 15px rgba(255,204,112,0.5);
}
/* Compact Footer Version */
.site-footer-modern {
  background-color: #0d1b2a;
  color: #ddd;
  padding: 25px 0;
  font-size: 0.9rem;
}
.footer-inner {
  display: flex;
  flex-wrap: wrap;
  justify-content: space-between;
  gap: 25px;
}
.footer-col {
  flex: 1 1 200px;
  min-width: 180px;
}
.footer-title {
  font-size: 1.05rem;
  font-weight: 700;
  margin-bottom: 8px;
  background: linear-gradient(45deg, #ff6f91, #ff9671, #ffc75f);
  -webkit-background-clip: text;
  -moz-background-clip: text;
  background-clip: text;
  -webkit-text-fill-color: transparent;
  -moz-text-fill-color: transparent;
  color: transparent; 
  display: inline-block; 
}
.footer-desc {
  font-size: 0.85rem;
  line-height: 1.4;
  margin-bottom: 8px;
}
.footer-links {
  list-style: none;
  padding: 0;
  margin: 0;
}
.footer-links li {
  margin-bottom: 4px;
}
.footer-links a {
  color: #bbb;
  text-decoration: none;
  font-size: 0.85rem;
}
.footer-links a:hover {
  color: #ff9671;
}
.footer-contact {
  font-size: 0.85rem;
  margin-bottom: 8px;
}
.footer-social a {
  color: #bbb;
  margin-right: 8px;
  font-size: 1rem;
}
.footer-social a:hover {
  color: #ff9671;
}
.footer-bottom {
  text-align: center;
  font-size: 0.8rem;
  color: #aaa;
  margin-top: 15px;
  padding-top: 10px;
  border-top: 1px solid rgba(255, 255, 255, 0.05);
}
</style>
</head>
<body>
<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark fixed-top">
<div class="container">
<a class="navbar-brand" href="#">FitZone</a>
<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
<span class="navbar-toggler-icon"></span>
</button>
<div class="collapse navbar-collapse" id="navMenu">
<ul class="navbar-nav me-auto">
<li class="nav-item"><a class="nav-link" href="#membership">Membership</a></li>
<li class="nav-item"><a class="nav-link" href="#appointments">Appointments</a></li>
<li class="nav-item"><a class="nav-link" href="#query">Query</a></li>
</ul>
<a href="logout.php" class="btn btn-sm btn-outline-light"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>
</div>
</nav>

<div class="container" style="margin-top:100px;">
<!-- Membership Section -->
<div id="membership" class="section-card">
<h2>Register for Membership</h2>
<div id="membershipMsg"></div> <!-- Message container -->
<form id="membershipForm" class="row g-3">
<div class="col-md-6 form-floating">
  <input type="text" name="name" class="form-control" placeholder="Full Name" required>
  <label>Full Name</label>
</div>
<div class="col-md-6 form-floating">
  <input type="text" name="address" class="form-control" placeholder="Address" required>
  <label>Address</label>
</div>
<div class="col-md-4 form-floating">
  <input type="number" name="age" class="form-control" placeholder="Age" required>
  <label>Age</label>
</div>
<div class="col-md-4 form-floating">
  <select name="gender" class="form-control" required>
    <option value="" disabled selected>Select Gender</option>
    <option value="Male">Male</option>
    <option value="Female">Female</option>
    <option value="Other">Other</option>
  </select>
  <label>Gender</label>
</div>
<div class="col-md-4 form-floating">
  <input type="tel" name="phone" class="form-control" placeholder="Phone Number" required>
  <label>Phone Number</label>
</div>
<div class="col-md-6 form-floating">
  <input type="email" name="email" class="form-control" placeholder="Email Address" required>
  <label>Email Address</label>
</div>
<div class="col-md-6 form-floating">
  <input type="password" name="password" class="form-control" placeholder="Password" required>
  <label>Password</label>
</div>
<div class="col-md-12 form-floating">
  <select name="package" class="form-control" required>
    <option value="" disabled selected>Choose Package</option>
    <?php foreach ($memberships as $m): ?>
      <option value="<?= htmlspecialchars($m['name']) ?>"><?= htmlspecialchars($m['name']) ?> - Rs. <?= number_format($m['price'], 2) ?>/month</option>
    <?php endforeach; ?>
  </select>
  <label>Choose Package</label>
</div>
<div class="col-12"><button type="submit" class="btn btn-custom w-100">Register</button></div>
</form>
</div>

<!-- Appointments Section -->
<div id="appointments" class="section-card">
<h2>Book Appointment</h2>
<div id="appointmentMsg"></div> <!-- Message container -->
<form id="appointmentForm" class="row g-3">
<div class="col-md-6 form-floating">
  <select name="class_id" class="form-control" required>
    <option value="" disabled selected>Select Class</option>
    <?php foreach($classes as $c): ?>
      <option value="<?= $c['class_id'] ?>"><?= htmlspecialchars($c['class_name']) ?></option>
    <?php endforeach; ?>
  </select>
  <label>Select Class</label>
</div>
<div class="col-md-6 form-floating">
  <select name="trainer_id" class="form-control" required>
    <option value="" disabled selected>Select Trainer</option>
    <?php foreach($trainers as $t): ?>
      <option value="<?= $t['trainer_id'] ?>"><?= htmlspecialchars($t['name']) ?></option>
    <?php endforeach; ?>
  </select>
  <label>Select Trainer</label>
</div>
<div class="col-md-12 form-floating">
  <input type="datetime-local" name="appointment_date" class="form-control" required>
  <label>Appointment Date & Time</label>
</div>
<div class="col-12"><button type="submit" class="btn btn-custom w-100">Book Appointment</button></div>
</form>
</div>

<!-- Query Section -->
<div id="query" class="section-card">
<h2>Submit Your Query</h2>
<div id="queryMsg"></div> <!-- Message container -->
<form id="queryForm" class="row g-3">
<div class="col-12 form-floating">
  <input type="text" name="subject" class="form-control" placeholder="Subject" required>
  <label>Subject</label>
</div>
<div class="col-12 form-floating">
  <textarea name="message" rows="3" class="form-control" placeholder="Message" style="height: 100px" required></textarea>
  <label>Message</label>
</div>
<div class="col-12"><button type="submit" class="btn btn-custom w-100">Submit Query</button></div>
</form>
</div>
</div>
<!-- FOOTER -->
<footer class="site-footer-modern">
  <div class="container container-lg">
    <div class="footer-inner">
      <div class="footer-col">
        <h5 class="footer-title">FitZone Fitness Center</h5>
        <p class="footer-desc">Kurunegala, Sri Lanka — Premier coaching and a vibrant community for your fitness journey.</p>
      </div>
      <div class="footer-col">
        <h5 class="footer-title">Quick Links</h5>
        <ul class="footer-links">
          <li><a href="#membership">Member Registration</a></li>
          <li><a href="#appointments">Book Appointment</a></li>
          <li><a href="#query">Submit Query</a></li>
        </ul>
      </div>
      <div class="footer-col">
        <h5 class="footer-title">Contact</h5>
        <div class="footer-contact">
          <div class="contact-phone">+94 77 123 4567</div>
          <div class="contact-hours">Mon - Sat: 5:00 AM - 10:00 PM</div>
        </div>
        <div class="footer-social">
          <a href="#" aria-label="facebook"><i class="fab fa-facebook-f"></i></a>
          <a href="#" aria-label="instagram"><i class="fab fa-instagram"></i></a>
          <a href="#" aria-label="twitter"><i class="fab fa-twitter"></i></a>
        </div>
      </div>
    </div>
    <div class="footer-bottom">
      <small>© <?= date('Y') ?> FitZone. All rights reserved.</small>
    </div>
  </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Scroll effect for cards
    const sections = document.querySelectorAll('.section-card');
    const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if(entry.isIntersecting){
                entry.target.classList.add('show');
            }
        });
    },{threshold:0.2});
    sections.forEach(section => observer.observe(section));

    // Navbar scroll background
    window.addEventListener('scroll',function(){
        document.querySelector('.navbar').classList.toggle('scrolled',window.scrollY > 50);
    });

    // Function to show messages under forms
    function showMessage(element, type, message){
        // Replace \n with <br> for HTML display
        const formattedMessage = message.replace(/\n/g, '<br>');
        element.innerHTML = `<div class="alert alert-${type} alert-dismissible fade show mt-3" role="alert">
            ${formattedMessage}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>`;
    }

    // Membership Registration Form
    const membershipForm = document.getElementById('membershipForm');
    const membershipMsgDiv = document.getElementById('membershipMsg');
    if(membershipForm){
        membershipForm.addEventListener('submit', function(e){
            e.preventDefault();
            fetch('register.php', { 
                method: 'POST', 
                body: new FormData(this) 
            })
            .then(res => res.json())
            .then(data => {
                if(data.success){
                    showMessage(membershipMsgDiv, 'success', data.success);
                    membershipForm.reset();
                } else {
                    showMessage(membershipMsgDiv, 'danger', data.error || 'Registration failed. Please check your input.');
                }
            })
            .catch(()=> showMessage(membershipMsgDiv, 'danger', 'An error occurred. Could not connect to the server.'));
        });
    }

    // Appointment Booking Form
    const appointmentForm = document.getElementById('appointmentForm');
    const appointmentMsgDiv = document.getElementById('appointmentMsg');
    if(appointmentForm){
        appointmentForm.addEventListener('submit', function(e){
            e.preventDefault();
            fetch('book_appointment.php', { method: 'POST', body: new FormData(this) })
            .then(res => res.json())
            .then(data => {
                if(data.success){
                    showMessage(appointmentMsgDiv,'success', data.success);
                    appointmentForm.reset();
                } else {
                    showMessage(appointmentMsgDiv,'danger', data.error || 'Booking failed.');
                }
            })
            .catch(()=> showMessage(appointmentMsgDiv,'danger','Error submitting appointment.'));
        });
    }

    // Query Form
    const queryForm = document.getElementById('queryForm');
    const queryMsgDiv = document.getElementById('queryMsg');
    if(queryForm){
        queryForm.addEventListener('submit', function(e){
            e.preventDefault();
            fetch('submit_query.php', { method: 'POST', body: new FormData(this) })
            .then(res => res.json())
            .then(data => {
                if(data.success){
                    showMessage(queryMsgDiv,'success', data.success);
                    queryForm.reset();
                } else {
                    showMessage(queryMsgDiv,'danger', data.error || 'Query submission failed.');
                }
            })
            .catch(()=> showMessage(queryMsgDiv,'danger','Error submitting query.'));
        });
    }
});
</script>
</body>
</html>
