<?php
session_start();
include 'database.php';

$result = $conn->query("SELECT * FROM appointments");


if (isset($_POST["cancel"])) {
    $id = $_POST["id"];
    $conn->query("UPDATE appointments SET status='Cancelled' WHERE id=$id");
    header("Refresh:0");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Appointments</title>
    <link rel="stylesheet" href="md.css"> 
</head>
<body>
    <h1>Manage Appointments</h1>
    <table border="1">
        <tr><th>ID</th><th>Patient</th><th>Doctor</th><th>Status</th><th>Action</th></tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['patient_username']; ?></td>
                <td><?php echo $row['doctor_username']; ?></td>
                <td><?php echo $row['status']; ?></td>
                <td>
                    <?php if ($row['status'] == 'Active') { ?>
                        <form method="POST">
                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                            <button class="hh" type="submit" name="cancel">Cancel</button>
                        </form>
                    <?php } else { echo "Cancelled"; } ?>
                </td>
            </tr>
        <?php } ?>
    </table>
    <h3><a href="admin.php">Go Back</a><h3>
</body>
</html>
