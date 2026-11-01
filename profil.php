<?php
// profil.php - Halaman Publik Profil HIMASTI
require_once __DIR__ . '/config/database.php';

// =========================================================
// 1. AMBIL DATA PROFIL (Visi, Misi, Sejarah)
// =========================================================
try {
    $stmt = $pdo->query("SELECT * FROM profile_settings LIMIT 1");
    $profil = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $profil = [];
}

$visi = !empty($profil['vision']) ? $profil['vision'] : 'Data visi sedang dalam proses pembaruan.';
$misi = !empty($profil['mission']) ? $profil['mission'] : 'Data misi sedang dalam proses pembaruan.';
$sejarah = !empty($profil['history']) ? $profil['history'] : 'Data sejarah sedang dalam proses pembaruan.';

// =========================================================
// 2. AMBIL & KELOMPOKKAN DATA PENGURUS DENGAN SMART SORTING
// =========================================================
$grouped_pengurus = [];
try {
    // Kueri cerdas: Memprioritaskan BPH, lalu Jabatan dengan urutan hirarki yang akurat
    $stmt = $pdo->query("
        SELECT m.*, d.name as divisi_name 
        FROM members m 
        LEFT JOIN divisions d ON m.division_id = d.id 
        WHERE m.is_active = 1 
        ORDER BY 
            -- Prioritas 1: Taruh BPH paling atas
            CASE 
                WHEN d.name LIKE '%BPH%' OR d.name LIKE '%Badan Pengurus Harian%' OR d.name LIKE '%Inti%' THEN 1 
                ELSE 2 
            END ASC,
            d.name ASC, 
            
            -- Prioritas 2: Hirarki Jabatan (Perbaikan Logika)
            CASE 
                WHEN m.position LIKE '%Wakil Ketua%' THEN 2
                WHEN m.position LIKE '%Ketua%' THEN 1
                WHEN m.position LIKE '%Wakil Sekretaris%' THEN 4
                WHEN m.position LIKE '%Sekretaris%' THEN 3
                WHEN m.position LIKE '%Wakil Bendahara%' THEN 6
                WHEN m.position LIKE '%Bendahara%' THEN 5
                WHEN m.position LIKE '%Koordinator%' OR m.position LIKE '%Kepala%' THEN 7
                ELSE 8
            END ASC,
            
            -- Prioritas 3: Abjad Nama
            m.name ASC
    ");
    $pengurus_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($pengurus_list as $pg) {
        $nama_divisi = !empty($pg['divisi_name']) ? $pg['divisi_name'] : 'Divisi Lainnya';
        $grouped_pengurus[$nama_divisi][] = $pg;
    }
} catch (PDOException $e) {
    $grouped_pengurus = [];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil HIMASTI - Institut Teknologi Del</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { font-family: 'Inter', 'Segoe UI', Tahoma, sans-serif; }
        body { background-color: #f8fafc; }
        
        /* Navbar Style */
        .navbar-custom { background-color: #0f172a; padding: 15px 0; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
        .navbar-brand { color: #f8fafc !important; font-weight: 800; font-size: 24px; letter-spacing: 0.5px; }
        .nav-link { color: #cbd5e1 !important; font-weight: 600; font-size: 14px; margin: 0 8px; transition: 0.3s; padding: 8px 15px !important; border-radius: 8px;}
        .nav-link:hover, .nav-link.active { color: #fff !important; background: rgba(255,255,255,0.1); }
        .btn-portal { background-color: #38bdf8; color: #0f172a; font-weight: 700; border-radius: 8px; padding: 10px 20px; transition: 0.3s; border: none; font-size: 14px;}
        .btn-portal:hover { background-color: #0284c7; color: white; transform: translateY(-2px);}
        
        /* Header Profil */
        .page-header-custom { background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: white; padding: 60px 0; text-align: center; margin-bottom: 50px;}
        .page-header-custom h1 { font-weight: 800; font-size: 36px; margin-bottom: 15px; }
        .page-header-custom p { color: #cbd5e1; font-size: 16px; max-width: 600px; margin: 0 auto;}

        /* Section Title */
        .section-title { text-align: center; margin-bottom: 40px; margin-top: 60px; }
        .section-subtitle { color: #38bdf8; font-weight: 700; font-size: 13px; letter-spacing: 2px; text-transform: uppercase; margin-bottom: 10px; }
        .section-title h2 { color: #0f172a; font-weight: 800; font-size: 32px; }
        
        /* Card Style */
        .content-card { background: #fff; border-radius: 12px; padding: 45px; text-align: center; max-width: 900px; margin: 0 auto; line-height: 1.8; color: #475569; font-size: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);}
        .content-card-dark { background: #1e293b; color: #e2e8f0; border-radius: 12px; padding: 45px; max-width: 900px; margin: 0 auto; line-height: 1.8; font-size: 16px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1);}
        .misi-text { text-align: left; display: inline-block; width: 100%;}
        
        /* Gaya Judul Divisi */
        .divisi-wrapper { margin-top: 60px; margin-bottom: 30px; background: #fff; padding: 40px; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 2px 10px rgba(0,0,0,0.02);}
        .divisi-header { text-align: center; margin-bottom: 35px; }
        .divisi-header h3 { font-size: 20px; font-weight: 800; color: #0f172a; text-transform: uppercase; letter-spacing: 1px; margin: 0;}
        .divisi-line { width: 60px; height: 4px; background: #38bdf8; margin: 15px auto 0; border-radius: 2px;}
        
        /* Card Pengurus (Seragam Semuanya) */
        .member-card { background: #f8fafc; transition: 0.3s; padding: 30px 20px; border-radius: 12px; border: 1px solid #e2e8f0; height: 100%;}
        .member-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.05); border-color: #cbd5e1; background: #fff;}
        .member-avatar { width: 90px; height: 90px; border-radius: 50%; object-fit: cover; border: 3px solid #fff; box-shadow: 0 4px 10px rgba(0,0,0,0.08); margin-bottom: 15px;}
    </style>
</head>
<body>

    <?php @include 'includes/navbar.php'; ?>

    <div class="page-header-custom">
        <div class="container">
            <h1>Mengenal HIMASTI Lebih Dekat</h1>
            <p>Menyelami arah gerak, tujuan, dan tokoh penggerak di balik Himpunan Mahasiswa Sarjana Informatika Institut Teknologi Del.</p>
        </div>
    </div>

    <div class="container mb-5 pb-5">
        
        <div class="section-title">
            <div class="section-subtitle">ARAH KAMI</div>
            <h2>Visi Organisasi</h2>
        </div>
        <div class="content-card mb-5"><?= nl2br(htmlspecialchars($visi)) ?></div>

        <div class="section-title">
            <div class="section-subtitle">LANGKAH KAMI</div>
            <h2>Misi Organisasi</h2>
        </div>
        <div class="content-card-dark mb-5"><div class="misi-text"><?= nl2br(htmlspecialchars($misi)) ?></div></div>

        <div class="section-title">
            <div class="section-subtitle">REKAM JEJAK</div>
            <h2>Sejarah Singkat</h2>
        </div>
        <div class="content-card mb-5" style="text-align: justify;"><?= nl2br(htmlspecialchars($sejarah)) ?></div>
        
        <div class="section-title" style="margin-top: 100px;">
            <div class="section-subtitle">MOTOR PENGGERAK</div>
            <h2>Struktur Pengurus HIMASTI</h2>
            <p class="text-muted mt-2">Daftar anggota aktif yang mendedikasikan diri untuk kemajuan himpunan.</p>
        </div>
        
        <?php if(empty($grouped_pengurus)): ?>
            <div class="text-center text-muted mt-4">
                <div class="alert alert-light border">Belum ada data struktur pengurus yang didaftarkan.</div>
            </div>
        <?php else: ?>
            <?php foreach($grouped_pengurus as $nama_divisi => $members): ?>
                <div class="divisi-wrapper">
                    <div class="divisi-header">
                        <h3><i class="fas fa-users text-primary me-2" style="font-size: 18px;"></i> <?= htmlspecialchars($nama_divisi) ?></h3>
                        <div class="divisi-line"></div>
                    </div>
                    
                    <div class="row g-4 justify-content-center">
                        <?php foreach($members as $pg): 
                            $nama_bersih = trim(preg_replace('/[^a-zA-Z\s]/', '', $pg['name']));
                        ?>
                        <div class="col-lg-3 col-md-4 col-sm-6">
                            <div class="member-card text-center">
                                <img src="https://ui-avatars.com/api/?name=<?= urlencode($nama_bersih) ?>&background=0f172a&color=fff&size=150" class="member-avatar" alt="<?= htmlspecialchars($pg['name']) ?>">
                                <h6 class="fw-bold mb-1" style="font-size: 15px; color: #0f172a;"><?= htmlspecialchars($pg['name']) ?></h6>
                                <!-- Warna font sekarang seragam biru semua (#0284c7) -->
                                <p class="mb-0" style="font-size: 13px; font-weight: 600; color: #0284c7 !important;"><?= htmlspecialchars($pg['position']) ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

    </div>

    <footer style="background: #0b1120; padding: 30px 0; color: #94a3b8; text-align: center; font-size: 14px;">
        <div class="container">© 2026 HIMASTI IT Del. All Rights Reserved.</div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>