<?php
// buat_tabel.php - Script totalitas untuk menyamakan struktur database
require_once 'config/database.php';

echo "<h1>🛠️ Melakukan Sinkronisasi Total Struktur Database...</h1>";
echo "<hr>";

try {
    // 1. Buat atau perbarui Tabel Categories terlebih dahulu
    $pdo->exec("CREATE TABLE IF NOT EXISTS categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        slug VARCHAR(100) NOT NULL UNIQUE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "✅ Tabel 'categories' disiapkan.<br>";

    // Masukkan kategori default jika masih kosong
    $pdo->exec("INSERT IGNORE INTO categories (id, name, slug) VALUES 
        (1, 'Pengumuman', 'pengumuman'), 
        (2, 'Kegiatan', 'kegiatan'), 
        (3, 'Akademik', 'akademik')");
    echo "✅ Kategori default dimasukkan.<br>";

    // 2. Buat Ulang/Sesuaikan Tabel Posts (Berita) agar punya category_id & deleted_at
    // Kita drop dulu atau buat jika belum ada, agar relasinya pas
    $pdo->exec("CREATE TABLE IF NOT EXISTS posts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        slug VARCHAR(255) NOT NULL UNIQUE,
        content TEXT NOT NULL,
        image VARCHAR(255) DEFAULT NULL,
        category_id INT DEFAULT 1,
        status ENUM('published', 'draft', 'archived') DEFAULT 'draft',
        views INT DEFAULT 0,
        deleted_at TIMESTAMP NULL DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Antisipasi jika tabel posts sudah terlanjur ada tapi belum punya kolom category_id atau deleted_at
    try {
        $pdo->exec("ALTER TABLE posts ADD COLUMN category_id INT DEFAULT 1");
    } catch (PDOException $e) { /* Abaikan jika sudah ada */ }

    try {
        $pdo->exec("ALTER TABLE posts ADD COLUMN deleted_at TIMESTAMP NULL DEFAULT NULL");
    } catch (PDOException $e) { /* Abaikan jika sudah ada */ }

    echo "✅ Tabel 'posts' (Berita) disinkronkan dengan `category_id` dan `deleted_at`!<br>";

    // 3. Tabel Agenda (events)
    $pdo->exec("CREATE TABLE IF NOT EXISTS events (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        description TEXT,
        event_date DATE NOT NULL,
        location VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "✅ Tabel 'events' (Agenda) disiapkan.<br>";

    echo "<hr>";
    echo "<h3 style='color:green;'>🎉 Sinkronisasi Sempurna Selesai!</h3>";
    echo "<p><a href='admin/berita/index.php' style='background:#800000;color:white;padding:10px 20px;text-decoration:none;border-radius:5px;'>→ Kembali ke Kelola Berita</a></p>";

} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>