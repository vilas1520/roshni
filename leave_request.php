<?php
// Start session and check if user is logged in
session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

include 'partials/db_data.php'; // Your DB connection file

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $employee_id = $_SESSION["id"]; // Assuming you store employee ID in session after login
    $leave_type = $_POST["leave_type"];
    $leave_date = $_POST["leave_date"];
    $reason = $_POST["reason"];

    $sql = "INSERT INTO leave_requests (employee_id, leave_type, leave_date, reason) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("isss", $employee_id, $leave_type, $leave_date, $reason);
        if ($stmt->execute()) {
            echo "<script>alert('Leave request submitted successfully');</script>";
        } else {
            echo "<script>alert('Error submitting leave request');</script>";
        }
    } else {
        echo "Prepare failed: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Leave Request</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .sidebar {
            background-color: #343a40;
            min-height: 100vh;
            color: white;
            padding-top: 20px;
        }
        .sidebar .nav-link {
            color: white;
            padding: 10px 20px;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background-color: #198754;
            color: white;
        }
        .sidebar h5 {
            text-align: center;
            margin-bottom: 20px;
            color: #ffc107;
        }
    </style>
</head>
<body>
<?php include 'partials/_nav.php'; ?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2 sidebar">
            <h5>User Menu</h5>
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
                    <a class="nav-link " href="leave_data.php"><i class="fas fa-calendar-alt me-2"></i>Leave Status</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a>
                </li>
            </ul>
        </div>

        <!-- Main content -->
        <div class="col-md-9 col-lg-10 p-5">
            <h2 class="mb-4">Leave Request Form</h2>
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="leave_type" class="form-label">Type of Leave</label>
                    <select class="form-select" name="leave_type" id="leave_type" required>
                        <option value="">-- Select --</option>
                        <option value="CL">Casual Leave (CL)</option>
                        <option value="SL">Sick Leave (SL)</option>
                        <option value="AL">Annual Leave (AL)</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="leave_date" class="form-label">Date of Leave</label>
                    <input type="date" class="form-control" name="leave_date" id="leave_date" required>
                </div>

                <div class="mb-3">
                    <label for="reason" class="form-label">Reason</label>
                    <textarea class="form-control" name="reason" id="reason" rows="4" required></textarea>
                </div>

                <button type="submit" class="btn btn-primary">Submit Request</button>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
