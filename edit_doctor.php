<?php
include "database.php";

if (!isset($_GET["id"])) {
    die("Doctor ID is required.");
}

$doctor_id = $_GET["id"];
$query = "SELECT * FROM doctors WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();
$doctor = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $specialization = $_POST["specialization"];
    $username = $_POST["username"];
    $email = $_POST["email"];
    $available_slots = intval($_POST["available_slots"]);
    $phone_no = $_POST["phone_no"];

    $update_query = "UPDATE doctors SET name=?, specialization=?, username=?, email=?, available_slots=?, phone_no=? WHERE id=?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("ssssisi", $name, $specialization, $username, $email, $available_slots, $phone_no, $doctor_id);

    if ($stmt->execute()) {
        header("Location: manage_doctors.php?success=Doctor updated");
    } else {
        header("Location: edit_doctor.php?id=$doctor_id&error=Failed to update doctor");
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  
    <title>Edit Doctor</title>
    <link rel="stylesheet" href="md.css"> 
</head>
<body>

<h2>Edit Doctor</h2>
<div class="md">
<form action="" method="POST">
    <div class="n">
    <label>Name:</label>
    <input type="text" name="name" value="<?php echo $doctor['name']; ?>" required><br>
</div>
<div class="s">
    <label>Specialization:</label>
    <input type="text" name="specialization" value="<?php echo $doctor['specialization']; ?>" required><br>
    </div>
    <div class="u">
    <label>Username:</label>
    <input type="text" name="username" value="<?php echo $doctor['username']; ?>" required><br>
    </div>
    <div class="v">
    <label>Email:</label>
    <input type="email" name="email" value="<?php echo $doctor['email']; ?>" required><br>
    </div>
    <div class="w">
    <label>Available Slots:</label>
    <input type="number" name="available_slots" value="<?php echo $doctor['available_slots']; ?>" required><br>
    <label>Phone no:</label>
    <input type="tel" name="phone_no" value="<?php echo $doctor['phone_no']; ?>" required><br>
    </div>
  
    
    <button class="hh" type="submit">Update Doctor</button>
    <h3><a href="manage_doctors.php">Go Back</h3>
</form>
</div>
</body>
</html>
