<?php
// patch_user.php - Script untuk mereset tabel users ke struktur yang benar
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once 'config/database.php';

echo "<h1>🛠️ Mereset Struktur Tabel Pengguna Sistem...</h1><hr>";

try {
    // 1. Hapus tabel lama yang rusak / tidak sesuai
    $pdo->exec("DROP TABLE IF EXISTS users");

    // 2. Buat tabel baru dengan struktur modern (menggunakan username)
    $pdo->exec("CREATE TABLE users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        name VARCHAR(100) NOT NULL,
        role ENUM('admin', 'editor') DEFAULT 'editor',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // 3. Suntikkan akun Administrator Utama secara default
    $default_pass = password_hash('admin123', PASSWORD_DEFAULT);
    $pdo->exec("INSERT INTO users (username, password, name, role) VALUES ('admin', '$default_pass', 'Administrator', 'admin')");

    echo "✅ Tabel pengguna berhasil direset ke versi terbaru!<br>";
    echo "🔑 Akun Default Terbuat: <br>";
    echo "<b>Username:</b> admin <br>";
    echo "<b>Password:</b> admin123 <br><br>";
    echo "<h3 style='color:green;'>🎉 Silakan kembali ke Panel Admin dan Refresh halaman Pengguna Sistem.</h3>";

} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>