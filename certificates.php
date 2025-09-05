<?php

require_once 'db.php';

require_once 'guard.php';
require_auth();

if ($_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php");
    exit;
}

// Example certificates
$certs = []; // Replace with DB
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Certificates – Barangay Majada</title>
  <link href="style.css" rel="stylesheet">
</head>
<body>
<div class="app">
  <?php include 'sidebar.php'; ?>
  <main class="main">
    <?php include 'topbar.php'; ?>

    <div class="content">
      <h2 class="hi">Return Certificates</h2>
      <p class="subtle">View and download generated return certificates</p>

      <div class="section">
        <?php if (empty($certs)): ?>
          <div class="empty">
            <div style="font-size:46px;">🏅</div>
            <div>No certificates generated</div>
          </div>
        <?php else: ?>
          <div class="grid">
            <?php foreach ($certs as $c): ?>
              <div class="card">
                <h4>Certificate #<?= $c['id'] ?></h4>
                <div>Report: <?= htmlspecialchars($c['report']) ?></div>
                <a class="btn primary" href="download_cert.php?id=<?= $c['id'] ?>">Download</a>
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
