<?php
session_start();
include 'database.php';

if (!isset($_SESSION["patient"])) {
    header("Location: patientlogin.html");
    exit();
}

$patient_username = $_SESSION["patient"];

$docQuery = "SELECT username, name, specialization, available_slots FROM doctors";
$result = $conn->query($docQuery);

$appQuery = "SELECT doctor_username, appointment_time, status, cancelled_at, visited 
             FROM appointments 
             WHERE patient_username = ?";
$stmt = $conn->prepare($appQuery);
$stmt->bind_param("s", $patient_username);
$stmt->execute();
$appResult = $stmt->get_result();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["book_appointment"])) {
    $age = $_POST["age"];
    $disease = $_POST["disease"];
    $description = $_POST["description"];
    $doctor_username = $_POST["doctor"];

    $slotQuery = "SELECT available_slots FROM doctors WHERE username = ?";
    $stmt = $conn->prepare($slotQuery);
    $stmt->bind_param("s", $doctor_username);
    $stmt->execute();
    $slotResult = $stmt->get_result();
    $slotData = $slotResult->fetch_assoc();
    $available_slots = $slotData['available_slots'];

    if ($available_slots > 0) {
        $updateQuery = "UPDATE doctors SET available_slots = available_slots - 1 WHERE username = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("s", $doctor_username);
        $stmt->execute();

        $insertQuery = "INSERT INTO appointments (patient_username, doctor_username, age, disease, description, status, visited) 
                        VALUES (?, ?, ?, ?, ?, 'Active', 'Not Marked')";
        $stmt = $conn->prepare($insertQuery);
        $stmt->bind_param("ssiss", $patient_username, $doctor_username, $age, $disease, $description);
        $stmt->execute();

        header("Refresh:0");
        exit();
    } else {
        echo "<p style='color: red;'>Doctor's slots are full. Try another day.</p>";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["cancel_appointment"])) {
    $doctor_username = $_POST["doctor_username"];

    $cancelQuery = "DELETE FROM appointments 
                    WHERE patient_username = ? AND doctor_username = ? AND status = 'Active'";
    $stmt = $conn->prepare($cancelQuery);
    $stmt->bind_param("ss", $patient_username, $doctor_username);
    $stmt->execute();

    $updateSlotsQuery = "UPDATE doctors SET available_slots = available_slots + 1 WHERE username = ?";
    $stmt = $conn->prepare($updateSlotsQuery);
    $stmt->bind_param("s", $doctor_username);
    $stmt->execute();

    header("Refresh:0");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["mark_visited"])) {
    $doctor_username = $_POST["doctor_username"];
    $visited_status = $_POST["visited"];

    $updateVisitedQuery = "UPDATE appointments 
                           SET visited = ? 
                           WHERE patient_username = ? AND doctor_username = ? AND status = 'Active'";
    $stmt = $conn->prepare($updateVisitedQuery);
    $stmt->bind_param("sss", $visited_status, $patient_username, $doctor_username);
    $stmt->execute();

    header("Refresh:0");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Patient Dashboard</title>
    <link rel="stylesheet" href="prs.css">
</head>
<body>
    <h2>Welcome, <?php echo $patient_username; ?></h2>
    <div class="logout">
        <a href="patientlogin.php">Logout</a>
    </div>

    <div class="akshaya">
        <h3 class="hut">Your Appointments</h3>
        <table border="1">
            <tr><th>Doctor</th><th>Time</th><th>Status</th><th>Action</th></tr>
            <?php while ($row = $appResult->fetch_assoc()) {
                if ($row['status'] === 'Cancelled') {
                    $deleteQuery = "DELETE FROM appointments 
                                    WHERE patient_username = ? AND doctor_username = ? 
                                    AND status = 'Cancelled'";
                    $deleteStmt = $conn->prepare($deleteQuery);
                    $deleteStmt->bind_param("ss", $patient_username, $row['doctor_username']);
                    $deleteStmt->execute();
                    continue;
                }

                $appointmentDate = date("Y-m-d", strtotime($row['appointment_time']));
                $todayDate = date("Y-m-d");
                if ($row['status'] === 'Active' && $appointmentDate !== $todayDate) {
                    continue;
                }

                echo "<tr>
                        <td>{$row['doctor_username']}</td>
                        <td>{$row['appointment_time']}</td>
                        <td>{$row['status']}</td>";

                if ($row['status'] == 'Active') {
                    if (strtotime($row['appointment_time']) < time() && $row['visited'] === 'Not Marked') {
                        echo "<td>
                                <form method='POST'>
                                    <input type='hidden' name='doctor_username' value='{$row['doctor_username']}'>
                                    <select name='visited' required>
                                        <option value=''>--Visited?--</option>
                                        <option value='Yes'>Yes</option>
                                        <option value='No'>No</option>
                                    </select>
                                    <button type='submit' name='mark_visited'>Submit</button>
                                </form>
                              </td>";
                    } else {
                        echo "<td>Visited: {$row['visited']}</td>";
                    }

                    echo "<tr>
                            <td colspan='4' style='text-align:center;'>
                                <form method='POST'>
                                    <input type='hidden' name='doctor_username' value='{$row['doctor_username']}'>
                                    <button type='submit' name='cancel_appointment'>Cancel Appointment</button>
                                </form>
                            </td>
                          </tr>";
                }

                echo "</tr>";
            } ?>
        </table>

        <h3 class="tiramisu">Book a New Appointment</h3>
        <form method="POST">
            <label class="a">Age:</label> <input type="number" name="age" required><br>
            <label class="b">Disease:</label> <input type="text" name="disease" required><br>
            <div class="form-group inline-group">
                <div>
                    <label>Description:</label> 
                    <textarea name="description" required></textarea>
                </div>

                <div>
                    <label>Select Doctor:</label>
                    <select name="doctor" required>
                        <?php
                        $docResult = $conn->query($docQuery);
                        while ($row = $docResult->fetch_assoc()) {
                            echo "<option value='{$row['username']}'>{$row['name']} - {$row['specialization']} (Slots Left: {$row['available_slots']})</option>";
                        } ?>
                    </select>
                    <button type="submit" name="book_appointment">Book Appointment</button>
                </div>
            </div>
        </form>
    </div>
</body>
</html>
