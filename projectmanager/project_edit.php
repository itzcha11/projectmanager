<?php
require 'config.php';
include 'includes/header.php'; // starts session

// Check if user is logged in only logged in users can edit projects
if (!isset($_SESSION['uid'])) {
    header("Location: login.php");
    exit();
}

// Check if pid is provided
if (!isset($_GET['pid']) || !is_numeric($_GET['pid'])) {
    header("Location: index.php?message=Invalid+project+ID");
    exit();
}

$pid = (int)$_GET['pid'];
$message = '';

// Fetch project and check ownership
$stmt = $conn->prepare("SELECT * FROM projects WHERE pid = ?");
$stmt->execute([$pid]);
$project = $stmt->fetch(PDO::FETCH_ASSOC);

// If project doesn't exist, redirect to dashboard
if (!$project) {
    header("Location: index.php?message=Project+not+found");
    exit();
}

// Check ownership
if ($project['uid'] != $_SESSION['uid']) {
    header("Location: index.php?message=Not+authorized");
    exit();
}

// Handle deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_project'])) {
    // CSRF validation
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token validation failed");
    }
    
    $stmt = $conn->prepare("DELETE FROM projects WHERE pid = ? AND uid = ?");
    if ($stmt->execute([$pid, $_SESSION['uid']])) {
        header("Location: index.php?message=Project+deleted+successfully");
        exit();
    } else {
        $message = "Error: Could not delete project.";
    }
}



// Handle update
if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['delete_project'])) {
    // CSRF validation
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token validation failed");
    }
    
    $title = trim($_POST['title']);
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $short_description = trim($_POST['short_description']);
    $phase = $_POST['phase'];

    if (empty($title) || empty($start_date) || empty($end_date) || empty($short_description) || empty($phase)) {
        $message = "All fields are required!";
    } else {
        $stmt = $conn->prepare("UPDATE projects SET title = ?, start_date = ?, end_date = ?, short_description = ?, phase = ? WHERE pid = ? AND uid = ?");
        if ($stmt->execute([$title, $start_date, $end_date, $short_description, $phase, $pid, $_SESSION['uid']])) {
            $message = "Project updated successfully!";
            $stmt = $conn->prepare("SELECT * FROM projects WHERE pid = ?");
            $stmt->execute([$pid]);
            $project = $stmt->fetch(PDO::FETCH_ASSOC);
            
        } else {
            $message = "Error: Could not update project.";
        }
    }
}
?>

<h2>Edit Project</h2>

<?php if (!empty($message)): ?>
    <p class="message <?= (strpos($message, 'success') !== false ? 'success' : 'error') ?>">
        <?= htmlspecialchars($message) ?>
    </p>
<?php endif; ?>

<div class="form-card">
    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
        
        <label for="title">Title</label>
        <input type="text" name="title" id="title"
               value="<?= htmlspecialchars($project['title']) ?>" required>
        <span class="error" id="titleError"></span><br><br>

        <label for="start_date">Start Date</label>
        <input type="date" name="start_date" id="start_date"
               value="<?= htmlspecialchars($project['start_date']) ?>" required>
        <span class="error" id="startDateError"></span><br><br>

        <label for="end_date">End Date</label>
        <input type="date" name="end_date" id="end_date"
               value="<?= htmlspecialchars($project['end_date']) ?>" required>
        <span class="error" id="endDateError"></span><br><br>

        <label for="short_description">Description</label>
        <textarea name="short_description" id="short_description"
                  rows="5" required><?= htmlspecialchars($project['short_description']) ?></textarea>
        <span class="error" id="descError"></span><br><br>

        <label for="phase">Phase</label>
        <select name="phase" id="phase" required>
            <?php foreach (['design','development','testing','deployment','complete'] as $p): ?>
                <option value="<?= $p ?>" <?= $project['phase'] == $p ? 'selected' : '' ?>><?= ucfirst($p) ?></option>
            <?php endforeach; ?>
        </select><br><br>

        <button type="submit">Update Project</button>
    </form>

    <form method="POST" style="margin-top:15px;" onsubmit="return confirm('Are you sure you want to delete this project?');">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
        <input type="hidden" name="delete_project" value="1">
        <button type="submit" style="background:#dc3545;color:#fff;">Delete Project</button>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
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

    title.addEventListener('input', validateTitle);
    shortDesc.addEventListener('input', validateDesc);
    startDate.addEventListener('change', validateStartDate);
    endDate.addEventListener('change', validateEndDate);

    form.addEventListener('submit', function(e) {
        if (!validateTitle() || !validateDesc() || !validateStartDate() || !validateEndDate()) {
            e.preventDefault();
        }
    });
});
</script>

<?php include 'includes/footer.php'; ?>