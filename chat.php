<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_login();
include __DIR__ . '/includes/header.php';

$user = current_user();
$conversation_id = isset($_GET['conversation_id']) ? (int)$_GET['conversation_id'] : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = trim($_POST['message'] ?? '');
    $conversation_id = (int)($_POST['conversation_id'] ?? $conversation_id);

    if ($message !== '' && $conversation_id > 0) {
        $stmt = $mysqli->prepare("INSERT INTO messages (conversation_id, sender_id, message) VALUES (?, ?, ?)");
        $stmt->bind_param('iis', $conversation_id, $user['id'], $message);
        $stmt->execute();
    }
}

$conversations = [];
$stmt = $mysqli->prepare("SELECT c.id, p.title, u.full_name AS client_name, f.full_name AS freelancer_name
    FROM conversations c
    JOIN projects p ON c.project_id = p.id
    JOIN users u ON c.client_id = u.id
    JOIN users f ON c.freelancer_id = f.id
    WHERE c.client_id = ? OR c.freelancer_id = ?");
$stmt->bind_param('ii', $user['id'], $user['id']);
$stmt->execute();
$result = $stmt->get_result();
if ($result) {
    $conversations = $result->fetch_all(MYSQLI_ASSOC);
}

if ($conversation_id === 0 && !empty($conversations)) {
    $conversation_id = (int)$conversations[0]['id'];
}

$messages = [];
if ($conversation_id > 0) {
    $msg_stmt = $mysqli->prepare("SELECT m.message, m.created_at, u.full_name FROM messages m JOIN users u ON m.sender_id = u.id WHERE m.conversation_id = ? ORDER BY m.created_at ASC");
    $msg_stmt->bind_param('i', $conversation_id);
    $msg_stmt->execute();
    $msg_result = $msg_stmt->get_result();
    if ($msg_result) {
        $messages = $msg_result->fetch_all(MYSQLI_ASSOC);
    }
}
?>
<section class="section container">
  <h2 class="section-title">Chat</h2>
  <div class="grid">
    <div class="card">
      <h3>Conversations</h3>
      <ul>
        <?php foreach ($conversations as $conversation): ?>
          <li style="margin: 8px 0;">
            <a href="chat.php?conversation_id=<?php echo $conversation['id']; ?>">
              <?php echo htmlspecialchars($conversation['title']); ?>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
    <div class="card">
      <h3>Messages</h3>
      <?php if (empty($messages)): ?>
        <p>No messages yet.</p>
      <?php else: ?>
        <?php foreach ($messages as $message): ?>
          <p><strong><?php echo htmlspecialchars($message['full_name']); ?>:</strong> <?php echo htmlspecialchars($message['message']); ?></p>
        <?php endforeach; ?>
      <?php endif; ?>

      <form class="form" method="post">
        <input type="hidden" name="conversation_id" value="<?php echo $conversation_id; ?>" />
        <textarea name="message" rows="3" placeholder="Type a message" required></textarea>
        <button class="btn" type="submit">Send</button>
      </form>
    </div>
  </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
