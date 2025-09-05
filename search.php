<?php
require_once 'guard.php';
require_auth();
require_once 'db.php';

// Fetch all reports from DB
$stmt = $pdo->query("
    SELECT r.id, r.type, r.title, r.description, r.location, r.date_lost, 
           r.category, r.image_path, u.username AS reporter
    FROM reports r
    JOIN users u ON r.user_id = u.id
    ORDER BY r.created_at DESC
");
$reports = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Filter (search)
$filtered = $reports;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $q = strtolower(trim($_POST['q'] ?? ''));
    $filtered = array_filter($reports, function($r) use ($q) {
        return !$q || strpos(strtolower($r['title']), $q) !== false || strpos(strtolower($r['type']), $q) !== false;
    });
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Search Reports – Barangay Majada</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="style.css" rel="stylesheet">
  <style>
    /* Modal */
    .modal {
      position: fixed;
      top:0; left:0;
      width:100%; height:100%;
      background: rgba(0,0,0,0.6);
      display:none;
      align-items:center;
      justify-content:center;
      z-index:1000;
    }
    .modal-content {
      background:#fff;
      padding:20px;
      border-radius:12px;
      max-width:500px;
      width:90%;
      box-shadow:0 4px 10px rgba(0,0,0,0.3);
      position:relative;
    }
    .close {
      position:absolute;
      top:10px; right:15px;
      font-size:24px;
      cursor:pointer;
    }
    .tag {
      display:inline-block;
      padding:4px 10px;
      border-radius:12px;
      font-size:12px;
      font-weight:bold;
      margin-right:6px;
    }
    .tag.lost { background:#ffe5e5; color:#d00; }
    .tag.found { background:#e5f7ff; color:#0077cc; }
    .tag.verified { background:#e5f0ff; color:#3344cc; }
    .report-img {
      margin-top:12px;
      max-width:100%;
      border-radius:8px;
    }
  </style>
</head>
<body>
  <div class="app">
    <?php include 'sidebar.php'; ?>
    <main class="main">
      <?php include 'topbar.php'; ?>

      <div class="content">
        <h2 class="hi">Search Reports</h2>
        <p class="subtle">Look for lost or found items and pets.</p>

        <!-- Search Form -->
        <div class="section">
          <div class="bd">
            <form method="post" style="display:flex; gap:10px;">
              <input class="input" type="text" name="q" placeholder="Enter item type or title" value="<?= htmlspecialchars($_POST['q'] ?? '') ?>">
              <button class="btn primary" type="submit">Search</button>
            </form>
          </div>
        </div>

        <!-- Results -->
        <div class="section">
          <div class="hd">Results</div>
          <div class="bd">
            <?php if (empty($filtered)): ?>
              <div class="empty">
                <div style="font-size:46px;line-height:1">🔍</div>
                <div style="margin-top:8px">No reports found</div>
              </div>
            <?php else: ?>
              <div class="grid">
                <?php foreach ($filtered as $r): ?>
                  <div class="card">
                    <h4><?= htmlspecialchars($r['type']) ?> • <?= htmlspecialchars($r['title']) ?></h4>
                    <div style="color:var(--muted); margin-bottom:8px;">
                      Location: <?= htmlspecialchars($r['location']) ?> • Date: <?= htmlspecialchars($r['date_lost']) ?>
                    </div>
                    <a class="btn outline view-btn"
                       href="#"
                       data-id="<?= $r['id'] ?>"
                       data-type="<?= htmlspecialchars($r['type']) ?>"
                       data-title="<?= htmlspecialchars($r['title']) ?>"
                       data-description="<?= htmlspecialchars($r['description']) ?>"
                       data-location="<?= htmlspecialchars($r['location']) ?>"
                       data-date="<?= htmlspecialchars($r['date_lost']) ?>"
                       data-category="<?= htmlspecialchars($r['category']) ?>"
                       data-reporter="<?= htmlspecialchars($r['reporter']) ?>"
                       data-image="<?= htmlspecialchars($r['image_path']) ?>">
                       👁️ View Details
                    </a>
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
  <div id="reportModal" class="modal">
    <div class="modal-content">
      <span class="close">&times;</span>
      <div id="modal-body"></div>
    </div>
  </div>

  <script>
  document.querySelectorAll('.view-btn').forEach(btn => {
    btn.addEventListener('click', function(e) {
      e.preventDefault();
      const modal = document.getElementById('reportModal');
      const body = document.getElementById('modal-body');

      let typeTag = this.dataset.type.toLowerCase() === 'lost'
        ? `<span class="tag lost">LOST</span>`
        : `<span class="tag found">FOUND</span>`;

      let verifiedTag = `<span class="tag verified">Verified</span>`; // optional status

      body.innerHTML = `
        <div style="margin-bottom:12px;">${typeTag} ${verifiedTag}</div>
        <h3 style="margin:0 0 8px;">${this.dataset.title}</h3>
        <p>${this.dataset.description}</p>
        <p><b>Location:</b> ${this.dataset.location}</p>
        <p><b>Date:</b> ${this.dataset.date}</p>
        <p><b>Category:</b> ${this.dataset.category}</p>
        <p><b>Reported by:</b> ${this.dataset.reporter}</p>
        ${this.dataset.image ? `<img src="${this.dataset.image}" class="report-img">` : ""}
      `;

      modal.style.display = 'flex';
    });
  });

  document.querySelector('.close').onclick = function() {
    document.getElementById('reportModal').style.display = 'none';
  };
  window.onclick = function(e) {
    if (e.target.classList.contains('modal')) {
      document.getElementById('reportModal').style.display = 'none';
    }
  };
  </script>
</body>
</html>
