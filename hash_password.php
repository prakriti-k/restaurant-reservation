<?php
// Replace "newpassword123" with the password you want for admin
$new_password = "admin123"; 
$hashed = password_hash($new_password, PASSWORD_DEFAULT);
echo "Hashed password: " . $hashed;
?>
