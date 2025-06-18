<?php
session_start();
include "database.php";

if (!isset($_SESSION['doctor_id'])) {
    die("Access denied.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $doctor_id = $_SESSION['doctor_id'];
    $security_question = trim($_POST["security_question"]);
    $security_answer = trim($_POST["security_answer"]);

    $stmt = $conn->prepare("UPDATE doctors SET security_question = ?, security_answer = ? WHERE id = ?");
    $stmt->bind_param("ssi", $security_question, $security_answer, $doctor_id);

    if ($stmt->execute()) {
        header("Location: doctorsroutine.php");
        exit();
    } else {
        echo "Error setting security question.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Set Security Question</title>
    <link rel="stylesheet" href="md.css">
</head>
<body>

<h2>Set Your Security Question</h2>

<form action="set_security.php" method="POST">
    <label>Security Question:</label>
    <select name="security_question" required>
        <option value="What is your favorite color?">What is your favorite color?</option>
        <option value="What is your mother's maiden name?">What is your mother's maiden name?</option>
        <option value="What was the name of your first pet?">What was the name of your first pet?</option>
    </select><br>

    <label>Security Answer:</label>
    <input type="text" name="security_answer" required><br>

    <button type="submit">Save</button>
</form>

</body>
</html>
