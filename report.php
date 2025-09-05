<?php
require_once 'guard.php';
require_auth();
require_once 'db.php';

// Fetch report details with reporter username
if (!isset($_GET['id'])) {
    die("Report not found.");
}

$stmt = $pdo->prepare("
    SELECT r.*, u.username AS reporter
    FROM reports r
    JOIN users u ON r.user_id = u.id
    WHERE r.id = ?
");
$stmt->execute([$_GET['id']]);
$report = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$report) {
    die("Report not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Report Details – Barangay Majada</title>
  <link href="style.css" rel="stylesheet">
  <style>
    .report-card {
      max-width: 600px;
      margin: 30px auto;
      background: #fff;
      border-radius: 12px;
      padding: 20px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }
    .report-card img {
      max-width: 100%;
      border-radius: 8px;
      margin-bottom: 15px;
    }
    .badge {
      display: inline-block;
      padding: 4px 10px;
      border-radius: 8px;
      font-size: 12px;
      font-weight: bold;
      margin-right: 6px;
    }
    .badge.lost { background: #fee2e2; color: #b91c1c; }
    .badge.found { background: #dcfce7; color: #166534; }
    .badge.pending { background: #fef3c7; color: #92400e; }
    .badge.verified { background: #dbeafe; color: #1e40af; }
  </style>
</head>
<body>
  <div class="app">
    <?php include 'sidebar.php'; ?>
    <main class="main">
      <?php include 'topbar.php'; ?>

      <div class="content">
        <div class="report-card">
          <!-- Status + Type -->
          <div style="margin-bottom:10px;">
            <span class="badge <?= strtolower($report['type']) ?>">
              <?= htmlspecialchars($report['type']) ?>
            </span>
            <span class="badge <?= strtolower($report['status']) ?>">
              <?= htmlspecialchars($report['status']) ?>
            </span>
          </div>

          <!-- Title -->
          <h2><?= htmlspecialchars($report['title']) ?></h2>

          <!-- Image -->
          <?php if (!empty($report['image_path'])): ?>
            <img src="<?= htmlspecialchars($report['image_path']) ?>" alt="Report image">
          <?php endif; ?>

          <!-- Description -->
          <p><?= nl2br(htmlspecialchars($report['description'])) ?></p>

          <!-- Details -->
          <p><strong>Location:</strong> <?= htmlspecialchars($report['location']) ?></p>
          <p><strong>Date:</strong> <?= htmlspecialchars($report['date_lost']) ?></p>
          <p><strong>Category:</strong> <?= htmlspecialchars($report['category']) ?></p>
          <p><strong>Reported by:</strong> <?= htmlspecialchars($report['reporter']) ?></p>
        </div>
      </div>
    </main>
  </div>
</body>
</html>
