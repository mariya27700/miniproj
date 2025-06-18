<?php
session_start();
include 'database.php';


$result = $conn->query("SELECT * FROM patients");


if (isset($_POST["delete"])) {
    $username = $_POST["username"];
    $conn->query("DELETE FROM patients WHERE username='$username'");
    header("Refresh:0");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Patients</title>
    <link rel="stylesheet" href="md.css"> 
</head>
<body>
    <h1>Manage Patients</h1>
    <table border="1">
        <tr><th>Username</th><th>Name</th><th>Action</th></tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $row['username']; ?></td>
                <td><?php echo $row['pname']; ?></td>
                <td>
                    <form method="POST">
                        <input type="hidden" name="username" value="<?php echo $row['username']; ?>">
                       
                        <button class="hh" type="submit" name="delete" value="123">Delete</button>

                        
       
                    </form>
                </td>
            </tr>
        <?php } ?>
    </table>
   <h3> <a href="admin.php">Go Back</a></h3>
</body>
</html>
