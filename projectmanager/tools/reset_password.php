<?php
require 'config.php';

$newPassword = 'password123'; // <-- set a password you know
$hashed = password_hash($newPassword, PASSWORD_DEFAULT);

$stmt = $conn->prepare("UPDATE users SET password = ? WHERE username = ?");
$stmt->execute([$hashed, 'jakehenry']);

echo "Password for jakehenry has been reset to: password123";
?>
