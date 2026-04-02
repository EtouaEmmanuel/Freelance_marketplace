<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
include __DIR__ . '/includes/header.php';

$project_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && current_user()) {
    $user = current_user();
    if ($user['role'] !== 'freelancer') {
        $message = 'Only freelancers can place bids.';
    } else {
        $amount = (float)($_POST['bid_amount'] ?? 0);
        $days = (int)($_POST['delivery_days'] ?? 0);
        $cover = trim($_POST['cover_letter'] ?? '');

        if ($amount > 0 && $days > 0 && $cover !== '') {
            $stmt = $mysqli->prepare("INSERT INTO bids (project_id, freelancer_id, bid_amount, delivery_days, cover_letter, status) VALUES (?, ?, ?, ?, ?, 'pending')");
            $stmt->bind_param('iidds', $project_id, $user['id'], $amount, $days, $cover);
            $stmt->execute();
            $message = 'Bid submitted successfully.';
        } else {
            $message = 'Please fill in all bid fields.';
        }
    }
}

$project = null;
$stmt = $mysqli->prepare("SELECT p.*, u.full_name FROM projects p JOIN users u ON p.client_id = u.id WHERE p.id = ?");
$stmt->bind_param('i', $project_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result) {
    $project = $result->fetch_assoc();
}

$bids = [];
if ($project) {
    $bid_result = $mysqli->query("SELECT b.bid_amount, b.delivery_days, b.cover_letter, u.full_name FROM bids b JOIN users u ON b.freelancer_id = u.id WHERE b.project_id = {$project_id} ORDER BY b.created_at DESC");
    if ($bid_result) {
        $bids = $bid_result->fetch_all(MYSQLI_ASSOC);
    }
}
?>
<section class="section container">
  <?php if (!$project): ?>
    <p class="notice">Project not found.</p>
  <?php else: ?>
    <div class="project-hero">
      <div>
        <div class="project-hero-top">
          <span class="badge"><?php echo htmlspecialchars(ucfirst($project['status'])); ?></span>
          <span class="pill">Verified client</span>
        </div>
        <h2 class="project-title"><?php echo htmlspecialchars($project['title']); ?></h2>
        <p class="project-sub"><?php echo htmlspecialchars($project['description']); ?></p>
        <div class="project-meta">
          <span>Client: <?php echo htmlspecialchars($project['full_name']); ?></span>
          <span>Budget: $<?php echo number_format($project['budget_min']); ?> - $<?php echo number_format($project['budget_max']); ?></span>
          <span>Category: <?php echo htmlspecialchars($project['category']); ?></span>
        </div>
        <div class="project-highlights">
          <div>
            <strong>3</strong>
            <span>Active bids</span>
          </div>
          <div>
            <strong>4.8</strong>
            <span>Client rating</span>
          </div>
          <div>
            <strong>5 days</strong>
            <span>Avg response</span>
          </div>
        </div>
      </div>
      <div class="project-card">
        <h3>Project packages</h3>
        <p class="muted">Choose a lane to propose the best scope.</p>
        <div class="package-grid">
          <div class="package">
            <h4>Starter</h4>
            <p>Quick audit + delivery plan.</p>
            <strong>$<?php echo number_format($project['budget_min']); ?></strong>
            <span>Delivery in 5 days</span>
          </div>
          <div class="package featured">
            <h4>Growth</h4>
            <p>Design + build with weekly check-ins.</p>
            <strong>$<?php echo number_format(($project['budget_min'] + $project['budget_max']) / 2); ?></strong>
            <span>Delivery in 12 days</span>
          </div>
          <div class="package">
            <h4>Scale</h4>
            <p>Full implementation + handoff.</p>
            <strong>$<?php echo number_format($project['budget_max']); ?></strong>
            <span>Delivery in 20 days</span>
          </div>
        </div>
        <a class="btn" href="#bid-form">Submit a proposal</a>
        <p class="micro">Payments are protected by escrow milestones.</p>
      </div>
    </div>

    <?php if ($message): ?>
      <div class="<?php echo strpos($message, 'successfully') !== false ? 'success' : 'notice'; ?>">
        <?php echo htmlspecialchars($message); ?>
      </div>
    <?php endif; ?>

    <div class="project-layout">
      <div class="project-main">
        <div class="project-section">
          <h3>Overview</h3>
          <ul class="feature-list">
            <li>Project scope approved and ready to assign.</li>
            <li>Expect clear milestones and fast feedback cycles.</li>
            <li>Open to small team proposals.</li>
          </ul>
        </div>

        <div class="project-section">
          <h3>Frequently asked</h3>
          <div class="faq">
            <div>
              <strong>Can I suggest a different timeline?</strong>
              <p class="muted">Yes. Include it in your proposal and explain tradeoffs.</p>
            </div>
            <div>
              <strong>Is there a preferred stack?</strong>
              <p class="muted">Open to modern PHP, React, or polished no-code builds.</p>
            </div>
            <div>
              <strong>How many rounds of revisions?</strong>
              <p class="muted">Two planned feedback rounds with async check-ins.</p>
            </div>
          </div>
        </div>

        <div class="project-section">
          <h3>Recent bids</h3>
          <?php if (empty($bids)): ?>
            <p>No bids yet. Be the first to apply.</p>
          <?php else: ?>
            <table class="table">
              <thead>
                <tr>
                  <th>Freelancer</th>
                  <th>Bid</th>
                  <th>Delivery</th>
                  <th>Proposal</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($bids as $bid): ?>
                  <tr>
                    <td><?php echo htmlspecialchars($bid['full_name']); ?></td>
                    <td>$<?php echo number_format($bid['bid_amount']); ?></td>
                    <td><?php echo (int)$bid['delivery_days']; ?> days</td>
                    <td><?php echo htmlspecialchars($bid['cover_letter']); ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          <?php endif; ?>
        </div>
      </div>

      <aside class="project-side">
        <div class="seller-card">
          <h3>Client profile</h3>
          <div class="seller-meta">
            <div class="avatar"><?php echo strtoupper(substr($project['full_name'], 0, 1)); ?></div>
            <div>
              <strong><?php echo htmlspecialchars($project['full_name']); ?></strong>
              <span class="muted">Repeat client • 12 hires</span>
            </div>
          </div>
          <div class="seller-badges">
            <span>Fast replies</span>
            <span>Payments on time</span>
            <span>Detailed briefs</span>
          </div>
          <button class="btn secondary" type="button">Message client</button>
        </div>

        <div class="project-section" id="bid-form">
          <h3>Place a bid</h3>
          <?php if (!current_user()): ?>
            <p>Please <a href="login.php">log in</a> to submit a bid.</p>
          <?php else: ?>
            <form class="form" method="post">
              <input type="number" step="0.01" name="bid_amount" placeholder="Bid amount" required />
              <input type="number" name="delivery_days" placeholder="Delivery days" required />
              <textarea name="cover_letter" rows="4" placeholder="Short proposal" required></textarea>
              <button class="btn" type="submit">Submit bid</button>
            </form>
          <?php endif; ?>
        </div>
      </aside>
    </div>
  <?php endif; ?>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
