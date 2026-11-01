<?php
// admin/dosen/index.php
require_once __DIR__ . '/../cek-login.php';
require_once __DIR__ . '/../../config/database.php';

$page_title = "Kelola Dosen";

$stat_total = $pdo->query("SELECT COUNT(*) FROM lecturers")->fetchColumn();
$stat_aktif = $pdo->query("SELECT COUNT(*) FROM lecturers WHERE is_active = 1")->fetchColumn();
$stat_nonaktif = $stat_total - $stat_aktif;

$stmt = $pdo->query("SELECT * FROM lecturers ORDER BY name ASC");
$dosen_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
        
        .lecturer-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 25px; text-align: center; transition: 0.3s; box-shadow: 0 2px 4px -1px rgba(0,0,0,0.03); position: relative; height: 100%; display: flex; flex-direction: column; }
        .lecturer-card:hover { transform: translateY(-3px); box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); border-color: #cbd5e1; }
        
        .lecturer-avatar { width: 90px; height: 90px; border-radius: 50%; object-fit: cover; border: 4px solid #f8fafc; margin: 0 auto 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); background-color: #f1f5f9; }
        .lecturer-name { font-size: 16px; font-weight: 700; color: #0f172a; margin-bottom: 5px; }
        .lecturer-nidn { font-size: 13px; color: #64748b; margin-bottom: 10px; font-family: monospace; background: #f1f5f9; padding: 2px 8px; border-radius: 4px; display: inline-block;}
        .lecturer-expertise { font-size: 12.5px; color: #0369a1; font-weight: 600; background: #e0f2fe; padding: 5px 12px; border-radius: 20px; display: inline-block; margin-bottom: 15px; border: 1px solid #bae6fd;}
        
        .badge-status { position: absolute; top: 15px; right: 15px; font-size: 10px; padding: 4px 8px; border-radius: 20px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;}
        .bg-active { background: #dcfce7; color: #16a34a; }
        .bg-inactive { background: #f1f5f9; color: #64748b; }
        
        .lecturer-actions { display: flex; justify-content: center; gap: 8px; border-top: 1px dashed #e2e8f0; padding-top: 15px; margin-top: auto;}
        .btn-icon { width: 34px; height: 34px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; text-decoration: none; transition: 0.3s; font-size: 13px; }
        .btn-scholar { background: #fef08a; color: #854d0e; }
        .btn-scholar:hover { background: #fde047; }
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
        <div class="sidebar-header"><h2>HIMASTI</h2><p>Admin Panel &bull; IT Del</p></div>
        <ul class="menu">
            <li><a href="../dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
            <li class="menu-divider">Profil Organisasi</li>
            <li><a href="../profil/index.php"><i class="fas fa-university"></i> Edit Profil Institusi</a></li>
            <li><a href="../pengurus/index.php"><i class="fas fa-sitemap"></i> Struktur Pengurus</a></li>
            <li><a href="../program/index.php"><i class="fas fa-star"></i> Program Unggulan</a></li>
            <li class="menu-divider">Master Data</li>
            <li><a href="index.php" class="active"><i class="fas fa-chalkboard-teacher"></i> Data Dosen</a></li>
            <li><a href="../alumni/index.php"><i class="fas fa-user-graduate"></i> Jejaring Alumni</a></li>
            <li><a href="../user/index.php"><i class="fas fa-users-cog"></i> Pengguna Sistem</a></li>
            <li><a href="../pengaturan/index.php"><i class="fas fa-cog"></i> Konfigurasi Web</a></li>
        </ul>
    </div>
    
    <div class="main">
        <div class="page-header">
            <div>
                <h1><i class="fas fa-chalkboard-teacher text-primary me-2"></i> Kelola Data Dosen</h1>
                <p class="text-muted mt-1 mb-0" style="font-size: 14px;">Manajemen profil tenaga pendidik Program Studi Sarjana Informatika.</p>
            </div>
            <a href="tambah.php" class="btn-tambah shadow-sm"><i class="fas fa-user-plus me-2"></i>Tambah Dosen</a>
        </div>
        
        <?php if (isset($_GET['msg'])): ?>
            <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 rounded-3">
                <i class="fas fa-check-circle me-2"></i> <?= htmlspecialchars($_GET['msg']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row g-4 mb-5">
            <div class="col-md-4"><div class="stat-card"><div class="stat-icon bg-primary bg-opacity-10 text-primary"><i class="fas fa-chalkboard-teacher"></i></div><div class="stat-info"><p>Total Dosen Prodi</p><h3><?= $stat_total ?></h3></div></div></div>
            <div class="col-md-4"><div class="stat-card"><div class="stat-icon bg-success bg-opacity-10 text-success"><i class="fas fa-user-check"></i></div><div class="stat-info"><p>Dosen Aktif Mengajar</p><h3><?= $stat_aktif ?></h3></div></div></div>
            <div class="col-md-4"><div class="stat-card"><div class="stat-icon bg-secondary bg-opacity-10 text-secondary"><i class="fas fa-plane-departure"></i></div><div class="stat-info"><p>Tugas Belajar / Non-Aktif</p><h3><?= $stat_nonaktif ?></h3></div></div></div>
        </div>

        <h4 class="fw-bold mb-4" style="color: #0f172a;"><i class="fas fa-address-book me-2"></i> Direktori Tenaga Pendidik</h4>
        
        <div class="row g-4">
            <?php if (empty($dosen_list)): ?>
                <div class="col-12"><div class="alert alert-info border-0 shadow-sm rounded-3 bg-light text-center py-5"><h5 class="text-muted">Belum ada data dosen yang terdaftar.</h5></div></div>
            <?php else: ?>
                <?php foreach ($dosen_list as $dsn): 
                    // Menentukan foto yang akan ditampilkan. Jika kosong, panggil foto default.
                    $foto_tampil = !empty($dsn['photo']) ? $dsn['photo'] : 'default-user.png';
                ?>
                <div class="col-md-4 col-sm-6">
                    <div class="lecturer-card">
                        <span class="badge-status <?= $dsn['is_active'] ? 'bg-active' : 'bg-inactive' ?>"><?= $dsn['is_active'] ? 'Aktif' : 'Tugas Belajar' ?></span>
                        
                        <!-- Jalur foto sudah diperbaiki ke assets/img/lecturers/ -->
                        <img src="../../assets/img/lecturers/<?= htmlspecialchars($foto_tampil) ?>" 
                             onerror="this.src='../../assets/img/lecturers/default-user.png'" 
                             class="lecturer-avatar" alt="<?= htmlspecialchars($dsn['name']) ?>">
                        
                        <h4 class="lecturer-name"><?= htmlspecialchars($dsn['name']) ?></h4>
                        <div class="lecturer-nidn">NIDN: <?= htmlspecialchars($dsn['nidn'] ?: '-') ?></div>
                        <div class="mt-2 mb-2" style="font-size: 13px; color: #475569;"><?= htmlspecialchars($dsn['position'] ?: 'Dosen') ?></div>
                        <div class="lecturer-expertise"><i class="fas fa-microscope me-1"></i> <?= htmlspecialchars($dsn['expertise'] ?: 'Informatika Umum') ?></div>
                        
                        <div class="lecturer-actions">
                            <?php if(!empty($dsn['scholar_url'])): ?><a href="<?= htmlspecialchars($dsn['scholar_url']) ?>" target="_blank" class="btn-icon btn-scholar" title="Google Scholar"><i class="fas fa-graduation-cap"></i></a><?php endif; ?>
                            <?php if(!empty($dsn['linkedin_url'])): ?><a href="<?= htmlspecialchars($dsn['linkedin_url']) ?>" target="_blank" class="btn-icon btn-linkedin" title="LinkedIn"><i class="fab fa-linkedin-in"></i></a><?php endif; ?>
                            <a href="edit.php?id=<?= $dsn['id'] ?>" class="btn-icon btn-edit" title="Edit Data"><i class="fas fa-pen"></i></a>
                            <a href="hapus.php?id=<?= $dsn['id'] ?>" class="btn-icon btn-delete" title="Hapus Dosen" onclick="return confirm('Yakin ingin menghapus data dosen ini?')"><i class="fas fa-trash"></i></a>
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