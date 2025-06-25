<?php
session_start();
include 'partials/db_data.php';

// Check if user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

$username = $_SESSION["username"] ?? '';

// Check if employee exists
$emp_exists = false;
$result = mysqli_query($conn, "SELECT * FROM user WHERE username = '$username'");
if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}
if (mysqli_num_rows($result) > 0) {
    $emp_exists = true;
}

if (!$emp_exists) {
    echo "<h3>You are not authorized to apply for leave.</h3>";
    exit;
}

$error = "";
$success = "";

// Current year for validation
$currentYear = date('Y');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $from_date = $_POST["from_date"];
    $to_date = $_POST["to_date"];
    $reason = mysqli_real_escape_string($conn, $_POST["reason"]);

    if (!$from_date || !$to_date) {
        $error = "Please select both From Date and To Date.";
    } elseif (strtotime($from_date) > strtotime($to_date)) {
        $error = "'From Date' cannot be after 'To Date'.";
    } elseif (date('Y', strtotime($from_date)) != $currentYear || date('Y', strtotime($to_date)) != $currentYear) {
        $error = "Leave dates must be within the current year ($currentYear).";
    } else {
        $insert = mysqli_query($conn, "INSERT INTO leaves (name, from_date, to_date, reason, status) VALUES ('$username', '$from_date', '$to_date', '$reason', 'Pending')");
        if ($insert) {
            $success = "Leave application submitted successfully!";
        } else {
            $error = "Error submitting leave: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Leave Application</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <style>
    body {
      background-color: #e9ecef; /* Light gray background */
      font-family: 'Arial', sans-serif;
    }
    .navbar {
      background-color: #546e7a !important; /* A softer dark color */
    }
    .navbar-brand {
      font-weight: bold;
    }
    .sidebar {
      background-color: #37474f; /* Darker sidebar */
      color: #fff;
    }
    .sidebar .nav-link {
      color: #fff;
      padding: 12px 20px;
      border-radius: 10px;
      transition: background-color 0.3s ease;
    }
    .sidebar .nav-link:hover {
      background-color: #455a64;
    }
    .card {
      border-radius: 12px;
      box-shadow: 0 6px 12px rgba(0,0,0,0.15);
      border: none;
    }
    .card-header {
      background-color: #607d8b; /* A complementary color for header */
      color: #fff;
      font-weight: bold;
      padding: 15px;
      border-radius: 12px 12px 0 0;
    }
    .form-label {
      font-weight: 500;
      color: #343a40;
    }
    .form-control, .btn {
      border-radius: 8px;
      padding: 10px;
    }
    .btn-primary {
      background-color: #546e7a;
      border-color: #546e7a;
      transition: background-color 0.3s ease;
    }
    .btn-primary:hover {
      background-color: #455a64;
      border-color: #455a64;
    }
    .alert {
      border-radius: 8px;
      margin-bottom: 15px;
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
      <h5 class="text-white text-center mb-4">User Menu</h5>
      <ul class="nav flex-column">
        <li class="nav-item">
          <a class="nav-link text-white" href="user_dashboard.php"><i class="fas fa-home me-2"></i>Dashboard</a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-white" href="attendence.php"><i class="fas fa-check-square me-2"></i>Mark Attendance</a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-white active" href="leave.php"><i class="fas fa-paper-plane me-2"></i>Apply Leave</a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-white" href="leave_data.php"><i class="fas fa-calendar-alt me-2"></i>Leaves Status</a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-white" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a>
        </li>
      </ul>
    </div>

    <!-- Main Content -->
    <div class="col-md-9 col-lg-10 d-flex justify-content-center align-items-center" style="min-height: 90vh;">
      <div class="card p-4 w-100" style="max-width: 600px;">
        <div class="card-header text-center">
          Leave Application
        </div>
        <div class="card-body">
          <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
          <?php elseif ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
          <?php endif; ?>

          <form method="POST" action="leave.php" onsubmit="return validateDates()">
            <div class="mb-3">
              <label for="from_date" class="form-label">From Date</label>
              <input type="date" class="form-control" id="from_date" name="from_date" required min="<?= $currentYear ?>-01-01" max="<?= $currentYear ?>-12-31">
            </div>

            <div class="mb-3">
              <label for="to_date" class="form-label">To Date</label>
              <input type="date" class="form-control" id="to_date" name="to_date" required min="<?= $currentYear ?>-01-01" max="<?= $currentYear ?>-12-31">
            </div>

            <div class="mb-3">
              <label for="reason" class="form-label">Type of Leave</label>
              <select class="form-control" id="reason" name="reason" required>
                <option value="">Select Leave Type</option>
                <option value="Sick Leave">Sick Leave</option>
                <option value="Casual Leave">Casual Leave</option>
                <option value="Annual Leave">Annual Leave</option>
                <option value="Other">Other</option>
              </select>
            </div>

            <div class="text-center">
              <button type="submit" class="btn btn-primary px-4">Apply Leave</button>
            </div>
          </form>
        </div>
      </div>
    </div>

  </div>
</div>

<script>
function validateDates() {
  const fromDate = document.getElementById('from_date').value;
  const toDate = document.getElementById('to_date').value;
  const currentYear = new Date().getFullYear();

  if (!fromDate || !toDate) {
    alert("Please select both From Date and To Date.");
    return false;
  }

  const from = new Date(fromDate);
  const to = new Date(toDate);

  if (from > to) {
    alert("❌ 'From Date' cannot be after 'To Date'.");
    return false;
  }

  if (from.getFullYear() !== currentYear || to.getFullYear() !== currentYear) {
    alert(`❌ Leave dates must be within the current year (${currentYear}).`);
    return false;
  }

  return true;
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
