<?php
// galeri.php - Halaman Publik Galeri Dokumentasi HIMASTI
require_once __DIR__ . '/config/database.php';

$page_title = 'Galeri Kegiatan';

// =========================================================
// AMBIL DATA GALERI DARI DATABASE
// =========================================================
try {
    // Mengambil semua data foto dari tabel galleries (atau galeri) diurutkan dari yang terbaru
    // Catatan: Nama tabel umumnya 'galleries' atau 'galeri' pada sistem ini
    $stmt = $pdo->query("SELECT * FROM galleries ORDER BY id DESC");
    $galeri_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    try {
        // Fallback jika nama tabelnya 'galeri' (bahasa indonesia)
        $stmt = $pdo->query("SELECT * FROM galeri ORDER BY id DESC");
        $galeri_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e2) {
        $galeri_list = [];
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?> - HIMASTI IT Del</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { font-family: 'Inter', 'Segoe UI', Tahoma, sans-serif; }
        body { background-color: #f8fafc; }
        
        /* Navbar Style Premium (Konsisten) */
        .navbar-custom { background-color: #0f172a; padding: 15px 0; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
        .navbar-brand { color: #f8fafc !important; font-weight: 800; font-size: 24px; letter-spacing: 0.5px; }
        .nav-link { color: #cbd5e1 !important; font-weight: 600; font-size: 14px; margin: 0 8px; transition: 0.3s; padding: 8px 15px !important; border-radius: 8px;}
        .nav-link:hover, .nav-link.active { color: #fff !important; background: rgba(255,255,255,0.1); }
        .btn-portal { background-color: #38bdf8; color: #0f172a; font-weight: 700; border-radius: 8px; padding: 10px 20px; transition: 0.3s; border: none; font-size: 14px;}
        .btn-portal:hover { background-color: #0284c7; color: white; transform: translateY(-2px);}
        
        /* Header Halaman */
        .page-header-custom { background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: white; padding: 70px 0; text-align: center; margin-bottom: 50px;}
        .page-header-custom h1 { font-weight: 800; font-size: 38px; margin-bottom: 15px; }
        .page-header-custom p { color: #cbd5e1; font-size: 16px; max-width: 600px; margin: 0 auto;}

        /* Desain Kartu Galeri Premium */
        .gallery-card { 
            position: relative; 
            border-radius: 16px; 
            overflow: hidden; 
            height: 280px; 
            box-shadow: 0 10px 20px -5px rgba(0,0,0,0.05);
            cursor: pointer;
            border: 1px solid #e2e8f0;
            background: #fff;
        }
        
        .gallery-img { 
            width: 100%; 
            height: 100%; 
            object-fit: cover; 
            transition: transform 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94); 
        }
        
        .gallery-card:hover .gallery-img { 
            transform: scale(1.08); 
        }
        
        /* Overlay Gradasi Hitam di Bawah Foto */
        .gallery-overlay { 
            position: absolute; 
            bottom: 0; 
            left: 0; 
            right: 0; 
            background: linear-gradient(to top, rgba(15,23,42,0.95) 0%, rgba(15,23,42,0.6) 50%, rgba(15,23,42,0) 100%); 
            padding: 40px 20px 20px; 
            color: white; 
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            transition: 0.3s;
        }
        
        .gallery-title { 
            font-weight: 700; 
            font-size: 16px; 
            margin-bottom: 5px; 
            line-height: 1.3;
            transform: translateY(0);
            transition: 0.3s;
        }
        
        .gallery-date { 
            font-size: 12px; 
            color: #38bdf8; 
            font-weight: 600;
            margin-bottom: 0; 
            display: flex;
            align-items: center;
            gap: 6px;
        }
        
        /* Ikon Kaca Pembesar saat Hover */
        .gallery-zoom-icon {
            position: absolute;
            top: 20px;
            right: 20px;
            background: rgba(255,255,255,0.9);
            color: #0f172a;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transform: scale(0.5);
            transition: 0.3s;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        
        .gallery-card:hover .gallery-zoom-icon {
            opacity: 1;
            transform: scale(1);
        }

    </style>
</head>
<body>

    <!-- NAVBAR KONSISTEN -->
    <?php @include 'includes/navbar.php'; ?>

    <!-- HEADER HALAMAN -->
    <div class="page-header-custom">
        <div class="container">
            <h1><i class="fas fa-images me-3" style="color:#38bdf8;"></i>Galeri Dokumentasi</h1>
            <p>Jelajahi momen-momen berharga, kegiatan akademik, dan aktivitas seru mahasiswa Prodi Informatika IT Del.</p>
        </div>
    </div>

    <div class="container mb-5 pb-5">
        
        <!-- GRID KARTU FOTO -->
        <div class="row g-4 justify-content-center">
            <?php if(empty($galeri_list)): ?>
                <div class="col-12 text-center mt-2">
                    <div class="alert alert-light border p-5" style="border-radius: 15px;">
                        <i class="fas fa-camera-retro fa-4x text-muted mb-3 opacity-50"></i>
                        <h5 class="fw-bold text-muted">Belum Ada Foto Tersedia</h5>
                        <p class="text-muted small">Album galeri saat ini sedang kosong atau dalam proses unggah.</p>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach($galeri_list as $gal): 
                    $image_path = 'assets/uploads/' . ($gal['image'] ?? $gal['foto'] ?? 'default.jpg');
                    $judul = $gal['title'] ?? $gal['judul'] ?? 'Dokumentasi Kegiatan';
                    $tanggal = !empty($gal['date']) ? date('d M Y', strtotime($gal['date'])) : (!empty($gal['created_at']) ? date('d M Y', strtotime($gal['created_at'])) : '');
                ?>
                <div class="col-lg-4 col-md-6">
                    <div class="gallery-card">
                        <!-- Foto Utama -->
                        <img src="<?= htmlspecialchars($image_path) ?>" class="gallery-img" alt="<?= htmlspecialchars($judul) ?>" onerror="this.src='https://via.placeholder.com/600x400?text=Foto+Tidak+Ditemukan'">
                        
                        <!-- Ikon Melayang Kanan Atas -->
                        <div class="gallery-zoom-icon">
                            <i class="fas fa-search-plus"></i>
                        </div>
                        
                        <!-- Overlay Informasi di Bawah -->
                        <div class="gallery-overlay">
                            <h5 class="gallery-title"><?= htmlspecialchars($judul) ?></h5>
                            <?php if($tanggal): ?>
                                <p class="gallery-date"><i class="far fa-calendar-alt"></i> <?= $tanggal ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

    </div>

    <!-- FOOTER SEDERHANA -->
    <footer style="background: #0b1120; padding: 30px 0; color: #94a3b8; text-align: center; font-size: 14px;">
        <div class="container">
            © 2026 HIMASTI IT Del. All Rights Reserved.
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>