<?php
// admin/galeri/hapus.php
require_once __DIR__ . '/../cek-login.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/auth.php';

cek_editor(); // Pastikan hanya admin/editor yang bisa hapus

$id = $_GET['id'] ?? 0;

try {
    // Cari nama file fotonya dulu
    $stmt = $pdo->prepare("SELECT image FROM galleries WHERE id = ?");
    $stmt->execute([$id]);
    $foto = $stmt->fetch();
    
    if ($foto) {
        // Hapus file fisik dari folder uploads
        $file_path = __DIR__ . '/../../assets/uploads/' . $foto['image'];
        if (file_exists($file_path)) {
            unlink($file_path);
        }
        
        // Hapus data dari database
        $pdo->prepare("DELETE FROM galleries WHERE id = ?")->execute([$id]);
        
        header("Location: index.php?msg=" . urlencode("Foto berhasil dihapus dari galeri"));
    } else {
        header("Location: index.php?error=" . urlencode("Foto tidak ditemukan"));
    }
} catch (PDOException $e) {
    header("Location: index.php?error=" . urlencode("Gagal menghapus foto: " . $e->getMessage()));
}
exit;
?>