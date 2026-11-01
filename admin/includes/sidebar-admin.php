<?php
$current_page = basename($_SERVER['PHP_SELF']);
$current_folder = basename(dirname($_SERVER['PHP_SELF']));

if ($current_folder === 'admin') {
    $base = '';
    $base_root = '../';
} else {
    $base = '../';
    $base_root = '../../';
}
?>

<div class="sidebar">
    <div class="sidebar-header">
        <h2>HIMASTI</h2>
        <p>Himpunan Mahasiswa Sarjana Informatika</p>
    </div>
    <ul class="menu">
        <li><a href="<?= $base ?>dashboard.php" class="<?= $current_page == 'dashboard.php' ? 'active' : '' ?>">🏠 Dashboard</a></li>
        <li class="menu-divider">Konten</li>
        <li><a href="<?= $base ?>berita/index.php" class="<?= $current_folder == 'berita' ? 'active' : '' ?>">📰 Kelola Berita</a></li>
        <li><a href="<?= $base ?>agenda/index.php" class="<?= $current_folder == 'agenda' ? 'active' : '' ?>">Kelola Agenda</a></li>
        <li><a href="<?= $base ?>galeri/index.php" class="<?= $current_folder == 'galeri' ? 'active' : '' ?>">Kelola Galeri</a></li>
        <li class="menu-divider">Profil Organisasi</li>
        <li><a href="<?= $base ?>profil/index.php" class="<?= $current_folder == 'profil' ? 'active' : '' ?>">Edit Profil</a></li>
        <li><a href="<?= $base ?>pengurus/index.php" class="<?= $current_folder == 'pengurus' ? 'active' : '' ?>">Kelola Pengurus</a></li>
        <li><a href="<?= $base ?>program/index.php" class="<?= $current_folder == 'program' ? 'active' : '' ?>">Program Unggulan</a></li>
        <?php if (isset($_SESSION['role']) && ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'super_admin')): ?>
            <li class="menu-divider">Master Data (Admin Only)</li>
            <li><a href="<?= $base ?>dosen/index.php" class="<?= $current_folder == 'dosen' ? 'active' : '' ?>">Kelola Dosen</a></li>
            <li><a href="<?= $base ?>alumni/index.php" class="<?= $current_folder == 'alumni' ? 'active' : '' ?>">Kelola Alumni</a></li>
            <li><a href="<?= $base ?>user/index.php" class="<?= $current_folder == 'users' ? 'active' : '' ?>">Kelola User</a></li>
            <li><a href="<?= $base ?>settings/index.php" class="<?= $current_folder == 'settings' ? 'active' : '' ?>">Pengaturan</a></li>
        <?php endif; ?>
        <li class="menu-divider">Website</li>
        <li><a href="<?= $base_root ?>index.php" target="_blank">Lihat Website</a></li>
        <li><a href="<?= $base_root ?>profil.php" target="_blank">Halaman Profil</a></li>
        <li class="menu-divider">Akun</li>
        <li><a href="<?= $base ?>login.php?logout=1" style="color: #ef4444;">Logout</a></li>
    </ul>
</div>

<div class="main-content">