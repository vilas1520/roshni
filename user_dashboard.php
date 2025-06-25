<?php
session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}
require_once 'partials/db_data.php';

$username = $_SESSION["username"] ?? '';

// Get today's date
$today = date('Y-m-d');

// Check if user is marked present today
$presentToday = false;
$sql = "SELECT * FROM attendence WHERE name = ? AND date = ? AND status = 'Present'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $username, $today);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $presentToday = true;
}

// Count pending leave requests
$pendingLeaves = 0;
$sql_pending = "SELECT COUNT(*) as pending_count FROM leaves WHERE name = ? AND status = 'Pending'";
$stmt_pending = $conn->prepare($sql_pending);
if (!$stmt_pending) {
    die("SQL Error (Pending Leaves): " . $conn->error);
}
$stmt_pending->bind_param("s", $username);
$stmt_pending->execute();
$result_pending = $stmt_pending->get_result();
if ($result_pending && $row = $result_pending->fetch_assoc()) {
    $pendingLeaves = $row['pending_count'];
}

$totalLeavesAllowed = 12; // or pull from user profile if it's user-specific
$leavesUsed = 0;
$sql_approved = "SELECT COUNT(*) AS approved_count FROM leaves WHERE name = ? AND status = 'Approved'";
$stmt_approved = $conn->prepare($sql_approved);
if (!$stmt_approved) {
    die("SQL Error (Approved Leaves): " . $conn->error);
}
$stmt_approved->bind_param("s", $username);
$stmt_approved->execute();
$result_approved = $stmt_approved->get_result();
if ($result_approved && $row = $result_approved->fetch_assoc()) {
    $leavesUsed = $row['approved_count'];
}
$remainingLeaves = max(0, $totalLeavesAllowed - $leavesUsed);

$rejectedLeaves = 0;
$sql_rejected = "SELECT COUNT(*) AS rejected_count FROM leaves WHERE name = ? AND status = 'Rejected'";
$stmt_rejected = $conn->prepare($sql_rejected);
if (!$stmt_rejected) {
    die("SQL Error (Rejected Leaves): " . $conn->error);
}
$stmt_rejected->bind_param("s", $username);
$stmt_rejected->execute();
$result_rejected = $stmt_rejected->get_result();
if ($result_rejected && $row = $result_rejected->fetch_assoc()) {
    $rejectedLeaves = $row['rejected_count'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #f8f9fa;
    }
    .sidebar {
      min-height: 100vh;
      background-color: #343a40;
      color: #fff;
    }
    .sidebar a {
      color: #fff;
      display: block;
      padding: 12px 20px;
      text-decoration: none;
      transition: background 0.3s ease;
    }
    .sidebar a:hover {
      background-color: #495057;
    }
    .dashboard-card {
      border-radius: 16px;
      box-shadow: 0 6px 20px rgba(0,0,0,0.1);
      transition: transform 0.3s ease;
    }
    .dashboard-card:hover {
      transform: scale(1.03);
    }
    .navbar {
      box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    h2 {
      font-weight: 600;
      color: #343a40;
    }
    .card-title {
      font-size: 1.2rem;
    }
  </style>
</head>
<body>

<!-- Top Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">EMS</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link active" href="#"><i class="fas fa-user me-1"></i> <?= htmlspecialchars($username) ?></a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt me-1"></i>Logout</a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<!-- Main Layout -->
<div class="container-fluid">
  <div class="row">

    <!-- Sidebar -->
    <div class="col-md-3 col-lg-2 sidebar p-3">
      <h5 class="text-center mb-4">User Menu</h5>
      <a href="user_dashboard.php"><i class="fas fa-home me-2"></i> Dashboard</a>
      <a href="attendence.php"><i class="fas fa-check-square me-2"></i> Mark Attendance</a>
      <a href="leave_request.php"><i class="fas fa-paper-plane me-2"></i> Apply Leave</a>
      <a href="leave_data.php"><i class="fas fa-calendar-alt me-2"></i> Leaves Status</a>
      <a href="logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a>
    </div>

    <!-- Main Content -->
    <div class="col-md-9 col-lg-10 p-4">
      <h2 class="mb-4">Welcome, <?= htmlspecialchars($username) ?></h2>

      <!-- Dashboard Cards -->
      <div class="row g-4">
        <div class="col-md-6 col-xl-3">
          <div class="bg-success text-white p-4 dashboard-card">
            <h5><i class="fas fa-calendar-check me-2"></i>Present Today</h5>
            <h3><?php echo $presentToday ? 'Yes' : 'No'; ?></h3>
          </div>
        </div>
        <div class="col-md-6 col-xl-3">
          <div class="bg-warning text-dark p-4 dashboard-card">
            <h5><i class="fas fa-file-alt me-2"></i>Remaining Leaves</h5>
            <h3><?= $remainingLeaves ?></h3>
          </div>
        </div>
        <div class="col-md-6 col-xl-3">
          <div class="bg-info text-dark p-4 dashboard-card">
            <h5><i class="fas fa-envelope-open-text me-2"></i>Pending Requests</h5>
            <h3><?= $pendingLeaves ?></h3>
          </div>
        </div>
        <div class="col-md-6 col-xl-3">
          <div class="bg-danger text-white p-4 dashboard-card">
            <h5><i class="fas fa-ban me-2"></i>Rejected Leaves</h5>
            <h3><?= $rejectedLeaves ?></h3>
          </div>
        </div>
      </div>
      <!-- Stylish Leave Summary -->
<div class="mt-5">
  <h4 class="mb-4">📋 Leave Summary</h4>
  <div class="row g-4">
    <div class="col-md-4">
      <div class="p-4 rounded shadow text-white" style="background: linear-gradient(135deg, #1abc9c, #16a085);">
        <div class="d-flex align-items-center">
          <i class="fas fa-calendar-day fa-2x me-3"></i>
          <div>
            <h6 class="mb-1">Total Leaves Allowed</h6>
            <h3 class="mb-0"><?= $totalLeavesAllowed ?></h3>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="p-4 rounded shadow text-white" style="background: linear-gradient(135deg, #3498db, #2980b9);">
        <div class="d-flex align-items-center">
          <i class="fas fa-check-circle fa-2x me-3"></i>
          <div>
            <h6 class="mb-1">Approved Leaves</h6>
            <h3 class="mb-0"><?= $leavesUsed ?></h3>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="p-4 rounded shadow text-white" style="background: linear-gradient(135deg, #f39c12, #e67e22);">
        <div class="d-flex align-items-center">
          <i class="fas fa-hourglass-half fa-2x me-3"></i>
          <div>
            <h6 class="mb-1">Remaining Leaves</h6>
            <h3 class="mb-0"><?= $remainingLeaves ?></h3>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>


    </div>
  </div>
  
</div>

<!-- JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
