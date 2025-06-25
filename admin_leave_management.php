<?php
session_start();
include 'partials/db_data.php';

// Check if admin is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Handle approve/reject
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['leave_id'], $_POST['action'])) {
    $leave_id = intval($_POST['leave_id']);
    $action = $_POST['action'];

    // Get leave request details
    $stmt = $conn->prepare("SELECT employee_id, leave_type FROM leave_requests WHERE id = ?");
    $stmt->bind_param("i", $leave_id);
    $stmt->execute();
    $stmt->bind_result($employee_id, $leave_type);
    $stmt->fetch();
    $stmt->close();

    if ($action === 'approve') {
        // 1. Deduct leave from leave_balance
        $update = $conn->prepare("UPDATE leave_balance SET days_available = days_available - 1 WHERE employee_id = ? AND leave_type = ?");
        $update->bind_param("is", $employee_id, $leave_type);
        $update->execute();
        $update->close();

        // 2. Update leave_requests status
        $status = 'Approved';
    } else {
        $status = 'Rejected';
    }

    $stmt = $conn->prepare("UPDATE leave_requests SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $leave_id);
    $stmt->execute();
    $stmt->close();

    header("Location: admin_leave_management.php");
    exit;
}

// Fetch all leave requests (use LEFT JOIN to ensure employee data is fetched even if employee is missing in 'data')
$query = "SELECT lr.id, lr.employee_id, e.name, lr.leave_type, lr.leave_date, lr.reason, lr.status 
          FROM leave_requests lr
          LEFT JOIN data e ON lr.employee_id = e.id
          ORDER BY lr.leave_date DESC";

$result = mysqli_query($conn, $query);

// Error check
if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Admin Leave Management</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    body { background-color: #f8f9fa; }
    .sidebar { background-color: #343a40; min-height: 100vh; padding-top: 20px; }
    .sidebar a { color: #ffffff; display: block; padding: 12px 20px; text-decoration: none; }
    .sidebar a:hover { background-color: #495057; text-decoration: none; }
    .sidebar h4 { color: white; text-align: center; margin-bottom: 30px; }
    .table thead { background-color: #0d6efd; color: white; }
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
    <div class="col-md-9 col-lg-10 py-4">
      <div class="container">
        <h2 class="mb-4 text-center">Admin Leave Management</h2>
        <div class="table-responsive">
          <table class="table table-bordered table-striped align-middle text-center">
            <thead>
              <tr>
                <th>#</th>
                <th>Employee id</th>
                <th>Leave Type</th>
                <th>Leave Date</th>
                <th>Reason</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>  
              <?php
              $count = 1;
              if (mysqli_num_rows($result) > 0):
                while ($row = mysqli_fetch_assoc($result)):
              ?>
                <tr>
                  <td><?= $count++ ?></td>
                  <td><?= htmlspecialchars( ($row['name'] ?? "unknown")) ?></td>
                  <td><?= htmlspecialchars($row['leave_type']) ?></td>
                  <td><?= htmlspecialchars($row['leave_date']) ?></td>  
                  <td style="white-space: pre-wrap;"><?= htmlspecialchars($row['reason']) ?></td>
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
                  <td>
                    <?php if ($status === 'pending'): ?>
                      <form method="post" style="display:inline-block;">
                        <input type="hidden" name="leave_id" value="<?= $row['id'] ?>" />
                        <button type="submit" name="action" value="approve" class="btn btn-sm btn-success" onclick="return confirm('Approve this leave?')">Approve</button>
                      </form>
                      <form method="post" style="display:inline-block;">
                        <input type="hidden" name="leave_id" value="<?= $row['id'] ?>" />
                        <button type="submit" name="action" value="reject" class="btn btn-sm btn-danger" onclick="return confirm('Reject this leave?')">Reject</button>
                      </form>
                    <?php else: ?>
                      <em>No actions</em>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endwhile; else: ?>
                <tr><td colspan="7" class="text-center">No leave requests found.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
