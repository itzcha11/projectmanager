<?php
include 'config.php';  // Provides $conn (PDO) for database access
include 'includes/header.php'; // Header includes session start and CSRF token generation

// Handle search request
$search = ""; // Default search value
$query = "SELECT projects.pid, projects.title, projects.start_date, projects.short_description FROM projects"; // Base query: select essential project info

// If the user submitted a search term via GET
if (isset($_GET['search']) && $_GET['search'] != "") {
    $search = $_GET['search'];
    $query .= " WHERE projects.title LIKE :search OR projects.start_date LIKE :search";
}

// Prepares query and executes query
$stmt = $conn->prepare($query);
if ($search != "") {
    $stmt->execute(['search' => "%$search%"]);
} else {
    $stmt->execute();
}

// Fetch all projects
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>All Projects</h2>

<form method="GET" action="index.php" class="search-form">
    <input type="text" name="search" placeholder="Search by title or start date..." value="<?php echo htmlspecialchars($search); ?>">
    <button type="submit">Search</button>
</form>

<!-- -----------------------------
     Display project list
     ----------------------------- -->
     
<?php if (count($projects) > 0): ?>
<table border="1" cellpadding="8">
    <tr>
        <th>Title</th>
        <th>Start Date</th>
        <th>Description</th>
        <th>Details</th>
    </tr>

    <?php foreach ($projects as $project): ?>
    <tr>
        <td><?php echo htmlspecialchars($project['title']); ?></td>
        <td><?php echo htmlspecialchars($project['start_date']); ?></td>
        <td><?php echo htmlspecialchars($project['short_description']); ?></td>
        <td><a href="project_view.php?pid=<?php echo $project['pid']; ?>">View</a></td>
    </tr>
    <?php endforeach; ?>
</table>
<?php else: ?>
    <p class="no-results">No projects found matching your search.</p>
<?php endif; ?>


<?php include 'includes/footer.php'; ?>
