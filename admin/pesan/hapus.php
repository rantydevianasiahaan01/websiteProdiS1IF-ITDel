<?php
// admin/pesan/hapus.php
require_once __DIR__ . '/../cek-login.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/auth.php';

cek_editor(); // Pastikan admin login

$id = $_GET['id'] ?? 0;

try {
    $stmt = $pdo->prepare("DELETE FROM contacts WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: index.php?msg=" . urlencode("Pesan berhasil dihapus!"));
} catch (PDOException $e) {
    header("Location: index.php?msg=" . urlencode("Gagal menghapus pesan."));
}
exit;
?>