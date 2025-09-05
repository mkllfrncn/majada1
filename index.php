<?php
// Start session
session_start();

// Check if guest mode is activated
if (isset($_GET['guest']) && $_GET['guest'] == 'true') {
    $_SESSION['guest'] = true;
} elseif (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}

// Function to display header
function displayHeader() {
    echo '<h1>Barangay Lost and Found Information System</h1>';
    echo '<h2>Barangay Majada</h2>';
    echo '<h3>Lost & Found Information System</h3>';
}

// Function to display footer without watermark
function displayFooter() {
    echo '<p>© 2025 Barangay Majada. All rights reserved.</p>';
}

// Main content
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Barangay Lost and Found Information System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin: 50px;
        }
        footer {
            margin-top: 50px;
            font-size: 0.9em;
            color: #555;
        }
        .guest-mode {
            margin: 20px 0;
            font-weight: bold;
            color: #007BFF;
        }
        .login-link {
            margin-top: 20px;
            display: inline-block;
            text-decoration: none;
            color: #007BFF;
            border: 1px solid #007BFF;
            padding: 5px 10px;
            border-radius: 5px;
        }
        .login-link:hover {
            background-color: #007BFF;
            color: white;
        }
    </style>
</head>
<body>

<?php
displayHeader();

if (isset($_SESSION['guest']) && $_SESSION['guest'] === true) {
    echo '<div class="guest-mode">You are currently in Guest Mode.</div>';
    echo '<a href="index.php?logout=true" class="login-link">Exit Guest Mode</a>';
} else {
    echo '<a href="index.php?guest=true" class="login-link">Enter Guest Mode</a>';
}

displayFooter();
?>

</body>
</html>