<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "database.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $specialization = trim($_POST["specialization"]);
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $phone_no=trim($_POST["phone_no"]);
    $available_slots = intval($_POST["available_slots"]);

   
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Error: Invalid email format. <a href='add_doctor.php'>Go back</a>");
    }
    if (strlen($phone_no) < 10 || strlen($phone_no) > 11) {
        die("Error: Phone number should be 10  digits. <a href='add_doctor.php'>Go back</a>");
    }
    

    
    $stmt = $conn->prepare("SELECT * FROM doctors WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        die("Error: Email already in use. <a href='add_doctor.php'>Go back</a>");
    }

    
    $stmt = $conn->prepare("INSERT INTO doctors (name, specialization, username, email,phone_no, available_slots) VALUES (?, ?, ?, ?,?, ?)");
    $stmt->bind_param("sssssi", $name, $specialization, $username, $email,$phone_no, $available_slots);
    
    if ($stmt->execute()) {
        header("Location: manage_doctors.php?success=Doctor added successfully.");
        exit();
    } else {
        die("Error: Failed to add doctor. <a href='add_doctor.php'>Try again</a>");
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Add Doctor</title>
    <link rel="stylesheet" href="md.css">
</head>
<body>
<div class="h">
<h2>Add Doctor</h2>
</div>
<div class="add">
<form action="add_doctor.php" method="POST">
    <label>Name:</label>
    <input type="text" name="name" required><br>
</div>
<div class="ad">
    <label>Specialization:</label>
    <input type="text" name="specialization" required><br>
    </div>
    <div class="a">
    <label>Username:</label>
    <input type="text" name="username" required><br>
    </div>
    <div class="b">
    <label>Email:</label>
    <input type="email" name="email" required><br>
    </div>
    <div class="c">
    <label>Available Slots:</label>
    
    <input type="number" name="available_slots" min="0" required><br>
    </div>
    <div class="cc">
    <label>Phone no:</label>
    <input type="tel" name="phone_no"  required><br>
    </div>
    <div class="d">
    <button type="submit">Add Doctor</button>
    </div>

</form>
<div class="r">
<h3><a href="manage_doctors.php">Go Back</a></h3>
</div>
</body>
</html>
