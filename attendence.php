<?php
session_start();
date_default_timezone_set('Asia/Kolkata'); // Set timezone to Kolkata

include 'partials/db_data.php';

// Check login
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

$username = $_SESSION["username"] ?? '';
$today = date('Y-m-d');

// Get today's attendance
$result = mysqli_query($conn, "SELECT * FROM attendence WHERE name = '$username' AND date = '$today'");
$attendance = mysqli_fetch_assoc($result);

// Handle In-Time + Present
if (isset($_POST["mark_in"])) {
    $status = $_POST["status"] ?? 'Present';
    $in_time = date('H:i:s');

    $insert = mysqli_query($conn, "INSERT INTO attendence (name, status, date, in_time) VALUES ('$username', '$status', '$today', '$in_time')");

    if ($insert) {
        echo "<script>alert('In-Time marked successfully'); window.location='attendence.php';</script>";
        exit;
    }
}

// Handle Out-Time
if (isset($_POST["mark_out"])) {
    $out_time = date('H:i:s');

    $update = mysqli_query($conn, "UPDATE attendence SET out_time = '$out_time' WHERE name = '$username' AND date = '$today'");

    if ($update) {
        echo "<script>alert('Out-Time marked successfully'); window.location='attendence.php';</script>";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Attendance</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    body {
      background-color: #f4f6f9;
    }
    .sidebar {
      min-height: 100vh;
      background-color: #343a40;
      color: white;
    }
    .sidebar .nav-link {
      color: white;
      padding: 10px 15px;
      border-radius: 8px;
      margin-bottom: 5px;
      display: block;
      transition: background 0.3s;
    }
    .sidebar .nav-link:hover,
    .sidebar .nav-link.active {
      background-color: #495057;
    }
    .card {
      margin-top: 50px;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
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

<div class="container-fluid">
  <div class="row">
    <!-- Sidebar -->
    <div class="col-md-3 col-lg-2 sidebar p-3">
      <h5 class="text-center mb-4">User Menu</h5>
      <ul class="nav flex-column">
        <li class="nav-item">
          <a class="nav-link" href="user_dashboard.php"><i class="fas fa-home me-2"></i>Dashboard</a>
        </li>
        <li class="nav-item">
          <a class="nav-link active bg-success" href="attendence.php"><i class="fas fa-check-square me-2"></i>Mark Attendance</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="leave_request.php"><i class="fas fa-paper-plane me-2"></i>Apply Leave</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="leave_data.php"><i class="fas fa-calendar-alt me-2"></i>Leaves Status</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a>
        </li>
      </ul>
    </div>

    <!-- Main content -->
    <div class="col-md-9 col-lg-10">
      <div class="card mx-auto" style="max-width: 550px;">
        <h3 class="text-center mb-4">Attendance - <?= date('d M Y') ?></h3>
        <h5 class="text-center text-primary">Hello, <?= htmlspecialchars($username); ?> 👋</h5>

        <!-- Show current time -->
        <p class="text-center mt-2">
          <span id="current-time" class="text-success fw-bold"></span>
        </p>

        <!-- If no record, show Present + In-Time box -->
        <?php if (!$attendance): ?>
          <form method="post">
            <div class="mb-3 mt-4 text-center">
              <label class="form-label fw-bold">Mark Your Attendance:</label><br>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="status" id="present" value="Present" checked>
                <label class="form-check-label" for="present">Present</label>
              </div>
            </div>
            <button name="mark_in" class="btn btn-success w-100">Submit In-Time</button>
          </form>

        <?php elseif (!empty($attendance['status']) && empty($attendance['out_time'])): ?>
          <div class="mt-3 text-center">
            <p><strong>Status:</strong> <?= htmlspecialchars($attendance['status']); ?></p>
            <p><strong>In-Time:</strong> <?= $attendance['in_time'] ?? 'N/A'; ?></p>
          </div>
          <form method="post">
            <button name="mark_out" class="btn btn-primary w-100">Submit Out-Time</button>
          </form>

        <?php else: ?>
          <div class="alert alert-success text-center">✅ Attendance Completed!</div>
          <p><strong>Status:</strong> <?= htmlspecialchars($attendance['status']); ?></p>
          <p><strong>In-Time:</strong> <?= $attendance['in_time'] ?? 'N/A'; ?></p>
          <p><strong>Out-Time:</strong> <?= $attendance['out_time'] ?? 'N/A'; ?></p>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>

<!-- Live Kolkata Time Script -->
<script>
function updateKolkataTime() {
  const options = {
    timeZone: "Asia/Kolkata",
    hour: "2-digit",
    minute: "2-digit",
    second: "2-digit",
    hour12: true
  };
  const formatter = new Intl.DateTimeFormat('en-IN', options);
  document.getElementById('current-time').innerText = formatter.format(new Date());
}
setInterval(updateKolkataTime, 1000);
updateKolkataTime();
</script>

</body>
</html>
