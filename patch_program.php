<?php
// patch_program.php - Memperbarui Program Unggulan dengan Copywriting Profesional
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once 'config/database.php';

echo "<h1>🛠️ Menyuntikkan Data Program Unggulan Premium...</h1><hr>";

try {
    // 1. BUAT TABEL JIKA BELUM ADA (Ini solusi penangkal error-nya!)
    $pdo->exec("CREATE TABLE IF NOT EXISTS programs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        description TEXT NULL,
        icon VARCHAR(50) DEFAULT 'fas fa-star',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // 2. Kosongkan data program contoh yang lama (jika ada)
    $pdo->exec("TRUNCATE TABLE programs");

    // 3. Data baru dengan bahasa setara Universitas Top
    $programs = [
        [
            'title' => 'Software Engineering (Rekayasa Perangkat Lunak)',
            'description' => 'Berfokus pada arsitektur dan pengembangan aplikasi skala enterprise. Mahasiswa dibekali keterampilan komprehensif mulai dari clean coding, pengujian sistem perangkat lunak, hingga manajemen proyek teknologi digital. Lulusan dipersiapkan untuk menjadi Software Engineer andal yang memenuhi tuntutan industri global.',
            'icon' => 'fas fa-laptop-code'
        ],
        [
            'title' => 'Artificial Intelligence (Kecerdasan Buatan)',
            'description' => 'Menjawab tantangan era Society 5.0 melalui rekayasa sistem cerdas. Program ini melatih mahasiswa dalam merancang algoritma Machine Learning dan analitik data tingkat lanjut, memungkinkan sistem komputasi untuk memprediksi tren, mengotomatisasi proses kompleks, dan memberikan solusi cerdas secara mandiri.',
            'icon' => 'fas fa-brain'
        ],
        [
            'title' => 'Cyber Security (Keamanan Siber)',
            'description' => 'Garda terdepan dalam perlindungan infrastruktur digital krusial. Mahasiswa ditempa untuk menguasai teknik mitigasi ancaman, perlindungan privasi data, serta taktik defensif guna menangkal eksploitasi peretas (hacker) dan mengamankan aset informasi dari eskalasi kejahatan siber.',
            'icon' => 'fas fa-shield-alt'
        ],
        [
            'title' => 'Ekosistem Pembelajaran Standar Industri',
            'description' => 'Lebih dari sekadar teori, kurikulum dirancang secara khusus mengadaptasi model inkubasi industri. Didukung fasilitas Laboratorium Komputer Terpadu dan Data Center mandiri, program ini mencetak tingkat serapan kerja yang luar biasa (90,91% lulusan direkrut dalam waktu kurang dari 6 bulan).',
            'icon' => 'fas fa-chart-line'
        ]
    ];

    // 4. Eksekusi penyimpanan ke database
    $stmt = $pdo->prepare("INSERT INTO programs (title, description, icon) VALUES (?, ?, ?)");
    
    foreach ($programs as $prog) {
        $stmt->execute([$prog['title'], $prog['description'], $prog['icon']]);
    }

    echo "✅ Berhasil membuat tabel dan memperbarui data Program Unggulan!<br>";
    echo "<h3 style='color:green;'>🎉 Silakan tutup halaman ini dan Refresh halaman Program Unggulan di Admin Panel Anda.</h3>";

} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>