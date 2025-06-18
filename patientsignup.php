<?php
session_start();
include 'database.php';

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $pname = trim($_POST["pname"]);
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);
    $email = trim($_POST["email"]);
    $security_question = trim($_POST["security_question"]);
    $security_answer = trim($_POST["security_answer"]);

    // Validate inputs
    if (
        empty($pname) || empty($username) || empty($password) || empty($email) ||
        empty($security_question) || empty($security_answer)
    ) {
        $error = "❌ All fields are required!";
    } elseif (
        strlen($password) < 6 ||
        !preg_match("/[A-Za-z]/", $password) ||
        !preg_match("/\d/", $password) ||
        !preg_match("/[^A-Za-z\d]/", $password)
    ) {
        $error = "❌ Password must be at least 6 characters and include a letter, a number, and a special character.";
    } else {
        // Check for duplicate username/email
        $query = "SELECT * FROM patients WHERE username = ? OR email = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = "❌ Username or Email already exists!";
        } else {
            // Optional: Use hashed password instead of plain text
            // $password = password_hash($password, PASSWORD_DEFAULT);

            // Insert into database
            $query = "INSERT INTO patients (pname, username, password, email, security_question, security_answer) 
                      VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ssssss", $pname, $username, $password, $email, $security_question, $security_answer);

            if ($stmt->execute()) {
                $success = "✅ Registration successful! <a href='patientlogin.php'>Login here</a>";
            } else {
                $error = "❌ Registration failed. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Patient Registration</title>
    <link rel="stylesheet" href="sp.css">
</head>
<body>
    <div class="login-container">
        <h4><a href="index.html">Home</a></h4>
        <h1>Patient Registration</h1>
        <div class="wrapper">
            <form action="patientsignup.php" method="POST">
                <div class="input-group">
                    <input type="text" name="pname" placeholder="Full Name" required>
                </div>
                <div class="input-group">
                    <input type="text" name="username" placeholder="Username" required>
                </div>
                <div class="input-group">
                    <input type="email" name="email" placeholder="Email" required>
                </div>
                <div class="input-group">
                    <input type="password" name="password" placeholder="Password" required
                        pattern="^(?=.*[A-Za-z])(?=.*\d)(?=.*[^A-Za-z\d]).{6,}$"
                        title="Password must include a letter, number, and special character.">
                </div>
                <div class="input-group">
                    <select name="security_question" required>
                        <option value="">Select a security question</option>
                        <option value="What is your favorite color?">What is your favorite color?</option>
                        <option value="What is your pet’s name?">What is your pet’s name?</option>
                        <option value="What is your favorite car?">What is your favorite car?</option>
                    </select>
                </div>
                <div class="input-group">
                    <input type="text" name="security_answer" placeholder="Security Answer" required>
                </div>
                <div class="i">
                    <button type="submit" class="login-btn">Register</button>
                </div>
                <div class="i">
                    <button type="reset" class="reset-btn">Reset</button>
                </div>
            </form>

            <?php if (!empty($error)) : ?>
                <p style="color: rgb(167, 16, 33); font-size: 20px;"><?php echo $error; ?></p>
            <?php endif; ?>

            <?php if (!empty($success)) : ?>
                <p style="color: green; font-size: 20px;"><?php echo $success; ?></p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
