<?php
session_start();
include 'database.php';

if (!isset($_SESSION["admin"])) {
    header("Location: adminlogin.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $doctor_name = $_POST["doctor_name"];
    $username = $_POST["username"];
    $password = $_POST["password"];
    $specialization = $_POST["specialization"];

    $query = "INSERT INTO doctors (name, username, password, specialization) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssss", $doctor_name, $username, $password, $specialization);
    
    if ($stmt->execute()) {
        $message = "Doctor added successfully!";
    } else {
        $message = "Error adding doctor.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel</title>
    <link rel="stylesheet" href="s.css">
</head>
<body>
    <h2>Welcome, Admin</h2>
    <form method="POST">
        <h3>Add New Doctor</h3>
        <input type="text" name="doctor_name" placeholder="Doctor Name" required>
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="text" name="specialization" placeholder="Specialization" required>
        <button type="submit">Add Doctor</button>
    </form>

    <?php if (!empty($message)) : ?>
        <p style="color:green;"><?php echo $message; ?></p>
    <?php endif; ?>

    <a href="adminlogout.php">Logout</a>
</body>
</html>
