<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_login();
include __DIR__ . '/includes/header.php';

$user = current_user();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $bio = trim($_POST['bio'] ?? '');
    $skills = trim($_POST['skills'] ?? '');
    $location = trim($_POST['location'] ?? '');

    $stmt = $mysqli->prepare("UPDATE users SET title = ?, bio = ?, skills = ?, location = ? WHERE id = ?");
    $stmt->bind_param('ssssi', $title, $bio, $skills, $location, $user['id']);
    $stmt->execute();
    $message = 'Profile updated.';
}

$stmt = $mysqli->prepare("SELECT full_name, email, role, title, bio, skills, location FROM users WHERE id = ?");
$stmt->bind_param('i', $user['id']);
$stmt->execute();
$result = $stmt->get_result();
$profile = $result ? $result->fetch_assoc() : [];
?>
<section class="section container">
  <h2 class="section-title">Profile</h2>
  <?php if ($message): ?>
    <div class="success"><?php echo htmlspecialchars($message); ?></div>
  <?php endif; ?>
  <div class="grid">
    <div class="card">
      <h3><?php echo htmlspecialchars($profile['full_name'] ?? ''); ?></h3>
      <p><?php echo htmlspecialchars($profile['email'] ?? ''); ?></p>
      <p>Role: <?php echo htmlspecialchars(ucfirst($profile['role'] ?? '')); ?></p>
      <p><?php echo htmlspecialchars($profile['title'] ?? ''); ?></p>
      <p><?php echo htmlspecialchars($profile['location'] ?? ''); ?></p>
    </div>
    <div class="card">
      <h3>Edit profile</h3>
      <form class="form" method="post">
        <input type="text" name="title" placeholder="Headline" value="<?php echo htmlspecialchars($profile['title'] ?? ''); ?>" />
        <input type="text" name="location" placeholder="Location" value="<?php echo htmlspecialchars($profile['location'] ?? ''); ?>" />
        <input type="text" name="skills" placeholder="Skills (comma separated)" value="<?php echo htmlspecialchars($profile['skills'] ?? ''); ?>" />
        <textarea name="bio" rows="4" placeholder="Short bio"><?php echo htmlspecialchars($profile['bio'] ?? ''); ?></textarea>
        <button class="btn" type="submit">Save changes</button>
      </form>
    </div>
  </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
