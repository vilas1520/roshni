<?php
session_start();
include 'partials/db_data.php';

// Redirect if not logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Fetch attendance records
$query = "SELECT * FROM attendance ORDER BY date DESC, name ASC";
$result = mysqli_query($conn, $query);
if (!$result) {
    die("Query Failed: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Attendance Records</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    body {
      background-color: #f8f9fa;
    }
    .sidebar {
      min-height: 100vh;
      background-color: #343a40;
      padding-top: 20px;
    }
    .sidebar a {
      color: #ffffff;
      display: block;
      padding: 10px 20px;
      text-decoration: none;
    }
    .sidebar a:hover {
      background-color: #495057;
    }
    .sidebar h4 {
      color: white;
      text-align: center;
      margin-bottom: 30px;
    }
    .main-content {
      padding: 40px;
    }
    .table thead {
      background-color: #198754;
      color: white;
    }
  </style>
</head>
<body>

<?php require 'partials/_nav.php'; ?> 

<div class="container-fluid">
  <div class="row">
    <!-- Sidebar -->
    <div class="col-md-3 col-lg-2 sidebar">
      <h4>Admin Panel</h4>
      <a href="dashboard.php"><i class="fas fa-home me-2"></i> Dashboard</a>
      <a href="data.php"><i class="fas fa-users me-2"></i> Employees</a>
      <a href="attendence_data.php"><i class="fas fa-calendar-check me-2"></i> Attendance</a>
      <a href="admin_leave_management.php"><i class="fas fa-file-alt me-2"></i> Leave Requests</a>
      <a href="logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a>
    </div>

    <!-- Main Content -->
    <div class="col-md-9 col-lg-10 main-content">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="flex-grow-1 text-center">Employee Attendance Records</h2>
        <div>
          <a href="add_atendence.php" class="btn btn-primary me-2">+ Add Attendance</a>
          <a href="edit_attendance_all.php" class="btn btn-warning">Edit Attendance</a>
        </div>
      </div>

      <div class="table-responsive">
        <table class="table table-bordered table-striped text-center">
          <thead>
            <tr>
              <th>Name</th>
              <th>Status</th>
              <th>Date</th>
              <th>In-Time</th>
              <th>Out-Time</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)) : ?>
              <tr>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['status']) ?></td>
                <td><?= htmlspecialchars($row['date']) ?></td>
                <td><?= $row['in_time'] ?? '—' ?></td>
                <td><?= $row['out_time'] ?? '—' ?></td>
                <td>
                  <a href="edit_attendance_all.php?name=<?= urlencode($row['name']) ?>&date=<?= urlencode($row['date']) ?>" class="btn btn-sm btn-warning">
                    <i class="fas fa-edit"></i> Edit
                  </a>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
