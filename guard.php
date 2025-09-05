<?php
session_start();

function is_logged_in(): bool {
    return isset($_SESSION['user_id']) || isset($_SESSION['username']);
}

function require_auth() {
    if (!is_logged_in()) {
        header("Location: login.php");
        exit;
    }
}

function require_role($roles) {
    if (!in_array($_SESSION['role'] ?? '', (array)$roles)) {
        header("Location: dashboard.php");
        exit;
    }
}
