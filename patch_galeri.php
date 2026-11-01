<?php
// patch_galeri.php - Script untuk membuat tabel galeri
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'config/database.php';

echo "<h1>🛠️ Menyiapkan Tabel Galeri...</h1><hr>";

try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS galleries (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        image VARCHAR(255) NOT NULL,
        description TEXT NULL,
        uploaded_by INT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "✅ Tabel 'galleries' berhasil disiapkan!<br>";
    echo "<h3 style='color:green;'>🎉 Siap digunakan!</h3>";

} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>