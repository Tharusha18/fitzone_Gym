<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Database connection details
$servername = "localhost"; // Change this if needed
$username = "root";        // Your MySQL username
$password = "";            // Your MySQL password
$dbname = "fitzone";       // Your database name

// Create database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch trainers from the database
$trainers_query = "SELECT name, proficiency, image FROM trainers";
$trainers_result = $conn->query($trainers_query);

// Check if query execution for trainers was successful
if (!$trainers_result) {
    die("Error fetching trainers: " . $conn->error);
}

$trainers = [];
if ($trainers_result->num_rows > 0) {
    while ($row = $trainers_result->fetch_assoc()) {
        $trainers[] = $row;
    }
}

// Fetch membership packages from the database
$memberships_query = "SELECT * FROM memberships";
$memberships_result = $conn->query($memberships_query);

if (!$memberships_result) {
    die("Error fetching memberships: " . $conn->error);
}

$memberships = [];
if ($memberships_result->num_rows > 0) {
    while ($row = $memberships_result->fetch_assoc()) {
        $memberships[] = $row;
    }
}

// Fetch classes from the database
$classes_query = "
    SELECT 
        c.class_id, 
        c.name AS class_name, 
        c.description, 
        c.type 
    FROM 
        classes c
";
$classes_result = $conn->query($classes_query);

if (!$classes_result) {
    die("Error fetching classes: " . $conn->error);
}

$classes = [];
if ($classes_result->num_rows > 0) {
    while ($row = $classes_result->fetch_assoc()) {
        $classes[] = $row;
    }
}

// Close the database connection
$conn->close();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>FitZone Fitness Center</title>

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- AOS for scroll animations -->
  <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">

  <!-- FontAwesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <style>
    /* ---------- THEME COLORS & BASE ---------- */
    :root {
      --deep-1: #071029;      /* deep navy */
      --deep-2: #0f2a44;
      --glass: rgba(255,255,255,0.06);
      --section-bg: #f6f9fb;  /* soft light background */
      --card: #fff;
      --muted: #6b7280;       /* ash */
      --text-dark: #0f1724;
      --accent: #06b6d4;      /* teal/cyan */
      --accent-2: #ff6f61;    /* coral */
      --radius: 14px;
      --shadow-md: 0 12px 30px rgba(2,6,23,0.18);
      --glass-strong: rgba(255,255,255,0.85);
    }
    /* --- Charm Theme Colors --- */
    :root {
        --charm-start: #ff6f91;
        --charm-mid: #ff9671;
        --charm-end: #ffc75f;
        --label-color: #ff6f91;
        --input-bg: #fff;
        --input-border: #ffe0ec;
        --focus-glow: rgba(255, 111, 145, 0.4);
    }

    /* Gradient Text */
    .text-gradient {
        background: linear-gradient(90deg, var(--charm-start), var(--charm-mid), var(--charm-end));
        background-clip: text;
        -webkit-background-clip: text;
        color: transparent;
        -webkit-text-fill-color: transparent;
    }

    /* Form Card */
    .registration-form-wrapper {
        background: #fff;
        border-radius: 18px;
        box-shadow: 0 10px 35px rgba(255, 111, 145, 0.15);
        animation: fadeInUp 0.8s ease forwards;
    }

    /* Labels */
    .form-label {
        color: var(--label-color);
        font-weight: 600;
    }

    /* Inputs & Selects */
    .form-control, .form-select {
        background-color: var(--input-bg);
        border: 2px solid var(--input-border);
        border-radius: 10px;
        padding: 0.75rem 1rem;
        font-size: 1rem;
        transition: all 0.3s ease;
    }
    .form-control:focus, .form-select:focus {
        border-color: var(--charm-start);
        box-shadow: 0 0 8px var(--focus-glow);
    }

    /* Gradient Button */
    .btn-gradient {
        background: linear-gradient(90deg, var(--charm-start), var(--charm-mid), var(--charm-end));
        border: none;
        color: #fff;
        transition: all 0.3s ease;
        border-radius: 12px;
    }
    .btn-gradient:hover {
        background: linear-gradient(90deg, var(--charm-end), var(--charm-mid), var(--charm-start));
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    }

    /* Fade In Animation */
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }

    html,body{
      height:100%;
      margin:0;
      font-family:'Inter',system-ui,-apple-system,Segoe UI,Roboto,Arial;
      background: linear-gradient(135deg,var(--deep-1) 0%, var(--deep-2) 100%);
      color: #fff;
      -webkit-font-smoothing:antialiased;
      -moz-osx-font-smoothing:grayscale;
    }

    a { text-decoration:none; transition: all .2s ease; }
    img { max-width:100%; display:block; border-radius:10px; }

    .container-lg { max-width:1140px; }

    /* ---------- NAV ---------- */
    .navbar {
      background: rgba(6,12,25,0.6);
      border-bottom: 1px solid rgba(255,255,255,0.03);
      backdrop-filter: blur(6px);
      padding: 0.8rem 1rem;
    }
    .navbar-brand { color:var(--accent-2) !important; font-weight:700; }
    .nav-link { color: #dbeafe !important; font-weight:600; margin-left:.35rem; }
    .nav-link:hover { color:var(--accent) !important; }

    /* ---------- HERO ---------- */
    .hero {
      padding:5.5rem 1rem;
      display:grid;
      grid-template-columns:1fr 420px;
      gap:2rem;
      align-items:center;
      background:
        linear-gradient(180deg, rgba(6,12,25,0.15), rgba(6,12,25,0.15)),
        url('https://images.unsplash.com/photo-1579758629938-03607ccdbaba?auto=format&fit=crop&w=1800&q=80') center/cover no-repeat;
      border-bottom: 1px solid rgba(255,255,255,0.03);
    }
    @media (max-width:992px){ .hero{ grid-template-columns:1fr; padding:3.5rem 1rem;} }

    .hero-card {
      background: linear-gradient(180deg, rgba(255,255,255,0.02), rgba(255,255,255,0.01));
      padding:36px;
      border-radius:16px;
      box-shadow:var(--shadow-md);
      border:1px solid rgba(255,255,255,0.03);
    }
    .hero-card h1{
      margin:0 0 .6rem 0;
      font-size: clamp(28px, 4.4vw, 44px);
      line-height:1.02;
      color:var(--glass-strong);
      font-weight:800;
    }
    .hero-card p.lead{
      color: #dbeafe;
      margin-bottom:18px;
      font-weight:500;
    }

    /* primary buttons */
    .btn-cta {
      background: linear-gradient(90deg,var(--accent), #0ea5b3);
      border: none;
      color: #042029;
      font-weight:700;
      padding:.8rem 1.25rem;
      border-radius:12px;
      box-shadow:0 10px 28px rgba(6,182,212,0.12);
    }
    .btn-cta:hover { filter:brightness(.95); transform:translateY(-2px); }

    .btn-ghost {
      background:transparent;
      border:1px solid rgba(255,255,255,0.12);
      color:#e6f7fb;
      padding:.7rem 1.1rem;
      border-radius:12px;
    }
    .btn-ghost:hover { background: rgba(255,255,255,0.04); }

    /* hero right stats card */
    .hero-stats {
      background: rgba(255,255,255,0.06);
      padding:18px;
      border-radius:12px;
      border:1px solid rgba(255,255,255,0.04);
    }
    .hero-stats .stat {
      display:flex; justify-content:space-between; align-items:center;
      padding:8px 0; border-bottom:1px dashed rgba(255,255,255,0.03);
    }
    .hero-stats .stat:last-child{ border-bottom:none; }
    .hero-stats .stat small{ color:#c7ebf0; }
    .hero-stats .stat strong{ color:var(--glass-strong); font-size:1.15rem; }

    <!-- Blog Styles 

.text-gradient {
    background: linear-gradient(90deg, #ff7f50, #ff6f91, #ffc107);
    background-clip: text;
    -webkit-background-clip: text;
    color: transparent;
    -webkit-text-fill-color: transparent;
}

.blog-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.blog-card:hover {
    transform: translateY(-8px);
    box-shadow: 0px 12px 30px rgba(255, 111, 145, 0.3);
}
.blog-img-wrapper {
    overflow: hidden;
}
.blog-img-wrapper img {
    transition: transform 0.4s ease;
}
.blog-card:hover img {
    transform: scale(1.08);
}
.blog-img-wrapper {
  height: 200px; /* Fixed height for all blog images */
  overflow: hidden;
}

.blog-img-wrapper img {
  width: 100%;
  height: 100%;
  object-fit: cover; /* Makes images uniform without distortion */
}

.blog-card {
  display: flex;
  flex-direction: column;
}

.blog-card .p-4 {
  flex-grow: 1;
}


.btn-gradient {
    background: linear-gradient(90deg, #ff7f50, #ff6f91, #ffc107);
    border: none;
    color: #000;
    font-weight: bold;
    padding: 6px 16px;
    border-radius: 25px;
    transition: all 0.3s ease;
}
.btn-gradient:hover {
    opacity: 0.85;
    color: #000;
}

.text-gradient {
    background: linear-gradient(90deg, #001f54, #003f88, #00509e); /* Navy shades */
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text; /* Standard property for Firefox/Edge */
    color: transparent; /* Fallback for unsupported browsers */
    text-shadow: 0 0 8px rgba(0, 80, 158, 0.6), 
                 0 0 15px rgba(0, 63, 136, 0.5); /* Lighting effect */
}


/* Gradient Title Text */
.text-gradient {
    background: linear-gradient(90deg, #ffc107, #ff7f50, #ff6f91);
    background-clip: text;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    color: transparent;
    text-shadow: 0 0 8px rgba(255, 193, 7, 0.4);
}

/* Gradient Text */
.text-gradient {
    background: linear-gradient(90deg, #ffc107, #ff7f50, #ff6f91);
    background-clip: text;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    text-shadow: 0 0 8px rgba(255, 193, 7, 0.4);
}

/* Membership Card Style */
.membership-card {
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(8px);
    border-radius: 15px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.membership-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 30px rgba(255, 193, 7, 0.4);
}

/* Price Bubble */
.price-bubble {
    background: linear-gradient(90deg, #ffc107, #ff7f50, #ff6f91);
    color: #000;
    font-weight: bold;
    padding: 6px 14px;
    border-radius: 25px;
    font-size: 14px;
}

/* Gradient Button */
.btn-gradient {
    background: linear-gradient(90deg, #ffc107, #ff7f50, #ff6f91);
    border: none;
    color: #000;
    font-weight: bold;
    padding: 10px 16px;
    border-radius: 25px;
    transition: all 0.3s ease;
}
.btn-gradient:hover {
    opacity: 0.85;
    color: #000;
}

/* Trainer Card */
.trainer-card {
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(8px);
    border-radius: 15px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.trainer-card:hover {
    transform: translateY(-8px);
    box-shadow: 0px 12px 30px rgba(255, 193, 7, 0.4);
}

/* Trainer Image */
.trainer-img-wrapper {
    width: 130px;
    height: 130px;
    margin: 0 auto;
    overflow: hidden;
    border: 3px solid rgba(255, 193, 7, 0.6);
}
.trainer-img-wrapper img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.4s ease;
}
.trainer-card:hover img {
    transform: scale(1.08);
}

/* Button Gradient */
.btn-gradient {
    background: linear-gradient(90deg, #ffc107, #ff7f50, #ff6f91);
    border: none;
    color: #000;
    font-weight: bold;
    padding: 6px 16px;
    border-radius: 25px;
    transition: all 0.3s ease;
}
.btn-gradient:hover {
    opacity: 0.85;
    color: #000;
}

    /* ---------- SECTIONS ---------- */
    section {
      padding:4.6rem 1rem;
      background: var(--section-bg);
      color: var(--text-dark);
    }
    .section-title {
      display:flex; align-items:center; gap:12px; margin-bottom:20px;
    }
    .section-title h2{ color:var(--accent-2); margin:0; font-weight:800; font-size:1.5rem; }
    .section-sub { color:var(--muted); margin:0; }

/* ---------- CARDS ---------- */
.card-modern {
  background: rgba(255,255,255,0.05);
  backdrop-filter: blur(12px);
  border-radius: 16px;
  padding: 20px;
  box-shadow: 0 12px 28px rgba(0,0,0,0.2);
  transition: transform .25s ease, box-shadow .25s ease;
}
.card-modern:hover {
  transform: translateY(-8px);
  box-shadow: 0 20px 45px rgba(255, 193, 7, 0.3);
}

/* Gradient Title Text */
.text-gradient {
  display: inline-block;
  background: linear-gradient(90deg, #00c6ff, #3aa0ff, #ffc107);
  background-clip: text;
  -webkit-background-clip: text;
  color: transparent;
  -webkit-text-fill-color: transparent;
}

/* Class icon */
.class-ico {
  width: 54px;
  height: 54px;
  border-radius: 12px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #06b6d4, #3aa0ff);
  color: #fff;
  font-size: 20px;
  box-shadow: 0 6px 18px rgba(6, 182, 212, 0.4);
}

/* Class type text */
.class-type {
  color: #e0eaff;
  font-size: 0.9rem;
  font-weight: 500;
}

/* Class description */
.class-desc {
  color: #d6dee9;
  font-size: 0.88rem;
  line-height: 1.5;
}

/* Buttons */
.btn-gradient {
  background: linear-gradient(90deg, #ff7f50, #ff6f91, #ffc107);
  border: none;
  color: #fff;
  transition: all 0.3s ease;
}
.btn-gradient:hover {
  background: linear-gradient(90deg, #ffc107, #ff6f91, #ff7f50);
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(0,0,0,0.25);
}


    /* Modern Card with Glass Effect */
.form-card-modern {
  background: rgba(255, 255, 255, 0.07);
  border-radius: 20px;
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.15);
  box-shadow: 0 8px 40px rgba(0, 0, 0, 0.25);
  animation: fadeInUp 0.8s ease forwards;
}

.form-label {
  font-weight: 600;
  color: #fceaff;
}

.form-control,
.form-select {
  background: rgba(255, 255, 255, 0.1);
  color: #fff;
  border: 1px solid rgba(255, 255, 255, 0.25);
  border-radius: 12px;
}

.form-control:focus,
.form-select:focus {
  box-shadow: 0 0 0 3px rgba(255, 111, 145, 0.4);
}

.small-muted {
  color: rgba(255, 255, 255, 0.65);
  font-size: 0.92rem;
}

/* Gradient Text */
.text-gradient {
  background: linear-gradient(90deg, #ff7f50, #ff6f91, #ffc107, #00e5ff);
  background-clip: text;
  -webkit-background-clip: text;
  color: transparent;
}

/* Time slots */
.timeslots {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  margin-top: 5px;
}

.timeslot {
  padding: 8px 14px;
  border-radius: 12px;
  background: rgba(255, 255, 255, 0.15);
  color: #fff;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
}

.timeslot:hover {
  background: linear-gradient(90deg, #ff7f50, #ff6f91);
  transform: translateY(-3px);
}

.timeslot.active {
  background: linear-gradient(90deg, #ffc107, #ff6f91);
  color: #fff;
  box-shadow: 0 6px 18px rgba(255, 193, 7, 0.4);
}

/* Button Gradient */
.btn-gradient {
  background: linear-gradient(90deg, #ff7f50, #ff6f91, #ffc107);
  border: none;
  color: #fff;
  font-weight: bold;
  border-radius: 25px;
  transition: all 0.3s ease;
}

.btn-gradient:hover {
  opacity: 0.9;
  transform: translateY(-2px);
}

/* Animation */
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(25px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Centered Query Card */
.query-card-modern {
  background: rgba(255, 255, 255, 0.07);
  border-radius: 20px;
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.15);
  box-shadow: 0 8px 40px rgba(0, 0, 0, 0.25);
  animation: fadeInUp 0.8s ease forwards;
  max-width: 600px;
  margin: 0 auto;
}

.query-card-modern .form-label {
  font-weight: 600;
  color: #fceaff;
}

.query-card-modern .form-control {
  background: rgba(255, 255, 255, 0.1);
  color: #fff;
  border: 1px solid rgba(255, 255, 255, 0.25);
  border-radius: 12px;
}

.query-card-modern .form-control::placeholder {
  color: rgba(255, 255, 255, 0.6);
}

.query-card-modern .form-control:focus {
  box-shadow: 0 0 0 3px rgba(255, 111, 145, 0.4);
}

.divider-modern {
  border: none;
  height: 2px;
  background: linear-gradient(90deg, #ff7f50, #ff6f91, #ffc107);
  opacity: 0.7;
}
.query-card-modern {
  margin-bottom: 60px; /* Adjust to your preference */
}


.icon-circle {
  flex: 0 0 50px;
  height: 50px;
  border-radius: 50%;
  background: linear-gradient(135deg, #ff7f50, #ff6f91);
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
}

/* Footer Styles */
.site-footer-modern {
  background: linear-gradient(180deg, rgba(20,20,20,0.9), rgba(10,10,10,0.95));
  backdrop-filter: blur(8px);
  padding: 60px 0 20px;
  color: #fff;
}

.footer-inner {
  display: flex;
  flex-wrap: wrap;
  gap: 40px;
  justify-content: space-between;
}

.footer-col {
  flex: 1 1 250px;
}

.footer-title {
  font-weight: 700;
  margin-bottom: 15px;
  background: linear-gradient(45deg, #ff6f91, #ff9671, #ffc75f);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text; /* Added for Firefox */
  color: transparent; /* Fallback for browsers without background-clip support */
  display: inline-block; /* Ensures background gradient applies correctly */
}

.footer-desc {
  font-size: 0.9rem;
  color: #c0c0c0;
  line-height: 1.6;
}

.footer-links {
  list-style: none;
  padding: 0;
}

.footer-links li {
  margin-bottom: 8px;
}

.footer-links a {
  color: #ccc;
  text-decoration: none;
  transition: color 0.3s;
}

.footer-links a:hover {
  color: #ff6f91;
}

.footer-contact {
  margin-bottom: 12px;
}

.contact-phone {
  font-weight: 700;
  font-size: 1.1rem;
}

.contact-hours {
  font-size: 0.85rem;
  color: #c0c0c0;
}

.footer-social a {
  display: inline-flex;
  justify-content: center;
  align-items: center;
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background: rgba(255,255,255,0.1);
  margin-right: 8px;
  color: #fff;
  font-size: 0.9rem;
  transition: all 0.3s;
}

.footer-social a:hover {
  background: linear-gradient(45deg, #ff6f91, #ff9671);
  transform: translateY(-3px);
}

.footer-bottom {
  text-align: center;
  margin-top: 40px;
  font-size: 0.85rem;
  color: #a5a5a5;
  border-top: 1px solid rgba(255,255,255,0.08);
  padding-top: 15px;
}
.site-footer-modern {
  background-color: #0d1b2a;
  color: #ddd;
  padding: 25px 0; /* reduced from ~50px */
  font-size: 0.9rem;
}

.footer-inner {
  display: flex;
  flex-wrap: wrap;
  justify-content: space-between;
  gap: 25px; /* reduced gap */
}

.footer-col {
  flex: 1 1 200px;
  min-width: 180px;
}

.footer-title {
  font-size: 1.05rem; /* slightly smaller */
  font-weight: 700;
  margin-bottom: 8px;
  background: linear-gradient(45deg, #ff6f91, #ff9671, #ffc75f);
  -webkit-background-clip: text; /* Chrome, Safari */
  background-clip: text;         /* Standard */
  -webkit-text-fill-color: transparent; /* Chrome, Safari */
  color: transparent; /* Fallback for other browsers */
  display: inline-block; /* Required for background-clip:text to work */
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


<!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="#">FitZone Fitness Center</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="#home">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#classes">Classes</a></li>
                    <li class="nav-item"><a class="nav-link" href="#blog">Blog</a></li>
                    <li class="nav-item"><a class="nav-link" href="#trainers">Trainers</a></li>
                    <li class="nav-item"><a class="nav-link" href="#membership">Membership Packages</a></li>
                    <li class="nav-item"><a class="nav-link" href="login.php"><i class="fas fa-user"></i> Login</a></li>
                </ul>
            </div>
        </div>
    </nav>

<!-- HERO -->
<section id="home" class="hero">
  <div class="hero-card" data-aos="fade-up">
    <h1>Train Hard. Live Strong.</h1>
    <p class="lead">Personalized coaching, group classes, and a motivating community. Join FitZone and begin your transformation.</p>
    <div class="d-flex gap-3">
      <a href="#membership" class="btn btn-cta"><i class="fas fa-fire me-2"></i>Join Now</a>
      <a href="#classes" class="btn btn-ghost"><i class="fas fa-dumbbell me-2"></i>Explore Classes</a>
    </div>
  </div>

  <aside class="hero-stats" data-aos="fade-left">
    <?php
      $hero_default = "https://static.vecteezy.com/system/resources/thumbnails/041/406/518/small_2x/ai-generated-empty-gym-filled-with-treadmills-free-photo.jpeg";
      $hero_path = "uploads/hero.jpg";
      $hero_src = file_exists($hero_path) ? $hero_path : $hero_default;
    ?>
    <img src="<?= $hero_src ?>" alt="Hero" style="width:100%; height:230px; object-fit:cover; border-radius:10px; margin-bottom:12px;">
    <div class="stat"><small>Membership Plans</small><strong><?= count($memberships) ?></strong></div>
    <div class="stat"><small>Certified Trainers</small><strong><?= count($trainers) ?></strong></div>
    <div class="stat"><small>Class Types</small><strong><?= count($classes) ?></strong></div>

    <div style="display:flex; gap:10px; margin-top:12px; align-items:center;">
      <div>
        <div class="small-muted">Open Hours</div>
        <div style="font-weight:700; color:#dffdfd">Mon-Sat: 5:00AM - 10:00PM</div>
      </div>
      <div style="margin-left:auto; text-align:right;">
        <div class="small-muted">Contact</div>
        <div style="font-weight:700; color:#dffdfd">+94 74 153 6078</div>
      </div>
    </div>
  </aside>
</section>

<!-- CLASSES -->
<section id="classes" style="background-color: #0f2a44; padding: 60px 0;">
  <div class="container">
    <!-- Section Title -->
    <div class="text-center mb-5" data-aos="fade-up">
      <h2 class="text-gradient fw-bold">Our Classes</h2>
      <p class="text-light">Variety of sessions designed for all fitness levels</p>
    </div>

    <div class="row g-4">
      <?php if (!empty($classes)): ?>
        <?php foreach ($classes as $class): ?>
          <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="40">
            <div class="card-modern class-card h-100">
              <!-- Class Icon -->
              <div class="d-flex align-items-center gap-3 mb-3">
                <div class="class-ico"><i class="fas fa-dumbbell"></i></div>
                <div>
                  <h5 class="mb-1 text-warning"><?= htmlspecialchars($class['class_name']) ?></h5>
                  <small class="class-type"><?= htmlspecialchars($class['type']) ?></small>
                </div>
              </div>
              <!-- Class Description -->
              <p class="class-desc"><?= htmlspecialchars($class['description']) ?></p>
              
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="col-12">
          <div class="card-modern p-4 text-center text-light">
            No classes found at the moment.
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>
</section>

 <!-- FitZone Blog Section -->
<section id="blog" class="py-5" style="background: linear-gradient(135deg, #0d0d0d, #1a1a1a);">
  <div class="container">
    <h1 class="text-center text-uppercase fw-bold mb-4 text-gradient">FitZone Blog</h1>
    <p class="text-center mb-5 text-light">
      Explore workout routines, healthy recipes, and inspiring fitness success stories.
    </p>

    <div class="row g-4">
      <!-- Blog Card -->
      <div class="col-md-6 col-lg-4 d-flex">
        <div class="blog-card shadow-lg rounded-4 overflow-hidden h-100 d-flex flex-column">
          <div class="blog-img-wrapper">
            <img src="data:image/jpeg;base64,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" alt="Workout Routine">
          </div>
          <div class="p-4 bg-dark text-light flex-grow-1">
            <h5 class="fw-bold text-warning">Workout Routine for Beginners</h5>
            <p class="mb-0">Kickstart your fitness journey with a beginner-friendly plan. Build strength and endurance with simple exercises.</p>
          </div>
        </div>
      </div>

      <!-- Blog Card -->
      <div class="col-md-6 col-lg-4 d-flex">
        <div class="blog-card shadow-lg rounded-4 overflow-hidden h-100 d-flex flex-column">
          <div class="blog-img-wrapper">
            <img src="https://images.squarespace-cdn.com/content/v1/61b7d0c4c5a10d1c445590cf/5bf949b8-1d97-4632-b87b-c41a93d10a73/b63e57a941f31cf2654a54746a4fc1e34ad7d45b.jpg" alt="Meal Plan">
          </div>
          <div class="p-4 bg-dark text-light flex-grow-1">
            <h5 class="fw-bold text-warning">Healthy Meal Plan</h5>
            <p class="mb-0">Fuel your workouts with delicious, nutritious meals for muscle gain and fat loss.</p>
          </div>
        </div>
      </div>

      <!-- Blog Card -->
      <div class="col-md-6 col-lg-4 d-flex">
        <div class="blog-card shadow-lg rounded-4 overflow-hidden h-100 d-flex flex-column">
          <div class="blog-img-wrapper">
            <img src="https://flex-web-media-prod.storage.googleapis.com/2024/10/weightlifting-on-3-day-workout.jpg" alt="Strength Training">
          </div>
          <div class="p-4 bg-dark text-light flex-grow-1">
            <h5 class="fw-bold text-warning">Strength Training Basics</h5>
            <p class="mb-0">Master proper lifting form and safe strength-building techniques.</p>
          </div>
        </div>
      </div>

      <!-- Blog Card -->
      <div class="col-md-6 col-lg-4 d-flex">
        <div class="blog-card shadow-lg rounded-4 overflow-hidden h-100 d-flex flex-column">
          <div class="blog-img-wrapper">
            <img src="https://cdn.oxygenmag.com/wp-content/uploads/2013/05/image-placeholder-title-287.jpg?width=730" alt="Success Stories">
          </div>
          <div class="p-4 bg-dark text-light flex-grow-1">
            <h5 class="fw-bold text-warning">Success Stories</h5>
            <p class="mb-0">Be inspired by real transformations from FitZone members.</p>
          </div>
        </div>
      </div>

      <!-- Blog Card -->
      <div class="col-md-6 col-lg-4 d-flex">
        <div class="blog-card shadow-lg rounded-4 overflow-hidden h-100 d-flex flex-column">
          <div class="blog-img-wrapper">
            <img src="https://images.pexels.com/photos/1552249/pexels-photo-1552249.jpeg" alt="Yoga">
          </div>
          <div class="p-4 bg-dark text-light flex-grow-1">
            <h5 class="fw-bold text-warning">Yoga for Recovery</h5>
            <p class="mb-0">Improve flexibility, reduce soreness, and speed up recovery with yoga.</p>
          </div>
        </div>
      </div>

      <!-- Blog Card -->
      <div class="col-md-6 col-lg-4 d-flex">
        <div class="blog-card shadow-lg rounded-4 overflow-hidden h-100 d-flex flex-column">
          <div class="blog-img-wrapper">
            <img src="https://images.pexels.com/photos/3764015/pexels-photo-3764015.jpeg" alt="Motivation">
          </div>
          <div class="p-4 bg-dark text-light flex-grow-1">
            <h5 class="fw-bold text-warning">Stay Motivated</h5>
            <p class="mb-0">Practical tips to keep your motivation high during your fitness journey.</p>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- Trainers Section -->
<section id="trainers" style="background-color: #0f2a44; padding: 60px 0;">
    <div class="container">
        <h2 class="text-center text-gradient mb-5">MEET OUR TRAINERS</h2>
        <div class="row justify-content-center">
            <?php if (!empty($trainers)): ?>
                <?php foreach ($trainers as $trainer): ?>
                    <div class="col-md-4 col-lg-3 mb-4">
                        <div class="trainer-card text-center p-4 rounded shadow-lg">
                            <div class="trainer-img-wrapper rounded-circle mb-3">
                                <img 
                                    src="<?= htmlspecialchars($trainer['image'] ?: 'uploads/trainers/default.jpg') ?>" 
                                    alt="<?= htmlspecialchars($trainer['name']) ?>" 
                                    class="img-fluid"
                                >
                            </div>
                            <h5 class="text-warning"><?= htmlspecialchars($trainer['name']) ?></h5>
                            <p class="text-light small"><?= htmlspecialchars($trainer['proficiency']) ?></p>
                           
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-center text-light">No trainers available at the moment.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

   <!-- MEMBERSHIP SECTION -->
<section id="membership" style="background-color: #0f2a44; padding: 60px 0;">
  <div class="container">
    <!-- Title -->
    <div class="text-center mb-5" data-aos="fade-up">
      <h2 class="text-gradient fw-bold">Membership Packages</h2>
      <p class="text-light">Choose a plan that fits your goals</p>
    </div>
    <!-- Packages -->
    <div class="row g-4">
      <?php if (!empty($memberships)): ?>
        <?php foreach ($memberships as $m): ?>
          <div class="col-md-6 col-lg-4" data-aos="zoom-in" data-aos-delay="60">
            <div class="membership-card h-100 p-4 d-flex flex-column">
              <!-- Title & Price -->
              <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                  <h5 class="text-warning"><?= htmlspecialchars($m['name']) ?></h5>
                  <small class="text-light"><?= htmlspecialchars($m['benefits']) ?></small>
                </div>
                <div class="price-bubble">Rs. <?= number_format($m['price'], 2) ?>/mo</div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="col-12">
          <div class="membership-card p-4 text-center text-light">
            Memberships coming soon.
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>
</section>

<!-- FOOTER -->
<footer class="site-footer-modern">
  <div class="container container-lg">
    <div class="footer-inner">
      <!-- Brand Info -->
      <div class="footer-col">
        <h5 class="footer-title">FitZone Fitness Center</h5>
        <p class="footer-desc">Kurunegala, Sri Lanka — Premier coaching and a vibrant community for your fitness journey.</p>
      </div>

      <!-- Quick Links -->
      <div class="footer-col">
        <h5 class="footer-title">Quick Links</h5>
        <ul class="footer-links">
          <li><a href="#classes">Classes</a></li>
          <li><a href="#trainers">Trainers</a></li>
          <li><a href="#membership">Membership</a></li>
        </ul>
      </div>

      <!-- Contact -->
      <div class="footer-col">
        <h5 class="footer-title">Contact</h5>
        <div class="footer-contact">
          <div class="contact-phone">+94 74 153 6078</div>
          <div class="contact-hours">Mon - Sat: 5:00 AM - 10:00 PM</div>
        </div>
        <div class="footer-social">
          <a href="#" aria-label="facebook"><i class="fab fa-facebook-f"></i></a>
          <a href="#" aria-label="instagram"><i class="fab fa-instagram"></i></a>
          <a href="#" aria-label="twitter"><i class="fab fa-twitter"></i></a>
        </div>
      </div>
    </div>

    <!-- Bottom Bar -->
    <div class="footer-bottom">
      <small>© <?= date('Y') ?> FitZone. All rights reserved.</small>
    </div>
  </div>
</footer>


<!-- Toast (for success messages) -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index:10550">
  <div id="successToast" class="toast align-items-center text-bg-light border-0" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="d-flex">
      <div class="toast-body" id="successToastBody" style="color:#06384a; font-weight:700;"></div>
      <button type="button" class="btn-close btn-close-dark me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
  </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
<script>
  AOS.init({ duration:700, once:true, offset:60 });

  // TIME SLOTS (simple client-side demo)
  const timeslotsEl = document.getElementById('timeslots');
  const times = [
    '06:00 AM','07:00 AM','08:00 AM','09:00 AM','10:00 AM',
    '05:00 PM','06:00 PM','07:00 PM','08:00 PM'
  ];

  function renderTimeslots() {
    timeslotsEl.innerHTML = '';
    times.forEach(t => {
      const btn = document.createElement('button');
      btn.type = 'button';
      btn.className = 'timeslot';
      btn.textContent = t;
      btn.onclick = () => selectTimeslot(btn, t);
      timeslotsEl.appendChild(btn);
    });
  }
  function selectTimeslot(btn, time) {
    document.querySelectorAll('.timeslot').forEach(x=>x.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById('selectedTimeslot').value = time;
  }
  renderTimeslots();

  // FORM: appointment submit (demo client-side)
  const apptForm = document.getElementById('appointmentForm');
  apptForm?.addEventListener('submit', function(e){
    e.preventDefault();

    const name = document.getElementById('apptName').value.trim();
    const phone = document.getElementById('apptPhone').value.trim();
    const date = document.getElementById('apptDate').value;
    const timeslot = document.getElementById('selectedTimeslot').value;
    const classId = document.getElementById('apptClass').value;

    if(!classId){ alert('Please select a class.'); return; }
    if(!date){ alert('Please select a date.'); return; }
    if(!timeslot){ alert('Please pick a time slot.'); return; }
    if(!name || !phone){ alert('Please enter your name and phone.'); return; }

    // For this front-end update we simulate success (you should POST to backend endpoint here)
    showToast('Appointment request sent. We will contact you to confirm!');
    apptForm.reset();
    document.getElementById('selectedTimeslot').value = '';
    document.querySelectorAll('.timeslot').forEach(x=>x.classList.remove('active'));
  });

  // QUERY FORM: small UX improvement (keeps server action as-is)
  const queryForm = document.getElementById('queryForm');
  queryForm?.addEventListener('submit', function(e){
    // allow normal POST to submit_query.php (keeps backend)
    // but show client-side validation
    const subject = this.subject.value.trim();
    const message = this.message.value.trim();
    if(!subject || !message){ e.preventDefault(); alert('Please fill subject and message'); return false; }
    // let the browser submit the form to your existing submit_query.php
  });

  // Password toggle if still present (keeps previous behavior)
  const toggle = document.getElementById('togglePw');
  if(toggle){
    toggle.addEventListener('click', function(){
      const pw = document.getElementById('password');
      if(pw){
        if(pw.type === 'password'){ pw.type = 'text'; this.innerHTML = '<i class="fa-regular fa-eye-slash"></i>'; }
        else { pw.type = 'password'; this.innerHTML = '<i class="fa-regular fa-eye"></i>'; }
      }
    });
  }

  // Toast helper
  function showToast(message){
    const body = document.getElementById('successToastBody');
    body.textContent = message;
    const toastEl = document.getElementById('successToast');
    const bsToast = new bootstrap.Toast(toastEl, { delay: 3500 });
    bsToast.show();
  }

  // small helper used on class "View Details" button
  function viewDetails(name){
    showToast(name + ' — details will be shown in the full app (demo).');
  }

  // Smooth anchor scroll fallback
  document.querySelectorAll('a[href^="#"]').forEach(a=>{
    a.addEventListener('click', function(e){
      const target = document.querySelector(this.getAttribute('href'));
      if(target){ e.preventDefault(); target.scrollIntoView({behavior:'smooth', block:'start'}); }
    });
  });
</script>

<script>
    // Example smooth scroll after registration
    document.getElementById('membershipForm').addEventListener('submit', function (e) {
        // You can add validation or success popup here
    });
</script>
<!-- Smooth scroll effect for blog links -->
<script>
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener("click", function(e) {
        e.preventDefault();
        document.querySelector(this.getAttribute("href")).scrollIntoView({
            behavior: "smooth"
        });
    });
});
</script>

<!-- Optional Hover Effect JS -->
<script>
document.querySelectorAll('.trainer-card').forEach(card => {
    card.addEventListener('mouseenter', () => {
        card.style.boxShadow = "0px 15px 35px rgba(255, 193, 7, 0.6)";
    });
    card.addEventListener('mouseleave', () => {
        card.style.boxShadow = "";
    });
});
</script>
<!-- JS Validation + Messages -->
<script>
document.getElementById("membershipForm").addEventListener("submit", function(event) {
    event.preventDefault();
    
    let form = event.target;
    let formMessages = document.getElementById("formMessages");

    if (!form.checkValidity()) {
        formMessages.style.display = "block";
        formMessages.innerHTML = `<div class="alert alert-danger">⚠ Please fill out all required fields correctly.</div>`;
        return;
    }

    // Simulate successful registration (AJAX or PHP processing can go here)
    setTimeout(() => {
        formMessages.style.display = "block";
        formMessages.innerHTML = `<div class="alert alert-success">✅ Registration successful! Welcome to FitZone.</div>`;
        form.reset();
    }, 500);
});
</script>

<script>
// Fade-in & smooth animation for membership cards
document.addEventListener("DOMContentLoaded", () => {
    const cards = document.querySelectorAll(".membership-card");

    const revealOnScroll = () => {
        cards.forEach(card => {
            const rect = card.getBoundingClientRect();
            if (rect.top < window.innerHeight - 50) {
                card.style.opacity = 1;
                card.style.transform = "translateY(0)";
            }
        });
    };

    // Initial hidden state
    cards.forEach(card => {
        card.style.opacity = 0;
        card.style.transform = "translateY(30px)";
        card.style.transition = "all 0.6s ease";
    });

    // Scroll listener
    window.addEventListener("scroll", revealOnScroll);

    // Run once on load
    revealOnScroll();
});
</script>


</body>
</html>
