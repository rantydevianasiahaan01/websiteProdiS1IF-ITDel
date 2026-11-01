<?php
// admin/program/hapus.php
require_once __DIR__ . '/../cek-login.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/auth.php';

cek_editor(); // Pastikan admin login

$id = $_GET['id'] ?? 0;

try {
    $pdo->prepare("DELETE FROM programs WHERE id = ?")->execute([$id]);
    header("Location: index.php?msg=" . urlencode("Program berhasil dihapus."));
} catch (PDOException $e) {
    header("Location: index.php?msg=" . urlencode("Gagal menghapus program."));
}
exit;
?>