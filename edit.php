<?php
session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "user");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$id = intval($_GET['id']);

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $phone = $_POST["phone"];
    $dep = $_POST["dep"];
    $address = $_POST["address"];
    $role = $_POST["role"];

    $sql = "UPDATE data SET name='$name', email='$email', phone='$phone', dep='$dep', address='$address', role='$role' WHERE id=$id";

    if ($conn->query($sql) === TRUE) {
        header("Location: data.php");
        exit;
    } else {
        echo "Error updating record: " . $conn->error;
    }
}

// Fetch existing data
$sql = "SELECT * FROM data WHERE id = $id";
$result = $conn->query($sql);
$employee = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Employee</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
  <h2>Edit Employee</h2>
  <form method="post">
    <div class="mb-3">
      <label>Full Name</label>
      <input type="text" name="name" class="form-control" value="<?= $employee['name']; ?>" required>
    </div>
    <div class="mb-3">
      <label>Email</label>
      <input type="email" name="email" class="form-control" value="<?= $employee['email']; ?>" required>
    </div>
    <div class="mb-3">
      <label>Phone</label>
      <input type="text" name="phone" class="form-control" value="<?= $employee['phone']; ?>" required>
    </div>
    <div class="mb-3">
      <label>Department</label>
      <input type="text" name="dep" class="form-control" value="<?= $employee['dep']; ?>" required>
    </div>
    <div class="mb-3">
      <label>Address</label>
      <input type="text" name="address" class="form-control" value="<?= $employee['address']; ?>" required>
    </div>
    <div class="mb-3">
      <label>Role</label>
      <input type="text" name="role" class="form-control" value="<?= $employee['role']; ?>" required>
    </div>
    <button type="submit" class="btn btn-success">Update</button>
    <a href="data.php" class="btn btn-secondary">Cancel</a>
  </form>
</div>
</body>
</html>
