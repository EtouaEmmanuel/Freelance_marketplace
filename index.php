<?php
require_once __DIR__ . '/includes/db.php';
include __DIR__ . '/includes/header.php';

$projects = [];
$result = $mysqli->query("SELECT p.id, p.title, p.budget_min, p.budget_max, u.full_name FROM projects p JOIN users u ON p.client_id = u.id ORDER BY p.created_at DESC LIMIT 3");
if ($result) {
    $projects = $result->fetch_all(MYSQLI_ASSOC);
}
?>
<section class="hero-neo">
  <div class="container hero-neo-inner">
    <div class="hero-neo-copy">
      <span class="pill">Curated talent, verified workflows</span>
      <h1>Launch the next version of your product with freelancers who ship.</h1>
      <p>TaskWeave pairs high-signal profiles with a structured delivery flow, so teams can move from brief to launch without losing momentum.</p>
      <div class="hero-neo-actions">
        <a class="btn" href="projects.php">Browse Projects</a>
        <a class="btn secondary" href="register.php">Join as Freelancer</a>
      </div>
      <div class="hero-neo-metrics">
        <div>
          <strong>48 hrs</strong>
          <span>Average match time</span>
        </div>
        <div>
          <strong>92%</strong>
          <span>On-time delivery</span>
        </div>
        <div>
          <strong>$4.2M</strong>
          <span>Processed in escrow</span>
        </div>
      </div>
    </div>
    <div class="hero-neo-panel">
      <div class="hero-neo-card">
        <div class="hero-neo-card-head">
          <div>
            <p class="hero-neo-eyebrow">Client brief</p>
            <h3>AI onboarding redesign</h3>
          </div>
          <span class="badge">Open</span>
        </div>
        <p>Looking for a designer + developer duo to refresh onboarding and build a responsive UI kit.</p>
        <div class="hero-neo-tags">
          <span>Product Design</span>
          <span>Frontend</span>
          <span>3 weeks</span>
        </div>
        <div class="hero-neo-footer">
          <div>
            <p class="hero-neo-eyebrow">Budget</p>
            <strong>$3.5k - $5k</strong>
          </div>
          <button class="btn secondary" type="button">Review Bids</button>
        </div>
      </div>
      <div class="hero-neo-chip">
        <img src="assets/img/hero.jpg" alt="Modern workspace" />
      </div>
    </div>
  </div>
</section>

<section class="section container">
  <h2 class="section-title">Onboarding flow</h2>
  <div class="onboarding">
    <div class="onboarding-track">
      <span>01</span>
      <span>02</span>
      <span>03</span>
      <span>04</span>
    </div>
    <div class="onboarding-grid">
      <div class="onboarding-card">
        <h3>Craft the brief</h3>
        <p>Define scope, budget, and outcomes. We translate it into a focused project card.</p>
      </div>
      <div class="onboarding-card">
        <h3>Shortlist talent</h3>
        <p>Review curated bids, compare portfolios, and start a chat with top matches.</p>
      </div>
      <div class="onboarding-card">
        <h3>Lock milestones</h3>
        <p>Set delivery checkpoints and escrow amounts to keep both sides aligned.</p>
      </div>
      <div class="onboarding-card">
        <h3>Ship with confidence</h3>
        <p>Approve each milestone and release funds when work is delivered.</p>
      </div>
    </div>
    <div class="onboarding-cta">
      <div>
        <h3>Ready to start?</h3>
        <p>Create a project in minutes or join as a freelancer and start receiving invites.</p>
      </div>
      <div class="onboarding-actions">
        <a class="btn" href="projects.php">Post a Project</a>
        <a class="btn secondary" href="register.php">Join TaskWeave</a>
      </div>
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
