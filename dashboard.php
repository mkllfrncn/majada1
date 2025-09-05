<?php
require_once 'guard.php';
require_auth();
require_once 'db.php';

$u = $_SESSION['username'] ?? 'User';
$role = $_SESSION['role'] ?? 'guest';

// Metrics
$metrics = [
  'total' => $pdo->query("SELECT COUNT(*) FROM reports")->fetchColumn(),
  'lost' => $pdo->query("SELECT COUNT(*) FROM reports WHERE type='Lost'")->fetchColumn(),
  'found' => $pdo->query("SELECT COUNT(*) FROM reports WHERE type='Found'")->fetchColumn(),
  'pending' => $pdo->query("SELECT COUNT(*) FROM reports WHERE status='Pending'")->fetchColumn(),
  'matches' => 0, // You can add real logic later
  'returned' => $pdo->query("SELECT COUNT(*) FROM reports WHERE status='Returned'")->fetchColumn(),
];

// Recent reports
$stmt = $pdo->query("SELECT r.id, r.type, r.category, r.title, r.description, r.location, r.date_lost, r.status, r.image_path, u.username 
                     FROM reports r 
                     JOIN users u ON r.user_id = u.id 
                     ORDER BY r.created_at DESC 
                     LIMIT 5");
$recent = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Dashboard – Barangay Majada</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="style.css" rel="stylesheet">
  <style>
    /* Modal */
    .modal {
      display: none;
      position: fixed;
      z-index: 1000;
      left: 0; top: 0;
      width: 100%; height: 100%;
      background: rgba(0,0,0,0.6);
      justify-content: center;
      align-items: center;
    }
    .modal-content {
      background: #fff;
      padding: 20px;
      border-radius: 12px;
      width: 500px;
      max-height: 80vh;
      overflow-y: auto;
      box-shadow: 0 4px 12px rgba(0,0,0,0.2);
      position: relative;
    }
    .modal-content img {
      max-width: 100%;
      border-radius: 8px;
      margin-bottom: 15px;
    }
    .close-btn {
      position: absolute;
      right: 15px; top: 10px;
      font-size: 20px;
      cursor: pointer;
    }
    .badge {
      display:inline-block;
      padding:4px 10px;
      border-radius:8px;
      font-size:12px;
      font-weight:bold;
      margin-right:6px;
    }
    .badge.lost { background:#fee2e2;color:#b91c1c; }
    .badge.found { background:#dcfce7;color:#166534; }
    .badge.pending { background:#fef3c7;color:#92400e; }
    .badge.returned { background:#e0f2fe;color:#1e40af; }
  </style>
</head>
<body>
  <div class="app">
    <!-- Sidebar -->
    <?php include 'sidebar.php'; ?>

    <!-- Main -->
    <main class="main">
      <?php include 'topbar.php'; ?>

      <div class="content">
        <h2 class="hi">Welcome back, <?= htmlspecialchars($u) ?>!</h2>
        <p class="subtle">Here's what's happening in the lost and found system</p>

        <!-- Cards -->
        <div class="grid">
          <div class="card"><div class="icon info">📄</div><h4>Total Reports</h4><div class="metric"><?= $metrics['total'] ?></div></div>
          <div class="card"><div class="icon rose">🔍</div><h4>Lost Items/Pets</h4><div class="metric"><?= $metrics['lost'] ?></div></div>
          <div class="card"><div class="icon mint">✅</div><h4>Found Items/Pets</h4><div class="metric"><?= $metrics['found'] ?></div></div>
          <div class="card"><div class="icon warn">🕒</div><h4>Pending Review</h4><div class="metric"><?= $metrics['pending'] ?></div></div>
          <div class="card"><div class="icon purple">📈</div><h4>Matches Made</h4><div class="metric"><?= $metrics['matches'] ?></div></div>
          <div class="card"><div class="icon warn">☑️</div><h4>Items Returned</h4><div class="metric"><?= $metrics['returned'] ?></div></div>
        </div>

        <!-- Recent Reports -->
        <div class="section">
          <div class="hd">Recent Reports</div>
          <div class="bd">
            <?php if (empty($recent)): ?>
              <div class="empty">
                <div style="font-size:46px;line-height:1">🗂️</div>
                <div style="margin-top:8px">No recent reports to display</div>
              </div>
            <?php else: ?>
              <div class="grid">
                <?php foreach ($recent as $r): ?>
                  <div class="card">
                    <h4><?= htmlspecialchars($r['type']) ?> • <?= htmlspecialchars($r['title']) ?></h4>
                    <div style="color:var(--muted); margin-bottom:8px;">
                      Status: <?= htmlspecialchars($r['status']) ?> • Date: <?= htmlspecialchars($r['date_lost']) ?>
                    </div>
                    <button class="btn" onclick='openModal(<?= json_encode($r) ?>)'>👁 View</button>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </main>
  </div>

  <!-- Modal -->
  <div class="modal" id="reportModal">
    <div class="modal-content">
      <span class="close-btn" onclick="closeModal()">&times;</span>
      <div id="modal-body"></div>
    </div>
  </div>

  <script>
    function openModal(report) {
      let body = `
        <div>
          <span class="badge ${report.type.toLowerCase()}">${report.type}</span>
          <span class="badge ${report.status.toLowerCase()}">${report.status}</span>
        </div>
        <h2>${report.title}</h2>
        ${report.image_path ? `<img src="${report.image_path}" alt="Report image">` : ""}
        <p>${report.description}</p>
        <p><strong>Location:</strong> ${report.location}</p>
        <p><strong>Date:</strong> ${report.date_lost}</p>
        <p><strong>Category:</strong> ${report.category}</p>
        <p><strong>Reported by:</strong> ${report.username}</p>
      `;
      document.getElementById("modal-body").innerHTML = body;
      document.getElementById("reportModal").style.display = "flex";
    }
    function closeModal() {
      document.getElementById("reportModal").style.display = "none";
    }
  </script>
</body>
</html>
