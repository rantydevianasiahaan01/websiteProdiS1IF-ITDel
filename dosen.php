<?php
// dosen.php - Halaman Publik Direktori Dosen HIMASTI
require_once __DIR__ . '/config/database.php';

// Ambil seluruh data dosen yang aktif dari database
try {
    $stmt = $pdo->query("SELECT * FROM lecturers WHERE is_active = 1 ORDER BY name ASC");
    $dosen_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $dosen_list = [];
}

// Fungsi Pintar Pembersih Gelar (Tetap disimpan untuk keperluan lain)
function getCleanName($fullname) {
    $nama_tanpa_gelar = explode(',', $fullname)[0];
    $nama_bersih = trim(preg_replace('/[^a-zA-Z\s]/', '', str_replace(['Dr.', 'Prof.', 'Ir.'], '', $nama_tanpa_gelar)));
    return $nama_bersih ?: 'Dosen';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Direktori Dosen - HIMASTI IT Del</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { font-family: 'Inter', 'Segoe UI', Tahoma, sans-serif; }
        body { background-color: #f8fafc; }
        
        /* Navbar Style Premium */
        .navbar-custom { background-color: #0f172a; padding: 15px 0; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
        .navbar-brand { color: #f8fafc !important; font-weight: 800; font-size: 24px; letter-spacing: 0.5px; }
        .nav-link { color: #cbd5e1 !important; font-weight: 600; font-size: 14px; margin: 0 8px; transition: 0.3s; padding: 8px 15px !important; border-radius: 8px;}
        .nav-link:hover, .nav-link.active { color: #fff !important; background: rgba(255,255,255,0.1); }
        .btn-portal { background-color: #38bdf8; color: #0f172a; font-weight: 700; border-radius: 8px; padding: 10px 20px; transition: 0.3s; border: none; font-size: 14px;}
        .btn-portal:hover { background-color: #0284c7; color: white; transform: translateY(-2px);}
        
        /* Header Halaman */
        .page-header-custom { background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: white; padding: 60px 0; text-align: center; margin-bottom: 50px;}
        .page-header-custom h1 { font-weight: 800; font-size: 36px; margin-bottom: 15px; }
        .page-header-custom p { color: #cbd5e1; font-size: 16px; max-width: 600px; margin: 0 auto;}

        /* Desain Kartu Dosen */
        .dsn-card { background: #fff; border-radius: 16px; padding: 35px 25px; text-align: center; border: 1px solid #e2e8f0; transition: all 0.3s ease; height: 100%; display: flex; flex-direction: column; position: relative; overflow: hidden;}
        .dsn-card:hover { box-shadow: 0 20px 40px -5px rgba(0,0,0,0.1); transform: translateY(-8px); border-color:#cbd5e1;}
        
        /* Aksen Garis Biru di atas Kartu */
        .dsn-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 5px; background: #38bdf8; }
        
        .dsn-avatar { width: 110px; height: 110px; border-radius: 50%; margin: 0 auto 20px; object-fit: cover; border: 4px solid #f1f5f9; box-shadow: 0 4px 10px rgba(0,0,0,0.05); background-color: #f8f9fa;}
        .dsn-name { font-size: 17px; font-weight: 800; color: #0f172a; margin-bottom: 5px; line-height: 1.4;}
        
        /* Label NIDN */
        .dsn-nidn { font-family: monospace; font-size: 13px; color: #64748b; background: #f8fafc; padding: 3px 10px; border-radius: 6px; display: inline-block; margin-bottom: 12px; border: 1px solid #e2e8f0;}
        
        .dsn-position { font-size: 14px; font-weight: 600; color: #475569; margin-bottom: 15px;}
        
        /* Keahlian (Expertise) */
        .dsn-expertise-wrapper { flex-grow: 1; display: flex; align-items: flex-start; justify-content: center;}
        .dsn-expertise { font-size: 12.5px; color: #0284c7; font-weight: 700; background: #e0f2fe; padding: 6px 15px; border-radius: 20px; border: 1px solid #bae6fd; line-height: 1.5;}
        
        /* Tombol Sosial Media / Scholar */
        .dsn-actions { display: flex; justify-content: center; gap: 10px; margin-top: 25px; padding-top: 20px; border-top: 1px dashed #e2e8f0;}
        .btn-social { width: 38px; height: 38px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; text-decoration: none; transition: 0.3s; font-size: 15px; }
        .btn-scholar { background: #fef08a; color: #854d0e; }
        .btn-scholar:hover { background: #fde047; transform: translateY(-3px);}
        .btn-linkedin { background: #f0f9ff; color: #0077b5; }
        .btn-linkedin:hover { background: #e0f2fe; transform: translateY(-3px);}
    </style>
</head>
<body>

    <!-- NAVBAR -->
    <?php @include 'includes/navbar.php'; ?>

    <!-- HEADER HALAMAN DOSEN -->
    <div class="page-header-custom">
        <div class="container">
            <h1>Direktori Dosen</h1>
            <p>Mengenal lebih dekat para tenaga pendidik, pakar, dan praktisi yang membimbing mahasiswa Program Studi Informatika IT Del.</p>
        </div>
    </div>

    <!-- KONTEN KARTU DOSEN -->
    <div class="container mb-5 pb-5">
        
        <div class="row g-4 justify-content-center">
            <?php if(empty($dosen_list)): ?>
                <div class="col-12 text-center mt-5">
                    <div class="alert alert-light border p-5" style="border-radius: 15px;">
                        <i class="fas fa-chalkboard-teacher fa-3x text-muted mb-3 opacity-50"></i>
                        <h5 class="text-muted">Data dosen sedang dalam proses pembaruan.</h5>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach($dosen_list as $dsn): ?>
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="dsn-card">
                        
                        <!-- Foto Avatar dengan Jalur Folder Baru -->
                        <?php 
                            // Pastikan jika kolom photo kosong di database, menggunakan default-user.png
                            $foto_tampil = !empty($dsn['photo']) ? $dsn['photo'] : 'default-user.png';
                        ?>
                        <img src="assets/img/lecturers/<?= htmlspecialchars($foto_tampil) ?>" 
                             onerror="this.src='assets/img/lecturers/default-user.png'" 
                             class="dsn-avatar" alt="<?= htmlspecialchars($dsn['name']) ?>">
                        
                        <!-- Nama & NIDN -->
                        <h4 class="dsn-name"><?= htmlspecialchars($dsn['name']) ?></h4>
                        <?php if(!empty($dsn['nidn'])): ?>
                            <div class="dsn-nidn">NIDN: <?= htmlspecialchars($dsn['nidn']) ?></div>
                        <?php endif; ?>
                        
                        <!-- Jabatan -->
                        <div class="dsn-position"><?= htmlspecialchars($dsn['position'] ?: 'Tenaga Pendidik') ?></div>
                        
                        <!-- Kelompok Keahlian -->
                        <div class="dsn-expertise-wrapper">
                            <div class="dsn-expertise">
                                <i class="fas fa-microscope me-1"></i> <?= htmlspecialchars($dsn['expertise'] ?: 'Informatika Umum') ?>
                            </div>
                        </div>
                        
                        <!-- Tombol Sosial/Publikasi -->
                        <div class="dsn-actions">
                            <?php if(!empty($dsn['scholar_url'])): ?>
                                <a href="<?= htmlspecialchars($dsn['scholar_url']) ?>" target="_blank" class="btn-social btn-scholar" title="Profil Google Scholar">
                                    <i class="fas fa-graduation-cap"></i>
                                </a>
                            <?php endif; ?>
                            
                            <?php if(!empty($dsn['linkedin_url'])): ?>
                                <a href="<?= htmlspecialchars($dsn['linkedin_url']) ?>" target="_blank" class="btn-social btn-linkedin" title="Profil LinkedIn">
                                    <i class="fab fa-linkedin-in"></i>
                                </a>
                            <?php endif; ?>
                            
                            <!-- Tombol Email Default -->
                            <a href="mailto:info@del.ac.id" class="btn-social" style="background:#f1f5f9; color:#475569;" title="Kirim Email">
                                <i class="fas fa-envelope"></i>
                            </a>
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