<?php
require_once 'guard.php';
require_once 'db.php';
session_destroy();
header("Location: login.php");
