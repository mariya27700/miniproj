<?php
include "database.php"; // Ensure this file correctly establishes a database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['name'], $_POST['specialization'], $_POST['username'], $_POST['available_slots'])) {
        header("Location: add_doctor.php?error=Missing required fields");
        exit();
    }

    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $specialization = mysqli_real_escape_string($conn, $_POST['specialization']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $slots = intval($_POST['available_slots']);

    // Generate a random 8-character password
    $generated_password = substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"), 0, 8);
    $hashed_password = password_hash($generated_password, PASSWORD_DEFAULT); // Securely hash password

    // Insert into database
    $query = "INSERT INTO doctors (name, specialization, username, password, available_slots) 
              VALUES ('$name', '$specialization', '$username', '$hashed_password', '$slots')";

    if (mysqli_query($conn, $query)) {
        header("Location: add_doctor.php?success=1");
        exit();
    } else {
        header("Location: add_doctor.php?error=" . urlencode(mysqli_error($conn)));
        exit();
    }
} else {
    header("Location: add_doctor.php?error=Invalid request");
    exit();
}

mysqli_close($conn);
?>
