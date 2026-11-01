<?php
// admin/dashboard.php - Tema HIMASTI (Standalone)
require_once 'cek-login.php';
require_once '../config/database.php';
require_once '../config/auth.php';

try {
    $stats = [
        'berita' => $pdo->query("SELECT COUNT(*) FROM posts WHERE status='published'")->fetchColumn(),
        'dosen' => $pdo->query("SELECT COUNT(*) FROM lecturers WHERE is_active=1")->fetchColumn(),
        'agenda' => $pdo->query("SELECT COUNT(*) FROM events WHERE status='upcoming'")->fetchColumn(),
        'alumni' => $pdo->query("SELECT COUNT(*) FROM alumni WHERE is_active=1")->fetchColumn(),
        'pengurus' => $pdo->query("SELECT COUNT(*) FROM pengurus WHERE is_active=1")->fetchColumn(),
        'program' => $pdo->query("SELECT COUNT(*) FROM program_unggulan WHERE is_active=1")->fetchColumn(),
    ];
} catch (PDOException $e) {
    $stats = ['berita' => 0, 'dosen' => 0, 'agenda' => 0, 'alumni' => 0, 'pengurus' => 0, 'program' => 0];
}

// Ambil berita terbaru untuk quick preview
try {
    $berita_terbaru = $pdo->query("
        SELECT title, created_at, status 
        FROM posts 
        ORDER BY created_at DESC 
        LIMIT 5
    ")->fetchAll();
} catch (PDOException $e) {
    $berita_terbaru = [];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - HIMASTI IT Del</title>
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
        .user-info { display: flex; align-items: center; gap: 15px; }
        .user-info span { color: #666; font-size: 14px; }
        .user-role { 
            background: #1a2a3a; 
            color: white; 
            padding: 4px 12px; 
            border-radius: 20px; 
            font-size: 12px; 
            font-weight: 600;
        }
        .btn-logout {
            background: #c0392b;
            color: white;
            padding: 8px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s;
        }
        .btn-logout:hover { background: #a93226; }
        
        /* Welcome Section */
        .welcome {
            background: linear-gradient(135deg, #1a2a3a 0%, #2c3e50 100%);
            color: white;
            padding: 30px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        .welcome h2 { 
            font-size: 24px; 
            margin-bottom: 10px; 
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .welcome p { opacity: 0.9; }
        
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
        
        /* Quick Actions */
        .quick-actions {
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            margin-bottom: 30px;
        }
        .quick-actions h3 {
            color: #1a2a3a;
            font-size: 18px;
            margin-bottom: 20px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .action-buttons {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 12px;
        }
        .action-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 14px 18px;
            background: #f4f6f8;
            color: #1a2a3a;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s;
            border: 2px solid transparent;
        }
        .action-btn:hover {
            background: #1a2a3a;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(26, 42, 58, 0.2);
        }
        
        /* Content Grid */
        .content-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
        }
        .content-card {
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .content-card h3 {
            color: #1a2a3a;
            font-size: 18px;
            margin-bottom: 20px;
            font-weight: 600;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .content-card h3 span {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .content-card h3 a {
            font-size: 13px;
            color: #4a90a4;
            text-decoration: none;
            font-weight: 500;
        }
        .content-card h3 a:hover { text-decoration: underline; }
        
        .berita-list { list-style: none; }
        .berita-list li {
            padding: 12px 0;
            border-bottom: 1px solid #f0f0f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .berita-list li:last-child { border-bottom: none; }
        .berita-list .title {
            color: #2c3e50;
            font-size: 14px;
            font-weight: 500;
            flex: 1;
        }
        .berita-list .date {
            font-size: 12px;
            color: #95a5a6;
            margin-top: 4px;
        }
        .badge-status {
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            margin-left: 10px;
        }
        .badge-published { background: #d4edda; color: #155724; }
        .badge-draft { background: #fff3cd; color: #856404; }
        .badge-archived { background: #e2e3e5; color: #383d41; }
        
        .info-list { list-style: none; }
        .info-list li {
            padding: 12px 0;
            border-bottom: 1px solid #f0f0f0;
            color: #555;
            font-size: 14px;
        }
        .info-list li:last-child { border-bottom: none; }
        .info-list strong { 
            color: #1a2a3a; 
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 4px;
        }
        
        .empty-state {
            text-align: center;
            padding: 30px 20px;
            color: #95a5a6;
        }
        .empty-state .icon { 
            font-size: 48px; 
            margin-bottom: 10px; 
            opacity: 0.5; 
        }
        
        /* Responsive */
        @media (max-width: 992px) {
            .content-grid {
                grid-template-columns: 1fr;
            }
        }
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
            <li><a href="dashboard.php" class="active"><i class="fas fa-home"></i> Dashboard</a></li>
            
            <li class="menu-divider">Konten</li>
            <li><a href="berita/index.php"><i class="fas fa-newspaper"></i> Kelola Berita</a></li>
            <li><a href="agenda/index.php"><i class="fas fa-calendar-alt"></i> Kelola Agenda</a></li>
            <li><a href="galeri/index.php"><i class="fas fa-images"></i> Kelola Galeri</a></li>
            
            <li class="menu-divider">Profil Organisasi</li>
            <li><a href="profil/index.php"><i class="fas fa-edit"></i> Edit Profil</a></li>
            <li><a href="pengurus/index.php"><i class="fas fa-users"></i> Kelola Pengurus</a></li>
            <li><a href="program/index.php"><i class="fas fa-star"></i> Program Unggulan</a></li>
            
            <?php if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'super_admin'): ?>
                <li class="menu-divider">Master Data (Admin Only)</li>
                <li><a href="dosen/index.php"><i class="fas fa-chalkboard-teacher"></i> Kelola Dosen</a></li>
                <li><a href="alumni/index.php"><i class="fas fa-user-graduate"></i> Kelola Alumni</a></li>
                <li><a href="user/index.php"><i class="fas fa-user-cog"></i> Kelola User</a></li>
                <li><a href="settings/index.php"><i class="fas fa-cog"></i> Pengaturan</a></li>
            <?php endif; ?>
            
            <li class="menu-divider">Website</li>
            <li><a href="../index.php" target="_blank"><i class="fas fa-globe"></i> Lihat Website</a></li>
            <li><a href="../profil.php" target="_blank"><i class="fas fa-eye"></i> Halaman Profil</a></li>
        </ul>
    </div>
    
    <!-- Main Content -->
    <div class="main">
        <div class="header">
            <h1><i class="fas fa-tachometer-alt"></i> Dashboard</h1>
            <div class="user-info">
                <span>Halo, <strong><?= htmlspecialchars($_SESSION['user_name']) ?></strong></span>
                <span class="user-role"><?= strtoupper($_SESSION['role']) ?></span>
                <a href="login.php?logout=1" class="btn-logout">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
        
        <div class="welcome">
            <h2><i class="fas fa-hand-wave"></i> Selamat Datang di Panel Admin!</h2>
            <p>Ini adalah dashboard untuk mengelola website HIMASTI IT Del. Silakan pilih menu di samping untuk mulai mengelola konten.</p>
        </div>
        
        <!-- Stats Cards -->
        <div class="stats">
            <div class="stat-card">
                <div class="icon"><i class="fas fa-newspaper"></i></div>
                <h3>Berita Terpublikasi</h3>
                <div class="number"><?= $stats['berita'] ?></div>
            </div>
            <div class="stat-card">
                <div class="icon"><i class="fas fa-calendar-alt"></i></div>
                <h3>Agenda Mendatang</h3>
                <div class="number"><?= $stats['agenda'] ?></div>
            </div>
            <div class="stat-card">
                <div class="icon"><i class="fas fa-users"></i></div>
                <h3>Pengurus Aktif</h3>
                <div class="number"><?= $stats['pengurus'] ?></div>
            </div>
            <div class="stat-card">
                <div class="icon"><i class="fas fa-star"></i></div>
                <h3>Program Unggulan</h3>
                <div class="number"><?= $stats['program'] ?></div>
            </div>
            <div class="stat-card">
                <div class="icon"><i class="fas fa-chalkboard-teacher"></i></div>
                <h3>Dosen Aktif</h3>
                <div class="number"><?= $stats['dosen'] ?></div>
            </div>
            <div class="stat-card">
                <div class="icon"><i class="fas fa-user-graduate"></i></div>
                <h3>Alumni Terdaftar</h3>
                <div class="number"><?= $stats['alumni'] ?></div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="quick-actions">
            <h3><i class="fas fa-bolt"></i> Aksi Cepat</h3>
            <div class="action-buttons">
                <a href="berita/tambah.php" class="action-btn">
                    <i class="fas fa-plus"></i> Tambah Berita
                </a>
                <a href="pengurus/tambah.php" class="action-btn">
                    <i class="fas fa-user-plus"></i> Tambah Pengurus
                </a>
                <a href="program/tambah.php" class="action-btn">
                    <i class="fas fa-plus-circle"></i> Tambah Program
                </a>
                <a href="profil/index.php" class="action-btn">
                    <i class="fas fa-edit"></i> Edit Profil
                </a>
                <a href="dosen/tambah.php" class="action-btn">
                    <i class="fas fa-user-plus"></i> Tambah Dosen
                </a>
                <a href="agenda/tambah.php" class="action-btn">
                    <i class="fas fa-calendar-plus"></i> Tambah Agenda
                </a>
            </div>
        </div>
        
        <!-- Content Grid -->
        <div class="content-grid">
            <!-- Berita Terbaru -->
            <div class="content-card">
                <h3>
                    <span><i class="fas fa-newspaper"></i> Berita Terbaru</span>
                    <a href="berita/index.php">Lihat Semua <i class="fas fa-arrow-right"></i></a>
                </h3>
                <?php if (empty($berita_terbaru)): ?>
                    <div class="empty-state">
                        <div class="icon"><i class="fas fa-inbox"></i></div>
                        <p>Belum ada berita yang dibuat</p>
                        <a href="berita/tambah.php" class="action-btn" style="display: inline-block; margin-top: 15px;">
                            <i class="fas fa-plus"></i> Tambah Berita Pertama
                        </a>
                    </div>
                <?php else: ?>
                    <ul class="berita-list">
                        <?php foreach ($berita_terbaru as $berita): ?>
                            <li>
                                <div>
                                    <div class="title"><?= htmlspecialchars($berita['title']) ?></div>
                                    <div class="date">
                                        <i class="fas fa-clock"></i>
                                        <?= date('d M Y, H:i', strtotime($berita['created_at'])) ?>
                                    </div>
                                </div>
                                <span class="badge-status badge-<?= $berita['status'] ?>">
                                    <?= ucfirst($berita['status']) ?>
                                </span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
            
            <!-- Info Akun -->
            <div class="content-card">
                <h3><i class="fas fa-user-circle"></i> Informasi Akun</h3>
                <ul class="info-list">
                    <li>
                        <strong><i class="fas fa-user"></i> Nama:</strong>
                        <?= htmlspecialchars($_SESSION['user_name']) ?>
                    </li>
                    <li>
                        <strong><i class="fas fa-envelope"></i> Email:</strong>
                        <?= htmlspecialchars($_SESSION['user_username']) ?>
                    </li>
                    <li>
                        <strong><i class="fas fa-shield-alt"></i> Role:</strong>
                        <?= ucfirst(htmlspecialchars($_SESSION['role'])) ?>
                    </li>
                    <?php if (isset($_SESSION['login_time'])): ?>
                    <li>
                        <strong><i class="fas fa-sign-in-alt"></i> Login Sejak:</strong>
                        <?= date('d M Y, H:i', $_SESSION['login_time']) ?>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>