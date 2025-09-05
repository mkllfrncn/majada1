<?php

require_once 'db.php';

require_once 'guard.php';
require_auth();

if ($_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Export Reports – Barangay Majada</title>
  <link href="style.css" rel="stylesheet">
</head>
<body>
<div class="app">
  <?php include 'sidebar.php'; ?>
  <main class="main">
    <?php include 'topbar.php'; ?>

    <div class="content">
      <h2 class="hi">Export Reports</h2>
      <p class="subtle">Generate and download reports for analysis</p>

      <!-- Export Form -->
      <form method="post" action="export.php" class="section">
        <label>Report Type:</label>
        <select class="input" name="type">
          <option>All Reports</option>
          <option>Lost</option>
          <option>Found</option>
        </select>
        <label>Start Date:</label>
        <input class="input" type="date" name="start">
        <label>End Date:</label>
        <input class="input" type="date" name="end">

        <div style="margin-top:12px; display:flex; gap:10px;">
          <button class="btn primary" type="submit" name="mode" value="detailed">Export Detailed Reports (CSV)</button>
          <button class="btn" style="background:#f9a825;" type="submit" name="mode" value="stats">Export Statistics (CSV)</button>
        </div>
      </form>

      <!-- Info -->
      <div class="section">
        <div class="hd">Export Information</div>
        <div class="bd">
          <ul>
            <li>Detailed reports include all report information and reporter details</li>
            <li>Statistics export provides summary data for analysis</li>
            <li>Use date filters to export specific time periods</li>
            <li>All exports are in CSV format for easy analysis</li>
          </ul>
        </div>
      </div>
    </div>
  </main>
</div>
</body>
</html>
