<?php
require_once 'db.php';
session_start();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $address = trim($_POST['address']);
    $phone = trim($_POST['phone']);

    try {
        // Check if email already exists in users
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            $error = "Email already registered!";
        } else {
            // 1. Insert into users
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'resident')");
            $stmt->execute([$username, $email, $password]);

            // 2. Insert into residents
            $stmt = $pdo->prepare("INSERT INTO residents (full_name, address, contact_number, email) VALUES (?, ?, ?, ?)");
            $stmt->execute([$username, $address, $phone, $email]);

            // Auto login after register
            $_SESSION['username'] = $username;
            $_SESSION['role'] = 'resident';
            $_SESSION['email'] = $email;

            header("Location: dashboard.php");
            exit;
        }
    } catch (Exception $e) {
        $error = "Registration failed: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Register – Barangay Majada</title>
  <link href="style.css" rel="stylesheet">
</head>
<body class="auth">
  <div class="panel">
    <h2>Create Resident Account</h2>

    <?php if ($error): ?>
      <div style="color:red;"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post" style="display:grid;gap:10px;max-width:400px;">
      <input type="text" name="username" class="input" placeholder="Full Name" required>
      <input type="email" name="email" class="input" placeholder="Email" required>
      <input type="password" name="password" class="input" placeholder="Password" required>
      <input type="text" name="address" class="input" placeholder="Address" required>
      <input type="text" name="phone" class="input" placeholder="Phone Number" required>
      <button class="btn primary" type="submit">Register</button>
    </form>
  </div>
</body>
</html>
