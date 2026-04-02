<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $mysqli->prepare("SELECT id, full_name, email, role, password_hash FROM users WHERE email = ?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result ? $result->fetch_assoc() : null;

    if ($user && password_verify($password, $user['password_hash'])) {
        if ($user['role'] === 'admin' && strtolower($user['email']) !== 'admin123@gmail.com') {
            $error = 'Invalid email or password.';
        } else {
        $_SESSION['user'] = [
            'id' => $user['id'],
            'full_name' => $user['full_name'],
            'email' => $user['email'],
            'role' => $user['role'],
        ];
        if ($user['role'] === 'admin') {
            header('Location: admin.php');
        } else {
            header('Location: projects.php');
        }
        exit;
        }
    }

    if ($error === '') {
        $error = 'Invalid email or password.';
    }
}

include __DIR__ . '/includes/header.php';
?>
<section class="section container">
  <h2 class="section-title">Login</h2>
  <!-- <p>Demo accounts: <strong>client@taskweave.test</strong> or <strong>freelancer@taskweave.test</strong>. Password: <strong>Password123!</strong></p> -->
  <p>No account yet? <a href="register.php"> Create one here</a>.</p>
  <?php if ($error): ?>
    <div class="notice"><?php echo htmlspecialchars($error); ?></div>
  <?php endif; ?>
  <form class="form" method="post">
    <input type="email" name="email" placeholder="Email" required />
    <input type="password" name="password" placeholder="Password" required />
    <button class="btn" type="submit">Sign in</button>
  </form>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
