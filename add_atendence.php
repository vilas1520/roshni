<?php
session_start();
include 'partials/db_data.php';

// Fetch employee names from database
$employees = [];
$emp_result = mysqli_query($conn, "SELECT name FROM leaves");
if ($emp_result && mysqli_num_rows($emp_result) > 0) {
    while ($row = mysqli_fetch_assoc($emp_result)) {
        $employees[] = $row['name'];
    }
}

// Handle attendance form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"] ?? '';
    $status = $_POST["status"] ?? 'Present';
    $date = $_POST["date"] ?? date('Y-m-d');
    $in_time = $_POST["in_time"] ?? '';
    $out_time = $_POST["out_time"] ?? '';

    $query = "INSERT INTO attendence (name, status, date, in_time, out_time) 
              VALUES ('$name', '$status', '$date', '$in_time', '$out_time')";
    $result = mysqli_query($conn, $query);

    if ($result) {
        echo "<script>alert('Attendance added successfully'); window.location='attendance_data.php';</script>";
        exit;
    } else {
        echo "<script>alert('Error: " . mysqli_error($conn) . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Attendance</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
  <h3 class="text-center mb-4">Add Attendance Record</h3>
  <form method="POST">
    <div class="mb-3">
      <label for="name" class="form-label">Employee Name</label>
      <select name="name" class="form-select" required>
        <option value="">-- Select Employee --</option>
        <?php foreach ($employees as $emp_name): ?>
          <option value="<?= htmlspecialchars($emp_name) ?>"><?= htmlspecialchars($emp_name) ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="mb-3">
      <label for="status" class="form-label">Status</label>
      <select name="status" class="form-select">
        <option value="Present">Present</option>
        <option value="Absent">Absent</option>
        <option value="Leave">Leave</option>
      </select>
    </div>

    <div class="mb-3">
      <label for="date" class="form-label">Date</label>
      <input type="date" name="date" class="form-control" required>
    </div>

    <div class="mb-3">
      <label for="in_time" class="form-label">In-Time</label>
      <input type="time" name="in_time" class="form-control">
    </div>

    <div class="mb-3">
      <label for="out_time" class="form-label">Out-Time</label>
      <input type="time" name="out_time" class="form-control">
    </div>

    <button type="submit" class="btn btn-success">Save Attendance</button>
    <a href="attendance_data.php" class="btn btn-secondary">Back</a>
  </form>
</div>
</body>
</html>

