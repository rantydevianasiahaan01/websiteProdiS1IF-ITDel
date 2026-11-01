<?php
require_once __DIR__ . '/../cek-login.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/auth.php';

cek_editor();

$page_title = "Kelola Berita";

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    try {
        $stmt = $pdo->prepare("SELECT image FROM posts WHERE id = ?");
        $stmt->execute([$id]);
        $berita = $stmt->fetch();
        
        if ($berita && !empty($berita['image']) && $berita['image'] != 'default.jpg') {
            $image_path = __DIR__ . '/../../assets/uploads/' . $berita['image'];
            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }
        
        $stmt = $pdo->prepare("DELETE FROM posts WHERE id = ?");
        $stmt->execute([$id]);
        
        header("Location: index.php?msg=Berita berhasil dihapus");
        exit;
    } catch (PDOException $e) {
        $error_msg = "Gagal menghapus: " . $e->getMessage();
    }
}

// Ambil semua berita
try {
    $stmt = $pdo->query("
        SELECT p.*, c.name as category_name, u.name as author_name 
        FROM posts p
        LEFT JOIN categories c ON p.category_id = c.id
        LEFT JOIN users u ON p.author_id = u.id
        WHERE p.deleted_at IS NULL
        ORDER BY p.is_featured DESC, p.created_at DESC
    ");
    $berita_list = $stmt->fetchAll();
} catch (PDOException $e) {
    $berita_list = [];
    $error_msg = "Gagal mengambil data: " . $e->getMessage();
}

// Hitung stats
$total = count($berita_list);
$published = count(array_filter($berita_list, fn($b) => $b['status'] === 'published'));
$draft = count(array_filter($berita_list, fn($b) => $b['status'] === 'draft'));
$archived = count(array_filter($berita_list, fn($b) => $b['status'] === 'archived'));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?> - HIMASTI IT Del</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, sans-serif; }
        body { background: #f4f6f8; display: flex; min-height: 100vh; }
        
        /* Sidebar */
        .sidebar {
            width: 250px;
            background: #1a2a3a;
            color: white;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }
        .sidebar-header {
            padding: 25px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            background: rgba(0,0,0,0.2);
        }
        .sidebar-header h2 { 
            color: #ffffff; 
            font-size: 22px; 
            margin-bottom: 5px; 
            letter-spacing: 1px;
            font-weight: 700;
        }
        .sidebar-header p { color: #b0bec5; font-size: 12px; }
        
        .menu { list-style: none; padding: 20px 0; }
        .menu li a {
            display: flex;
            align-items: center;
            gap: 12px;
            color: white;
            text-decoration: none;
            padding: 12px 20px;
            transition: all 0.3s;
            border-left: 3px solid transparent;
            font-size: 14px;
        }
        .menu li a i { width: 20px; text-align: center; }
        .menu li a:hover { 
            background: rgba(61, 90, 115, 0.3); 
            border-left-color: #4a90a4;
        }
        .menu li a.active { 
            background: #2c3e50; 
            border-left-color: #4a90a4;
        }
        .menu-divider {
            color: #7f8c8d;
            font-size: 11px;
            padding: 15px 20px 5px;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
        }
        
        /* Main Content */
        .main { margin-left: 250px; padding: 30px; width: calc(100% - 250px); }
        .header {
            background: white;
            padding: 20px 25px;
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            margin-bottom: 30px;
        }
        .header h1 { 
            color: #1a2a3a; 
            font-size: 24px; 
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .btn-primary {
            background: #1a2a3a;
            color: white;
            padding: 10px 20px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: none;
            cursor: pointer;
            transition: all 0.3s;
        }
        .btn-primary:hover { background: #2c3e50; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(26, 42, 58, 0.3); }
        
        /* Alert */
        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
        }
        .alert-success { background: #d4edda; color: #155724; border-left: 4px solid #28a745; }
        .alert-danger { background: #f8d7da; color: #721c24; border-left: 4px solid #dc3545; }
        
        /* Stats Cards */
        .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            border-left: 4px solid #1a2a3a;
            transition: transform 0.2s;
        }
        .stat-card:hover { 
            transform: translateY(-3px); 
            box-shadow: 0 5px 15px rgba(0,0,0,0.1); 
        }
        .stat-card h3 { 
            color: #7f8c8d; 
            font-size: 14px; 
            margin-bottom: 10px; 
            font-weight: 500; 
        }
        .stat-card .number { 
            font-size: 36px; 
            color: #1a2a3a; 
            font-weight: 700; 
        }
        .stat-card .icon { 
            float: right; 
            font-size: 40px; 
            opacity: 0.2;
            margin-top: -10px;
            color: #1a2a3a;
        }
        
        /* Content Card */
        .content-card {
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            margin-bottom: 30px;
        }
        .content-card h3 {
            color: #1a2a3a;
            font-size: 18px;
            margin-bottom: 20px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        /* Table */
        .table-wrapper { overflow-x: auto; }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }
        table thead th {
            background: #f4f6f8;
            color: #1a2a3a;
            font-weight: 600;
            font-size: 13px;
            padding: 15px;
            text-align: left;
            border-bottom: 2px solid #e2e8f0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        table tbody td {
            padding: 15px;
            border-bottom: 1px solid #f0f0f0;
            color: #555;
            font-size: 14px;
            vertical-align: middle;
        }
        table tbody tr:hover { background: #f8fafc; }
        table tbody tr:last-child td { border-bottom: none; }
        
        /* Badge */
        .badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
        }
        .badge-published { background: #d4edda; color: #155724; }
        .badge-draft { background: #fff3cd; color: #856404; }
        .badge-archived { background: #e2e3e5; color: #383d41; }
        .badge-category { background: #e0e7ff; color: #3730a3; }
        
        /* Action Buttons */
        .action-buttons { display: flex; gap: 5px; justify-content: center; }
        .btn-action {
            width: 32px;
            height: 32px;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-decoration: none;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
            font-size: 13px;
        }
        .btn-edit { background: #4a90a4; }
        .btn-view { background: #17a2b8; }
        .btn-delete { background: #c0392b; }
        .btn-action:hover { transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,0.15); }
        
        /* Image */
        .img-thumbnail {
            width: 80px;
            height: 60px;
            object-fit: cover;
            border-radius: 6px;
        }
        .img-placeholder {
            width: 80px;
            height: 60px;
            background: linear-gradient(135deg, #1a2a3a 0%, #2c3e50 100%);
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
        }
        
        /* Featured Star */
        .featured-star { color: #f59e0b; font-size: 18px; }
        
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 50px 20px;
            color: #95a5a6;
        }
        .empty-state .icon { 
            font-size: 64px; 
            margin-bottom: 15px; 
            opacity: 0.3;
            color: #1a2a3a;
        }
        .empty-state p {
            font-size: 16px;
            margin-bottom: 20px;
        }
        
        /* Footer Info */
        .footer-info {
            padding-top: 20px;
            margin-top: 20px;
            border-top: 1px solid #f0f0f0;
            color: #95a5a6;
            font-size: 13px;
        }
        .footer-info strong { color: #1a2a3a; }
        
        /* Responsive */
        @media (max-width: 768px) {
            .sidebar { width: 100%; height: auto; position: relative; }
            .main { margin-left: 0; width: 100%; }
            body { flex-direction: column; }
            .header { flex-direction: column; gap: 15px; align-items: flex-start; }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h2>HIMASTI</h2>
            <p>Himpunan Mahasiswa Sarjana Informatika</p>
        </div>
        <ul class="menu">
            <li><a href="../dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
            
            <li class="menu-divider">Konten</li>
            <li><a href="index.php" class="active"><i class="fas fa-newspaper"></i> Kelola Berita</a></li>
            <li><a href="../agenda/index.php"><i class="fas fa-calendar-alt"></i> Kelola Agenda</a></li>
            <li><a href="../galeri/index.php"><i class="fas fa-images"></i> Kelola Galeri</a></li>
            
            <li class="menu-divider">Profil Organisasi</li>
            <li><a href="../profil/index.php"><i class="fas fa-edit"></i> Edit Profil</a></li>
            <li><a href="../pengurus/index.php"><i class="fas fa-users"></i> Kelola Pengurus</a></li>
            <li><a href="../program/index.php"><i class="fas fa-star"></i> Program Unggulan</a></li>
            
            <?php if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'super_admin'): ?>
                <li class="menu-divider">Master Data (Admin Only)</li>
                <li><a href="../dosen/index.php"><i class="fas fa-chalkboard-teacher"></i> Kelola Dosen</a></li>
                <li><a href="../alumni/index.php"><i class="fas fa-user-graduate"></i> Kelola Alumni</a></li>
                <li><a href="../user/index.php"><i class="fas fa-user-cog"></i> Kelola User</a></li>
                <li><a href="../settings/index.php"><i class="fas fa-cog"></i> Pengaturan</a></li>
            <?php endif; ?>
            
            <li class="menu-divider">Website</li>
            <li><a href="../../index.php" target="_blank"><i class="fas fa-globe"></i> Lihat Website</a></li>
            <li><a href="../../profil.php" target="_blank"><i class="fas fa-eye"></i> Halaman Profil</a></li>
        </ul>
    </div>
    
    <!-- Main Content -->
    <div class="main">
        <div class="header">
            <h1><i class="fas fa-newspaper"></i> Kelola Berita</h1>
            <a href="tambah.php" class="btn-primary">
                <i class="fas fa-plus"></i> Tambah Berita
            </a>
        </div>
        
        <?php if (isset($_GET['msg'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <span><?= htmlspecialchars($_GET['msg']) ?></span>
            </div>
        <?php endif; ?>
        
        <?php if (isset($error_msg)): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                <span><?= htmlspecialchars($error_msg) ?></span>
            </div>
        <?php endif; ?>
        
        <!-- Stats Cards -->
        <div class="stats">
            <div class="stat-card" style="border-left-color: #1a2a3a;">
                <div class="icon"><i class="fas fa-newspaper"></i></div>
                <h3>Total Berita</h3>
                <div class="number"><?= $total ?></div>
            </div>
            <div class="stat-card" style="border-left-color: #27ae60;">
                <div class="icon"><i class="fas fa-check-circle"></i></div>
                <h3>Published</h3>
                <div class="number"><?= $published ?></div>
            </div>
            <div class="stat-card" style="border-left-color: #f39c12;">
                <div class="icon"><i class="fas fa-edit"></i></div>
                <h3>Draft</h3>
                <div class="number"><?= $draft ?></div>
            </div>
            <div class="stat-card" style="border-left-color: #7f8c8d;">
                <div class="icon"><i class="fas fa-archive"></i></div>
                <h3>Archived</h3>
                <div class="number"><?= $archived ?></div>
            </div>
        </div>
        
        <!-- Table Content -->
        <div class="content-card">
            <h3><i class="fas fa-list"></i> Daftar Berita</h3>
            
            <?php if (empty($berita_list)): ?>
                <div class="empty-state">
                    <div class="icon"><i class="fas fa-newspaper"></i></div>
                    <p>Belum ada berita yang dibuat</p>
                    <a href="tambah.php" class="btn-primary">
                        <i class="fas fa-plus"></i> Tambah Berita Pertama
                    </a>
                </div>
            <?php else: ?>
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 50px;">No</th>
                                <th style="width: 100px;">Gambar</th>
                                <th>Judul Berita</th>
                                <th style="width: 120px;">Kategori</th>
                                <th style="width: 120px;">Penulis</th>
                                <th style="width: 110px;">Status</th>
                                <th style="width: 80px; text-align: center;">Featured</th>
                                <th style="width: 140px; text-align: center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($berita_list as $index => $berita): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td>
                                        <?php if (!empty($berita['image']) && $berita['image'] != 'default.jpg' && file_exists(__DIR__ . '/../../assets/uploads/' . $berita['image'])): ?>
                                            <img src="../../assets/uploads/<?= htmlspecialchars($berita['image']) ?>" 
                                                 class="img-thumbnail" 
                                                 alt="<?= htmlspecialchars($berita['title']) ?>">
                                        <?php else: ?>
                                            <div class="img-placeholder">
                                                <i class="fas fa-newspaper"></i>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <strong style="color: #1a2a3a;"><?= htmlspecialchars($berita['title']) ?></strong>
                                        <?php if (!empty($berita['excerpt'])): ?>
                                            <br><small style="color: #95a5a6;"><?= htmlspecialchars(substr($berita['excerpt'], 0, 80)) ?>...</small>
                                        <?php endif; ?>
                                        <br><small style="color: #95a5a6;">
                                            <i class="fas fa-eye"></i> <?= $berita['view_count'] ?? 0 ?> views
                                        </small>
                                    </td>
                                    <td>
                                        <?php if (!empty($berita['category_name'])): ?>
                                            <span class="badge badge-category"><?= htmlspecialchars($berita['category_name']) ?></span>
                                        <?php else: ?>
                                            <small style="color: #95a5a6;">-</small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <small>
                                            <i class="fas fa-user"></i>
                                            <?= htmlspecialchars($berita['author_name'] ?? 'Admin') ?>
                                        </small>
                                    </td>
                                    <td>
                                        <?php
                                        $status_labels = [
                                            'published' => 'Published',
                                            'draft' => 'Draft',
                                            'archived' => 'Archived'
                                        ];
                                        $label = $status_labels[$berita['status']] ?? $berita['status'];
                                        ?>
                                        <span class="badge badge-<?= $berita['status'] ?>">
                                            <?= $label ?>
                                        </span>
                                    </td>
                                    <td style="text-align: center;">
                                        <?php if ($berita['is_featured']): ?>
                                            <i class="fas fa-star featured-star"></i>
                                        <?php else: ?>
                                            <small style="color: #95a5a6;">-</small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="edit.php?id=<?= $berita['id'] ?>" class="btn-action btn-edit" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="../../detail/detail-berita.php?slug=<?= htmlspecialchars($berita['slug']) ?>" 
                                               target="_blank" 
                                               class="btn-action btn-view" 
                                               title="Lihat">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="?delete=<?= $berita['id'] ?>" 
                                               class="btn-action btn-delete"
                                               onclick="return confirm('Yakin ingin menghapus berita &quot;<?= htmlspecialchars($berita['title']) ?>&quot;?')" 
                                               title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="footer-info">
                    Menampilkan <strong><?= $total ?></strong> berita 
                    (<strong><?= $published ?></strong> published, 
                    <strong><?= $draft ?></strong> draft, 
                    <strong><?= $archived ?></strong> archived)
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>