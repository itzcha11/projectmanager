<?php
// Show all errors (for debugging)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// The password you want to use for your test user
$plainPassword = 'password123';

// Generate the hash
$hash = password_hash($plainPassword, PASSWORD_DEFAULT);

// Show the hash
echo "Plain password: $plainPassword<br>";
echo "Hashed password: $hash";
?>
