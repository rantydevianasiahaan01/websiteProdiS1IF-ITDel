<?php
// patch_divisi.php - Script untuk mereset dan mengganti nama divisi
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once 'config/database.php';

echo "<h1>🛠️ Memperbarui Daftar Divisi HIMASTI...</h1><hr>";

try {
    // 1. Kosongkan tabel divisi dan anggota lama agar bersih
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
    $pdo->exec("TRUNCATE TABLE members");
    $pdo->exec("TRUNCATE TABLE divisions");
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

    // 2. Daftar divisi baru sesuai permintaanmu (ditambah BPH)
    $divisi_baru = [
        'BPH (BADAN PENGURUS HARIAN)',
        'DIVISI PENDIDIKAN',
        'DIVISI RISET DAN TEKNOLOGI',
        'DIVISI MINAT DAN BAKAT',
        'DIVISI DANA DAN USAHA',
        'DIVISI HUBUNGAN MASYARAKAT',
        'DIVISI KOMUNIKASI DAN INFORMASI'
    ];

    // 3. Masukkan ke database
    $stmt = $pdo->prepare("INSERT INTO divisions (name, description) VALUES (?, 'Deskripsi tupoksi divisi ini belum diisi.')");
    foreach ($divisi_baru as $div) {
        $stmt->execute([$div]);
    }

    echo "✅ Nama-nama divisi berhasil diperbarui!<br>";
    echo "<h3 style='color:green;'>🎉 Silakan cek kembali form Tambah Anggota.</h3>";

} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>