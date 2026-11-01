<?php
// admin/pengaturan/index.php
require_once __DIR__ . '/../cek-login.php';
require_once __DIR__ . '/../../config/database.php';

$page_title = "Konfigurasi Website";
$pesan_sukses = "";
$pesan_error = "";

// =========================================================================
// 1. AUTO-PATCH DATABASE (Otomatis membuat tabel web_settings)
// =========================================================================
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS web_settings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        site_title VARCHAR(255) DEFAULT 'HIMASTI IT Del',
        email VARCHAR(150) NULL,
        phone VARCHAR(50) NULL,
        address TEXT NULL,
        instagram VARCHAR(255) NULL,
        youtube VARCHAR(255) NULL,
        footer_text VARCHAR(255) NULL,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");

    // Masukkan data default jika tabel kosong
    $cek = $pdo->query("SELECT COUNT(*) FROM web_settings")->fetchColumn();
    if ($cek == 0) {
        $pdo->exec("INSERT INTO web_settings (site_title, email, phone, address, instagram, footer_text) VALUES 
            ('HIMASTI IT Del', 'himasti@del.ac.id', '081234567890', 'Gedung Fakultas Informatika, Institut Teknologi Del, Jl. Sisingamangaraja, Sitoluama, Laguboti, Toba Samosir.', 'https://instagram.com/himasti.itdel', '© 2026 HIMASTI IT Del. All Rights Reserved.')");
    }
} catch (PDOException $e) {
    $pesan_error = "Error Database: " . $e->getMessage();
}

// =========================================================================
// 2. PROSES UPDATE PENGATURAN
// =========================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $site_title  = trim($_POST['site_title'] ?? '');
    $email       = trim($_POST['email'] ?? '');
    $phone       = trim($_POST['phone'] ?? '');
    $address     = trim($_POST['address'] ?? '');
    $instagram   = trim($_POST['instagram'] ?? '');
    $youtube     = trim($_POST['youtube'] ?? '');
    $footer_text = trim($_POST['footer_text'] ?? '');
    
    if (empty($site_title)) {
        $pesan_error = "Nama Website wajib diisi!";
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE web_settings SET site_title=?, email=?, phone=?, address=?, instagram=?, youtube=?, footer_text=? WHERE id=1");
            $stmt->execute([$site_title, $email, $phone, $address, $instagram, $youtube, $footer_text]);
            
            $pesan_sukses = "Konfigurasi website berhasil diperbarui secara global.";
        } catch (PDOException $e) {
            $pesan_error = "Gagal menyimpan pengaturan: " . $e->getMessage();
        }
    }
}

// =========================================================================
// 3. AMBIL DATA SAAT INI
// =========================================================================
$stmt = $pdo->query("SELECT * FROM web_settings LIMIT 1");
$setting = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$setting) {
    $setting = array_fill_keys(['site_title','email','phone','address','instagram','youtube','footer_text'], '');
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
        
        /* STANDAR SIDEBAR */
        .sidebar { width: 250px; background: #0f172a; color: white; position: fixed; height: 100vh; overflow-y: auto; z-index: 1000; }
        .sidebar-header { padding: 25px 20px; border-bottom: 1px solid rgba(255,255,255,0.05); background: #0b1120; }
        .sidebar-header h2 { color: #f8fafc; font-size: 22px; font-weight: 700; margin-bottom: 5px;}
        .sidebar-header p { color: #94a3b8; font-size: 12px; margin:0;}
        .menu { list-style: none; padding: 20px 0; margin:0;}
        .menu li a { display: flex; align-items: center; gap: 12px; color: #cbd5e1; text-decoration: none; padding: 12px 20px; transition: all 0.3s; font-size: 14px; border-left: 3px solid transparent;}
        .menu li a:hover, .menu li a.active { background: #1e293b; color: #fff; border-left-color: #38bdf8; }
        .menu-divider { color: #64748b; font-size: 11px; padding: 15px 20px 5px; text-transform: uppercase; font-weight: 700; letter-spacing: 1px;}
        
        .main { margin-left: 250px; padding: 35px 40px; width: calc(100% - 250px); }
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .page-header h1 { font-size: 26px; font-weight: 700; color: #0f172a; margin: 0; }
        
        .card-custom { background: #ffffff; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; margin-bottom: 30px; overflow: hidden; }
        .card-header-custom { background: #f8fafc; padding: 18px 25px; border-bottom: 1px solid #e2e8f0; font-weight: 600; color: #0f172a; font-size: 16px; display: flex; align-items: center; gap: 10px; }
        .card-body-custom { padding: 30px; }
        
        .form-label { font-weight: 600; color: #475569; font-size: 14px; margin-bottom: 8px; }
        .form-control { border-radius: 8px; border: 1px solid #cbd5e1; padding: 12px 15px; font-size: 14px; transition: 0.3s; }
        .form-control:focus { border-color: #38bdf8; box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.1); outline:none;}
        
        .btn-simpan { background: #0f172a; color: white; border: none; border-radius: 8px; font-weight: 600; padding: 12px 30px; transition: 0.3s; font-size: 15px; }
        .btn-simpan:hover { background: #1e293b; transform: translateY(-2px); box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        
        .input-group-text { background-color: #f1f5f9; border-color: #cbd5e1; color: #64748b; font-weight: 600; }
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
            <li><a href="../alumni/index.php"><i class="fas fa-user-graduate"></i> Jejaring Alumni</a></li>
            <li><a href="../user/index.php"><i class="fas fa-users-cog"></i> Pengguna Sistem</a></li>
            <li><a href="index.php" class="active"><i class="fas fa-cog"></i> Konfigurasi Web</a></li>
            <li class="menu-divider">Eksternal</li>
            <li><a href="../../index.php" target="_blank"><i class="fas fa-external-link-alt"></i> Buka Website Publik</a></li>
        </ul>
    </div>
    
    <div class="main">
        <div class="page-header">
            <div>
                <h1><i class="fas fa-cog text-primary me-2"></i> Konfigurasi Website</h1>
                <p class="text-muted mt-1 mb-0" style="font-size: 14px;">Kelola informasi kontak, alamat, dan sosial media secara global.</p>
            </div>
        </div>
        
        <?php if ($pesan_sukses): ?>
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3">
                <i class="fas fa-check-circle me-2"></i> <?= htmlspecialchars($pesan_sukses) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <?php if ($pesan_error): ?>
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3">
                <i class="fas fa-exclamation-triangle me-2"></i> <?= htmlspecialchars($pesan_error) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="card-custom">
                <div class="card-header-custom"><i class="fas fa-info-circle text-primary"></i> Identitas & Kontak Umum</div>
                <div class="card-body-custom">
                    <div class="row g-4">
                        <div class="col-md-12">
                            <label class="form-label">Nama Website / Organisasi <span class="text-danger">*</span></label>
                            <input type="text" name="site_title" class="form-control" value="<?= htmlspecialchars($setting['site_title']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email Publik Himpunan</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($setting['email']) ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nomor WhatsApp / Telepon</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($setting['phone']) ?>" placeholder="Contoh: 628123456789">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Alamat Lengkap Sekretariat</label>
                            <textarea name="address" class="form-control" rows="3"><?= htmlspecialchars($setting['address']) ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-custom">
                <div class="card-header-custom"><i class="fas fa-hashtag text-primary"></i> Media Sosial & Footer</div>
                <div class="card-body-custom">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label">Tautan Instagram</label>
                            <div class="input-group">
                                <span class="input-group-text text-danger"><i class="fab fa-instagram"></i></span>
                                <input type="url" name="instagram" class="form-control" value="<?= htmlspecialchars($setting['instagram']) ?>" placeholder="https://instagram.com/...">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tautan YouTube</label>
                            <div class="input-group">
                                <span class="input-group-text text-danger"><i class="fab fa-youtube"></i></span>
                                <input type="url" name="youtube" class="form-control" value="<?= htmlspecialchars($setting['youtube']) ?>" placeholder="https://youtube.com/...">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Teks Hak Cipta (Footer Text)</label>
                            <input type="text" name="footer_text" class="form-control" value="<?= htmlspecialchars($setting['footer_text']) ?>" placeholder="© 2026 HIMASTI IT Del">
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end mb-5">
                <button type="submit" class="btn-simpan shadow">
                    <i class="fas fa-cloud-upload-alt me-2"></i> Terapkan Pengaturan
                </button>
            </div>
        </form>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>