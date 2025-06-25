<?php
// Start session
session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}
?>
<?php

// Database connection
$severname = "localhost";
$username = "root";
$password = "";
$datbase = "user";

$conn = mysqli_connect($severname, $username, $password, $datbase);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch employee data
$sql = "SELECT * FROM data";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>All Employees</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    body {
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
    }
    .sidebar a:hover {
      background-color: #495057;
      text-decoration: none;
    }
    .sidebar h4 {
      color: #fff;
    }
  </style>
</head>
<body>

<?php require 'partials/_nav.php'; ?>

<div class="container-fluid">
  <div class="row">
    
    <!-- Sidebar -->
    <div class="col-md-3 col-lg-2 p-0">
      <div class="sidebar p-3">
        <h4 class="text-center mb-4">Admin Panel</h4>
        <a href="dashboard.php"><i class="fas fa-home me-2"></i> Dashboard</a>
        <a href="data.php"><i class="fas fa-users me-2"></i> Employees</a>
        <a href="attendence_data.php"><i class="fas fa-calendar-check me-2"></i> Attendance</a>
        <a href="admin_leave_management.php"><i class="fas fa-file-alt me-2"></i> Leave Requests</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a>
      </div>
    </div>

    <!-- Main Content Area -->
    <div class="col-md-9 col-lg-10 mt-4">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Employee Records</h2>
        <a href="welcome.php" class="btn btn-success">
          <i class="fas fa-user-plus me-1"></i> Add New Employee
        </a>
      </div>

      <?php if ($result->num_rows > 0): ?>
      <table class="table table-bordered table-striped">
        <thead class="table-dark">
          <tr>
            <!-- <th>ID</th> -->
            <th>Full Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Department</th>
            <th>Address</th>
            <th>Role</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php while($row = $result->fetch_assoc()): ?>
          <tr>
            <!-- <td><?= $row['id']; ?></td> -->
            <td><?= $row['name']; ?></td>
            <td><?= $row['email']; ?></td>
            <td><?= $row['phone']; ?></td>
            <td><?= $row['dep']; ?></td>
            <td><?= $row['address']; ?></td>
            <td><?= $row['role']; ?></td>
            <td>
              <a href="edit.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
              <a href="delete.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-danger"
                 onclick="return confirm('Are you sure you want to delete this record?');">Delete</a>
            </td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
      <?php else: ?>
        <p class="text-danger">No employee data found.</p>
      <?php endif; ?>
    </div>
  </div>
</div>

</body>
</html>
