<?php
session_start();
include 'database.php';

$error = "";
$success = "";

if (!isset($_SESSION['username'])) {
    header("Location: forgot-password.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);

    if (empty($new_password) || empty($confirm_password)) {
        $error = "❌ Both fields are required!";
    } elseif ($new_password !== $confirm_password) {
        $error = "❌ Passwords do not match!";
    } elseif (
        strlen($new_password) < 6 ||
        !preg_match("/[A-Za-z]/", $new_password) ||
        !preg_match("/\d/", $new_password) ||
        !preg_match("/[^A-Za-z\d]/", $new_password)
    ) {
        $error = "❌ Password must be at least 6 characters long and include a letter, number, and special character.";
    } else {
        $username = $_SESSION['username'];
        $query = "UPDATE patients SET password = ? WHERE username = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $new_password, $username);

        if ($stmt->execute()) {
            $success = "✅ Password reset successful! <a href='patientlogin.php'>Login here</a>";
            session_destroy(); // clear session after reset
        } else {
            $error = "❌ Error updating password. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Reset Password</title>
    <link rel="stylesheet" href="sss.css">
</head>
<body>
    <div class="container">
        <div class="d">
            <h4><a href="index.html">Home</a></h4>
            <h1>Reset Password</h1>
            <form action="reset-passwordp.php" method="POST">
                <div class="wrapper">
                    <input type="password" name="new_password"
                           placeholder="Enter New Password"
                           required
                           pattern="^(?=.*[A-Za-z])(?=.*\d)(?=.*[^A-Za-z\d]).{6,}$"
                           title="Must include a letter, number, special character, and be at least 6 characters.">
                </div>
                <div class="wrapper">
                    <input type="password" name="confirm_password" placeholder="Confirm New Password" required>
                </div>
                <button type="submit">Reset Password</button>

                <?php if (!empty($error)) : ?>
                    <p style="color: red; font-size: 18px;"><?php echo $error; ?></p>
                <?php endif; ?>

                <?php if (!empty($success)) : ?>
                    <p style="color: green; font-size: 18px;"><?php echo $success; ?></p>
                <?php endif; ?>
            </form>
        </div>
    </div>
</body>
</html>
