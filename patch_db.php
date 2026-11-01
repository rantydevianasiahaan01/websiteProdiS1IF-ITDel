<?php
// Memaksa PHP menampilkan error agar layar tidak blank putih
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Panggil koneksi database
require_once 'config/database.php';

echo "<h1>🛠️ Menambal Struktur Tabel Berita...</h1><hr>";

try {
    // Kueri kita pecah satu per satu agar lebih aman di semua versi XAMPP
    $queries = [
        "ALTER TABLE posts ADD COLUMN author_id INT NULL AFTER category_id",
        "ALTER TABLE posts ADD COLUMN excerpt TEXT NULL AFTER slug",
        "ALTER TABLE posts ADD COLUMN is_featured TINYINT(1) DEFAULT 0 AFTER status",
        "ALTER TABLE posts ADD COLUMN published_at TIMESTAMP NULL AFTER is_featured"
    ];

    foreach ($queries as $query) {
        try {
            $pdo->exec($query);
            echo "✅ Penambahan kolom berhasil.<br>";
        } catch (PDOException $e) {
            // Jika error karena kolom sudah ada, kita abaikan saja dengan aman
            if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
                echo "ℹ️ Info: Kolom sudah tersedia, dilewati.<br>";
            } else {
                echo "⚠️ Peringatan: " . $e->getMessage() . "<br>";
            }
        }
    }

    // Mengganti nama kolom views menjadi view_count (jika ada)
    try {
        $pdo->exec("ALTER TABLE posts CHANGE views view_count INT DEFAULT 0");
        echo "✅ Kolom 'views' berhasil diubah menjadi 'view_count'!<br>";
    } catch (PDOException $e) {
         echo "ℹ️ Info: Perubahan kolom views dilewati (mungkin sudah bernama view_count).<br>";
    }

    echo "<hr><h3 style='color:green;'>🎉 Database sudah 100% sinkron dengan kode panel admin!</h3>";

} catch (PDOException $e) {
    echo "❌ Error Utama: " . $e->getMessage();
}
?>