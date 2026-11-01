<?php
// admin/alumni/index.php
require_once __DIR__ . '/../cek-login.php';
require_once __DIR__ . '/../../config/database.php';

$page_title = "Jejaring Alumni";

$stat_total = $pdo->query("SELECT COUNT(*) FROM alumni")->fetchColumn();
$stat_bekerja = $pdo->query("SELECT COUNT(*) FROM alumni WHERE current_job != '' AND current_job IS NOT NULL")->fetchColumn();
$stat_tahun_terbaru = $pdo->query("SELECT MAX(graduation_year) FROM alumni")->fetchColumn() ?: '-';

$stmt = $pdo->query("SELECT * FROM alumni ORDER BY graduation_year DESC, name ASC");
$alumni_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?> - Admin HIMASTI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { font-family: 'Inter', 'Segoe UI', Tahoma, sans-serif; }
        body { background: #f8fafc; display: flex; min-height: 100vh; margin: 0; color: #334155; }
        
        .sidebar { width: 250px; background: #0f172a; color: white; position: fixed; height: 100vh; overflow-y: auto; z-index: 1000; }
        .sidebar-header { padding: 25px 20px; border-bottom: 1px solid rgba(255,255,255,0.05); background: #0b1120; }
        .sidebar-header h2 { color: #f8fafc; font-size: 22px; margin-bottom: 5px; font-weight: 700; letter-spacing: 1px;}
        .sidebar-header p { color: #94a3b8; font-size: 12px; margin:0;}
        .menu { list-style: none; padding: 20px 0; margin:0;}
        .menu li a { display: flex; align-items: center; gap: 12px; color: #cbd5e1; text-decoration: none; padding: 12px 20px; transition: all 0.3s; border-left: 3px solid transparent; font-size: 14px; }
        .menu li a i { width: 20px; text-align: center; }
        .menu li a:hover, .menu li a.active { background: #1e293b; color: #fff; border-left-color: #38bdf8; }
        .menu-divider { color: #64748b; font-size: 11px; padding: 15px 20px 5px; text-transform: uppercase; font-weight: 700; letter-spacing: 1px;}
        
        .main { margin-left: 250px; padding: 35px 40px; width: calc(100% - 250px); }
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .page-header h1 { font-size: 26px; font-weight: 700; color: #0f172a; margin: 0; }
        
        .stat-card { background: #fff; border-radius: 12px; padding: 25px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); display: flex; align-items: center; gap: 20px; border: 1px solid #e2e8f0; }
        .stat-icon { width: 60px; height: 60px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px; }
        .stat-info h3 { font-size: 28px; font-weight: 700; margin: 0; color: #0f172a; }
        .stat-info p { font-size: 14px; color: #64748b; margin: 0; font-weight: 500; text-transform: uppercase; letter-spacing: 0.5px; }
        
        .btn-tambah { background: #0f172a; color: white; border: none; border-radius: 8px; font-weight: 600; padding: 10px 20px; transition: 0.3s; text-decoration: none; display: inline-flex; align-items: center;}
        .btn-tambah:hover { background: #1e293b; color: white; transform: translateY(-2px); box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        
        .alumni-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 25px; text-align: center; transition: 0.3s; box-shadow: 0 2px 4px -1px rgba(0,0,0,0.03); position: relative; height: 100%; display: flex; flex-direction: column; }
        .alumni-card:hover { transform: translateY(-3px); box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); border-color: #cbd5e1; }
        
        .alumni-avatar { width: 85px; height: 85px; border-radius: 50%; object-fit: cover; border: 3px solid #f8fafc; margin: 0 auto 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .alumni-name { font-size: 16px; font-weight: 700; color: #0f172a; margin-bottom: 5px; }
        .alumni-job { font-size: 13px; color: #0369a1; font-weight: 600; margin-bottom: 5px; }
        .alumni-company { font-size: 12.5px; color: #475569; margin-bottom: 15px; }
        
        .badge-year { position: absolute; top: 15px; right: 15px; font-size: 11px; padding: 5px 10px; border-radius: 20px; font-weight: 700; background: #fef3c7; color: #d97706; border: 1px solid #fde68a;}
        
        .alumni-actions { display: flex; justify-content: center; gap: 8px; border-top: 1px dashed #e2e8f0; padding-top: 15px; margin-top: auto;}
        .btn-icon { width: 34px; height: 34px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; text-decoration: none; transition: 0.3s; font-size: 13px; }
        .btn-linkedin { background: #f0f9ff; color: #0077b5; }
        .btn-linkedin:hover { background: #e0f2fe; }
        .btn-edit { background: #f8fafc; color: #475569; border: 1px solid #e2e8f0;}
        .btn-edit:hover { background: #e2e8f0; color: #0f172a; }
        .btn-delete { background: #fee2e2; color: #ef4444; }
        .btn-delete:hover { background: #fecaca; color: #dc2626; }
    </style>
</head>
<body>
    
    <div class="sidebar">
        <div class="sidebar-header">
            <h2>HIMASTI</h2>
            <p>Admin Panel &bull; IT Del</p>
        </div>
        <ul class="menu">
            <li><a href="../dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
            <li class="menu-divider">Konten</li>
            <li><a href="../berita/index.php"><i class="fas fa-newspaper"></i> Kelola Berita</a></li>
            <li><a href="../agenda/index.php"><i class="fas fa-calendar-alt"></i> Kelola Agenda</a></li>
            <li><a href="../galeri/index.php"><i class="fas fa-images"></i> Kelola Galeri</a></li>
            <li><a href="../pesan/index.php"><i class="fas fa-envelope"></i> Pesan Masuk</a></li>
            <li class="menu-divider">Profil Organisasi</li>
            <li><a href="../profil/index.php"><i class="fas fa-university"></i> Edit Profil Institusi</a></li>
            <li><a href="../pengurus/index.php"><i class="fas fa-sitemap"></i> Struktur Pengurus</a></li>
            <li><a href="../program/index.php"><i class="fas fa-star"></i> Program Unggulan</a></li>
            <li class="menu-divider">Master Data</li>
            <li><a href="../dosen/index.php"><i class="fas fa-chalkboard-teacher"></i> Data Dosen</a></li>
            <li><a href="index.php" class="active"><i class="fas fa-user-graduate"></i> Jejaring Alumni</a></li>
            <li><a href="../user/index.php"><i class="fas fa-users-cog"></i> Pengguna Sistem</a></li>
            <li><a href="../pengaturan/index.php"><i class="fas fa-cog"></i> Konfigurasi Web</a></li>
        </ul>
    </div>
    
    <div class="main">
        <div class="page-header">
            <div>
                <h1><i class="fas fa-user-graduate text-primary me-2"></i> Kelola Jejaring Alumni</h1>
                <p class="text-muted mt-1 mb-0" style="font-size: 14px;">Manajemen data sebaran lulusan Sarjana Informatika IT Del.</p>
            </div>
            <!-- Tombol Tambah diaktifkan -->
            <a href="tambah.php" class="btn-tambah shadow-sm">
                <i class="fas fa-user-plus me-2"></i>Tambah Alumni
            </a>
        </div>
        
        <?php if (isset($_GET['msg'])): ?>
            <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 rounded-3">
                <i class="fas fa-check-circle me-2"></i> <?= htmlspecialchars($_GET['msg']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row g-4 mb-5">
            <div class="col-md-4"><div class="stat-card"><div class="stat-icon bg-primary bg-opacity-10 text-primary"><i class="fas fa-users"></i></div><div class="stat-info"><p>Total Alumni Terdata</p><h3><?= $stat_total ?></h3></div></div></div>
            <div class="col-md-4"><div class="stat-card"><div class="stat-icon bg-success bg-opacity-10 text-success"><i class="fas fa-briefcase"></i></div><div class="stat-info"><p>Alumni Berkarir</p><h3><?= $stat_bekerja ?></h3></div></div></div>
            <div class="col-md-4"><div class="stat-card"><div class="stat-icon bg-warning bg-opacity-10 text-warning"><i class="fas fa-calendar-check"></i></div><div class="stat-info"><p>Lulusan Terbaru</p><h3><?= $stat_tahun_terbaru ?></h3></div></div></div>
        </div>

        <h4 class="fw-bold mb-4" style="color: #0f172a;"><i class="fas fa-address-book me-2"></i> Direktori Alumni Informatika</h4>
        
        <div class="row g-4">
            <?php if (empty($alumni_list)): ?>
                <div class="col-12"><div class="alert alert-info border-0 shadow-sm rounded-3 bg-light text-center py-5"><h5 class="text-muted">Belum ada data alumni yang didaftarkan.</h5></div></div>
            <?php else: ?>
                <?php foreach ($alumni_list as $alm): 
                    $nama_tanpa_gelar = explode(',', $alm['name'])[0];
                    $nama_bersih = trim(preg_replace('/[^a-zA-Z\s]/', '', $nama_tanpa_gelar));
                    if(empty($nama_bersih)) $nama_bersih = "Alumni";
                ?>
                <div class="col-md-4 col-sm-6">
                    <div class="alumni-card">
                        <span class="badge-year">Class of <?= htmlspecialchars($alm['graduation_year']) ?></span>
                        
                        <img src="../../assets/uploads/<?= htmlspecialchars($alm['photo']) ?>" 
                             onerror="this.src='https://ui-avatars.com/api/?name=<?= urlencode($nama_bersih) ?>&background=f1f5f9&color=0f172a&size=150'" 
                             class="alumni-avatar" alt="<?= htmlspecialchars($alm['name']) ?>">
                        
                        <h4 class="alumni-name"><?= htmlspecialchars($alm['name']) ?></h4>
                        
                        <?php if(!empty($alm['current_job'])): ?>
                            <div class="alumni-job"><i class="fas fa-briefcase me-1"></i> <?= htmlspecialchars($alm['current_job']) ?></div>
                        <?php endif; ?>
                        
                        <?php if(!empty($alm['company'])): ?>
                            <div class="alumni-company">at <strong><?= htmlspecialchars($alm['company']) ?></strong></div>
                        <?php endif; ?>
                        
                        <div class="alumni-actions">
                            <?php if(!empty($alm['linkedin_url'])): ?>
                            <a href="<?= htmlspecialchars($alm['linkedin_url']) ?>" target="_blank" class="btn-icon btn-linkedin" title="Profil LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                            <?php endif; ?>
                            
                            <!-- Tombol Edit dan Hapus diaktifkan -->
                            <a href="edit.php?id=<?= $alm['id'] ?>" class="btn-icon btn-edit" title="Edit Data"><i class="fas fa-pen"></i></a>
                            <a href="hapus.php?id=<?= $alm['id'] ?>" class="btn-icon btn-delete" title="Hapus Alumni" onclick="return confirm('Yakin ingin menghapus data alumni ini?')"><i class="fas fa-trash"></i></a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>