<?php
// admin/pengurus/edit_anggota.php
require_once __DIR__ . '/../cek-login.php';
require_once __DIR__ . '/../../config/database.php';

$id = $_GET['id'] ?? 0;
$pesan_error = "";

// 1. Ambil data anggota yang mau diedit
$stmt = $pdo->prepare("SELECT * FROM members WHERE id = ?");
$stmt->execute([$id]);
$anggota = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$anggota) {
    header("Location: index.php?msg=" . urlencode("Gagal: Data anggota tidak ditemukan."));
    exit;
}

// 2. Ambil daftar divisi untuk pilihan pindah divisi
$stmt_div = $pdo->query("SELECT id, name FROM divisions ORDER BY name ASC");
$daftar_divisi = $stmt_div->fetchAll();

// 3. Proses Update Data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $division_id = $_POST['division_id'] ?? '';
    $name        = trim($_POST['name'] ?? '');
    $position    = trim($_POST['position'] ?? '');
    $linkedin    = trim($_POST['linkedin_url'] ?? '');
    $is_active   = isset($_POST['is_active']) ? 1 : 0;
    
    if (empty($division_id) || empty($name) || empty($position)) {
        $pesan_error = "Harap isi semua kolom wajib (*).";
    } else {
        try {
            // Update database (Fokus pada teks dan status saja)
            $stmt_update = $pdo->prepare("UPDATE members SET division_id=?, name=?, position=?, linkedin_url=?, is_active=? WHERE id=?");
            $stmt_update->execute([$division_id, $name, $position, $linkedin, $is_active, $id]);
            
            // Kembalikan admin ke halaman detail divisi
            header("Location: lihat_anggota.php?divisi={$division_id}&msg=" . urlencode("Data {$name} berhasil diperbarui!"));
            exit;
        } catch (PDOException $e) {
            $pesan_error = "Error Database: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pengurus - Admin HIMASTI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { font-family: 'Inter', 'Segoe UI', Tahoma, sans-serif; }
        body { background: #f8fafc; display: flex; min-height: 100vh; margin: 0; color: #334155; }
        
        .sidebar { width: 250px; background: #0f172a; color: white; position: fixed; height: 100vh; overflow-y: auto; z-index: 1000; }
        .sidebar-header { padding: 25px 20px; border-bottom: 1px solid rgba(255,255,255,0.05); background: #0b1120; }
        .sidebar-header h2 { color: #f8fafc; font-size: 22px; margin-bottom: 5px; font-weight: 700; }
        .sidebar-header p { color: #94a3b8; font-size: 12px; margin:0;}
        .menu { list-style: none; padding: 20px 0; margin:0;}
        .menu li a { display: flex; align-items: center; gap: 12px; color: #cbd5e1; text-decoration: none; padding: 12px 20px; transition: 0.3s; font-size: 14px; border-left: 3px solid transparent;}
        .menu li a:hover, .menu li a.active { background: #1e293b; color: #fff; border-left-color: #38bdf8; }
        .menu-divider { color: #64748b; font-size: 11px; padding: 15px 20px 5px; text-transform: uppercase; font-weight: 700;}
        
        .main { margin-left: 250px; padding: 35px 40px; width: calc(100% - 250px); }
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; max-width: 800px; }
        .page-header h1 { font-size: 24px; font-weight: 700; color: #0f172a; margin: 0; }
        
        .card-custom { background: #ffffff; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; margin-bottom: 30px; max-width: 800px; padding: 35px;}
        .form-label { font-weight: 600; color: #475569; font-size: 14px; margin-bottom: 8px; }
        .form-control, .form-select { border-radius: 8px; border: 1px solid #cbd5e1; padding: 10px 15px; font-size: 14px; }
        .form-control:focus, .form-select:focus { border-color: #38bdf8; box-shadow: 0 0 0 3px rgba(56,189,248,0.1); outline:none;}
        
        .btn-kembali { background: #e2e8f0; color: #475569; border: none; border-radius: 8px; font-weight: 600; padding: 10px 20px; text-decoration: none;}
        .btn-simpan { background: #0f172a; color: white; border: none; border-radius: 8px; font-weight: 600; padding: 12px 25px; transition: 0.3s; }
        .btn-simpan:hover { background: #1e293b; transform: translateY(-2px); }
    </style>
</head>
<body>
    
    <!-- SIDEBAR -->
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
            <li><a href="index.php" class="active"><i class="fas fa-sitemap"></i> Struktur Pengurus</a></li>
            <li><a href="../program/index.php"><i class="fas fa-star"></i> Program Unggulan</a></li>
            <li class="menu-divider">Master Data</li>
            <li><a href="../dosen/index.php"><i class="fas fa-chalkboard-teacher"></i> Data Dosen</a></li>
            <li><a href="../alumni/index.php"><i class="fas fa-user-graduate"></i> Jejaring Alumni</a></li>
            <li><a href="../user/index.php"><i class="fas fa-users-cog"></i> Pengguna Sistem</a></li>
            <li><a href="../pengaturan/index.php"><i class="fas fa-cog"></i> Konfigurasi Web</a></li>
        </ul>
    </div>
    
    <div class="main">
        <div class="page-header">
            <h1><i class="fas fa-pen text-primary me-2"></i> Edit Data Pengurus</h1>
            <a href="lihat_anggota.php?divisi=<?= $anggota['division_id'] ?>" class="btn-kembali"><i class="fas fa-arrow-left me-2"></i>Batal</a>
        </div>
        
        <?php if ($pesan_error): ?>
            <div class="alert alert-danger shadow-sm" style="max-width: 800px; border-radius:10px;"><i class="fas fa-exclamation-triangle me-2"></i> <?= $pesan_error ?></div>
        <?php endif; ?>

        <div class="card-custom">
            <!-- Form ini sudah bersih, atribut enctype="multipart/form-data" telah dibuang -->
            <form method="POST" action="">
                <div class="row g-4">
                    <div class="col-md-12">
                        <label class="form-label">Penempatan Divisi <span class="text-danger">*</span></label>
                        <select class="form-select" name="division_id" required>
                            <?php foreach ($daftar_divisi as $div): ?>
                                <option value="<?= $div['id'] ?>" <?= ($div['id'] == $anggota['division_id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($div['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required value="<?= htmlspecialchars($anggota['name']) ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Jabatan <span class="text-danger">*</span></label>
                        <input type="text" name="position" class="form-control" required value="<?= htmlspecialchars($anggota['position']) ?>">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Tautan LinkedIn (Opsional)</label>
                        <input type="url" name="linkedin_url" class="form-control" value="<?= htmlspecialchars($anggota['linkedin_url'] ?? '') ?>">
                    </div>
                    <div class="col-md-12">
                        <div class="form-check form-switch mt-2">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" <?= $anggota['is_active'] ? 'checked' : '' ?> style="transform: scale(1.3); margin-right:10px;">
                            <label class="form-check-label fw-bold" for="is_active">Anggota Aktif (Matikan jika demisioner)</label>
                        </div>
                    </div>
                </div>

                <hr style="border-color:#e2e8f0; margin: 30px 0;">
                
                <div class="text-end">
                    <button type="submit" class="btn-simpan"><i class="fas fa-save me-2"></i> Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>