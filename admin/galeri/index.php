<?php
// admin/galeri/index.php
require_once __DIR__ . '/../cek-login.php';
require_once __DIR__ . '/../../config/database.php';

$page_title = "Kelola Galeri";

// Ambil data galeri
try {
    $stmt = $pdo->query("SELECT * FROM galleries ORDER BY created_at DESC");
    $galeri_list = $stmt->fetchAll();
    $total_foto = count($galeri_list);
} catch (PDOException $e) {
    $galeri_list = [];
    $total_foto = 0;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?> - HIMASTI IT Del</title>
    <!-- Bootstrap CSS (untuk fitur Modal/Popup) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, sans-serif; }
        body { background: #f4f6f8; display: flex; min-height: 100vh; }
        
        /* Sidebar */
        .sidebar { width: 250px; background: #1a2a3a; color: white; position: fixed; height: 100vh; overflow-y: auto; z-index: 1000; }
        .sidebar-header { padding: 25px 20px; border-bottom: 1px solid rgba(255,255,255,0.1); background: rgba(0,0,0,0.2); }
        .sidebar-header h2 { color: #ffffff; font-size: 22px; margin-bottom: 5px; font-weight: 700; }
        .sidebar-header p { color: #b0bec5; font-size: 12px; }
        .menu { list-style: none; padding: 20px 0; }
        .menu li a { display: flex; align-items: center; gap: 12px; color: white; text-decoration: none; padding: 12px 20px; transition: all 0.3s; border-left: 3px solid transparent; font-size: 14px; }
        .menu li a i { width: 20px; text-align: center; }
        .menu li a:hover { background: rgba(61, 90, 115, 0.3); border-left-color: #4a90a4; }
        .menu li a.active { background: #2c3e50; border-left-color: #4a90a4; }
        .menu-divider { color: #7f8c8d; font-size: 11px; padding: 15px 20px 5px; text-transform: uppercase; font-weight: 600; }
        
        /* Main Content */
        .main { margin-left: 250px; padding: 30px; width: calc(100% - 250px); }
        .header-custom { background: white; padding: 20px 25px; border-radius: 8px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 5px rgba(0,0,0,0.05); margin-bottom: 30px; }
        .header-custom h1 { color: #1a2a3a; font-size: 24px; margin: 0; display: flex; align-items: center; gap: 10px; }
        
        /* Stats Card */
        .stat-card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); border-left: 4px solid #4a90a4; display: inline-block; min-width: 250px; margin-bottom: 30px; }
        .stat-card h3 { color: #7f8c8d; font-size: 14px; margin-bottom: 5px; font-weight: 500; }
        .stat-card .number { font-size: 28px; color: #1a2a3a; font-weight: 700; }
        
        .btn-primary-custom { background: #1a2a3a; color: white; padding: 10px 20px; border-radius: 6px; text-decoration: none; font-size: 14px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; border: none; cursor: pointer; transition: all 0.3s; }
        .btn-primary-custom:hover { background: #2c3e50; color: white; transform: translateY(-2px); }
        
        .content-card { background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .empty-state { text-align: center; padding: 50px 20px; color: #95a5a6; }
        .empty-state .icon { font-size: 64px; margin-bottom: 15px; opacity: 0.3; color: #1a2a3a; }
        
        /* Gallery Grid dengan Efek Hover Keren */
        .gallery-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; }
        .gallery-card { border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden; transition: transform 0.3s, box-shadow 0.3s; background: #fff; position: relative; }
        .gallery-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
        
        .gallery-img-container { position: relative; width: 100%; height: 200px; overflow: hidden; border-bottom: 1px solid #e2e8f0; }
        .gallery-img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s; }
        .gallery-card:hover .gallery-img { transform: scale(1.05); }
        
        .gallery-overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(26, 42, 58, 0.6); display: flex; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.3s; cursor: pointer; }
        .gallery-card:hover .gallery-overlay { opacity: 1; }
        .gallery-overlay i { color: white; font-size: 32px; transform: scale(0.5); transition: transform 0.3s; }
        .gallery-card:hover .gallery-overlay i { transform: scale(1); }
        
        .gallery-info { padding: 15px; }
        .gallery-title { font-size: 16px; font-weight: 600; color: #1a2a3a; margin-bottom: 5px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .gallery-desc { font-size: 13px; color: #64748b; margin-bottom: 15px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .gallery-actions { display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #e2e8f0; padding-top: 10px; }
        .btn-delete { color: #e74c3c; background: none; border: none; cursor: pointer; font-size: 14px; text-decoration: none; transition: color 0.3s; display: inline-flex; align-items: center; gap: 5px; }
        .btn-delete:hover { color: #c0392b; }
        
        @media (max-width: 768px) { .sidebar { width: 100%; height: auto; position: relative; z-index: 1; } .main { margin-left: 0; width: 100%; } body { flex-direction: column; } .header-custom { flex-direction: column; gap: 15px; align-items: flex-start; } }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header">
            <h2>HIMASTI</h2>
            <p>Himpunan Mahasiswa Sarjana Informatika</p>
        </div>
        <ul class="menu">
            <li><a href="../dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
            <li class="menu-divider">Konten</li>
            <li><a href="../berita/index.php"><i class="fas fa-newspaper"></i> Kelola Berita</a></li>
            <li><a href="../agenda/index.php"><i class="fas fa-calendar-alt"></i> Kelola Agenda</a></li>
            <li><a href="index.php" class="active"><i class="fas fa-images"></i> Kelola Galeri</a></li>
            <li class="menu-divider">Profil Organisasi</li>
            <li><a href="../profil/index.php"><i class="fas fa-edit"></i> Edit Profil</a></li>
            <li><a href="../pengurus/index.php"><i class="fas fa-users"></i> Kelola Pengurus</a></li>
            <li><a href="../program/index.php"><i class="fas fa-star"></i> Program Unggulan</a></li>
            <li class="menu-divider">Lainnya</li>
            <li><a href="../../index.php" target="_blank"><i class="fas fa-globe"></i> Lihat Website</a></li>
            <li><a href="../login.php?logout=1" style="color: #e74c3c;"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>
    
    <div class="main">
        <div class="header-custom">
            <h1><i class="fas fa-images"></i> Kelola Galeri</h1>
            <a href="tambah.php" class="btn-primary-custom">
                <i class="fas fa-upload"></i> Tambah Foto Baru
            </a>
        </div>
        
        <?php if (isset($_GET['msg'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert" style="background: #d4edda; color: #155724; border-left: 4px solid #28a745; padding: 15px;">
                <i class="fas fa-check-circle me-2"></i> <?= htmlspecialchars($_GET['msg']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <!-- Statistik Kecil -->
        <div class="stat-card">
            <h3>Total Aset Foto</h3>
            <div class="number">
                <i class="fas fa-camera text-muted me-2" style="font-size: 20px;"></i>
                <?= $total_foto ?>
            </div>
        </div>
        
        <div class="content-card">
            <?php if (empty($galeri_list)): ?>
                <div class="empty-state">
                    <div class="icon"><i class="fas fa-images"></i></div>
                    <p>Belum ada foto di galeri</p>
                    <a href="tambah.php" class="btn-primary-custom" style="margin-top: 15px;">
                        <i class="fas fa-upload"></i> Upload Foto Pertama
                    </a>
                </div>
            <?php else: ?>
                <div class="gallery-grid">
                    <?php foreach ($galeri_list as $foto): ?>
                    <div class="gallery-card">
                        <!-- Area Gambar dengan Hover Effect -->
                        <div class="gallery-img-container" data-bs-toggle="modal" data-bs-target="#modalFoto<?= $foto['id'] ?>">
                            <img src="../../assets/uploads/<?= htmlspecialchars($foto['image']) ?>" alt="<?= htmlspecialchars($foto['title']) ?>" class="gallery-img">
                            <div class="gallery-overlay">
                                <i class="fas fa-eye"></i>
                            </div>
                        </div>
                        
                        <div class="gallery-info">
                            <div class="gallery-title" title="<?= htmlspecialchars($foto['title']) ?>">
                                <?= htmlspecialchars($foto['title']) ?>
                            </div>
                            <div class="gallery-desc">
                                <?= htmlspecialchars($foto['description'] ?? 'Tidak ada deskripsi') ?>
                            </div>
                            <div class="gallery-actions">
                                <small class="text-muted"><i class="fas fa-calendar-alt"></i> <?= date('d M Y', strtotime($foto['created_at'])) ?></small>
                                <a href="hapus.php?id=<?= $foto['id'] ?>" class="btn-delete" onclick="return confirm('Yakin ingin menghapus foto ini secara permanen?')">
                                    <i class="fas fa-trash"></i> Hapus
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Modal/Popup untuk Preview Foto -->
                    <div class="modal fade" id="modalFoto<?= $foto['id'] ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-lg">
                            <div class="modal-content">
                                <div class="modal-header border-0">
                                    <h5 class="modal-title fw-bold"><?= htmlspecialchars($foto['title']) ?></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body p-0 text-center bg-dark">
                                    <img src="../../assets/uploads/<?= htmlspecialchars($foto['image']) ?>" alt="<?= htmlspecialchars($foto['title']) ?>" style="max-width: 100%; max-height: 80vh;">
                                </div>
                                <?php if (!empty($foto['description'])): ?>
                                <div class="modal-footer border-0">
                                    <p class="text-muted mb-0 w-100 text-start"><?= htmlspecialchars($foto['description']) ?></p>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Bootstrap JS (Diperlukan untuk fitur Modal/Popup) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>