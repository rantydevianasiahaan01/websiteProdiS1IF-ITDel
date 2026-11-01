<?php
// admin/cek-login.php
// File ini wajib di-include di setiap halaman admin untuk proteksi

// Load config (membawa $pdo dan konstanta BASE_URL)
require_once __DIR__ . '/../config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "admin/login.php");
    exit;
}
?>
