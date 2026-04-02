<?php
require_once __DIR__ . '/auth.php';
$user = current_user();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>TaskWeave Marketplace</title>
  <link rel="stylesheet" href="assets/css/style.css" />
</head>
<body>
<header>
  <div class="container nav">
    <a class="brand" href="index.php">TaskWeave</a>
    <nav class="nav-links">
      <a href="projects.php">Projects</a>
      <a href="chat.php">Chat</a>
      <a href="payments.php">Payments</a>
      <a href="profile.php">Profile</a>
      <?php if ($user && ($user['role'] ?? '') === 'admin'): ?>
        <a href="admin.php">Admin</a>
      <?php endif; ?>
      <?php if ($user): ?>
        <a href="logout.php">Logout</a>
      <?php else: ?>
        <a href="login.php">Login</a>
        <a href="register.php">Register</a>
      <?php endif; ?>
    </nav>
  </div>
</header>
<main>
