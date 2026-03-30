<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_login();
include __DIR__ . '/includes/header.php';

$user = current_user();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $project_id = (int)($_POST['project_id'] ?? 0);
    $amount = (float)($_POST['amount'] ?? 0);
    $method = $_POST['method'] ?? 'card';
    $status = $_POST['status'] ?? 'escrow';
    $payee_id = (int)($_POST['payee_id'] ?? 0);

    if ($project_id && $amount > 0 && $payee_id) {
        $stmt = $mysqli->prepare("INSERT INTO payments (project_id, payer_id, payee_id, amount, currency, method, status) VALUES (?, ?, ?, ?, 'USD', ?, ?)");
        $stmt->bind_param('iiidss', $project_id, $user['id'], $payee_id, $amount, $method, $status);
        $stmt->execute();
        $message = 'Payment entry created.';
    } else {
        $message = 'Please complete payment details.';
    }
}

$payments = [];
$stmt = $mysqli->prepare("SELECT p.amount, p.currency, p.status, p.method, pr.title, u.full_name AS payee
    FROM payments p
    JOIN projects pr ON p.project_id = pr.id
    JOIN users u ON p.payee_id = u.id
    WHERE p.payer_id = ? OR p.payee_id = ?");
$stmt->bind_param('ii', $user['id'], $user['id']);
$stmt->execute();
$result = $stmt->get_result();
if ($result) {
    $payments = $result->fetch_all(MYSQLI_ASSOC);
}

$payees = [];
$payee_result = $mysqli->query("SELECT id, full_name FROM users WHERE role = 'freelancer'");
if ($payee_result) {
    $payees = $payee_result->fetch_all(MYSQLI_ASSOC);
}

$project_result = $mysqli->query("SELECT id, title FROM projects");
$projects = $project_result ? $project_result->fetch_all(MYSQLI_ASSOC) : [];
?>
<section class="section container">
  <h2 class="section-title">Payments</h2>
  <div class="grid">
    <div class="card">
      <h3>Recent transactions</h3>
      <?php if (empty($payments)): ?>
        <p>No payments yet.</p>
      <?php else: ?>
        <table class="table">
          <thead>
            <tr>
              <th>Project</th>
              <th>Payee</th>
              <th>Amount</th>
              <th>Status</th>
              <th>Method</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($payments as $payment): ?>
              <tr>
                <td><?php echo htmlspecialchars($payment['title']); ?></td>
                <td><?php echo htmlspecialchars($payment['payee']); ?></td>
                <td><?php echo htmlspecialchars($payment['currency']); ?> <?php echo number_format($payment['amount'], 2); ?></td>
                <td><?php echo htmlspecialchars(ucfirst($payment['status'])); ?></td>
                <td><?php echo htmlspecialchars(ucfirst($payment['method'])); ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>
    <div class="card">
      <h3>Create a payment</h3>
      <?php if ($message): ?>
        <div class="success"><?php echo htmlspecialchars($message); ?></div>
      <?php endif; ?>
      <form class="form" method="post">
        <select name="project_id" required>
          <option value="">Select project</option>
          <?php foreach ($projects as $project): ?>
            <option value="<?php echo $project['id']; ?>"><?php echo htmlspecialchars($project['title']); ?></option>
          <?php endforeach; ?>
        </select>
        <select name="payee_id" required>
          <option value="">Select freelancer</option>
          <?php foreach ($payees as $payee): ?>
            <option value="<?php echo $payee['id']; ?>"><?php echo htmlspecialchars($payee['full_name']); ?></option>
          <?php endforeach; ?>
        </select>
        <input type="number" step="0.01" name="amount" placeholder="Amount" required />
        <select name="method">
          <option value="card">Card</option>
          <option value="bank">Bank transfer</option>
          <option value="wallet">Wallet</option>
        </select>
        <select name="status">
          <option value="escrow">Escrow</option>
          <option value="released">Released</option>
          <option value="refunded">Refunded</option>
        </select>
        <button class="btn" type="submit">Add payment</button>
      </form>
    </div>
  </div>
</section>

<section class="section container">
  <div class="banner">
    <div>
      <h2>Payments with peace of mind.</h2>
      <p>Escrow holds funds until milestones are approved, keeping both sides protected.</p>
    </div>
    <img src="assets/img/payments.jpg" alt="Payment" />
  </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
