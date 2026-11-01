<?php
// config/auth.php
// Fungsi-fungsi untuk autentikasi dan otorisasi user

// Fungsi 1: Cek apakah user sudah login
// Jika belum login, tendang ke halaman login
function cek_login() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: " . BASE_URL . "admin/login.php");
        exit;
    }
}

// Fungsi 2: Cek apakah user adalah admin atau super_admin
// Jika bukan, tolak aksesnya
function cek_admin() {
    cek_login(); // Pastikan user sudah login dulu
    
    $role = $_SESSION['role'];
    if ($role !== 'admin' && $role !== 'super_admin') {
        die("⛔ Akses Ditolak! Halaman ini hanya untuk Admin.");
    }
}

// Fungsi 3: Cek apakah user adalah editor atau admin
// Editor bisa akses halaman konten (berita, agenda, galeri)
function cek_editor() {
    cek_login(); // Pastikan user sudah login dulu
    
    $role = $_SESSION['role'];
    if ($role !== 'admin' && $role !== 'super_admin' && $role !== 'editor') {
        die("⛔ Akses Ditolak! Anda tidak memiliki izin.");
    }
}

// Fungsi 4: Generate slug dari judul (untuk URL yang SEO-friendly)
// Contoh: "Berita Prodi" menjadi "berita-prodi"
function generate_slug($text) {
    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    $text = preg_replace('/[\s-]+/', '-', $text);
    return trim($text, '-');
}

// Fungsi 5: Format tanggal ke bahasa Indonesia
// Contoh: "2024-01-15" menjadi "15 Januari 2024"
function format_tanggal($tanggal) {
    $bulan = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    
    $tgl = date('j', strtotime($tanggal));
    $bln = $bulan[date('n', strtotime($tanggal))];
    $thn = date('Y', strtotime($tanggal));
    
    return "$tgl $bln $thn";
}

// Fungsi 6: Format rupiah
function format_rupiah($angka) {
    return "Rp " . number_format($angka, 0, ',', '.');
}
?>