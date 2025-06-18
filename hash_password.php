<?php
$new_password = "password";  // Change to your desired new password
$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
echo "New hashed password: " . $hashed_password;
?>
