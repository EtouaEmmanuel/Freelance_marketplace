<?php
require_once __DIR__ . '/includes/db.php';
include __DIR__ . '/includes/header.php';

$projects = [];
$result = $mysqli->query("SELECT p.id, p.title, p.description, p.budget_min, p.budget_max, p.status, u.full_name FROM projects p JOIN users u ON p.client_id = u.id ORDER BY p.created_at DESC");
if ($result) {
    $projects = $result->fetch_all(MYSQLI_ASSOC);
}
?>
<section class="section container">
  <h2 class="section-title">Live projects</h2>
  <div class="grid">
    <?php foreach ($projects as $project): ?>
      <div class="card">
        <span class="badge"><?php echo htmlspecialchars(ucfirst($project['status'])); ?></span>
        <h3><?php echo htmlspecialchars($project['title']); ?></h3>
        <p><?php echo htmlspecialchars(substr($project['description'], 0, 120)); ?>...</p>
        <p>Client: <?php echo htmlspecialchars($project['full_name']); ?></p>
        <p>Budget: $<?php echo number_format($project['budget_min']); ?> - $<?php echo number_format($project['budget_max']); ?></p>
        <a class="btn secondary" href="project.php?id=<?php echo $project['id']; ?>">View & Bid</a>
      </div>
    <?php endforeach; ?>
  </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
