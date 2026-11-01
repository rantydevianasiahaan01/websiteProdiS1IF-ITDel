<?php
// admin/dosen/hapus.php
require_once __DIR__ . '/../cek-login.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/auth.php';

cek_editor();

$id = $_GET['id'] ?? 0;

try {
    $pdo->prepare("DELETE FROM lecturers WHERE id = ?")->execute([$id]);
    header("Location: index.php?msg=" . urlencode("Data dosen berhasil dihapus dari direktori."));
} catch (PDOException $e) {
    header("Location: index.php?msg=" . urlencode("Gagal menghapus data dosen."));
}
exit;
?>