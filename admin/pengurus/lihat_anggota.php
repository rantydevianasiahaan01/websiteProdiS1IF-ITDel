<?php
// admin/pengurus/lihat_anggota.php
require_once __DIR__ . '/../cek-login.php';
require_once __DIR__ . '/../../config/database.php';

$divisi_id = $_GET['divisi'] ?? 0;

// 1. Ambil informasi Divisi/Departemen
$stmt = $pdo->prepare("SELECT * FROM divisions WHERE id = ?");
$stmt->execute([$divisi_id]);
$divisi = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$divisi) {
    header("Location: index.php?msg=" . urlencode("Gagal: Divisi tidak ditemukan."));
    exit;
}

$page_title = "Detail Anggota: " . $divisi['name'];

// 2. Ambil daftar anggota di divisi ini (Diurutkan: Ketua/Wakil di atas, lalu abjad)
$stmt_members = $pdo->prepare("
    SELECT * FROM members 
    WHERE division_id = ? 
    ORDER BY 
        CASE 
            WHEN position LIKE '%Ketua%' THEN 1 
            WHEN position LIKE '%Wakil%' THEN 2 
            WHEN position LIKE '%Sekretaris%' THEN 3 
            WHEN position LIKE '%Bendahara%' THEN 4 
            ELSE 5 
        END ASC, 
        name ASC
");
$stmt_members->execute([$divisi_id]);
$anggota_list = $stmt_members->fetchAll(PDO::FETCH_ASSOC);
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
        
        /* STANDAR SIDEBAR */
        .sidebar { width: 250px; background: #0f172a; color: white; position: fixed; height: 100vh; overflow-y: auto; z-index: 1000; }
        .sidebar-header { padding: 25px 20px; border-bottom: 1px solid rgba(255,255,255,0.05); background: #0b1120; }
        .sidebar-header h2 { color: #f8fafc; font-size: 22px; margin-bottom: 5px; font-weight: 700; letter-spacing: 1px;}
        .sidebar-header p { color: #94a3b8; font-size: 12px; margin:0;}
        .menu { list-style: none; padding: 20px 0; margin:0;}
        .menu li a { display: flex; align-items: center; gap: 12px; color: #cbd5e1; text-decoration: none; padding: 12px 20px; transition: all 0.3s; border-left: 3px solid transparent; font-size: 14px; }
        .menu li a i { width: 20px; text-align: center; }
        .menu li a:hover, .menu li a.active { background: #1e293b; color: #fff; border-left-color: #38bdf8; }
        .menu-divider { color: #64748b; font-size: 11px; padding: 15px 20px 5px; text-transform: uppercase; font-weight: 700; letter-spacing: 1px;}
        
        /* AREA KONTEN UTAMA */
        .main { margin-left: 250px; padding: 35px 40px; width: calc(100% - 250px); }
        .page-header { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 30px; border-bottom: 1px solid #e2e8f0; padding-bottom: 20px;}
        .page-header h1 { font-size: 24px; font-weight: 700; color: #0f172a; margin: 0; }
        
        /* TOMBOL AKSI */
        .btn-kembali { background: #e2e8f0; color: #475569; border: none; border-radius: 8px; font-weight: 600; padding: 10px 18px; transition: 0.3s; text-decoration: none; font-size: 14px; display: inline-flex; align-items: center;}
        .btn-kembali:hover { background: #cbd5e1; color: #334155; }
        .btn-tambah { background: #0f172a; color: white; border: none; border-radius: 8px; font-weight: 600; padding: 10px 18px; transition: 0.3s; text-decoration: none; font-size: 14px; display: inline-flex; align-items: center;}
        .btn-tambah:hover { background: #1e293b; color: white; transform: translateY(-2px); box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        
        /* KARTU PROFIL ANGGOTA */
        .member-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 25px 20px; text-align: center; transition: 0.3s; box-shadow: 0 2px 4px -1px rgba(0,0,0,0.03); position: relative; height: 100%; display: flex; flex-direction: column; }
        .member-card:hover { transform: translateY(-3px); box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); border-color: #cbd5e1; }
        
        .member-avatar { width: 85px; height: 85px; border-radius: 50%; object-fit: cover; border: 3px solid #f8fafc; margin: 0 auto 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .member-name { font-size: 16px; font-weight: 700; color: #0f172a; margin-bottom: 5px; }
        .member-position { font-size: 12.5px; color: #38bdf8; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 15px; flex-grow: 1;}
        
        .badge-status { position: absolute; top: 15px; right: 15px; font-size: 10px; padding: 4px 8px; border-radius: 20px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;}
        .bg-active { background: #dcfce7; color: #16a34a; }
        .bg-inactive { background: #f1f5f9; color: #64748b; }
        
        .member-actions { display: flex; justify-content: center; gap: 8px; border-top: 1px dashed #e2e8f0; padding-top: 15px; margin-top: auto;}
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
    
    <!-- KONTEN UTAMA -->
    <div class="main">
        <div class="page-header" style="max-width: 1000px;">
            <div>
                <h1 class="mb-2"><i class="fas fa-users text-primary me-2"></i> <?= htmlspecialchars($divisi['name']) ?></h1>
                <p class="text-muted mb-0" style="max-width: 600px; font-size: 14px; line-height: 1.6;">
                    <?= htmlspecialchars($divisi['description'] ?: 'Tidak ada deskripsi tupoksi.') ?>
                </p>
            </div>
            <div class="d-flex gap-2">
                <a href="index.php" class="btn-kembali shadow-sm"><i class="fas fa-arrow-left me-2"></i>Kembali</a>
                <!-- Tombol ini langsung otomatis memilih divisi saat diklik -->
                <a href="tambah_anggota.php?divisi=<?= $divisi['id'] ?>" class="btn-tambah shadow-sm"><i class="fas fa-user-plus me-2"></i>Tambah Anggota</a>
            </div>
        </div>

        <?php if (isset($_GET['msg'])): ?>
            <div class="alert alert-success alert-dismissible fade show shadow-sm" style="max-width: 1000px; border-radius:10px;">
                <i class="fas fa-check-circle me-2"></i> <?= htmlspecialchars($_GET['msg']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- GRID DAFTAR ANGGOTA -->
        <div class="row g-4" style="max-width: 1000px;">
            <?php if (empty($anggota_list)): ?>
                <div class="col-12">
                    <div class="alert alert-info border-0 shadow-sm rounded-3" style="background:#f0f9ff; color:#0369a1;">
                        <i class="fas fa-info-circle me-2"></i> Belum ada pengurus yang ditempatkan di departemen ini.
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($anggota_list as $anggota): ?>
                <div class="col-md-4 col-sm-6">
                    <div class="member-card">
                        <span class="badge-status <?= $anggota['is_active'] ? 'bg-active' : 'bg-inactive' ?>">
                            <?= $anggota['is_active'] ? 'Aktif' : 'Non-Aktif' ?>
                        </span>
                        
                        <!-- Trik cerdas: Jika belum ada foto, buat inisial nama otomatis menggunakan API ui-avatars -->
                        <img src="../../assets/uploads/<?= htmlspecialchars($anggota['photo']) ?>" 
                             onerror="this.src='https://ui-avatars.com/api/?name=<?= urlencode($anggota['name']) ?>&background=0f172a&color=fff&size=150&font-size=0.33'" 
                             class="member-avatar" 
                             alt="<?= htmlspecialchars($anggota['name']) ?>">
                        
                        <h4 class="member-name"><?= htmlspecialchars($anggota['name']) ?></h4>
                        <div class="member-position"><?= htmlspecialchars($anggota['position']) ?></div>
                        
                        <div class="member-actions">
                            <?php if(!empty($anggota['linkedin_url'])): ?>
                            <a href="<?= htmlspecialchars($anggota['linkedin_url']) ?>" target="_blank" class="btn-icon btn-linkedin" title="Profil LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                            <?php endif; ?>
                            <a href="edit_anggota.php?id=<?= $anggota['id'] ?>" class="btn-icon btn-edit" title="Edit Data"><i class="fas fa-pen"></i></a>
                            <a href="hapus_anggota.php?id=<?= $anggota['id'] ?>&divisi=<?= $divisi_id ?>" class="btn-icon btn-delete" title="Hapus Anggota" onclick="return confirm('Peringatan: Yakin ingin mencabut <?= htmlspecialchars($anggota['name']) ?> dari kepengurusan ini?')"><i class="fas fa-trash"></i></a>
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