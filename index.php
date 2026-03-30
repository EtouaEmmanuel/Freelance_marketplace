<?php
require_once __DIR__ . '/includes/db.php';
include __DIR__ . '/includes/header.php';

$projects = [];
$result = $mysqli->query("SELECT p.id, p.title, p.budget_min, p.budget_max, u.full_name FROM projects p JOIN users u ON p.client_id = u.id ORDER BY p.created_at DESC LIMIT 3");
if ($result) {
    $projects = $result->fetch_all(MYSQLI_ASSOC);
}
?>
<section class="hero container">
  <div>
    <span class="badge">Trusted by 1,200+ teams</span>
    <h1>Find standout freelance talent for projects that matter.</h1>
    <p>TaskWeave blends curated profiles, transparent bids, and milestone payments so clients and freelancers can focus on exceptional work.</p>
    <div>
      <a class="btn" href="projects.php">Browse Projects</a>
      <a class="btn secondary" href="register.php">Join as Freelancer</a>
    </div>
  </div>
  <div class="hero-card">
    <img src="assets/img/hero.jpg" alt="Modern workspace" />
  </div>
</section>

<section class="section container">
  <h2 class="section-title">How it works</h2>
  <div class="grid">
    <div class="card">
      <h3>Post a project</h3>
      <p>Define scope, budget, and timeline. Our matching engine brings you qualified freelancers.</p>
    </div>
    <div class="card">
      <h3>Review bids</h3>
      <p>Compare proposals, chat in-platform, and select the right talent with confidence.</p>
    </div>
    <div class="card">
      <h3>Pay with milestones</h3>
      <p>Release payments only when work is approved. Every project includes escrow protection.</p>
    </div>
  </div>
</section>

<section class="section container">
  <h2 class="section-title">Featured projects</h2>
  <div class="grid">
    <?php foreach ($projects as $project): ?>
      <div class="card">
        <h3><?php echo htmlspecialchars($project['title']); ?></h3>
        <p>Client: <?php echo htmlspecialchars($project['full_name']); ?></p>
        <p>Budget: $<?php echo number_format($project['budget_min']); ?> - $<?php echo number_format($project['budget_max']); ?></p>
        <a class="btn secondary" href="project.php?id=<?php echo $project['id']; ?>">View details</a>
      </div>
    <?php endforeach; ?>
  </div>
</section>

<section class="section container">
  <div class="banner">
    <div>
      <h2>Communication that keeps projects moving.</h2>
      <p>Track milestones, share files, and keep everything in one thread.</p>
      <a class="btn" href="chat.php">Open Chat</a>
    </div>
    <img src="assets/img/collaboration.jpg" alt="Team collaboration" />
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
