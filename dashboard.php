<?php
session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "user");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$today = date('Y-m-d');

// Total employees
$empCount = $conn->query("SELECT COUNT(*) AS total FROM data")->fetch_assoc()['total'] ?? 0;

// Today Present
$presentCount = $conn->query("SELECT COUNT(*) AS total_present FROM attendence WHERE date = '$today' AND status = 'Present'")->fetch_assoc()['total_present'] ?? 0;

// Pending Leaves
$pendingLeaves = $conn->query("SELECT COUNT(*) AS pending_count FROM leaves WHERE status = 'Pending'")->fetch_assoc()['pending_count'] ?? 0;

// Attendance by Department
$deptLabels = $presentCounts = $absentCounts = [];
$res = $conn->query("SELECT DISTINCT department FROM data");

while ($res && $row = $res->fetch_assoc()) {
    $dept = $row['department'];

    $present = $conn->query("
        SELECT COUNT(*) AS count 
        FROM attendence a 
        JOIN data d ON a.name = d.name 
        WHERE a.date = '$today' AND a.status = 'Present' AND d.department = '$dept'
    ")->fetch_assoc()['count'] ?? 0;

    $absent = $conn->query("
        SELECT COUNT(*) AS count 
        FROM data d 
        WHERE d.department = '$dept' AND d.name NOT IN (
            SELECT name FROM attendence WHERE date = '$today'
        )
    ")->fetch_assoc()['count'] ?? 0;

    $deptLabels[] = $dept;
    $presentCounts[] = $present;
    $absentCounts[] = $absent;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    body { background-color: #f8f9fa; font-family: 'Segoe UI', sans-serif; }
    .sidebar { min-height: 100vh; background-color: #343a40; color: #fff; }
    .sidebar a { color: #fff; padding: 12px 20px; display: block; text-decoration: none; }
    .sidebar a:hover { background-color: #495057; }
    .card:hover { transform: scale(1.02); transition: 0.3s; }
  </style>   

  
</head>
<body>

<?php require 'partials/_nav.php'; ?>
<div class="container-fluid">
  <div class="row">

    <!-- Sidebar -->
    <div class="col-md-3 col-lg-2 sidebar p-3">
      <h4 class="text-center mb-4">Admin Panel</h4>
      <a href="dashboard.php"><i class="fas fa-home me-2"></i> Dashboard</a>
      <a href="data.php"><i class="fas fa-users me-2"></i> Employees</a>
      <a href="attendence_data.php"><i class="fas fa-calendar-check me-2"></i> Attendance</a>
      <a href="admin_leave_management.php"><i class="fas fa-file-alt me-2"></i> Leave Requests</a>
      <a href="logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a>


    </div>

    <!-- Main Content -->
    <div class="col-md-9 col-lg-10 p-4">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Dashboard</h2>
        <p class="mb-0"><strong>Date & Time:</strong> <span id="date-time" class="text-primary"></span></p>
      </div>

      <!-- Dashboard Cards -->
      <div class="row g-4">
        <div class="col-md-4">
          <div class="card bg-primary text-white shadow-sm p-4">
            <h5><i class="fas fa-users me-2"></i>Total Employees</h5>
            <h2><?= $empCount ?></h2>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card bg-success text-white shadow-sm p-4">
            <h5><i class="fas fa-calendar-check me-2"></i>Today Present</h5>
            <h2><?= $presentCount ?></h2>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card bg-warning text-dark shadow-sm p-4">
            <h5><i class="fas fa-file-alt me-2"></i>Pending Leaves</h5>
            <h2><?= $pendingLeaves ?></h2>
          </div>
        </div>
      </div>

      <!-- Department-wise Attendance Chart -->
      <div class="mt-5">
        <h5 class="mb-3">Department-wise Attendance (Today)</h5>
        <div style="max-width: 700px; margin: auto;">
          <canvas id="deptAttendanceChart" height="300"></canvas>
        </div>
      </div>

    </div>
  </div>
</div>

<!-- JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  const deptLabels = <?= json_encode($deptLabels) ?>;
  const presentCounts = <?= json_encode($presentCounts) ?>;
  const absentCounts = <?= json_encode($absentCounts) ?>;

  new Chart(document.getElementById('deptAttendanceChart'), {
    type: 'bar',
    data: {
      labels: deptLabels,
      datasets: [
        { label: 'Present', data: presentCounts, backgroundColor: '#198754' },
        { label: 'Absent', data: absentCounts, backgroundColor: '#dc3545' }
      ]
    },
    options: {
      responsive: true,
      plugins: {
        title: { display: true, text: "Today's Attendance by Department" },
        legend: { position: 'top' }
      },
      scales: {
        x: { stacked: true },
        y: { beginAtZero: true, stacked: true }
      }
    }
  });

  // Time Updater
  function updateTime() {
    document.getElementById('date-time').textContent = new Date().toLocaleString();
  }
  setInterval(updateTime, 1000);
  updateTime();
</script>

</body>
</html>
