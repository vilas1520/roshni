<?php
session_start();
include 'partials/db_data.php';

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

$username = $_SESSION["username"] ?? '';

// Fetch all leave requests and join with employee name
$query = "SELECT lr.leave_date, lr.leave_type, lr.reason, lr.status, lr.employee_id, d.name 
          FROM leave_requests lr
          LEFT JOIN data d ON lr.employee_id = d.id
          ORDER BY lr.leave_date DESC";

$leave_result = mysqli_query($conn, $query);
if (!$leave_result) {
    die("Query Failed: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Leave Applications</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <style>
    body {
      background-color: #f8f9fa;
    }
    .sidebar {
      min-height: 100vh;
      background-color: #343a40;
    }
    .sidebar .nav-link {
      color: #fff;
      padding: 10px 20px;
      border-radius: 8px;
    }
    .sidebar .nav-link:hover {
      background-color: #495057;
    }
    .table thead {
      background-color: #198754;
      color: white;
    }
    .content-area {
      padding: 30px;
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
          <a class="nav-link" href="user_dashboard.php"><i class="fas fa-home me-2"></i>Dashboard</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="attendence.php"><i class="fas fa-check-square me-2"></i>Mark Attendance</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="leave_request.php"><i class="fas fa-paper-plane me-2"></i>Apply Leave</a>
        </li>
        <li class="nav-item">
          <a class="nav-link active bg-success" href="leave_data.php"><i class="fas fa-calendar-alt me-2"></i>Leaves Status</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a>
        </li>
      </ul>
    </div>

    <!-- Content Area -->
    <div class="col-md-9 col-lg-10 content-area">
      <h2 class="mb-4 text-center">All Leave Applications</h2>
      <div class="table-responsive">
        <table class="table table-bordered table-striped text-center align-middle">
          <thead>
            <tr>
              <th>Employee ID</th>
              <th>Leave Date</th>
              <th>Leave Type</th>
              <th>Reason</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php if (mysqli_num_rows($leave_result) > 0): ?>
              <?php while ($row = mysqli_fetch_assoc($leave_result)) : ?>
                <tr>
                  <td><?= htmlspecialchars($row['employee_id'] . ' - ' . ($row['name'] ?? "")) ?></td>
                  <td><?= htmlspecialchars($row['leave_date']) ?></td>
                  <td><?= htmlspecialchars($row['leave_type']) ?></td>
                  <td><?= nl2br(htmlspecialchars($row['reason'])) ?></td>
                  <td>
                    <?php
                      $status = strtolower($row['status'] ?? 'pending');
                      $badge_class = 'secondary';
                      if ($status === 'approved') $badge_class = 'success';
                      elseif ($status === 'rejected') $badge_class = 'danger';
                      elseif ($status === 'pending') $badge_class = 'warning text-dark';
                    ?>
                    <span class="badge bg-<?= $badge_class ?>"><?= ucfirst($status) ?></span>
                  </td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr><td colspan="5">No leave requests found.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
