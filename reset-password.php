<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
include 'database.php';

if (!isset($_SESSION['username'])) {
    header("Location: doctorlogin.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_SESSION['username'];
    $new_password = trim($_POST['new_password']);

   
    if (empty($new_password)) {
        echo "<script>alert('❌ Password cannot be empty!'); window.location.href='reset-password.php';</script>";
        exit();
    } elseif (
        strlen($new_password) < 6 ||
        !preg_match("/[A-Za-z]/", $new_password) ||       
        !preg_match("/[0-9]/", $new_password) ||          
        !preg_match("/[^A-Za-z0-9]/", $new_password)     
    ) {
        echo "<script>alert('❌ Password must be at least 6 characters long and include a letter, number, and special character.'); window.location.href='reset-password.php';</script>";
        exit();
    }

    
    $query = "UPDATE doctors SET password = ? WHERE username = ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        die("Query preparation failed: " . $conn->error);
    }

    $stmt->bind_param("ss", $new_password, $username);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        session_destroy(); 
        echo "<script>alert('✅ Password updated successfully! Please log in.'); window.location.href='doctorlogin.php';</script>";
        exit();
    } else {
        echo "<script>alert('❌ Failed to update password. Try again.'); window.location.href='reset-password.php';</script>";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <link rel="stylesheet" href="se.css">
</head>
<body>
<div class="r">
    <div class="re">
        <h2>Reset Password</h2>

        <form action="reset-password.php" method="POST">
            <label for="new_password">New Password:</label>
            <input type="password" name="new_password" 
                   required
                   pattern="^(?=.*[A-Za-z])(?=.*\d)(?=.*[^A-Za-z\d]).{6,}$"
                   title="Must be at least 6 characters long, include a letter, number, and special character."><br>

            <div class="rs">
                <button type="submit">Reset Password</button>
            </div>
        </form>
    </div>
</div>
</body>
</html>
