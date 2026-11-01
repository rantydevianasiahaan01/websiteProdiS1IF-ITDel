<?php
// admin/pesan/index.php
require_once __DIR__ . '/../cek-login.php';
require_once __DIR__ . '/../../config/database.php';

$page_title = "Pesan Masuk";

// Ambil data pesan dari tabel contacts
try {
    $stmt = $pdo->query("SELECT * FROM contacts ORDER BY created_at DESC");
    $pesan_list = $stmt->fetchAll();
} catch (PDOException $e) {
    $pesan_list = [];
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
        * { font-family: 'Segoe UI', Tahoma, sans-serif; }
        body { background: #f4f6f8; display: flex; min-height: 100vh; margin: 0; }
        
        /* Sidebar Standar */
        .sidebar { width: 250px; background: #1a2a3a; color: white; position: fixed; height: 100vh; overflow-y: auto; z-index: 1000; }
        .sidebar-header { padding: 25px 20px; border-bottom: 1px solid rgba(255,255,255,0.1); background: rgba(0,0,0,0.2); }
        .sidebar-header h2 { color: #ffffff; font-size: 22px; margin-bottom: 5px; font-weight: 700; }
        .sidebar-header p { color: #b0bec5; font-size: 12px; margin:0;}
        .menu { list-style: none; padding: 20px 0; margin:0;}
        .menu li a { display: flex; align-items: center; gap: 12px; color: white; text-decoration: none; padding: 12px 20px; transition: all 0.3s; border-left: 3px solid transparent; font-size: 14px; }
        .menu li a i { width: 20px; text-align: center; }
        .menu li a:hover { background: rgba(61, 90, 115, 0.3); border-left-color: #4a90a4; }
        .menu li a.active { background: #2c3e50; border-left-color: #4a90a4; }
        .menu-divider { color: #7f8c8d; font-size: 11px; padding: 15px 20px 5px; text-transform: uppercase; font-weight: 600; }
        
        /* Main Content */
        .main { margin-left: 250px; padding: 30px; width: calc(100% - 250px); }
        .header-custom { background: white; padding: 20px 25px; border-radius: 8px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 5px rgba(0,0,0,0.05); margin-bottom: 30px; }
        .header-custom h1 { color: #1a2a3a; font-size: 24px; margin: 0; display: flex; align-items: center; gap: 10px; }
        
        .content-card { background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .table-custom th { background: #f8fafc; color: #64748b; font-weight: 600; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; }
        .table-custom td { vertical-align: middle; color: #334155; font-size: 14px; }
        
        .badge-unread { background-color: #fecdd3; color: #e11d48; padding: 5px 10px; border-radius: 20px; font-weight: 600; font-size: 12px; }
        .badge-read { background-color: #e0f2fe; color: #0284c7; padding: 5px 10px; border-radius: 20px; font-weight: 600; font-size: 12px; }
        .badge-replied { background-color: #dcfce7; color: #16a34a; padding: 5px 10px; border-radius: 20px; font-weight: 600; font-size: 12px; }
        
        .btn-action { width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; border-radius: 6px; text-decoration: none; transition: 0.3s; border: none; }
        .btn-view { background: #e0f2fe; color: #0284c7; }
        .btn-view:hover { background: #bae6fd; color: #0369a1; }
        .btn-delete { background: #fee2e2; color: #ef4444; }
        .btn-delete:hover { background: #fecaca; color: #dc2626; }
        
        .row-unread { background-color: #fffbfa !important; font-weight: 600; }
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
            <li><a href="../galeri/index.php"><i class="fas fa-images"></i> Kelola Galeri</a></li>
            <li><a href="index.php" class="active"><i class="fas fa-envelope"></i> Pesan Masuk</a></li>
            <li class="menu-divider">Lainnya</li>
            <li><a href="../../index.php" target="_blank"><i class="fas fa-globe"></i> Lihat Website</a></li>
            <li><a href="../login.php?logout=1" style="color: #e74c3c;"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>
    
    <div class="main">
        <div class="header-custom">
            <h1><i class="fas fa-inbox text-primary"></i> Kotak Masuk</h1>
        </div>
        
        <?php if (isset($_GET['msg'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle me-2"></i> <?= htmlspecialchars($_GET['msg']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <div class="content-card">
            <div class="table-responsive">
                <table class="table table-hover table-custom mb-0">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Pengirim</th>
                            <th>Subjek</th>
                            <th>Status</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($pesan_list)): ?>
                        <tr><td colspan="5" class="text-center py-4 text-muted">Belum ada pesan masuk.</td></tr>
                        <?php else: ?>
                            <?php foreach ($pesan_list as $psn): ?>
                            <tr class="<?= $psn['status'] === 'unread' ? 'row-unread' : '' ?>">
                                <td><?= date('d M Y, H:i', strtotime($psn['created_at'])) ?></td>
                                <td>
                                    <?= htmlspecialchars($psn['name']) ?><br>
                                    <small class="text-muted fw-normal"><?= htmlspecialchars($psn['email']) ?></small>
                                </td>
                                <td><?= htmlspecialchars($psn['subject'] ?: '(Tanpa Subjek)') ?></td>
                                <td>
                                    <?php if($psn['status'] == 'unread'): ?>
                                        <span class="badge-unread">Belum Dibaca</span>
                                    <?php elseif($psn['status'] == 'read'): ?>
                                        <span class="badge-read">Sudah Dibaca</span>
                                    <?php else: ?>
                                        <span class="badge-replied">Sudah Dibalas</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <a href="baca.php?id=<?= $psn['id'] ?>" class="btn-action btn-view" title="Baca Pesan"><i class="fas fa-eye"></i></a>
                                    <a href="hapus.php?id=<?= $psn['id'] ?>" class="btn-action btn-delete" title="Hapus Pesan" onclick="return confirm('Yakin ingin menghapus pesan ini?')"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>