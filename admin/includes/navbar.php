<?php $halaman_sekarang = basename($_SERVER['PHP_SELF']); ?>
<style>
    /* 1. TOP BAR (Baris Atas Sangat Gelap) */
    .top-bar-custom { 
        background-color: #0f172a !important; 
        color: #cbd5e1 !important; 
        font-size: 12px !important; 
        padding: 8px 0 !important; 
        border-bottom: 1px solid rgba(255,255,255,0.05) !important;
    }
    .top-bar-custom a { color: #cbd5e1 !important; text-decoration: none !important; transition: 0.3s !important;}
    .top-bar-custom a:hover { color: #ffffff !important; }

    /* 2. NAVBAR UTAMA (Warna Gelap Navy seperti Gambar 2) */
    nav.navbar-custom { 
        background-color: #1e293b !important; 
        padding: 12px 0 !important; 
        border: none !important; 
        margin: 0 !important;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1) !important;
    }
    
    /* Logo Kotak Putih & Teks */
    nav.navbar-custom .navbar-brand { 
        color: #ffffff !important; 
        font-weight: 800 !important; 
        font-size: 20px !important; 
        display: flex !important; 
        align-items: center !important;
        text-decoration: none !important;
    }
    .logo-box {
        width: 38px !important; 
        height: 38px !important;
        background-color: #ffffff !important;
        border-radius: 6px !important;
        margin-right: 12px !important;
        display: inline-block !important;
    }
    nav.navbar-custom .brand-subtitle { 
        font-size: 10px !important; 
        color: #94a3b8 !important; 
        display: block !important; 
        font-weight: 500 !important;
        margin-top: 2px !important;
    }

    /* 3. MENU ITEMS (Teks Terang) */
    #navbarNav .nav-link { 
        color: #e2e8f0 !important; 
        font-weight: 600 !important; 
        font-size: 13.5px !important; 
        margin: 0 3px !important; 
        padding: 8px 15px !important; 
        border-radius: 8px !important; 
        transition: all 0.3s ease !important; 
    }
    #navbarNav .nav-link:hover { 
        background-color: rgba(255,255,255,0.1) !important; 
        color: #ffffff !important; 
    }
    /* Menu Aktif (Kotak Abu-Abu Khas Gambar 2) */
    #navbarNav .nav-link.active { 
        background-color: #475569 !important; 
        color: #ffffff !important; 
    }

    /* 4. TOMBOL DAFTAR (Warna Teal/Cyan Khas Gambar 2) */
    .btn-daftar-custom { 
        background-color: #498a96 !important; 
        color: #ffffff !important; 
        border-radius: 50px !important; 
        padding: 8px 22px !important; 
        font-weight: 600 !important; 
        font-size: 13.5px !important; 
        border: none !important; 
        display: inline-flex !important; 
        align-items: center !important; 
        gap: 8px !important;
        transition: 0.3s !important;
    }
    .btn-daftar-custom:hover { 
        background-color: #3a727d !important; 
        transform: translateY(-2px) !important;
    }
</style>

<!-- TOP BAR -->
<div class="top-bar-custom d-none d-lg-block">
    <div class="container-fluid px-lg-5 d-flex justify-content-between align-items-center">
        <div class="d-flex gap-4">
            <span><i class="fas fa-university me-2"></i>Institut Teknologi Del</span>
            <span><i class="fas fa-building me-2"></i>Fakultas Informatika dan Teknik Elektro</span>
        </div>
        <div class="d-flex gap-4">
            <a href="mailto:informatika@del.ac.id"><i class="fas fa-envelope me-2"></i>informatika@del.ac.id</a>
            <a href="admin/login.php"><i class="fas fa-lock me-2"></i>Login Admin</a>
        </div>
    </div>
</div>

<!-- NAVBAR UTAMA -->
<nav class="navbar navbar-expand-lg navbar-custom sticky-top">
    <div class="container-fluid px-lg-5">
        
        <a class="navbar-brand" href="index.php">
            <div class="logo-box"></div>
            <div>
                <div style="line-height: 1;">HIMASTI</div>
                <span class="brand-subtitle">Institut Teknologi Del</span>
            </div>
        </a>

        <button class="navbar-toggler bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto align-items-center">
                <li class="nav-item"><a class="nav-link <?= $halaman_sekarang == 'index.php' ? 'active' : '' ?>" href="index.php">Beranda</a></li>
                <li class="nav-item"><a class="nav-link <?= $halaman_sekarang == 'profil.php' ? 'active' : '' ?>" href="profil.php">Profil</a></li>
                <li class="nav-item"><a class="nav-link <?= $halaman_sekarang == 'akademik.php' ? 'active' : '' ?>" href="akademik.php">Akademik</a></li>
                <li class="nav-item"><a class="nav-link <?= $halaman_sekarang == 'dosen.php' ? 'active' : '' ?>" href="dosen.php">Dosen</a></li>
                <!-- Menu Riset Sudah Dihapus Dari Sini -->
                <li class="nav-item"><a class="nav-link <?= $halaman_sekarang == 'berita.php' ? 'active' : '' ?>" href="berita.php">Berita</a></li>
                <li class="nav-item"><a class="nav-link <?= $halaman_sekarang == 'agenda.php' ? 'active' : '' ?>" href="agenda.php">Agenda</a></li>
                <li class="nav-item"><a class="nav-link <?= $halaman_sekarang == 'galeri.php' ? 'active' : '' ?>" href="galeri.php">Galeri</a></li>
                <li class="nav-item"><a class="nav-link <?= $halaman_sekarang == 'alumni.php' ? 'active' : '' ?>" href="alumni.php">Alumni</a></li>
                <li class="nav-item"><a class="nav-link <?= $halaman_sekarang == 'kontak.php' ? 'active' : '' ?>" href="kontak.php">Kontak</a></li>
            </ul>
            
            <!-- Tombol Kanan -->
            <a href="https://spmbdel.del.ac.id" target="_blank" class="btn btn-daftar-custom shadow-sm">
                <i class="fas fa-user-plus"></i> Daftar Sekarang
            </a>
        </div>
        
    </div>
</nav>