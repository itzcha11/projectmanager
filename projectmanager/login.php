<?php
require 'config.php'; // Load the secure DB connection (PDO)
include 'includes/header.php'; // Starts the session + generates CSRF token if not set

// Variables for feedback messages shown to the user
$message = '';
$msg_class = '';

/* Handle form submission only when the method is POST */

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // CSRF validation
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token validation failed");
    }
    
    $username = trim($_POST['username']); // Trim username to avoid accidental spaces
    $password = $_POST['password'];

    /* Fetch user securely using a prepared statement */
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

  /* Verify credentials, password_verify() compares plain password with hashed one */
    if ($user && password_verify($password, $user['password'])) {
        // Store minimal identifying information in the session
        $_SESSION['uid'] = $user['uid'];
        $_SESSION['username'] = $user['username'];
        // Redirect to dashboard/homepage after login
        header("Location: index.php");
        exit();
    } else {
        // log in failed it will show a safe generic message
        $message = "Invalid username or password!";
        $msg_class = "error";
    }
}
?>

<!-- ------------------------------------------
     Login form (POST method – protected by CSRF)
------------------------------------------- -->

<div class="form-card">
    <h2>Login</h2>
    <?php if($message != ''): ?>
        <p class="message <?= $msg_class ?>"><?= $message ?></p>
    <?php endif; ?>

    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
        
        <label for="username">Username</label>
        <input type="text" name="username" id="username" required>

        <label for="password">Password</label>
        <input type="password" name="password" id="password" required>

        <button type="submit">Login</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>