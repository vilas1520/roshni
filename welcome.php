<?php
session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}
?>
<?php
include 'partials/db_data.php';

?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Welcome -
    <?php echo $_SESSION["username"]; ?>
  </title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">

  <!-- Custom CSS -->
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <?php require 'partials/_nav.php'; ?>

  <div class="container-fluid">
    <div class="row">
     

      <!-- Main content -->
      <div class="col-md-9 col-lg-10 p-4">
        <!-- Main content -->
        <div class="col-md-9 col-lg-10 p-4">
          <h2 class="mb-4">Employee Details Form</h2>
              <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">

            <div class="row mb-3">
              <div class="col-md-6">
                <label for="empName" class="form-label">Full Name</label>
                <input type="text" class="form-control" id="empName" name="empName" required>
              </div>
              <div class="col-md-6">
                <label for="empEmail" class="form-label">Email</label>
                <input type="email" class="form-control" id="empEmail" name="empEmail" required>
              </div>
            </div>

            <div class="row mb-3">
              <div class="col-md-6">
                <label for="empPhone" class="form-label">Phone Number</label>
                <input type="text" class="form-control" id="empPhone" name="empPhone" required>
              </div>
              <div class="col-md-6">
                <label for="empDept" class="form-label">Department</label>
                <input type="text" class="form-control" id="empDept" name="empDept" required>
              </div>
            </div>

            <div class="mb-3">
              <label for="empAddress" class="form-label">Address</label>
              <textarea class="form-control" id="empAddress" name="empAddress" rows="3" required></textarea>
            </div>

            <div class="mb-3">
              <label for="empRole" class="form-label">Position/Role</label>
              <input type="text" class="form-control" id="empRole" name="empRole" required>
            </div>
            <a href="data.php" class="btn btn-secondary">black</a>
            <button type="submit" class="btn btn-primary">Submit</button>
          </form>
        </div>


      </div>
    </div>
  </div>


  <!-- Footer -->
  <footer>
    <div class="container">
      <div class="row">
        <!-- Column 1 -->
        <div class="col-md-4">
          <h5>Company</h5>
          <p>We provide quality solutions for your needs.</p>
        </div>
        <!-- Column 2 -->
        <div class="col-md-4">
          <h5>Quick Links</h5>
          <ul class="list-unstyled">
            <li><a href="#">Home</a></li>
            <li><a href="#">Services</a></li>
            <li><a href="#">Contact</a></li>
          </ul>
        </div>
        <!-- Column 3 -->
        <div class="col-md-4">
          <h5>Contact</h5>
          <p>Email: info@example.com</p>
          <p>Phone: +91 12345 67890</p>
        </div>
      </div>
    </div>
    <div class="text-center p-3 bg-secondary">
      © 2025 YourWebsite.com
    </div>
  </footer>

  <!-- Bootstrap Bundle JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO"
    crossorigin="anonymous"></script>
</body>

</html>