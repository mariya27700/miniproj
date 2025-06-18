<?php
session_start();

ini_set('display_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['username'])) {
    // If no session, redirect to forgot-password.php
    header("Location: forgot-password.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $entered_answer = trim($_POST['answer']);

    // Check if the answer matches
    if (strtolower($entered_answer) == strtolower($_SESSION['security_answer'])) {
        // Correct answer, allow password reset
        header("Location: reset-passwordp.php");
        exit();
    } else {
        echo "<script>alert('Incorrect answer to the security question!'); window.location.href='question-security.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Security Question</title>
    <link rel="stylesheet" href="sss.css">
</head>
<body>
    <div class="d">
    <h1>Security Question</h1>
    <p><?php echo $_SESSION['security_question']; ?></p>  <!-- Display the security question -->
    <form action="question-security.php" method="POST">
        <input type="text" name="answer" required>
        <button type="submit">Submit Answer</button>
    </form>
  
</div>
</body>
</html>
