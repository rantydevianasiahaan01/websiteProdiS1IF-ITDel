<?php
// admin/user/index.php
require_once __DIR__ . '/../cek-login.php';
require_once __DIR__ . '/../../config/database.php';

$page_title = "Kelola Pengguna Sistem";

// =========================================================================
// 1. AUTO-PATCH DATABASE (Memastikan tabel users aman & ada)
// =========================================================================
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        name VARCHAR(100) NOT NULL,
        role ENUM('admin', 'editor') DEFAULT 'editor',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Buat akun admin default jika tabel kosong (Password: admin123)
    $cek = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    if ($cek == 0) {
        $default_pass = password_hash('admin123', PASSWORD_DEFAULT);
        $pdo->exec("INSERT INTO users (username, password, name, role) VALUES ('admin', '$default_pass', 'Administrator', 'admin')");
    }
} catch (PDOException $e) {
    $pesan_error = "Error Database: " . $e->getMessage();
}

// =========================================================================
// 2. AMBIL DATA USER
// =========================================================================
$stmt = $pdo->query("SELECT id, username, name, role, created_at FROM users ORDER BY role ASC, name ASC");
$user_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
        
        .user-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 25px; transition: 0.3s; box-shadow: 0 2px 4px -1px rgba(0,0,0,0.03); display: flex; align-items: center; gap: 20px; position: relative;}
        .user-card:hover { transform: translateY(-3px); box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); border-color: #cbd5e1; }
        
        .user-avatar { width: 70px; height: 70px; border-radius: 12px; object-fit: cover; border: 1px solid #e2e8f0; }
        .user-info { flex-grow: 1; }
        .user-name { font-size: 18px; font-weight: 700; color: #0f172a; margin-bottom: 4px; }
        .user-username { font-size: 13.5px; color: #64748b; font-family: monospace; background: #f1f5f9; padding: 3px 8px; border-radius: 4px; display: inline-block; margin-bottom: 8px;}
        
        .badge-role { font-size: 11px; padding: 5px 12px; border-radius: 20px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;}
        .role-admin { background: #fee2e2; color: #dc2626; border: 1px solid #fecaca;}
        .role-editor { background: #e0f2fe; color: #0284c7; border: 1px solid #bae6fd;}
        
        .user-actions { display: flex; gap: 8px; }
        .btn-icon { width: 36px; height: 36px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; text-decoration: none; transition: 0.3s; font-size: 14px; }
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
            <li><a href="../alumni/index.php"><i class="fas fa-user-graduate"></i> Jejaring Alumni</a></li>
            <li><a href="index.php" class="active"><i class="fas fa-users-cog"></i> Pengguna Sistem</a></li>
            <li><a href="../pengaturan/index.php"><i class="fas fa-cog"></i> Konfigurasi Web</a></li>
        </ul>
    </div>
    
    <div class="main">
        <div class="page-header">
            <div>
                <h1><i class="fas fa-users-cog text-primary me-2"></i> Pengguna Sistem (Users)</h1>
                <p class="text-muted mt-1 mb-0" style="font-size: 14px;">Manajemen hak akses dan akun administrator website.</p>
            </div>
            <a href="tambah.php" class="btn-tambah shadow-sm"><i class="fas fa-user-plus me-2"></i>Tambah User</a>
        </div>
        
        <?php if (isset($_GET['msg'])): ?>
            <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 rounded-3">
                <i class="fas fa-check-circle me-2"></i> <?= htmlspecialchars($_GET['msg']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <?php if (isset($_GET['err'])): ?>
            <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 rounded-3">
                <i class="fas fa-exclamation-triangle me-2"></i> <?= htmlspecialchars($_GET['err']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row g-4">
            <?php foreach ($user_list as $user): 
                $nama_bersih = trim(preg_replace('/[^a-zA-Z\s]/', '', $user['name']));
            ?>
            <div class="col-md-6">
                <div class="user-card">
                    <img src="https://ui-avatars.com/api/?name=<?= urlencode($nama_bersih) ?>&background=random&color=fff&size=150" class="user-avatar" alt="<?= htmlspecialchars($user['name']) ?>">
                    
                    <div class="user-info">
                        <div class="user-name"><?= htmlspecialchars($user['name']) ?></div>
                        <div class="user-username"><i class="fas fa-user-circle me-1"></i> <?= htmlspecialchars($user['username']) ?></div>
                        <div>
                            <span class="badge-role <?= $user['role'] === 'admin' ? 'role-admin' : 'role-editor' ?>">
                                <i class="fas <?= $user['role'] === 'admin' ? 'fa-crown' : 'fa-edit' ?> me-1"></i> <?= htmlspecialchars($user['role']) ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="user-actions">
                        <a href="edit.php?id=<?= $user['id'] ?>" class="btn-icon btn-edit" title="Edit User"><i class="fas fa-pen"></i></a>
                        <!-- Jangan izinkan menghapus akun "admin" utama -->
                        <?php if($user['username'] !== 'admin'): ?>
                            <a href="hapus.php?id=<?= $user['id'] ?>" class="btn-icon btn-delete" title="Hapus User" onclick="return confirm('Peringatan: Yakin ingin mencabut hak akses pengguna ini?')"><i class="fas fa-trash"></i></a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>