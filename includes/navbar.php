<style>
    /* 1. Memaksa Top Bar (Baris Atas) Menjadi Gelap */
    .top-bar, .topbar, #topbar, .top-header { 
        background-color: #0f172a !important; 
        color: #cbd5e1 !important; 
        border-bottom: 1px solid rgba(255,255,255,0.05) !important;
    }
    .top-bar a, .topbar a, #topbar a, .top-header a { color: #cbd5e1 !important; text-decoration: none !important; }
    .top-bar a:hover, .topbar a:hover, #topbar a:hover, .top-header a:hover { color: #ffffff !important; }

    /* 2. Memaksa Navbar Utama Menjadi Navy Dark */
    nav, .navbar, #navbar { 
        background-color: #1e293b !important; 
        border: none !important; 
        box-shadow: 0 4px 15px rgba(0,0,0,0.1) !important; 
    }
    
    /* 3. Memaksa Teks Logo Menjadi Putih */
    .navbar-brand, .navbar-brand *, .logo-text { color: #ffffff !important; }
    .navbar-brand small, .brand-subtitle { color: #94a3b8 !important; }
    
    /* 4. Memaksa Teks Menu Menjadi Terang & Rapi */
    .navbar-nav .nav-link { 
        color: #e2e8f0 !important; 
        font-weight: 600 !important; 
        font-size: 13.5px !important;
        border-radius: 8px !important; 
        padding: 8px 15px !important; 
        margin: 0 3px !important; 
        transition: 0.3s !important;
    }
    .navbar-nav .nav-link:hover { 
        background-color: rgba(255,255,255,0.1) !important; 
        color: #ffffff !important; 
    }
    
    /* 5. Memaksa Kotak Menu Aktif Menjadi Abu-abu Gelap (Khas Gambar 1) */
    .navbar-nav .nav-link.active, .navbar-nav .active > .nav-link { 
        background-color: #475569 !important; 
        color: #ffffff !important; 
    }
    
    /* 6. Memaksa Tombol Menjadi Warna Teal/Cyan Membulat */
    .btn-primary, .btn-daftar, .btn-portal, a[href*="spmbdel"], a[href*="login"] { 
        background-color: #498a96 !important; 
        border: none !important; 
        color: #ffffff !important; 
        border-radius: 50px !important; 
        font-weight: 600 !important;
        padding: 8px 22px !important;
    }
    .btn-primary:hover, .btn-daftar:hover, .btn-portal:hover, a[href*="spmbdel"]:hover, a[href*="login"]:hover { 
        background-color: #3a727d !important; 
    }
</style>

<?php
// includes/navbar.php
// Navbar untuk website publik - Prodi Informatika IT Del

// Ambil menu dari database jika ada tabel menus
$menu_items = [];
try {
    $stmt = $pdo->query("SELECT * FROM menus WHERE is_active = 1 AND parent_id IS NULL ORDER BY sort_order ASC");
    $menu_items = $stmt->fetchAll();
} catch (PDOException $e) {
    // Jika tabel belum ada, gunakan menu default
    $menu_items = [
        ['name' => 'Beranda', 'url' => 'index.php', 'slug' => 'beranda'],
        ['name' => 'Profil', 'url' => 'profil.php', 'slug' => 'profil'],
        ['name' => 'Akademik', 'url' => 'akademik.php', 'slug' => 'akademik'],
        ['name' => 'Dosen', 'url' => 'dosen.php', 'slug' => 'dosen'],
        ['name' => 'Berita', 'url' => 'berita.php', 'slug' => 'berita'],
        ['name' => 'Agenda', 'url' => 'agenda.php', 'slug' => 'agenda'],
        ['name' => 'Galeri', 'url' => 'galeri.php', 'slug' => 'galeri'],
        ['name' => 'Alumni', 'url' => 'alumni.php', 'slug' => 'alumni'],
        ['name' => 'Kontak', 'url' => 'kontak.php', 'slug' => 'kontak'],
    ];
}
?>

<!-- Top Bar -->
<div class="top-bar">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6 d-none d-md-block">
                <small><i class="fas fa-university me-2"></i>Institut Teknologi Del</small>
                <span class="mx-2">|</span>
                <small><i class="fas fa-building me-2"></i>Fakultas Informatika dan Teknik Elektro</small>
            </div>
            <div class="col-md-6 text-md-end">
                <a href="mailto:informatika@del.ac.id" class="text-white text-decoration-none small">
                    <i class="fas fa-envelope me-1"></i> informatika@del.ac.id
                </a>
                <span class="mx-2 text-white">|</span>
                <a href="<?= BASE_URL ?>admin/login.php" class="text-white text-decoration-none small">
                    <i class="fas fa-lock me-1"></i> Login Admin
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Main Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark sticky-top">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="<?= BASE_URL ?>">
            
            <!-- Logo Container -->
            <div class="me-2" style="background: white; padding: 5px; border-radius: 5px; height: 50px; width: 50px; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                
                <!-- PERBAIKAN MUTLAK: Menggunakan BASE_URL agar path menjadi absolut dan 100% benar -->
                <img src="<?= BASE_URL ?>assets/uploads/logo-himasti.jpeg" 
                     alt="Logo HIMASTI" 
                     style="max-width: 100%; max-height: 100%; object-fit: contain;">
                     
            </div>
            
            <div>
                <div class="fw-bold text-white" style="font-size: 20px; letter-spacing: 1px;">HIMASTI</div>
                <small class="text-white-50" style="font-size: 11px;">Institut Teknologi Del</small>
            </div>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <?php foreach ($menu_items as $item): ?>
                    <?php
                    $url = isset($item['url']) ? $item['url'] : '#';
                    $is_active = (basename($_SERVER['PHP_SELF']) == basename($url)) ? 'active' : '';
                    ?>
                    <li class="nav-item">
                        <a class="nav-link <?= $is_active ?>" href="<?= BASE_URL . $url ?>">
                            <?= htmlspecialchars($item['name']) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>

            <div class="ms-lg-3 mt-3 mt-lg-0">
                <a href="https://spmbdel.del.ac.id/login" target="_blank" class="btn btn-primary btn-sm rounded-pill px-4">
                    <i class="fas fa-user-plus me-1"></i> Daftar Sekarang
                </a>
            </div>
        </div>
    </div>
</nav>