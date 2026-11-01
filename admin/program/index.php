<?php
// admin/program/index.php
require_once __DIR__ . '/../cek-login.php';
require_once __DIR__ . '/../../config/database.php';

$page_title = "Program Unggulan";

try {
    $stmt = $pdo->query("SELECT * FROM programs ORDER BY id ASC");
    $program_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $program_list = [];
}
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
        
        .btn-tambah { background: #0f172a; color: white; border: none; border-radius: 8px; font-weight: 600; padding: 10px 20px; transition: 0.3s; text-decoration: none; display: inline-flex; align-items: center;}
        .btn-tambah:hover { background: #1e293b; color: white; transform: translateY(-2px); box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        
        .program-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 30px; transition: 0.3s; box-shadow: 0 2px 4px -1px rgba(0,0,0,0.03); height: 100%; display: flex; flex-direction: column; position: relative;}
        .program-card:hover { transform: translateY(-5px); box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); border-color: #cbd5e1; }
        
        .program-icon { width: 60px; height: 60px; background: #f0f9ff; color: #0284c7; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px; margin-bottom: 20px; }
        .program-title { font-size: 18px; font-weight: 700; color: #0f172a; margin-bottom: 15px; line-height: 1.4;}
        .program-desc { font-size: 14.5px; color: #64748b; line-height: 1.7; flex-grow: 1; margin-bottom: 25px; text-align: justify;}
        
        .program-actions { display: flex; gap: 8px; border-top: 1px dashed #e2e8f0; padding-top: 15px; margin-top: auto;}
        .btn-action { padding: 8px 15px; border-radius: 6px; font-size: 13px; font-weight: 600; text-decoration: none; transition: 0.3s; border: 1px solid transparent; }
        .btn-edit { background: #f8fafc; color: #475569; border-color: #e2e8f0; }
        .btn-edit:hover { background: #e2e8f0; color: #0f172a; }
        .btn-delete { color: #ef4444; }
        .btn-delete:hover { background: #fee2e2; }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header"><h2>HIMASTI</h2><p>Admin Panel &bull; IT Del</p></div>
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
            <li><a href="index.php" class="active"><i class="fas fa-star"></i> Program Unggulan</a></li>
            <li class="menu-divider">Master Data</li>
            <li><a href="../dosen/index.php"><i class="fas fa-chalkboard-teacher"></i> Data Dosen</a></li>
            <li><a href="../alumni/index.php"><i class="fas fa-user-graduate"></i> Jejaring Alumni</a></li>
            <li><a href="../user/index.php"><i class="fas fa-users-cog"></i> Pengguna Sistem</a></li>
            <li><a href="../pengaturan/index.php"><i class="fas fa-cog"></i> Konfigurasi Web</a></li>
        </ul>
    </div>
    
    <div class="main">
        <div class="page-header">
            <div>
                <h1><i class="fas fa-star text-warning me-2"></i> Program Unggulan</h1>
                <p class="text-muted mt-1 mb-0" style="font-size: 14px;">Membentuk Ekosistem Pembelajaran Standar Industri</p>
            </div>
            <a href="tambah.php" class="btn-tambah shadow-sm"><i class="fas fa-plus me-2"></i>Tambah Program</a>
        </div>
        
        <?php if (isset($_GET['msg'])): ?>
            <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 rounded-3">
                <i class="fas fa-check-circle me-2"></i> <?= htmlspecialchars($_GET['msg']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row g-4">
            <?php if (empty($program_list)): ?>
                <div class="col-12">
                    <div class="alert alert-info border-0 shadow-sm rounded-3 bg-light text-center py-5">
                        <i class="fas fa-box-open fa-3x text-muted mb-3 d-block opacity-50"></i>
                        <h5 class="text-muted">Belum ada Program Unggulan yang didaftarkan.</h5>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($program_list as $prog): ?>
                <div class="col-md-6">
                    <div class="program-card">
                        <div class="program-icon"><i class="<?= htmlspecialchars($prog['icon']) ?>"></i></div>
                        <h3 class="program-title"><?= htmlspecialchars($prog['title']) ?></h3>
                        <div class="program-desc"><?= htmlspecialchars($prog['description']) ?></div>
                        
                        <div class="program-actions">
                            <a href="edit.php?id=<?= $prog['id'] ?>" class="btn-action btn-edit"><i class="fas fa-pen me-1"></i> Edit</a>
                            <a href="hapus.php?id=<?= $prog['id'] ?>" class="btn-action btn-delete ms-auto" onclick="return confirm('Yakin ingin menghapus program unggulan ini?')"><i class="fas fa-trash me-1"></i> Hapus</a>
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