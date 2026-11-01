<?php
// admin/pengurus/tambah_anggota.php - Modul Pendaftaran Anggota Pengurus HIMASTI
require_once __DIR__ . '/../cek-login.php';
require_once __DIR__ . '/../../config/database.php';

$page_title = "Tambah Anggota Pengurus";
$pesan_error = "";

// ---------------------------------------------------------
// PERSIAPAN DATA (Mengambil daftar divisi untuk dropdown pilihan)
// ---------------------------------------------------------
try {
    $stmt_divisi = $pdo->query("SELECT id, name FROM divisions ORDER BY name ASC");
    $daftar_divisi = $stmt_divisi->fetchAll();
    
    if (empty($daftar_divisi)) {
        header("Location: index.php?msg=" . urlencode("Gagal: Anda harus membuat minimal 1 Divisi terlebih dahulu."));
        exit;
    }
} catch (PDOException $e) {
    $pesan_error = "Gagal memuat daftar divisi.";
    $daftar_divisi = [];
}

// ---------------------------------------------------------
// PROSES PENYIMPANAN DATA (Saat Form Disubmit)
// ---------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $division_id = $_POST['division_id'] ?? '';
    $name        = trim($_POST['name'] ?? '');
    $position    = trim($_POST['position'] ?? '');
    $linkedin    = trim($_POST['linkedin_url'] ?? '');
    
    if (empty($division_id) || empty($name) || empty($position)) {
        $pesan_error = "Harap isi semua kolom yang bertanda bintang (*).";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO members (division_id, name, position, linkedin_url, is_active) VALUES (?, ?, ?, ?, 1)");
            $insert_berhasil = $stmt->execute([$division_id, $name, $position, $linkedin]);
            
            if ($insert_berhasil) {
                header("Location: index.php?msg=" . urlencode("Pengurus atas nama {$name} berhasil ditambahkan."));
                exit;
            }
        } catch (PDOException $e) {
             $pesan_error = "Gagal Menyimpan Data (Error Database): " . $e->getMessage();
        }
    }
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
        .page-header h1 { font-size: 24px; font-weight: 700; color: #0f172a; margin: 0; }
        
        .card-custom { background: #ffffff; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; margin-bottom: 30px; max-width: 800px;}
        .card-body-custom { padding: 35px; }
        
        .form-label { font-weight: 600; color: #475569; font-size: 14px; margin-bottom: 8px; }
        .form-control, .form-select { border-radius: 8px; border: 1px solid #cbd5e1; padding: 12px 15px; font-size: 14px; transition: 0.3s; }
        .form-control:focus, .form-select:focus { border-color: #38bdf8; box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.1); outline:none;}
        
        .btn-kembali { background: #e2e8f0; color: #475569; border: none; border-radius: 8px; font-weight: 600; padding: 10px 20px; transition: 0.3s; text-decoration: none;}
        .btn-kembali:hover { background: #cbd5e1; color: #334155; }
        .btn-simpan { background: #0f172a; color: white; border: none; border-radius: 8px; font-weight: 600; padding: 12px 25px; transition: 0.3s; }
        .btn-simpan:hover { background: #1e293b; transform: translateY(-2px); box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
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
        <div class="page-header" style="max-width: 800px;">
            <h1><i class="fas fa-user-plus text-primary me-2"></i> Tambah Anggota Pengurus</h1>
            <a href="index.php" class="btn-kembali shadow-sm"><i class="fas fa-arrow-left me-2"></i>Kembali</a>
        </div>
        
        <?php if ($pesan_error): ?>
            <div class="alert alert-danger alert-dismissible fade show shadow-sm" style="max-width: 800px; border-radius:10px;">
                <i class="fas fa-exclamation-triangle me-2"></i> <?= htmlspecialchars($pesan_error) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="card-custom">
            <div class="card-body-custom">
                <form method="POST" action="">
                    
                    <div class="row g-4">
                        <div class="col-md-12">
                            <label class="form-label" for="division_id">Penempatan Divisi <span class="text-danger">*</span></label>
                            <select class="form-select" id="division_id" name="division_id" required>
                                <!-- Menggunakan 'hidden' agar opsi kosong ini hilang saat dropdown diklik -->
                                <option value="" disabled selected hidden></option>
                                <?php foreach ($daftar_divisi as $div): ?>
                                    <option value="<?= $div['id'] ?>"><?= htmlspecialchars($div['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label" for="name">Nama Lengkap Pengurus <span class="text-danger">*</span></label>
                            <!-- Atribut placeholder dihapus agar kotak bersih -->
                            <input type="text" id="name" name="name" class="form-control" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label" for="position">Jabatan / Posisi <span class="text-danger">*</span></label>
                            <!-- Atribut placeholder dihapus agar kotak bersih -->
                            <input type="text" id="position" name="position" class="form-control" required>
                        </div>
                        
                        <div class="col-md-12">
                            <label class="form-label" for="linkedin_url">Tautan Profil LinkedIn (Opsional)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-primary"><i class="fab fa-linkedin"></i></span>
                                <!-- Atribut placeholder dihapus agar kotak bersih -->
                                <input type="url" id="linkedin_url" name="linkedin_url" class="form-control">
                            </div>
                            <div class="form-text mt-2"><i class="fas fa-info-circle text-primary"></i> Standar kampus ternama: LinkedIn diletakkan agar relasi eksternal himpunan dapat melihat profesionalitas anggotanya.</div>
                        </div>
                    </div>

                    <hr style="border-color:#e2e8f0; margin: 30px 0;">
                    
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn-simpan shadow-sm">
                            <i class="fas fa-user-check me-2"></i> Daftarkan Anggota
                        </button>
                    </div>
                    
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>