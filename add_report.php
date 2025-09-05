<?php
require_once 'guard.php';
require_auth();
require_once 'db.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id']; // logged-in user
    $type = $_POST['type'];
    $category = $_POST['category'];
    $title = $_POST['title'];
    $desc = $_POST['description'];
    $location = $_POST['location'];
    $date_lost = $_POST['date_lost'];
    $image_path = null;

    // Handle image upload
    if (!empty($_FILES['image']['name'])) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) mkdir($target_dir);
        $file_name = time() . "_" . basename($_FILES['image']['name']);
        $target_file = $target_dir . $file_name;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $image_path = $target_file;
        }
    }

    // Insert into reports
    $stmt = $pdo->prepare("INSERT INTO reports (user_id, type, category, title, description, location, date_lost, image_path) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$user_id, $type, $category, $title, $desc, $location, $date_lost, $image_path]);

    header("Location: search.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Add Report – Barangay Majada</title>
  <link href="style.css" rel="stylesheet">
  <style>
    /* Center the form */
    .form-container {
      display: flex;
      justify-content: center;
      align-items: flex-start;
      min-height: 80vh;
    }

    .upload-box {
      border: 2px dashed #ccc;
      border-radius: 8px;
      padding: 30px;
      text-align: center;
      cursor: pointer;
      transition: border-color 0.3s ease, background 0.3s ease;
    }

    .upload-box:hover {
      border-color: #2563eb;
      background: #f9fafb;
    }

    .upload-box.dragover {
      border-color: #2563eb;
      background: #e0f2fe;
    }
  </style>
</head>
<body>
<div class="app">
  <?php include 'sidebar.php'; ?>
  <main class="main">
    <?php include 'topbar.php'; ?>
    <div class="content">
      <h2 class="hi">Report Lost or Found Item/Pet</h2>
      <p class="subtle">Help reunite lost items and pets with their owners</p>

      <div class="form-container">
        <form method="post" enctype="multipart/form-data" class="section" style="display:grid;gap:12px;max-width:600px;width:100%;">

          <label>Report Type:</label>
          <select name="type" class="input" required>
            <option value="Lost">Lost</option>
            <option value="Found">Found</option>
          </select>

          <label>Category:</label>
          <select name="category" class="input" required>
            <option value="Item">Item</option>
            <option value="Pet">Pet</option>
            <option value="Document">Document</option>
            <option value="Other">Other</option>
          </select>

          <label>Title:</label>
          <input type="text" name="title" class="input" placeholder="e.g., Black wallet, Golden Retriever" required>

          <label>Description:</label>
          <textarea name="description" class="input" rows="4" placeholder="Provide detailed description including color, size, features, etc."></textarea>

          <label>Upload Image (Optional):</label>
          <div class="upload-box" id="uploadBox">
            <div style="text-align:center;">
              <div style="font-size:40px; color:#888;">📷</div>
              <p style="margin:6px 0;color:#666;">
                Drag and drop an image here, or <span style="color:#2563eb;cursor:pointer;text-decoration:underline;">browse files</span>
              </p>
              <small style="color:#999;">JPG, JPEG, PNG up to 5MB</small>
            </div>
          </div>
          <input type="file" id="imageInput" name="image" accept="image/*" style="display:none;">

          <label>Location:</label>
          <input type="text" name="location" class="input" placeholder="Where was it lost/found? (e.g., Near Majada Elementary School)">

          <label>Date Lost/Found:</label>
          <input type="date" name="date_lost" class="input" required>

          <button class="btn primary" type="submit">Submit Report</button>
        </form>
      </div>
    </div>
  </main>
</div>

<script>
  const uploadBox = document.getElementById('uploadBox');
  const imageInput = document.getElementById('imageInput');

  // Click to trigger file select
  uploadBox.addEventListener('click', () => imageInput.click());

  // Drag & Drop events
  uploadBox.addEventListener('dragover', (e) => {
    e.preventDefault();
    uploadBox.classList.add('dragover');
  });

  uploadBox.addEventListener('dragleave', () => {
    uploadBox.classList.remove('dragover');
  });

  uploadBox.addEventListener('drop', (e) => {
    e.preventDefault();
    uploadBox.classList.remove('dragover');
    if (e.dataTransfer.files.length > 0) {
      imageInput.files = e.dataTransfer.files;
    }
  });
</script>
</body>
</html>
