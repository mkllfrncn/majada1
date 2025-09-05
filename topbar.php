<?php

$u = $_SESSION['username'] ?? 'User';
$role = $_SESSION['role'] ?? 'guest';
?>
<div class="topbar">
  <div></div>
  <div class="user-badge">
    <span>👤 <?= htmlspecialchars($u) ?></span>
    <span class="badge"><?= ucfirst($role) ?></span>
    <a class="btn" href="logout.php">Sign Out</a>
  </div>
</div>
