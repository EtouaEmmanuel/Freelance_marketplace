<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_admin();
include __DIR__ . '/includes/header.php';

$message = '';
$message_type = 'success';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'update_user_role') {
        $user_id = (int)($_POST['user_id'] ?? 0);
        $role = $_POST['role'] ?? '';
        if (!in_array($role, ['client', 'freelancer'], true)) {
            $message = 'Invalid role selected.';
            $message_type = 'notice';
        } else {
            $check = $mysqli->prepare("SELECT role FROM users WHERE id = ?");
            $check->bind_param('i', $user_id);
            $check->execute();
            $result = $check->get_result();
            $user_row = $result ? $result->fetch_assoc() : null;
            if (!$user_row) {
                $message = 'User not found.';
                $message_type = 'notice';
            } elseif ($user_row['role'] === 'admin') {
                $message = 'Administrator role cannot be changed.';
                $message_type = 'notice';
            } else {
                $stmt = $mysqli->prepare("UPDATE users SET role = ? WHERE id = ?");
                $stmt->bind_param('si', $role, $user_id);
                $stmt->execute();
                $message = 'User role updated.';
            }
        }
    } elseif ($action === 'update_project_status') {
        $project_id = (int)($_POST['project_id'] ?? 0);
        $status = $_POST['status'] ?? '';
        if (!in_array($status, ['open', 'in_progress', 'completed'], true)) {
            $message = 'Invalid project status.';
            $message_type = 'notice';
        } else {
            $stmt = $mysqli->prepare("UPDATE projects SET status = ? WHERE id = ?");
            $stmt->bind_param('si', $status, $project_id);
            $stmt->execute();
            $message = 'Project status updated.';
        }
    } elseif ($action === 'update_bid_status') {
        $bid_id = (int)($_POST['bid_id'] ?? 0);
        $status = $_POST['status'] ?? '';
        if (!in_array($status, ['pending', 'accepted', 'rejected'], true)) {
            $message = 'Invalid bid status.';
            $message_type = 'notice';
        } else {
            $stmt = $mysqli->prepare("UPDATE bids SET status = ? WHERE id = ?");
            $stmt->bind_param('si', $status, $bid_id);
            $stmt->execute();
            $message = 'Bid status updated.';
        }
    } elseif ($action === 'update_payment_status') {
        $payment_id = (int)($_POST['payment_id'] ?? 0);
        $status = $_POST['status'] ?? '';
        if (!in_array($status, ['escrow', 'released', 'refunded'], true)) {
            $message = 'Invalid payment status.';
            $message_type = 'notice';
        } else {
            $stmt = $mysqli->prepare("UPDATE payments SET status = ? WHERE id = ?");
            $stmt->bind_param('si', $status, $payment_id);
            $stmt->execute();
            $message = 'Payment status updated.';
        }
    }
}

$users = [];
$user_result = $mysqli->query("SELECT id, full_name, email, role, created_at FROM users ORDER BY created_at DESC");
if ($user_result) {
    $users = $user_result->fetch_all(MYSQLI_ASSOC);
}

$projects = [];
$project_result = $mysqli->query("SELECT p.id, p.title, p.status, p.budget_min, p.budget_max, u.full_name AS client_name
    FROM projects p
    JOIN users u ON p.client_id = u.id
    ORDER BY p.created_at DESC");
if ($project_result) {
    $projects = $project_result->fetch_all(MYSQLI_ASSOC);
}

$bids = [];
$bid_result = $mysqli->query("SELECT b.id, b.bid_amount, b.delivery_days, b.status, p.title AS project_title, u.full_name AS freelancer_name
    FROM bids b
    JOIN projects p ON b.project_id = p.id
    JOIN users u ON b.freelancer_id = u.id
    ORDER BY b.created_at DESC");
if ($bid_result) {
    $bids = $bid_result->fetch_all(MYSQLI_ASSOC);
}

$payments = [];
$payment_result = $mysqli->query("SELECT p.id, p.amount, p.currency, p.method, p.status, pr.title AS project_title,
    payer.full_name AS payer_name, payee.full_name AS payee_name
    FROM payments p
    JOIN projects pr ON p.project_id = pr.id
    JOIN users payer ON p.payer_id = payer.id
    JOIN users payee ON p.payee_id = payee.id
    ORDER BY p.created_at DESC");
if ($payment_result) {
    $payments = $payment_result->fetch_all(MYSQLI_ASSOC);
}
?>
<section class="section container">
  <h2 class="section-title">Administrator Console</h2>
  <p>Manage the entire platform: users, projects, bids, and payments. The administrator role is unique and cannot be reassigned.</p>
  <?php if ($message): ?>
    <div class="<?php echo $message_type === 'success' ? 'success' : 'notice'; ?>">
      <?php echo htmlspecialchars($message); ?>
    </div>
  <?php endif; ?>

  <div class="grid" style="margin-top: 20px;">
    <div class="card">
      <h3>Platform overview</h3>
      <p>Total users: <?php echo count($users); ?></p>
      <p>Total projects: <?php echo count($projects); ?></p>
      <p>Total bids: <?php echo count($bids); ?></p>
      <p>Total payments: <?php echo count($payments); ?></p>
    </div>
    <div class="card">
      <h3>Admin access</h3>
      <p>Email: admin123@gmail.com</p>
      <p>Username: admin123</p>
      <p>Password: 12345678</p>
    </div>
  </div>
</section>

<section class="section container">
  <h2 class="section-title">Users</h2>
  <table class="table">
    <thead>
      <tr>
        <th>Name</th>
        <th>Email</th>
        <th>Role</th>
        <th>Created</th>
        <th>Update role</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($users as $user): ?>
        <tr>
          <td><?php echo htmlspecialchars($user['full_name']); ?></td>
          <td><?php echo htmlspecialchars($user['email']); ?></td>
          <td><?php echo htmlspecialchars(ucfirst($user['role'])); ?></td>
          <td><?php echo htmlspecialchars($user['created_at']); ?></td>
          <td>
            <?php if ($user['role'] === 'admin'): ?>
              <span class="badge">Administrator</span>
            <?php else: ?>
              <form method="post">
                <input type="hidden" name="action" value="update_user_role" />
                <input type="hidden" name="user_id" value="<?php echo (int)$user['id']; ?>" />
                <select name="role">
                  <option value="client" <?php echo $user['role'] === 'client' ? 'selected' : ''; ?>>Client</option>
                  <option value="freelancer" <?php echo $user['role'] === 'freelancer' ? 'selected' : ''; ?>>Freelancer</option>
                </select>
                <button class="btn secondary" type="submit">Save</button>
              </form>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</section>

<section class="section container">
  <h2 class="section-title">Projects</h2>
  <table class="table">
    <thead>
      <tr>
        <th>Project</th>
        <th>Client</th>
        <th>Budget</th>
        <th>Status</th>
        <th>Update status</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($projects as $project): ?>
        <tr>
          <td><?php echo htmlspecialchars($project['title']); ?></td>
          <td><?php echo htmlspecialchars($project['client_name']); ?></td>
          <td>$<?php echo number_format($project['budget_min']); ?> - $<?php echo number_format($project['budget_max']); ?></td>
          <td><?php echo htmlspecialchars(ucfirst($project['status'])); ?></td>
          <td>
            <form method="post">
              <input type="hidden" name="action" value="update_project_status" />
              <input type="hidden" name="project_id" value="<?php echo (int)$project['id']; ?>" />
              <select name="status">
                <option value="open" <?php echo $project['status'] === 'open' ? 'selected' : ''; ?>>Open</option>
                <option value="in_progress" <?php echo $project['status'] === 'in_progress' ? 'selected' : ''; ?>>In progress</option>
                <option value="completed" <?php echo $project['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
              </select>
              <button class="btn secondary" type="submit">Save</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</section>

<section class="section container">
  <h2 class="section-title">Bids</h2>
  <table class="table">
    <thead>
      <tr>
        <th>Project</th>
        <th>Freelancer</th>
        <th>Bid</th>
        <th>Delivery</th>
        <th>Status</th>
        <th>Update status</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($bids as $bid): ?>
        <tr>
          <td><?php echo htmlspecialchars($bid['project_title']); ?></td>
          <td><?php echo htmlspecialchars($bid['freelancer_name']); ?></td>
          <td>$<?php echo number_format($bid['bid_amount']); ?></td>
          <td><?php echo (int)$bid['delivery_days']; ?> days</td>
          <td><?php echo htmlspecialchars(ucfirst($bid['status'])); ?></td>
          <td>
            <form method="post">
              <input type="hidden" name="action" value="update_bid_status" />
              <input type="hidden" name="bid_id" value="<?php echo (int)$bid['id']; ?>" />
              <select name="status">
                <option value="pending" <?php echo $bid['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                <option value="accepted" <?php echo $bid['status'] === 'accepted' ? 'selected' : ''; ?>>Accepted</option>
                <option value="rejected" <?php echo $bid['status'] === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
              </select>
              <button class="btn secondary" type="submit">Save</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</section>

<section class="section container">
  <h2 class="section-title">Payments</h2>
  <table class="table">
    <thead>
      <tr>
        <th>Project</th>
        <th>Payer</th>
        <th>Payee</th>
        <th>Amount</th>
        <th>Status</th>
        <th>Update status</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($payments as $payment): ?>
        <tr>
          <td><?php echo htmlspecialchars($payment['project_title']); ?></td>
          <td><?php echo htmlspecialchars($payment['payer_name']); ?></td>
          <td><?php echo htmlspecialchars($payment['payee_name']); ?></td>
          <td><?php echo htmlspecialchars($payment['currency']); ?> <?php echo number_format($payment['amount'], 2); ?></td>
          <td><?php echo htmlspecialchars(ucfirst($payment['status'])); ?></td>
          <td>
            <form method="post">
              <input type="hidden" name="action" value="update_payment_status" />
              <input type="hidden" name="payment_id" value="<?php echo (int)$payment['id']; ?>" />
              <select name="status">
                <option value="escrow" <?php echo $payment['status'] === 'escrow' ? 'selected' : ''; ?>>Escrow</option>
                <option value="released" <?php echo $payment['status'] === 'released' ? 'selected' : ''; ?>>Released</option>
                <option value="refunded" <?php echo $payment['status'] === 'refunded' ? 'selected' : ''; ?>>Refunded</option>
              </select>
              <button class="btn secondary" type="submit">Save</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
