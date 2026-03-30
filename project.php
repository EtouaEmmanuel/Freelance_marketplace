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
    <h2 class="section-title"><?php echo htmlspecialchars($project['title']); ?></h2>
    <p><?php echo htmlspecialchars($project['description']); ?></p>
    <p><strong>Client:</strong> <?php echo htmlspecialchars($project['full_name']); ?></p>
    <p><strong>Budget:</strong> $<?php echo number_format($project['budget_min']); ?> - $<?php echo number_format($project['budget_max']); ?></p>
    <p><strong>Status:</strong> <?php echo htmlspecialchars(ucfirst($project['status'])); ?></p>

    <?php if ($message): ?>
      <div class="<?php echo strpos($message, 'successfully') !== false ? 'success' : 'notice'; ?>">
        <?php echo htmlspecialchars($message); ?>
      </div>
    <?php endif; ?>

    <div class="section">
      <h3 class="section-title">Bids</h3>
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

    <div class="section">
      <h3 class="section-title">Place a bid</h3>
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
  <?php endif; ?>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
