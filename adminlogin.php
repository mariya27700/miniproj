<?php
session_start();
include "database.php"; 

$max_attempts = 3; 
$timeout_duration = 300; 


if (!isset($_SESSION['attempts'])) {
    $_SESSION['attempts'] = 0;
    $_SESSION['last_attempt_time'] = 0;
}


if ($_SESSION['attempts'] >= $max_attempts) {
    $remaining_time = ($_SESSION['last_attempt_time'] + $timeout_duration) - time();
    if ($remaining_time > 0) {
        die("<p style='color: red;'>Too many failed attempts. Try again in " . $remaining_time . " seconds.</p><a href='index.html'>Go to Homepage</a>");
    } else {
        
        $_SESSION['attempts'] = 0;
        $_SESSION['last_attempt_time'] = 0;
    }
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $error = "Please enter both username and password.";
    } else {
        // Fetch admin details securely
        $query = "SELECT * FROM admin WHERE username = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            if (password_verify($password, $row['password'])) {
                session_regenerate_id(true);
                $_SESSION['admin'] = $username;
                $_SESSION['attempts'] = 0; // Reset failed attempts
                header("Location: admin.php"); // Redirect to admin panel
                exit();
            } else {
                $_SESSION['attempts']++; // Increase failed attempts
                $_SESSION['last_attempt_time'] = time();
                $error = "Invalid username or password.";
            }
        } else {
            $_SESSION['attempts']++; // Increase failed attempts
            $_SESSION['last_attempt_time'] = time();
            $error = "Invalid username or password.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="styles.css"> 
</head>
<body>
    <h2>Admin Login</h2>
    
    <?php if (isset($error)) { ?>
        <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
    <?php } ?>
   <div class="a">
    <form action="adminlogin.php" method="POST">
    <div class="ad">
        <label for="username">Username:</label>
        <input type="text" name="username" required><br>
    </div>
    <div class="am">
        <label for="password">Password:</label>
        <input type="password" name="password" required><br>
    </div>
        <button type="submit">Login</button>
    </form>
    </div>
<div class="l">
    <a href="index.html">Go to Homepage</a>
    </div>
</body>
</html>
