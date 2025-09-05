<?php
$role = $_SESSION['role'] ?? 'guest';
?>

<aside class="sidebar">
  <div class="brand">
    <div class="logo">🔎</div>
    <div>
      <div class="name">Barangay Majada</div>
      <small class="sub">Lost &amp; Found System</small>
    </div>
  </div>

  <nav class="nav">
    <?php if ($role === 'admin'): ?>
      <a href="dashboard.php">🏠 Dashboard</a>
      <a href="add_report.php">➕ Add Report</a>
      <a href="search.php">🔎 Search Reports</a>
      <a href="residents.php">👥 Residents</a>
      <a href="manage.php">🗂️ Manage Reports</a>
      <a href="matches.php">👥 Matches</a>
      <a href="exports.php">📄 Export Reports</a>
      <a href="certificates.php">🏅 Certificates</a>
    
    <?php elseif ($role === 'resident'): ?>
      <a href="dashboard.php">🏠 Dashboard</a>  
      <a href="add_report.php">➕ Add Report</a>
      <a href="search.php">🔎 Search Reports</a>

    <?php else: ?>
      <!-- Guest or unknown role -->
      <a href="search.php">🔎 Search Reports</a>
    <?php endif; ?>
  </nav>
</aside>
