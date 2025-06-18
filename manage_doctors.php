<?php
include "database.php";


$query = "SELECT * FROM doctors";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Manage Doctors</title>
    <link rel="stylesheet" href="md.css">
</head>
<body>

<a href="add_doctor.php">
    <button style="background-color: green; color: white; padding: 10px; border: none;">➕ Add Doctor</button>
</a>

<table border="1">
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Specialization</th>
        <th>Username</th>
        <th>Email</th>
        <th>Phone no:</th>
        <th>Available Slots</th>
        <th>Security Question</th>
        <th>Security Answer</th> 
        <th>Actions</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?php echo $row["id"]; ?></td>
            <td><?php echo $row["name"]; ?></td>
            <td><?php echo $row["specialization"]; ?></td>
            <td><?php echo $row["username"]; ?></td>
            <td><?php echo $row["email"]; ?></td>
            <td><?php echo $row["phone_no"]; ?></td>
            <td><?php echo $row["available_slots"]; ?></td>
            <td><?php echo $row["security_question"]; ?></td>
            <td><?php echo $row["security_answer"]; ?></td> 
            <td>
                <a href="edit_doctor.php?id=<?php echo $row["id"]; ?>">✏️ Edit</a> |
                <a href="delete_doctor.php?id=<?php echo $row["id"]; ?>" onclick="return confirm('Are you sure?')">🗑️ Delete</a>
            </td>
        </tr>
    <?php } ?>
</table>

<h3><a href="admin.php">Go back</a></h3>

</body>
</html>
