<?php
include 'config.php'; // Load DB connection (PDO)
include 'includes/header.php'; // Starts session and generates CSRF token if not already set

/* Authentication check, Only logged-in users can add projects. If session UID is not set, redirect to login page. */
if (!isset($_SESSION['uid'])) {
    header("Location: login.php");
    exit();
}

// Variable to store messages to the user (success or error)
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF validation
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token validation failed");
    }
    
    // Retrieve form inputs safely
    $title = $_POST['title'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $short_description = $_POST['short_description'];
    $phase = $_POST['phase'];

    /* Server-side form validation, ensure all required fields are provided */
    if (empty($title) || empty($start_date) || empty($end_date) || empty($short_description) || empty($phase)) {
        $message = "All fields are required!";
    } else {
        $stmt = $conn->prepare("INSERT INTO projects (title, start_date, end_date, short_description, phase, uid) VALUES (?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$title, $start_date, $end_date, $short_description, $phase, $_SESSION['uid']])) {
            $message = "Project added successfully!";
            echo "<script>
                setTimeout(function() {
                    window.location.href = 'index.php';
                }, 1500); // redirect after 1.5 seconds
            </script>";
        } else {
            $message = "Error: Could not add project.";
        }
    }
}
?>

<h2>Add Project</h2>

<?php if (!empty($message)): ?>
    <p class="message <?= (strpos($message, 'success') !== false ? 'success' : 'error') ?>">
        <?= htmlspecialchars($message) ?>
    </p>
<?php endif; ?>

<div class="form-card">
    <form id="projectForm" method="POST">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
        
        <label>Title</label>
        <input type="text" name="title" id="title" value="<?= isset($title) ? htmlspecialchars($title) : '' ?>" required>
        <span class="error" id="titleError"></span><br><br>

        <label>Start Date</label>
        <input type="date" name="start_date" id="start_date" value="<?= isset($start_date) ? htmlspecialchars($start_date) : '' ?>" required>
        <span class="error" id="startDateError"></span><br><br>

        <label>End Date</label>
        <input type="date" name="end_date" id="end_date" value="<?= isset($end_date) ? htmlspecialchars($end_date) : '' ?>" required>
        <span class="error" id="endDateError"></span><br><br>

        <label>Description</label>
        <textarea name="short_description" id="short_description" rows="5" required><?= isset($short_description) ? htmlspecialchars($short_description) : '' ?></textarea>
        <span class="error" id="descError"></span><br><br>

        <label>Phase</label>
        <select name="phase" id="phase" required>
            <?php foreach (['design','development','testing','deployment','complete'] as $p): ?>
                <option value="<?= $p ?>" <?= (isset($phase) && $phase === $p) ? 'selected' : '' ?>><?= ucfirst($p) ?></option>
            <?php endforeach; ?>
        </select><br><br>

        <button type="submit">Add Project</button>
    </form>
</div>

<!-- Client-side validation -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('projectForm');
    const title = document.getElementById('title');
    const shortDesc = document.getElementById('short_description');
    const startDate = document.getElementById('start_date');
    const endDate = document.getElementById('end_date');

    const titleError = document.getElementById('titleError');
    const descError = document.getElementById('descError');
    const startDateError = document.getElementById('startDateError');
    const endDateError = document.getElementById('endDateError');

    function today() {
        const d = new Date();
        const month = String(d.getMonth() + 1).padStart(2, '0');
        const day = String(d.getDate()).padStart(2, '0');
        return `${d.getFullYear()}-${month}-${day}`;
    }

    function validateTitle() {
        if (title.value.length > 100) {
            titleError.textContent = "* Max 100 characters";
            return false;
        }
        titleError.textContent = "";
        return true;
    }

    function validateDesc() {
        if (shortDesc.value.length > 255) {
            descError.textContent = "* Max 255 characters";
            return false;
        }
        descError.textContent = "";
        return true;
    }

    function validateStartDate() {
        if (startDate.value < today()) {
            startDateError.textContent = "* Start date cannot be in the past";
            return false;
        }
        startDateError.textContent = "";
        return true;
    }

    function validateEndDate() {
        if (endDate.value < startDate.value) {
            endDateError.textContent = "* End date cannot be before start date";
            return false;
        }
        endDateError.textContent = "";
        return true;
    }

    // Attach live validation
    title.addEventListener('input', validateTitle);
    shortDesc.addEventListener('input', validateDesc);
    startDate.addEventListener('change', validateStartDate);
    endDate.addEventListener('change', validateEndDate);

    // Prevent form submission if client-side validation fails

    form.addEventListener('submit', function(e) {
        if (!validateTitle() || !validateDesc() || !validateStartDate() || !validateEndDate()) {
            e.preventDefault();
        }
    });
});
</script>

<?php include 'includes/footer.php'; ?>