<?php
// admin/user/hapus.php
require_once __DIR__ . '/../cek-login.php';
require_once __DIR__ . '/../../config/database.php';

$id = $_GET['id'] ?? 0;

try {
    // Keamanan tambahan: Jangan pernah izinkan ID akun admin utama (id = 1) terhapus!
    if ($id == 1) {
        header("Location: index.php?err=" . urlencode("Akses ditolak: Akun Administrator utama tidak boleh dihapus!"));
        exit;
    }
    
    $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$id]);
    header("Location: index.php?msg=" . urlencode("Akun pengguna berhasil dicabut/dihapus."));
} catch (PDOException $e) {
    header("Location: index.php?err=" . urlencode("Gagal menghapus pengguna."));
}
exit;
?>