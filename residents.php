<?php
require_once 'db.php';
require_once 'guard.php';
require_auth();

if ($_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php");
    exit;
}

// Add new resident
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("INSERT INTO residents (full_name, address, contact_number, email) VALUES (?, ?, ?, ?)");
    $stmt->execute([$_POST['name'], $_POST['address'], $_POST['phone'], $_POST['email']]);
    header("Location: residents.php");
    exit;
}

$residents = $pdo->query("SELECT * FROM residents ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Residents – Barangay Majada</title>
  <link href="style.css" rel="stylesheet">
</head>
<body>
<div class="app">
  <?php include 'sidebar.php'; ?>
  <main class="main">
    <?php include 'topbar.php'; ?>
    <div class="content">
      <h2 class="hi">Residents</h2>
      <p class="subtle">Manage residents and their contact details</p>

      <!-- Add Resident 
      <div class="section">
        <h3>Add Resident</h3>
        <form method="post" style="display:grid;gap:8px;max-width:500px;">
          <input type="text" name="name" class="input" placeholder="Full Name" required>
          <input type="text" name="address" class="input" placeholder="Address">
          <input type="text" name="phone" class="input" placeholder="Phone">
          <input type="email" name="email" class="input" placeholder="Email">
          <button class="btn primary" type="submit">Save</button>
        </form>
      </div> -->

      <!-- List Residents -->
      <div class="section">
        <h3>Resident List</h3>
        <?php if (empty($residents)): ?>
          <div class="empty">No residents found</div>
        <?php else: ?>
          <table class="table">
            <tr>
              <th>Name</th>
              <th>Address</th>
              <th>Phone</th>
              <th>Email</th>
            </tr>
            <?php foreach ($residents as $r): ?>
              <tr>
                <td><?= htmlspecialchars($r['full_name']) ?></td>
                <td><?= htmlspecialchars($r['address']) ?></td>
                <td><?= htmlspecialchars($r['contact_number']) ?></td>
                <td><?= htmlspecialchars($r['email']) ?></td>
              </tr>
            <?php endforeach; ?>
          </table>
        <?php endif; ?>
      </div>
    </div>
  </main>
</div>
</body>
</html>
