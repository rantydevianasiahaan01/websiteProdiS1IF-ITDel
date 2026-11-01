<?php
// config/database.php
// File ini berfungsi untuk menghubungkan PHP dengan database MySQL

// Konfigurasi database (sesuaikan dengan setting XAMPP kamu)
$host = 'localhost';       // Atau '127.0.0.1' jika localhost bermasalah
$dbname = 'informatika_itdel';
$username = 'root';
$password = '';            // Kosongkan, karena default XAMPP tidak pakai password

try {
    // Membuat koneksi menggunakan PDO (PHP Data Objects)
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    
    // Mengatur PDO agar menampilkan error jika ada query yang salah
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Mengatur agar hasil query langsung berupa array asosiatif (lebih mudah dibaca)
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    // Mematikan emulated prepares untuk keamanan yang lebih baik
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

} catch (PDOException $e) {
    // Jika koneksi gagal, hentikan program dan tampilkan pesan error
    die("❌ Koneksi Database Gagal: " . $e->getMessage());
}

// Mendefinisikan Base URL agar mudah dipanggil di file lain untuk redirect
define('BASE_URL', 'http://localhost/web-himasti/');
?>
