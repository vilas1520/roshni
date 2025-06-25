<?php
session_start();
include 'partials/db_data.php';

if (!isset($_GET['name']) || !isset($_GET['date']) || empty($_GET['name']) || empty($_GET['date'])) {
    echo "❌ Invalid parameters.";
    exit;
}

$name = $_GET['name'];
$date = $_GET['date'];

$query = "SELECT * FROM attendence WHERE name = ? AND date = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "ss", $name, $date);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (!$result || mysqli_num_rows($result) === 0) {
    echo "❌ Record not found.";
    exit;
}

$row = mysqli_fetch_assoc($result);



// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $status = $_POST['status'] ?? '';
    $in_time = $_POST['in_time'] ?? null;
    $out_time = $_POST['out_time'] ?? null;

    // Update query
    $stmt = $conn->prepare("UPDATE attendence SET status = ?, in_time = ?, out_time = ? WHERE name = ? AND date = ?");
    $stmt->bind_param("sssss", $status, $in_time, $out_time, $name, $date);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        header("Location: attendence_data.php?msg=Attendance updated successfully");
        exit;
    } else {
        $error = "No changes made or update failed.";
    }
}

// Fetch existing attendance record
$stmt = $conn->prepare("SELECT * FROM attendence WHERE name = ? AND date = ?");
$stmt->bind_param("ss", $name, $date);
$stmt->execute();
$result = $stmt->get_result();
$record = $result->fetch_assoc();

if (!$record) {
    die("Record not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Attendance for <?= htmlspecialchars($name) ?> on <?= htmlspecialchars($date) ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4">
  <h2>Edit Attendance</h2>
  
  <?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>
  
  <form method="POST" action="">
    <div class="mb-3">
      <label>Name</label>
      <input type="text" class="form-control" value="<?= htmlspecialchars($name) ?>" disabled>
    </div>
    <div class="mb-3">
      <label>Date</label>
      <input type="date" class="form-control" value="<?= htmlspecialchars($date) ?>" disabled>
    </div>
    <div class="mb-3">
      <label>Status</label>
      <select name="status" class="form-select" required>
        <option value="Present" <?= $record['status'] === 'Present' ? 'selected' : '' ?>>Present</option>
        <option value="Absent" <?= $record['status'] === 'Absent' ? 'selected' : '' ?>>Absent</option>
        <option value="Leave" <?= $record['status'] === 'Leave' ? 'selected' : '' ?>>Leave</option>
      </select>
    </div>
    <div class="mb-3">
      <label>In Time</label>
      <input type="time" name="in_time" class="form-control" value="<?= htmlspecialchars($record['in_time']) ?>">
    </div>
    <div class="mb-3">
      <label>Out Time</label>
      <input type="time" name="out_time" class="form-control" value="<?= htmlspecialchars($record['out_time']) ?>">
    </div>
    <button type="submit" class="btn btn-success">Update Attendance</button>
    <a href="attendence_data.php" class="btn btn-secondary">Cancel</a>
  </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
