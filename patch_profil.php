<?php
// patch_profil.php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once 'config/database.php';

echo "<h1>🛠️ Menyiapkan Tabel Profil Organisasi...</h1><hr>";

try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS profile_settings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        org_name VARCHAR(255) DEFAULT 'HIMASTI IT Del',
        tagline VARCHAR(255) NULL,
        logo VARCHAR(255) NULL,
        banner VARCHAR(255) NULL,
        vision TEXT NULL,
        mission TEXT NULL,
        history TEXT NULL,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");

    // Masukkan data awal jika tabel masih kosong
    $stmt = $pdo->query("SELECT COUNT(*) FROM profile_settings");
    if ($stmt->fetchColumn() == 0) {
        $pdo->exec("INSERT INTO profile_settings (org_name, tagline, vision, mission) VALUES 
            ('HIMASTI IT Del', 'Himpunan Mahasiswa Sarjana Informatika', 'Menjadi wadah pengembangan karakter dan keilmuan mahasiswa Informatika IT Del.', '1. Menyelenggarakan kegiatan pengembangan skill IT.\n2. Membangun relasi antar mahasiswa.')");
    }
    
    echo "✅ Tabel 'profile_settings' berhasil disiapkan!<br>";
    echo "<h3 style='color:green;'>🎉 Halaman Edit Profil siap digunakan!</h3>";
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>