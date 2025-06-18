<?php
session_start();
include 'database.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    
    $query = "SELECT * FROM patients WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();

        if ($password === $row["password"]) {
            $_SESSION["patient"] = $username;
            header("Location: patientroutine.php");
            exit();
        } else {
            $error = "Invalid Username or Password!";
        }
    } else {
        $error = "Invalid Username or Password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Patient Login</title>
    <link rel="stylesheet" href="sp.css">
</head>

<body>
    <div class="login-container">
        <h4><a href="index.html">Home</a></h4>

        <div class="d">
        <div class="wrapper">
            <form action="patientlogin.php" method="POST">
                <h1>Patient login</h1>
                <div class="input-group">
                    <input type="text" id="username" name="username" placeholder="Username" required>
                </div>

                <div class="input-group">
                    <input type="password" id="password" name="password" placeholder="Password" required>
                </div>

                <div class="i">
                    <button type="submit" class="login-btn">Login</button>
                </div>
                <div class="i">
                    <button type="reset" class="reset-btn">Reset</button>
                </div>
            </form>

            <?php if (!empty($error)) : ?>
                <p style="color: red;"><?php echo $error; ?></p>
            <?php endif; ?>

            <div class="registration">
                <a href="patientsignup.php">New registration</a>
            </div>
            <div class="forgot-password">
                <a href="forgot-passwordp.php">Forgot Password?</a>
            </div>
        </div>
        </div>
    </div>
</body>
</html>
