<?php
include "database.php";
$step = 1; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["email"])) {
       
        $email = trim($_POST["email"]);
        $stmt = $conn->prepare("SELECT * FROM doctors WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            
            if (!empty($row["password"]) || !empty($row["security_question"]) || !empty($row["security_answer"])) {
                $error = "❌ This doctor has already set their security details. Changes are not allowed.";
            } else {
                
                $step = 2;
            }
        } else {
            $error = "❌ Email not found. Please enter a valid email.";
        }
    } elseif (isset($_POST["security_question"], $_POST["security_answer"], $_POST["password"], $_POST["email_hidden"])) {
      
        $email = trim($_POST["email_hidden"]);
        $security_question = trim($_POST["security_question"]);
        $security_answer = trim($_POST["security_answer"]);
        $password = trim($_POST["password"]); 

        if (strlen($password) < 6) {
            $error = "❌ Password must be at least 6 characters long.";
        } else {
            $stmt = $conn->prepare("UPDATE doctors SET security_question = ?, security_answer = ?, password = ? WHERE email = ?");
            $stmt->bind_param("ssss", $security_question, $security_answer, $password, $email);

            if ($stmt->execute()) {
                $success = "✅ Security question and password set successfully.";
                $step = 0; 
            } else {
                $error = "❌ Error: Failed to update security details.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Set Security Question & Password</title>
    <link rel="stylesheet" href="md.css">
</head>
<body>

<h2>Set Your Security Question & Password</h2>

<?php if (isset($error)) echo "<p style='color: red;'>$error</p>"; ?>
<?php if (isset($success)) echo "<p style='color: green;'>$success</p>"; ?>

<?php if ($step == 1) { ?>
   
    <form action="set_security_question.php" method="POST">
        <label class="l">Email:</label>
        <input type="email" name="email" required><br>
        <button type="submit">Verify Email</button>
    </form>
<?php } elseif ($step == 2) { ?>
    
    <form action="set_security_question.php" method="POST">
        <input type="hidden" name="email_hidden" value="<?php echo htmlspecialchars($email); ?>">

        <label>Security Question:</label>
        <select name="security_question" required>
            <option value="">-- Select a question --</option>
            <option value="What is your favorite color?">What is your favorite color?</option>
            <option value="What is your mother's maiden name?">What is your mother's maiden name?</option>
            <option value="What was the name of your first pet?">What was the name of your first pet?</option>
            <option value="What is your favourite hobby to do?">What is your favourite hobby to do?</option>
            <option value="What is your nickname?">What is your nickname?</option>
            
        </select><br>

        <label>Security Answer:</label>
        <input type="text" name="security_answer" required><br>

        <label>Set Password:</label>
        <input type="password" name="password" required minlength="6"><br>

        <button type="submit">Set</button>
    </form>
<?php } ?>

<h3><a href="index.html">Go Back</a></h3>

</body>
</html>
