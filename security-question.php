<?php
session_start();
include 'database.php';

if (!isset($_SESSION['username']) || !isset($_SESSION['security_question'])) {
    header("Location: forgot-password.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $entered_answer = trim($_POST['security_answer']);

    // Verify answer
    if (strcasecmp($entered_answer, $_SESSION['security_answer']) == 0) {
        header("Location: reset-password.php"); // Redirect to reset password page
        exit();
    } else {
        echo "<script>alert('Incorrect answer! Try again.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Security Question</title>
    <link rel="stylesheet" href="se.css">
</head>
<body>
    <div class="container">
        <div class="c">
        <h2>Answer Security Question</h2>
        <form action="" method="POST">
            <p><strong><?php echo $_SESSION['security_question']; ?></strong></p>
            <div class="cc">
            <input type="text" name="security_answer" placeholder="Enter your answer" required>
            <button type="submit">Submit</button>
</div>
        </form>

        <a href="reset-security-question.php">Forgot your security answer? Verify with phone</a>


        <div class="back">
            <a href="forgot-password.php">Back</a>
</div>
        </div>
    </div>
</body>
</html>
