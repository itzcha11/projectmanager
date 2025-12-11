<?php
require 'config.php';
include 'includes/header.php'; // starts session

$errors = [
    'username' => '',
    'email' => '',
    'password' => ''
];
$success_msg = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // CSRF validation
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token validation failed");
    }
    
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $raw_password = $_POST['password'];

    // -----------------------------
    // Username validation
    // -----------------------------
    if (!preg_match('/^[a-zA-Z0-9]{3,20}$/', $username)) {
        $errors['username'] = "3-20 characters, letters & numbers only, no spaces or symbols.";
    }

    // -----------------------------
    // Email validation
    // -----------------------------
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email address.";
    }

    // -----------------------------
    // Password validation
    // -----------------------------
    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/', $raw_password)) {
        $errors['password'] = "At least 8 chars, 1 uppercase, 1 lowercase & 1 number.";
    }

    // -----------------------------
    // If no errors, check username uniqueness & register
    // -----------------------------
    if (empty($errors['username']) && empty($errors['email']) && empty($errors['password'])) {
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->rowCount() > 0) {
            $errors['username'] = "Username already taken!";
        } else {
            $password = password_hash($raw_password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)");
            if ($stmt->execute([$username, $password, $email])) {
                $success_msg = "Registration successful! <a href='login.php'>Login here</a>";
            } else {
                $success_msg = "Error: Could not register.";
            }
        }
    }
}
?>

<div class="form-card">
    <h2>Register</h2>
    <?php if($success_msg): ?>
        <p class="message success"><?= $success_msg ?></p>
    <?php endif; ?>

    <form method="POST" id="registerForm" novalidate>
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
        
        <label for="username">Username</label>
        <input type="text" name="username" id="username" required value="<?= htmlspecialchars($username ?? '') ?>">
        <small class="error-msg" id="usernameError"><?= $errors['username'] ?? '' ?></small>
        <small class="form-info" id="usernameInfo">3–20 chars, letters & numbers only, no spaces or symbols.</small>

        <label for="email">Email</label>
        <input type="email" name="email" id="email" required value="<?= htmlspecialchars($email ?? '') ?>">
        <small class="error-msg" id="emailError"><?= $errors['email'] ?? '' ?></small>
        <small class="form-info" id="emailInfo">Enter a valid email address.</small>

        <label for="password">Password</label>
        <input type="password" name="password" id="password" required>
        <small class="error-msg" id="passwordError"><?= $errors['password'] ?? '' ?></small>
        <small class="form-info" id="passwordInfo">At least 8 chars, include uppercase, lowercase, and a number.</small>

        <button type="submit">Register</button>
    </form>
</div>

<script>
const usernameInput = document.getElementById('username');
const emailInput = document.getElementById('email');
const passwordInput = document.getElementById('password');

const usernameError = document.getElementById('usernameError');
const emailError = document.getElementById('emailError');
const passwordError = document.getElementById('passwordError');

const usernameInfo = document.getElementById('usernameInfo');
const emailInfo = document.getElementById('emailInfo');
const passwordInfo = document.getElementById('passwordInfo');

// Username live validation
usernameInput.addEventListener('input', () => {
    const val = usernameInput.value;
    if (!/^[a-zA-Z0-9]{3,20}$/.test(val)) {
        usernameError.textContent = "* 3-20 chars, letters & numbers only, no spaces or symbols.";
        usernameError.style.display = 'block';
        usernameInfo.style.display = 'none';   // hide grey info on error
        usernameInfo.classList.remove('valid');
    } else {
        usernameError.textContent = '';
        usernameError.style.display = 'none';
        usernameInfo.style.display = 'block';
        usernameInfo.classList.add('valid');   // show green
    }
});

// Email live validation
emailInput.addEventListener('input', () => {
    const val = emailInput.value;
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(val)) {
        emailError.textContent = "* Invalid email address.";
        emailError.style.display = 'block';
        emailInfo.style.display = 'none';     // hide grey info on error
        emailInfo.classList.remove('valid');
    } else {
        emailError.textContent = '';
        emailError.style.display = 'none';
        emailInfo.style.display = 'block';
        emailInfo.classList.add('valid');     // show green
    }
});

// Password live validation
passwordInput.addEventListener('input', () => {
    const val = passwordInput.value;
    const passRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/;
    if (!passRegex.test(val)) {
        passwordError.textContent = "* At least 8 chars, include uppercase, lowercase, and a number.";
        passwordError.style.display = 'block';
        passwordInfo.style.display = 'none';  // hide grey info on error
        passwordInfo.classList.remove('valid');
    } else {
        passwordError.textContent = '';
        passwordError.style.display = 'none';
        passwordInfo.style.display = 'block';
        passwordInfo.classList.add('valid');   // show green
    }
});



// Prevent form submit if errors exist
document.getElementById('registerForm').addEventListener('submit', function(e) {
    if (usernameError.textContent || emailError.textContent || passwordError.textContent) {
        e.preventDefault();
    }
});
</script>

<?php include 'includes/footer.php'; ?>