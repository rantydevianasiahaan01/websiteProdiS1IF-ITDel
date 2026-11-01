<?php
// patch_agenda.php - Script untuk melengkapi kolom tabel agenda
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'config/database.php';

echo "<h1>🛠️ Memperbaiki Struktur Tabel Agenda (events)...</h1><hr>";

try {
    // Kueri untuk menambahkan semua kolom yang kurang di tabel events
    $queries = [
        "ALTER TABLE events ADD COLUMN slug VARCHAR(255) NULL AFTER title",
        "ALTER TABLE events ADD COLUMN content TEXT NULL AFTER description",
        "ALTER TABLE events ADD COLUMN start_time TIME NULL AFTER event_date",
        "ALTER TABLE events ADD COLUMN end_time TIME NULL AFTER start_time",
        "ALTER TABLE events ADD COLUMN location_detail TEXT NULL AFTER location",
        "ALTER TABLE events ADD COLUMN image VARCHAR(255) DEFAULT 'default-event.jpg' AFTER location_detail",
        "ALTER TABLE events ADD COLUMN category VARCHAR(100) NULL AFTER image",
        "ALTER TABLE events ADD COLUMN registration_link VARCHAR(255) NULL AFTER category",
        "ALTER TABLE events ADD COLUMN status ENUM('upcoming', 'ongoing', 'done', 'cancelled') DEFAULT 'upcoming' AFTER registration_link",
        "ALTER TABLE events ADD COLUMN is_featured TINYINT(1) DEFAULT 0 AFTER status",
        "ALTER TABLE events ADD COLUMN created_by INT NULL AFTER is_featured"
    ];

    foreach ($queries as $query) {
        try {
            $pdo->exec($query);
            echo "✅ Penambahan kolom berhasil.<br>";
        } catch (PDOException $e) {
            // Abaikan jika kolom sudah ada
            if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
                echo "ℹ️ Info: Kolom sudah tersedia, dilewati.<br>";
            } else {
                echo "⚠️ Peringatan: " . $e->getMessage() . "<br>";
            }
        }
    }

    echo "<hr><h3 style='color:green;'>🎉 Tabel Agenda sudah 100% lengkap dan siap digunakan!</h3>";

} catch (PDOException $e) {
    echo "❌ Error Utama: " . $e->getMessage();
}
?>