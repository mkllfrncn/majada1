<?php
require_once 'guard.php';
require_auth();
require_once 'db.php';

// Only admin can access
if ($_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php");
    exit;
}

// Handle actions (Verify / Reject)
if (isset($_POST['action'], $_POST['report_id'])) {
    $report_id = (int)$_POST['report_id'];
    $action = $_POST['action'];

    if ($action === 'verify') {
        $stmt = $pdo->prepare("UPDATE reports SET status='Verified' WHERE id=?");
        $stmt->execute([$report_id]);
    } elseif ($action === 'reject') {
        $stmt = $pdo->prepare("UPDATE reports SET status='Rejected' WHERE id=?");
        $stmt->execute([$report_id]);
    }
}

// Filter by status
$status = $_GET['status'] ?? 'Pending';
$stmt = $pdo->prepare("
    SELECT r.*, u.username 
    FROM reports r 
    JOIN users u ON r.user_id = u.id 
    WHERE r.status = ?
    ORDER BY r.created_at DESC
");
$stmt->execute([$status]);
$reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Manage Reports – Admin</title>
  <link href="style.css" rel="stylesheet">
  <style>
    .report-card {
      border: 1px solid #ddd;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
      display: flex;
      flex-direction: column;
    }
    .report-card img {
      width: 100%;
      height: 160px;
      object-fit: cover;
    }
    .report-body {
      padding: 12px;
    }
    .badge {
      display: inline-block;
      padding: 2px 8px;
      font-size: 12px;
      border-radius: 6px;
      margin-right: 4px;
    }
    .badge.lost { background: #fdd; color: #900; }
    .badge.found { background: #dfd; color: #090; }
    .badge.item { background: #eee; color: #333; }
    .badge.pending { background: #ffecb3; color: #b58900; }
    .badge.verified { background: #c8e6c9; color: #2e7d32; }
    .badge.rejected { background: #ffcdd2; color: #c62828; }
    .actions { display: flex; gap: 8px; margin-top: 8px; }
  </style>
</head>
<body>
<div class="app">
  <?php include 'sidebar.php'; ?>
  <main class="main">
    <?php include 'topbar.php'; ?>

    <div class="content">
      <h2 class="hi">Manage Reports</h2>
      <p class="subtle">Review and verify submitted reports</p>

      <!-- Filter -->
      <form method="get" style="margin-bottom:16px;">
        <label>Filter by Status</label>
        <select name="status" onchange="this.form.submit()" class="input">
          <option value="Pending" <?= $status=='Pending'?'selected':'' ?>>Pending Review</option>
          <option value="Verified" <?= $status=='Verified'?'selected':'' ?>>Verified</option>
          <option value="Rejected" <?= $status=='Rejected'?'selected':'' ?>>Rejected</option>
        </select>
      </form>

      <!-- Reports -->
      <div class="grid">
        <?php if (empty($reports)): ?>
          <div class="empty">
            <div style="font-size:40px;">📭</div>
            <div>No reports found</div>
          </div>
        <?php else: ?>
          <?php foreach ($reports as $r): ?>
            <div class="report-card">
              <?php if ($r['image_path']): ?>
                <img src="<?= htmlspecialchars($r['image_path']) ?>" alt="Report image">
              <?php endif; ?>
              <div class="report-body">
                <div>
                  <span class="badge <?= strtolower($r['type']) ?>"><?= htmlspecialchars($r['type']) ?></span>
                  <span class="badge item"><?= htmlspecialchars($r['category']) ?></span>
                  <span class="badge <?= strtolower($r['status']) ?>"><?= htmlspecialchars($r['status']) ?></span>
                </div>
                <h4><?= htmlspecialchars($r['title']) ?></h4>
                <p><?= nl2br(htmlspecialchars($r['description'])) ?></p>
                <p><strong>Location:</strong> <?= htmlspecialchars($r['location']) ?></p>
                <p><strong>Date:</strong> <?= htmlspecialchars($r['date_lost']) ?></p>
                <p><strong>Reported by:</strong> <?= htmlspecialchars($r['username']) ?></p>

                <div class="actions">
                  <a class="btn" href="report.php?id=<?= $r['id'] ?>">👁 View</a>
                  <?php if ($r['status'] == 'Pending'): ?>
                    <form method="post" style="display:inline;">
                      <input type="hidden" name="report_id" value="<?= $r['id'] ?>">
                      <button class="btn primary" type="submit" name="action" value="verify">✔ Verify</button>
                      <button class="btn danger" type="submit" name="action" value="reject">✖ Reject</button>
                    </form>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>
  </main>
</div>
</body>
</html>
