<?php
include "database.php";

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $query = "DELETE FROM doctors WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<script>alert('Doctor deleted successfully!'); window.location.href='manage_doctors.php';</script>";
    } else {
        echo "<script>alert('Error deleting doctor!'); window.location.href='manage_doctors.php';</script>";
    }

    $stmt->close();
} else {
    echo "<script>alert('Invalid request!'); window.location.href='manage_doctors.php';</script>";
}

$conn->close();
?>
