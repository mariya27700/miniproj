<?php
session_start();
include 'database.php';

// Step 1: Check if user is logged in (or else redirect to forgot-password.php)
if (!isset($_SESSION['username'])) {
    header("Location: forgot-password.php");
    exit();
}

// Step 2: Fetch the current security question from the database
$username = $_SESSION['username'];
$stmt = $conn->prepare("SELECT security_question FROM doctors WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

// If there's no security question set, use a default one (optional fallback)
$security_question = isset($row['security_question']) ? $row['security_question'] : "What is your favorite color?";

// Step 3: Process phone number verification
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['phone_no']) && !isset($_POST['security_answer'])) {
    $phone_no = trim($_POST['phone_no']);

    // Check if the phone number exists for the doctor
    $stmt = $conn->prepare("SELECT * FROM doctors WHERE username = ? AND phone_no = ?");
    $stmt->bind_param("ss", $username, $phone_no);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Phone number matches, allow to update the security question
        $_SESSION['phone_verified'] = true;
        echo "<script>alert('Phone number verified! You can now reset your security question.'); window.location.href='';</script>";
    } else {
        echo "<script>alert('Incorrect phone number. Please try again.');</script>";
    }
}

// Step 4: Process resetting the security question
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['security_answer'])) {
    if (!isset($_SESSION['phone_verified']) || !$_SESSION['phone_verified']) {
        header("Location: forgot-password.php");  // Redirect to phone number verification if not verified
        exit();
    }

    $security_answer = trim($_POST['security_answer']);

    // Ensure the new answer is different from the previous one (optional step)
    $stmt = $conn->prepare("SELECT security_answer FROM doctors WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    // If the new answer is the same as the previous one, prompt the user
    if ($security_answer == $row['security_answer']) {
        echo "<script>alert('New answer must be different from the previous answer.');</script>";
    } else {
        // Update the answer to the same security question in the database
        $stmt = $conn->prepare("UPDATE doctors SET security_answer = ? WHERE username = ?");
        $stmt->bind_param("ss", $security_answer, $username);
        if ($stmt->execute()) {
            // Clear phone verification session after successful update
            unset($_SESSION['phone_verified']);
            echo "<script>alert('Security question updated. You can now reset your password.'); window.location.href='reset-password.php';</script>";
            exit();
        } else {
            echo "<script>alert('Failed to update. Try again.');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Security Question</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 0;
            display: flex;
            justify-content: right;
            align-items: center;
            min-height: 100vh;
            color: rgb(29, 29, 32);
            font-size: 20px;
            background-image: url('ss.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        h2 {
            margin-top: -311px;
            margin-left: 400px;
            text-align: right;
            margin-right: -313px;
        }

        form {
            background-color: rgba(255, 255, 255, 0.7);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.3);
        }

        input[type="text"] {
            padding: 10px;
            width: 250px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ddd;
        }

        button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>

<?php if (!isset($_SESSION['phone_verified'])): ?>
    <!-- Step 4: Show the phone verification form -->
    <h2>Verify Your Phone Number</h2>
    <form method="POST">
        <label>Enter your Phone Number:</label><br>
        <input type="text" name="phone_no" required><br><br>
        <button type="submit">Verify Phone Number</button>
    </form>
<?php else: ?>
    <!-- Step 5: Show the security question reset form -->
    <h2>Set Your Security Question Answer</h2>
    <form method="POST">
        <p><strong><?php echo $security_question; ?></strong></p>
        <label>Answer:</label><br>
        <input type="text" name="security_answer" required><br><br>
        <button type="submit">Save</button>
    </form>
<?php endif; ?>

</body>
</html>
