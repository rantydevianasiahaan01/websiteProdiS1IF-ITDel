<?php
// patch_kontak.php - Script untuk membuat tabel kontak dan pengaturan
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once 'config/database.php';

echo "<h1>🛠️ Menyiapkan Tabel Kontak & Pengaturan...</h1><hr>";

try {
    // 1. Membuat tabel contacts untuk menampung pesan dari pengunjung
    $pdo->exec("CREATE TABLE IF NOT EXISTS contacts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL,
        phone VARCHAR(20),
        subject VARCHAR(200),
        message TEXT NOT NULL,
        status ENUM('unread', 'read', 'replied') DEFAULT 'unread',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "✅ Tabel 'contacts' berhasil dibuat!<br>";

    // 2. Membuat tabel settings untuk pengaturan profil website (email, alamat, dll)
    $pdo->exec("CREATE TABLE IF NOT EXISTS settings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        setting_key VARCHAR(50) NOT NULL UNIQUE,
        setting_value TEXT NULL
    )");
    echo "✅ Tabel 'settings' berhasil dibuat!<br>";
    
    // 3. Memasukkan data pengaturan default agar kontak.php tidak kosong
    $pdo->exec("INSERT IGNORE INTO settings (setting_key, setting_value) VALUES 
        ('prodi_name', 'Informatika IT Del'),
        ('alamat', 'Jl. Sisingamangaraja, Sitoluama, Laguboti, Toba, Sumatera Utara'),
        ('email', 'informatika@del.ac.id'),
        ('telepon', '+62 632 331234'),
        ('instagram', 'https://instagram.com/himasti.itdel'),
        ('linkedin', 'https://linkedin.com/school/institut-teknologi-del')
    ");
    echo "✅ Data default pengaturan berhasil dimasukkan!<br>";

    echo "<hr><h3 style='color:green;'>🎉 Halaman Kontak sudah siap digunakan tanpa error!</h3>";
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>