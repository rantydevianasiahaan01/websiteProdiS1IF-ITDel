<?php
// admin/pengurus/hapus_anggota.php
require_once __DIR__ . '/../cek-login.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/auth.php';

// Pastikan hanya admin yang bisa melakukan penghapusan
cek_editor(); 

$id = $_GET['id'] ?? 0;
$divisi_id = $_GET['divisi'] ?? 0;

try {
    // Eksekusi hapus data dari database
    $pdo->prepare("DELETE FROM members WHERE id = ?")->execute([$id]);
    
    // Kembalikan admin ke halaman daftar anggota divisi tersebut dengan pesan sukses
    header("Location: lihat_anggota.php?divisi={$divisi_id}&msg=" . urlencode("Satu anggota berhasil dihapus dari kepengurusan."));
} catch (PDOException $e) {
    header("Location: lihat_anggota.php?divisi={$divisi_id}&msg=" . urlencode("Gagal menghapus data."));
}
exit;
?>