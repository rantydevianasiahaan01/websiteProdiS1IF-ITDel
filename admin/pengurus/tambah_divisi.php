<?php
// admin/pengurus/tambah_divisi.php - Modul Penambahan Divisi HIMASTI
require_once __DIR__ . '/../cek-login.php';
require_once __DIR__ . '/../../config/database.php';

$page_title = "Tambah Divisi Baru";
$pesan_error = "";

// ---------------------------------------------------------
// PROSES PENYIMPANAN DATA SAAT FORM DISUBMIT (Metode POST)
// ---------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Sanitasi inputan Admin untuk mencegah XSS & Spasi berlebih
    $nama_divisi = trim($_POST['name'] ?? '');
    $deskripsi   = trim($_POST['description'] ?? '');

    // 2. Validasi input: Nama Divisi wajib diisi
    if (empty($nama_divisi)) {
        $pesan_error = "PERHATIAN: Nama divisi wajib diisi!";
    } else {
        try {
            // 3. Masukkan data (Insert) ke tabel 'divisions' menggunakan PDO Prepared Statement
            $stmt = $pdo->prepare("INSERT INTO divisions (name, description) VALUES (?, ?)");
            $insert_berhasil = $stmt->execute([$nama_divisi, $deskripsi]);

            if ($insert_berhasil) {
                // 4. Jika sukses, alihkan Admin kembali ke halaman depan Kelola Pengurus dengan pesan sukses.
                header("Location: index.php?msg=" . urlencode("Divisi '{$nama_divisi}' berhasil ditambahkan ke dalam struktur."));
                exit; // Hentikan eksekusi script setelah mengalihkan halaman
            }
        } catch (PDOException $e) {
            $pesan_error = "Gagal Menyimpan Data (Error Sistem): " . $e->getMessage();
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
        
        /* STANDAR SIDEBAR (Konsisten di semua halaman) */
        .sidebar { width: 250px; background: #0f172a; color: white; position: fixed; height: 100vh; overflow-y: auto; z-index: 1000; }
        .sidebar-header { padding: 25px 20px; border-bottom: 1px solid rgba(255,255,255,0.05); background: #0b1120; }
        .sidebar-header h2 { color: #f8fafc; font-size: 22px; margin-bottom: 5px; font-weight: 700; letter-spacing: 1px;}
        .sidebar-header p { color: #94a3b8; font-size: 12px; margin:0;}
        .menu { list-style: none; padding: 20px 0; margin:0;}
        .menu li a { display: flex; align-items: center; gap: 12px; color: #cbd5e1; text-decoration: none; padding: 12px 20px; transition: all 0.3s; border-left: 3px solid transparent; font-size: 14px; }
        .menu li a i { width: 20px; text-align: center; }
        .menu li a:hover, .menu li a.active { background: #1e293b; color: #fff; border-left-color: #38bdf8; }
        .menu-divider { color: #64748b; font-size: 11px; padding: 15px 20px 5px; text-transform: uppercase; font-weight: 700; letter-spacing: 1px;}
        
        /* AREA KONTEN UTAMA (Modern UI) */
        .main { margin-left: 250px; padding: 35px 40px; width: calc(100% - 250px); }
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .page-header h1 { font-size: 24px; font-weight: 700; color: #0f172a; margin: 0; }
        
        .card-custom { background: #ffffff; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; margin-bottom: 30px; overflow: hidden; max-width: 800px;}
        .card-body-custom { padding: 35px; }
        
        .form-label { font-weight: 600; color: #475569; font-size: 14px; margin-bottom: 8px; }
        .form-control { border-radius: 8px; border: 1px solid #cbd5e1; padding: 12px 15px; font-size: 14px; transition: 0.3s; }
        .form-control:focus { border-color: #38bdf8; box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.1); outline:none;}
        
        .btn-kembali { background: #e2e8f0; color: #475569; border: none; border-radius: 8px; font-weight: 600; padding: 10px 20px; transition: 0.3s; text-decoration: none;}
        .btn-kembali:hover { background: #cbd5e1; color: #334155; }
        .btn-simpan { background: #0f172a; color: white; border: none; border-radius: 8px; font-weight: 600; padding: 12px 25px; transition: 0.3s; }
        .btn-simpan:hover { background: #1e293b; transform: translateY(-2px); box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
    
    <!-- PANGGIL SIDEBAR UTAMA -->
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
    
    <!-- RUANG KERJA KONTEN UTAMA -->
    <div class="main">
        <div class="page-header" style="max-width: 800px;">
            <h1><i class="fas fa-folder-plus text-primary me-2"></i> Tambah Divisi / Departemen</h1>
            <a href="index.php" class="btn-kembali shadow-sm"><i class="fas fa-arrow-left me-2"></i>Kembali</a>
        </div>
        
        <?php if ($pesan_error): ?>
            <div class="alert alert-danger alert-dismissible fade show shadow-sm" style="max-width: 800px; border-radius:10px;">
                <i class="fas fa-exclamation-triangle me-2"></i> <?= htmlspecialchars($pesan_error) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- FORMULIR INPUT -->
        <div class="card-custom">
            <div class="card-body-custom">
                <form method="POST" action="">
                    <div class="mb-4">
                        <label class="form-label" for="name">Nama Divisi / Departemen <span class="text-danger">*</span></label>
                        <input type="text" id="name" name="name" class="form-control" required placeholder="Contoh: Departemen Pendidikan" value="<?= isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '' ?>">
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label" for="description">Deskripsi & Tupoksi Singkat (Opsional)</label>
                        <textarea id="description" name="description" class="form-control" rows="4" placeholder="Jelaskan peran divisi ini secara singkat..."><?= isset($_POST['description']) ? htmlspecialchars($_POST['description']) : '' ?></textarea>
                    </div>

                    <hr style="border-color:#e2e8f0; margin: 30px 0;">
                    
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn-simpan shadow-sm">
                            <i class="fas fa-save me-2"></i> Simpan Divisi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>