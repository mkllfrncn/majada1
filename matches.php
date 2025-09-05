<?php
require_once 'db.php';
require_once 'guard.php';
require_auth();

if ($_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php");
    exit;
}

// Get status filter
$status = $_POST['status'] ?? 'Pending Verification';

// Fetch matches from DB (Lost ↔ Found pairs with same category)
$sql = "
    SELECT 
        l.id   AS lost_id,
        l.title AS lost_title,
        f.id   AS found_id,
        f.title AS found_title,
        l.status,
        l.date_lost_found AS date
    FROM reports l
    JOIN reports f 
        ON l.category = f.category 
        AND l.type = 'Lost' 
        AND f.type = 'Found'
    WHERE l.status = :status
    ORDER BY l.date_lost_found DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute(['status' => $status]);
$matches = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Matches – Barangay Majada</title>
  <link href="style.css" rel="stylesheet">
</head>
<body>
<div class="app">
  <?php include 'sidebar.php'; ?>
  <main class="main">
    <?php include 'topbar.php'; ?>

    <div class="content">
      <h2 class="hi">Match Management</h2>
      <p class="subtle">Manage connections between lost and found reports</p>

      <!-- Filter -->
      <form method="post" class="section">
        <label>Filter by Status:</label>
        <select class="input" name="status" onchange="this.form.submit()">
          <option <?= $status === 'Pending Verification' ? 'selected' : '' ?>>Pending Verification</option>
          <option <?= $status === 'Confirmed' ? 'selected' : '' ?>>Confirmed</option>
        </select>
      </form>

      <!-- Results -->
      <div class="section">
        <?php if (empty($matches)): ?>
          <div class="empty">
            <div style="font-size:46px;">👥</div>
            <div>No matches found</div>
          </div>
        <?php else: ?>
          <div class="grid">
            <?php foreach ($matches as $m): ?>
              <div class="card">
                <h4><?= htmlspecialchars($m['lost_title']) ?> ↔ <?= htmlspecialchars($m['found_title']) ?></h4>
                <div>
                  Status: <?= htmlspecialchars($m['status']) ?> • 
                  Date: <?= htmlspecialchars($m['date'] ?? '-') ?>
                </div>
                <a class="btn primary" href="match.php?id=<?= $m['lost_id'] ?>">Review</a>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </main>
</div>
</body>
</html>
