<?php
$severname = "localhost";
$username = "root";
$password = "";
$datbase = "user";

$conn = mysqli_connect($severname, $username, $password, $datbase);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Now it's safe to access $_POST values
    $name = $_POST["empName"] ?? '';
    $email = $_POST["empEmail"] ?? '';
    $phone = $_POST["empPhone"] ?? '';
    $dept = $_POST["empDept"] ?? '';
    $address = $_POST["empAddress"] ?? '';
    $role = $_POST["empRole"] ?? '';

    // ... rest of the insert code



    // 🔍 Step 1: Insert employee
    $sql = "INSERT INTO data (name, email, phone, dep, address, role) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    // 🔥 Debugging line
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("ssssss", $name, $email, $phone, $dept, $address, $role);

    if ($stmt->execute()) {
        $emp_id = $stmt->insert_id;

        // Step 2: Insert leave balances
        $default_leaves = ['CL' => 3, 'SL' => 5, 'AL' => 10];
        foreach ($default_leaves as $type => $count) {
            $insert_leave = $conn->prepare("INSERT INTO leave_balance (employee_id, leave_type, days_available) VALUES (?, ?, ?)");
            if (!$insert_leave) {
                die("Leave insert prepare failed: " . $conn->error);
            }
            $insert_leave->bind_param("isi", $emp_id, $type, $count);
            $insert_leave->execute();
        }

        echo "<script>alert('Employee added with default leave balances');</script>";
    } else {
        echo "<script>alert('Error inserting employee data');</script>";
    }
}
?>
