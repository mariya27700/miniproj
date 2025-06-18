<?php
session_start();
include 'database.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);

    if (empty($username) || empty($email)) {
        echo "<script>alert('All fields are required!'); window.location.href='forgot-password.php';</script>";
        exit();
    }

    $query = "SELECT security_question, security_answer FROM doctors WHERE username = ? AND email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        $_SESSION['username'] = $username;
        $_SESSION['security_question'] = $user['security_question'];
        $_SESSION['security_answer'] = $user['security_answer']; 
        header("Location: security-question.php"); 
        exit();
    } else {
        echo "<script>alert('Invalid Username or Email!'); window.location.href='forgot-password.php';</script>";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Forgot Password</title>
    <link rel="stylesheet" href="s.css">
</head>
<body>
    <div class="login-container">
        <h4><a href="index.html">Home</a></h4>
        <div class="d">
            <h1>Forgot Password</h1>
            <div class="wrapper">
                <form action="forgot-password.php" method="POST">
                    <div class="input-group">
                        <input type="text" name="username" placeholder="Enter Username" required>
                    </div>
                    <div class="input-group">
                        <input type="email" name="email" placeholder="Enter Email" required>
                    </div>
                    <div class="i">
                        <button type="submit" class="login-btn">Next</button>
                    </div>
                </form>
                <div class="forgot-password">
                    <nav>
                    <a href="doctorlogin.php">Back to Login</a>
</nav>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
