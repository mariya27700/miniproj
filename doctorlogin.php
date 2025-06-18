<?php
session_start();
include 'database.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    $query = "SELECT * FROM doctors WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();

        if ($password === $row["password"]) {
            $_SESSION["doctor"] = $username;
            $_SESSION["doctor_id"] = $row["id"];

            if (empty($row["security_question"]) || empty($row["security_answer"])) {
                // If security question is NOT set, redirect to set it
                header("Location: set_security.php");
                exit();
            } else {
                // If security question is set, proceed to doctors routine
                header("Location: doctorsroutine.php");
                exit();
            }
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
    <title>Doctor Login</title>
    <link rel="stylesheet" href="ss.css">
</head>
<body>
    <div class="login-container">
        <h4><a href="index.html">Home</a></h4>
        <div class="d">
            <div class="wrapper">
                <form action="doctorlogin.php" method="POST">
                    <h1>Doctor Login</h1>
                    <div class="input-group">
                        <input type="text" name="username" placeholder="Username" required>
                    </div>
                    <div class="input-group">
                        <input type="password" name="password" placeholder="Password" required>
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

                <div class="forgot-password">
                    <a href="forgot-password.php">Forgot Password?</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
