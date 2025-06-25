<?php
session_start();

// Database connection
$conn = mysqli_connect("localhost", "root", "", "user");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Query to find the user
    $query = "SELECT * FROM user WHERE username='$username' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) == 1) { 
        $row = mysqli_fetch_assoc($result);

        if ($password === $row['password']) {
            // ✅ Plain text matched - Set session variables
            $_SESSION["loggedin"] = true;
            $_SESSION["username"] = $row['username'];
            $_SESSION["role"] = $row['role'];
            $_SESSION["id"] = $row['id']; // ✅ Store employee ID for use in leave form

            // Redirect based on role
            if ($row['role'] === 'admin') {
                header("Location: dashboard.php");
            } else {
                header("Location: user_dashboard.php");
            }
            exit;
        } else {
            $error = "❌ Incorrect password.";
        }

    } else {
        $error = "❌ Username not found.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="col-md-6 offset-md-3">
        <h2>Login</h2>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3"> 
                <label>Username</label>
                <input type="text" name="username" class="form-control" required />
            </div>
            <div class="mb-3"> 
                <label>Password</label> 
                <input type="password" name="password" class="form-control" required />
            </div>
            <button type="submit" class="btn btn-primary">Login</button>
            <a href="signup.php" class="btn btn-link">Don't have an account? Sign Up</a>
        </form>
    </div>
</div>
</body>
</html>
