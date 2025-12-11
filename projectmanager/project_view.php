<?php
include 'config.php';
include 'includes/header.php';

if (!isset($_GET['pid']) || !is_numeric($_GET['pid'])) {
    echo "<p>Invalid project ID.</p>";
    include 'includes/footer.php';
    exit;
}

$pid = (int)$_GET['pid'];

// Fetch project with owner info
$stmt = $conn->prepare("SELECT projects.*, users.username, users.email 
                        FROM projects 
                        JOIN users ON projects.uid = users.uid 
                        WHERE pid = ?");
$stmt->execute([$pid]);
$project = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$project) {
    echo "<p>Project not found.</p>";
    include 'includes/footer.php';
    exit;
}
?>

<div class="project-card">
    <h2><?php echo htmlspecialchars($project['title']); ?></h2>
    <p><strong>Start Date:</strong> <?php echo htmlspecialchars($project['start_date']); ?></p>
    <p><strong>End Date:</strong> <?php echo htmlspecialchars($project['end_date']); ?></p>
    <p><strong>Phase:</strong> <?php echo htmlspecialchars($project['phase']); ?></p>
    <p><strong>Description:</strong> <?php echo htmlspecialchars($project['short_description']); ?></p>
    <p><strong>Owner Email:</strong> <?php echo htmlspecialchars($project['email']); ?></p>

    <?php if (isset($_SESSION['uid']) && $_SESSION['uid'] == $project['uid']): ?>
        <a href="project_edit.php?pid=<?php echo $project['pid']; ?>">Edit Project</a>
    <?php endif; ?>
</div>


<?php include 'includes/footer.php'; ?>
