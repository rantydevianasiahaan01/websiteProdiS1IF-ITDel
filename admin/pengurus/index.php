<?php
// admin/pengurus/index.php
require_once __DIR__ . '/../cek-login.php';
require_once __DIR__ . '/../../config/database.php';

$page_title = "Kelola Pengurus";

// =========================================================================
// 1. AUTO-PATCH DATABASE (Otomatis membuat tabel divisions & members)
// =========================================================================
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS divisions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(150) NOT NULL,
        description TEXT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS members (
        id INT AUTO_INCREMENT PRIMARY KEY,
        division_id INT NOT NULL,
        name VARCHAR(150) NOT NULL,
        position VARCHAR(100) NOT NULL,
        photo VARCHAR(255) DEFAULT 'default-user.png',
        linkedin_url VARCHAR(255) NULL,
        is_active TINYINT(1) DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (division_id) REFERENCES divisions(id) ON DELETE CASCADE
    )");

    // Masukkan divisi standar Universitas Terkemuka (UI/ITB/UGM) jika kosong
    $cek = $pdo->query("SELECT COUNT(*) FROM divisions")->fetchColumn();
    if ($cek == 0) {
        $pdo->exec("INSERT INTO divisions (name, description) VALUES 
            ('BPH (Badan Pengurus Harian)', 'Bertanggung jawab atas arah gerak roda organisasi secara keseluruhan, administrasi, dan manajemen keuangan himpunan.'),
            ('Departemen Akademik & Keilmuan', 'Fokus pada pengembangan hard skill, riset teknologi, tutor sebaya, dan pembinaan kompetisi IT mahasiswa.'),
            ('Departemen PSDM', 'Pengembangan Sumber Daya Mahasiswa, kaderisasi, manajemen regenerasi, dan peningkatan soft skill kepemimpinan.'),
            ('Departemen Media & Informasi', 'Pengelolaan wajah digital himpunan, publikasi, desain visual (UI/UX), dan kehumasan digital.'),
            ('Departemen Hubungan Luar', 'Membangun dan merawat relasi dengan alumni (Ikatan Alumni), himpunan kampus lain, perusahaan, dan instansi eksternal.')
        ");
    }
} catch (PDOException $e) {
    $pesan_error = "Error Database: " . $e->getMessage();
}

// =========================================================================
// 2. AMBIL STATISTIK & DATA
// =========================================================================
$stat_divisi = $pdo->query("SELECT COUNT(*) FROM divisions")->fetchColumn();
$stat_anggota = $pdo->query("SELECT COUNT(*) FROM members")->fetchColumn();
$stat_aktif = $pdo->query("SELECT COUNT(*) FROM members WHERE is_active = 1")->fetchColumn();

// Ambil daftar divisi beserta jumlah anggotanya
$stmt = $pdo->query("
    SELECT d.*, COUNT(m.id) as total_members 
    FROM divisions d 
    LEFT JOIN members m ON d.id = m.division_id 
    GROUP BY d.id 
    ORDER BY d.id ASC
");
$divisi_list = $stmt->fetchAll();
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
        
        /* Sidebar Tetap Konsisten */
        .sidebar { width: 250px; background: #0f172a; color: white; position: fixed; height: 100vh; overflow-y: auto; z-index: 1000; }
        .sidebar-header { padding: 25px 20px; border-bottom: 1px solid rgba(255,255,255,0.05); background: #0b1120; }
        .sidebar-header h2 { color: #f8fafc; font-size: 22px; margin-bottom: 5px; font-weight: 700; letter-spacing: 1px;}
        .sidebar-header p { color: #94a3b8; font-size: 12px; margin:0;}
        .menu { list-style: none; padding: 20px 0; margin:0;}
        .menu li a { display: flex; align-items: center; gap: 12px; color: #cbd5e1; text-decoration: none; padding: 12px 20px; transition: all 0.3s; border-left: 3px solid transparent; font-size: 14px; }
        .menu li a i { width: 20px; text-align: center; }
        .menu li a:hover, .menu li a.active { background: #1e293b; color: #fff; border-left-color: #38bdf8; }
        .menu-divider { color: #64748b; font-size: 11px; padding: 15px 20px 5px; text-transform: uppercase; font-weight: 700; letter-spacing: 1px;}
        
        /* Main Layout */
        .main { margin-left: 250px; padding: 35px 40px; width: calc(100% - 250px); }
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .page-header h1 { font-size: 26px; font-weight: 700; color: #0f172a; margin: 0; }
        
        /* Stats Cards */
        .stat-card { background: #fff; border-radius: 12px; padding: 25px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); display: flex; align-items: center; gap: 20px; border: 1px solid #e2e8f0; }
        .stat-icon { width: 60px; height: 60px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px; }
        .stat-info h3 { font-size: 28px; font-weight: 700; margin: 0; color: #0f172a; }
        .stat-info p { font-size: 14px; color: #64748b; margin: 0; font-weight: 500; text-transform: uppercase; letter-spacing: 0.5px; }
        
        /* Division Cards */
        .division-card { background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 2px 4px -1px rgba(0,0,0,0.03); transition: 0.3s; height: 100%; display: flex; flex-direction: column; }
        .division-card:hover { transform: translateY(-3px); box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); border-color: #cbd5e1; }
        .division-header { padding: 20px 25px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: flex-start; }
        .division-title { font-weight: 700; color: #0f172a; font-size: 17px; margin-bottom: 5px; }
        .division-body { padding: 20px 25px; flex-grow: 1; }
        .division-desc { font-size: 14px; color: #64748b; line-height: 1.6; }
        .division-footer { padding: 15px 25px; background: #f8fafc; border-top: 1px solid #f1f5f9; border-radius: 0 0 12px 12px; display: flex; justify-content: space-between; align-items: center; }
        
        /* Buttons */
        .btn-custom-dark { background: #0f172a; color: white; border: none; border-radius: 8px; font-weight: 600; padding: 10px 20px; transition: 0.3s; }
        .btn-custom-dark:hover { background: #1e293b; color: white; transform: translateY(-2px); }
        .btn-custom-outline { background: transparent; color: #0f172a; border: 1px solid #cbd5e1; border-radius: 8px; font-weight: 600; padding: 10px 20px; transition: 0.3s; }
        .btn-custom-outline:hover { background: #f8fafc; border-color: #94a3b8; }
        
        .member-badge { background: #e0f2fe; color: #0284c7; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 700; }
    </style>
</head>
<body>
    
    <!-- SIDEBAR -->
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
            <li><a href="index.php" class="active"><i class="fas fa-sitemap"></i> Struktur Pengurus</a></li>
            <li><a href="../program/index.php"><i class="fas fa-star"></i> Program Unggulan</a></li>
            <li class="menu-divider">Master Data</li>
            <li><a href="../dosen/index.php"><i class="fas fa-chalkboard-teacher"></i> Data Dosen</a></li>
            <li><a href="../alumni/index.php"><i class="fas fa-user-graduate"></i> Jejaring Alumni</a></li>
            <li><a href="../user/index.php"><i class="fas fa-users-cog"></i> Pengguna Sistem</a></li>
            <li><a href="../pengaturan/index.php"><i class="fas fa-cog"></i> Konfigurasi Web</a></li>
            <li class="menu-divider">Eksternal</li>
            <li><a href="../../index.php" target="_blank"><i class="fas fa-external-link-alt"></i> Buka Website Publik</a></li>
        </ul>
    </div>
    
    <div class="main">
        <div class="page-header">
            <h1><i class="fas fa-users-cog me-2"></i> Kelola Struktur Pengurus</h1>
            <div class="d-flex gap-2">
                <a href="tambah_divisi.php" class="btn-custom-outline shadow-sm">
                    <i class="fas fa-folder-plus me-2"></i>Tambah Divisi
                </a>
                <a href="tambah_anggota.php" class="btn-custom-dark shadow-sm">
                    <i class="fas fa-user-plus me-2"></i>Tambah Anggota
                </a>
            </div>
        </div>
        
        <?php if (isset($pesan_error)): ?>
            <div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-2"></i> <?= $pesan_error ?></div>
        <?php endif; ?>

        <!-- KARTU STATISTIK -->
        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-icon bg-primary bg-opacity-10 text-primary"><i class="fas fa-sitemap"></i></div>
                    <div class="stat-info">
                        <p>Total Divisi / Departemen</p>
                        <h3><?= $stat_divisi ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-icon bg-success bg-opacity-10 text-success"><i class="fas fa-users"></i></div>
                    <div class="stat-info">
                        <p>Total Seluruh Anggota</p>
                        <h3><?= $stat_anggota ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-icon bg-info bg-opacity-10 text-info"><i class="fas fa-user-check"></i></div>
                    <div class="stat-info">
                        <p>Anggota Kepengurusan Aktif</p>
                        <h3><?= $stat_aktif ?></h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- DAFTAR DIVISI (TAMPILAN GRID KEKINIAN) -->
        <h4 class="fw-bold mb-4" style="color: #0f172a;"><i class="fas fa-layer-group me-2"></i> Daftar Departemen & Divisi</h4>
        
        <div class="row g-4">
            <?php foreach ($divisi_list as $div): ?>
            <div class="col-md-6 col-lg-4">
                <div class="division-card">
                    <div class="division-header">
                        <h3 class="division-title"><?= htmlspecialchars($div['name']) ?></h3>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-light border-0" type="button" data-bs-toggle="dropdown"><i class="fas fa-ellipsis-v text-muted"></i></button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                <li><a class="dropdown-item" href="edit_divisi.php?id=<?= $div['id'] ?>"><i class="fas fa-edit me-2 text-primary"></i> Edit Divisi</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="hapus_divisi.php?id=<?= $div['id'] ?>" onclick="return confirm('Hapus divisi ini? Semua anggota di dalamnya akan ikut terhapus!')"><i class="fas fa-trash-alt me-2"></i> Hapus Divisi</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="division-body">
                        <p class="division-desc"><?= htmlspecialchars($div['description'] ?: 'Tidak ada deskripsi tupoksi.') ?></p>
                    </div>
                    <div class="division-footer">
                        <span class="member-badge"><i class="fas fa-user-friends me-1"></i> <?= $div['total_members'] ?> Anggota</span>
                        <a href="lihat_anggota.php?divisi=<?= $div['id'] ?>" class="btn btn-sm btn-outline-primary" style="font-weight: 600;">Lihat Detail &rarr;</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>