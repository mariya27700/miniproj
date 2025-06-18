<?php
session_start();
include 'database.php';

if (!isset($_SESSION["doctor"])) {
    header("Location: doctorlogin.html");
    exit();
}

$doctor_username = $_SESSION["doctor"];

$query = "SELECT name, available_slots FROM doctors WHERE username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $doctor_username);
$stmt->execute();
$result = $stmt->get_result();
$doc = $result->fetch_assoc();

$doctor_name = $doc['name'];  
$available_slots = $doc['available_slots'];


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["appointment_limit"])) {
    $new_limit = intval($_POST["appointment_limit"]);
    $updateQuery = "UPDATE doctors SET available_slots = ? WHERE username = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("is", $new_limit, $doctor_username);
    $stmt->execute();
    $available_slots = $new_limit;
}


$appointmentQuery = "SELECT patients.pname, appointments.age, appointments.disease, appointments.description, appointments.appointment_time 
                     FROM appointments 
                     JOIN patients ON appointments.patient_username = patients.username 
                     WHERE appointments.doctor_username = ? AND appointments.status = 'Active'";
$stmt = $conn->prepare($appointmentQuery);
$stmt->bind_param("s", $doctor_username);
$stmt->execute();
$patients = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Doctor Dashboard</title>
    <link rel="stylesheet" href="drs.css">
</head>
<body>
  <div class="ww">
     <a href="doctorlogin.php">Back to login</a>
  </div>
    <h2>Welcome,  <?php echo $doctor_name; ?></h2>
    <form method="POST">
        <label>Set Maximum Appointments:</label>
        <input type="number" name="appointment_limit" min="1" value="<?php echo $available_slots; ?>" required>
        <button type="submit">Update</button>
    </form>

    <h3>Active Patient Appointments</h3>
    <table border="1">
        <tr><th>Patient Name</th><th>Age</th><th>Disease</th><th>Description</th><th>Appointment Time</th></tr>
        <?php while ($row = $patients->fetch_assoc()) {
            echo "<tr><td>{$row['pname']}</td><td>{$row['age']}</td><td>{$row['disease']}</td><td>{$row['description']}</td><td>{$row['appointment_time']}</td></tr>";
        } ?>
    </table>
</body>
</html>
