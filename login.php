<?php
require_once 'guard.php';
require_once 'db.php';

// If already logged in go to dashboard
if (is_logged_in()) { header("Location: dashboard.php"); exit; }  

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = trim($_POST['email'] ?? '');
  $pass  = trim($_POST['password'] ?? '');

  $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
  $stmt->execute([$email]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($user && password_verify($pass, $user['password'])) {
      $_SESSION['user_id'] = $user['id'];
      $_SESSION['username'] = $user['username'];
      $_SESSION['role'] = $user['role'];
      header("Location: dashboard.php"); 
      exit;
  } else {
      $error = "Invalid email or password. Please try again.";
  }
}

// Guest mode
if (isset($_GET['guest'])) {
  $_SESSION['username'] = 'Guest';
  $_SESSION['role'] = 'guest';
  header("Location: dashboard.php"); exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Barangay Majada – Lost & Found</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="style.css" rel="stylesheet">
</head>
<body class="auth">
  <div class="panel">
    <div class="center">
      <div class="logo" style="margin:0 auto 10px;width:54px;height:54px;border-radius:16px;background:var(--brand);display:grid;place-items:center;color:#fff;font-weight:800;">🔎</div>
      <h1>Barangay Majada</h1>
      <div class="subtle">Lost &amp; Found Information System</div>
    </div>

    <form method="post" style="margin-top:14px">
      <?php if ($error): ?>
        <div class="card" style="border-color:#fecaca;background:#fff1f2;color:#991b1b;margin-bottom:10px;">
          <?= htmlspecialchars($error) ?>
        </div>
      <?php endif; ?>

      <label>Email Address</label>
      <input class="input" type="email" name="email" placeholder="Enter your email" required>

      <label>Password</label>
      <input class="input" type="password" name="password" placeholder="Enter your password" required>

      <div class="actions" style="margin-top:6px">
        <button class="btn primary" type="submit" style="flex:1">Sign In</button>
        <a class="btn" href="login.php?guest=1" title="Limited access">Continue as Guest</a>
      </div>
    </form>

    <div class="meta">Don't have an account? <a class="link" href="register.php">Register here</a></div>
    <div class="meta" style="margin-top:8px;">© <?= date('Y') ?> Barangay Majada. All rights reserved.</div>
  </div>
</body>
</html>
