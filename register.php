<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $role = $_POST['role'] ?? 'freelancer';
    $password = $_POST['password'] ?? '';

    if ($name === '' || $email === '' || $password === '') {
        $error = 'Please fill in all required fields.';
    } elseif (strtolower($email) === 'admin123@gmail.com') {
        $error = 'This email is reserved for the platform administrator.';
    } elseif (!in_array($role, ['freelancer', 'client'], true)) {
        $error = 'Invalid role selection.';
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $mysqli->prepare("INSERT INTO users (full_name, email, role, password_hash) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('ssss', $name, $email, $role, $hash);
        if ($stmt->execute()) {
            $success = 'Account created. You can now log in.';
        } else {
            $error = 'Unable to create account. Email may already exist.';
        }
    }
}

include __DIR__ . '/includes/header.php';
?>
<section class="section container">
  <h2 class="section-title">Create an account</h2>
  <?php if ($error): ?>
    <div class="notice"><?php echo htmlspecialchars($error); ?></div>
  <?php endif; ?>
  <?php if ($success): ?>
    <div class="success"><?php echo htmlspecialchars($success); ?></div>
  <?php endif; ?>
  <form class="form" method="post">
    <input type="text" name="full_name" placeholder="Full name" required />
    <input type="email" name="email" placeholder="Email" required />
    <select name="role">
      <option value="freelancer">Freelancer</option>
      <option value="client">Client</option>
    </select>
    <input type="password" name="password" placeholder="Password" required />
    <button class="btn" type="submit">Create account</button>
  </form>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
