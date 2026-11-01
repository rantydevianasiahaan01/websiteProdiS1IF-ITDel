<?php
// admin/dosen/edit.php
require_once __DIR__ . '/../cek-login.php';
require_once __DIR__ . '/../../config/database.php';

$id = $_GET['id'] ?? 0;
$pesan_error = "";

$stmt = $pdo->prepare("SELECT * FROM lecturers WHERE id = ?");
$stmt->execute([$id]);
$dosen = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$dosen) {
    header("Location: index.php?msg=" . urlencode("Gagal: Data dosen tidak ditemukan."));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nidn      = trim($_POST['nidn'] ?? '');
    $name      = trim($_POST['name'] ?? '');
    $position  = trim($_POST['position'] ?? '');
    $expertise = trim($_POST['expertise'] ?? '');
    $scholar   = trim($_POST['scholar_url'] ?? '');
    $linkedin  = trim($_POST['linkedin_url'] ?? '');
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    // Simpan nama foto lama sebagai default jika admin tidak mengupload foto baru
    $photo_name = !empty($dosen['photo']) ? $dosen['photo'] : 'default-user.png';

    if (empty($name) || empty($expertise)) {
        $pesan_error = "Nama Lengkap dan Kelompok Keahlian wajib diisi.";
    } else {
        // Logika Upload Foto Baru
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
            $file_tmp = $_FILES['photo']['tmp_name'];
            $file_name = $_FILES['photo']['name'];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            
            $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
            
            if (in_array($file_ext, $allowed_ext)) {
                $new_photo_name = time() . '_' . str_replace(' ', '_', $file_name);
                $target_dir = "../../assets/img/lecturers/"; 
                $target_file = $target_dir . $new_photo_name;

                if (move_uploaded_file($file_tmp, $target_file)) {
                    // Jika upload berhasil, hapus foto lama (kecuali jika foto lama adalah default-user.png)
                    if ($photo_name !== "default-user.png" && file_exists($target_dir . $photo_name)) {
                        unlink($target_dir . $photo_name);
                    }
                    // Timpa variabel nama foto untuk disimpan ke database
                    $photo_name = $new_photo_name;
                } else {
                    $pesan_error = "Gagal mengunggah foto. Pastikan folder tujuan memiliki izin tulis.";
                }
            } else {
                $pesan_error = "Format file foto tidak valid. Hanya izinkan JPG, JPEG, PNG, atau GIF.";
            }
        }

        if (empty($pesan_error)) {
            try {
                // Tambahkan kolom photo ke query UPDATE
                $stmt_update = $pdo->prepare("UPDATE lecturers SET nidn=?, name=?, position=?, expertise=?, scholar_url=?, linkedin_url=?, is_active=?, photo=? WHERE id=?");
                $stmt_update->execute([$nidn, $name, $position, $expertise, $scholar, $linkedin, $is_active, $photo_name, $id]);
                
                header("Location: index.php?msg=" . urlencode("Profil {$name} berhasil diperbarui."));
                exit;
            } catch (PDOException $e) {
                 $pesan_error = "Error Database: " . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Dosen - Admin HIMASTI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { font-family: 'Inter', 'Segoe UI', Tahoma, sans-serif; }
        body { background: #f8fafc; display: flex; min-height: 100vh; margin: 0; color: #334155; }
        .sidebar { width: 250px; background: #0f172a; color: white; position: fixed; height: 100vh; z-index: 1000; }
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
        
        .card-custom { background: #ffffff; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; max-width: 800px; padding: 35px;}
        .form-label { font-weight: 600; color: #475569; font-size: 14px; margin-bottom: 8px; }
        .form-control { border-radius: 8px; border: 1px solid #cbd5e1; padding: 12px 15px; font-size: 14px; }
        .form-control:focus { border-color: #38bdf8; box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.1); outline:none;}
        
        .btn-kembali { background: #e2e8f0; color: #475569; border: none; border-radius: 8px; font-weight: 600; padding: 10px 20px; text-decoration: none;}
        .btn-simpan { background: #0f172a; color: white; border: none; border-radius: 8px; font-weight: 600; padding: 12px 25px; transition: 0.3s; }
        .btn-simpan:hover { background: #1e293b; transform: translateY(-2px); }
        
        .current-photo { width: 80px; height: 80px; object-fit: cover; border-radius: 8px; border: 2px solid #e2e8f0; margin-right: 15px; }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header"><h2>HIMASTI</h2><p>Admin Panel &bull; IT Del</p></div>
        <ul class="menu">
            <li><a href="../dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
            <li class="menu-divider">Master Data</li>
            <li><a href="index.php" class="active"><i class="fas fa-chalkboard-teacher"></i> Data Dosen</a></li>
            <li><a href="../../index.php" target="_blank" class="mt-4"><i class="fas fa-external-link-alt"></i> Buka Website</a></li>
        </ul>
    </div>
    
    <div class="main">
        <div class="page-header">
            <h1><i class="fas fa-pen text-primary me-2"></i> Edit Data Dosen</h1>
            <a href="index.php" class="btn-kembali shadow-sm"><i class="fas fa-arrow-left me-2"></i>Batal</a>
        </div>
        
        <?php if ($pesan_error): ?>
            <div class="alert alert-danger" style="max-width: 800px; border-radius:10px;"><i class="fas fa-exclamation-triangle me-2"></i> <?= $pesan_error ?></div>
        <?php endif; ?>

        <div class="card-custom">
            <!-- Tambahkan enctype="multipart/form-data" -->
            <form method="POST" action="" enctype="multipart/form-data">
                <div class="row g-4">
                    
                    <!-- Preview Foto Saat Ini & Upload Baru -->
                    <div class="col-md-12 d-flex align-items-center bg-light p-3 rounded border">
                        <?php $foto_tampil = !empty($dosen['photo']) ? $dosen['photo'] : 'default-user.png'; ?>
                        <img src="../../assets/img/lecturers/<?= htmlspecialchars($foto_tampil) ?>" class="current-photo" alt="Foto Saat Ini" onerror="this.src='../../assets/img/lecturers/default-user.png'">
                        <div class="flex-grow-1">
                            <label class="form-label mb-1">Ganti Foto Profil (Opsional)</label>
                            <input type="file" name="photo" class="form-control form-control-sm" accept="image/jpeg, image/png, image/jpg, image/gif">
                            <small class="text-muted d-block mt-1">Abaikan jika tidak ingin mengganti foto. Format: JPG, PNG.</small>
                        </div>
                    </div>

                    <div class="col-md-8">
                        <label class="form-label">Nama Lengkap (Berta Gelar) <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required value="<?= htmlspecialchars($dosen['name']) ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">NIDN</label>
                        <input type="text" name="nidn" class="form-control" value="<?= htmlspecialchars($dosen['nidn']) ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Jabatan Akademik</label>
                        <input type="text" name="position" class="form-control" value="<?= htmlspecialchars($dosen['position']) ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Kelompok Keahlian <span class="text-danger">*</span></label>
                        <input type="text" name="expertise" class="form-control" required value="<?= htmlspecialchars($dosen['expertise']) ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tautan Google Scholar</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-warning"><i class="fas fa-graduation-cap"></i></span>
                            <input type="url" name="scholar_url" class="form-control" value="<?= htmlspecialchars($dosen['scholar_url']) ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tautan LinkedIn</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-primary"><i class="fab fa-linkedin"></i></span>
                            <input type="url" name="linkedin_url" class="form-control" value="<?= htmlspecialchars($dosen['linkedin_url']) ?>">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-check form-switch mt-2">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" <?= $dosen['is_active'] ? 'checked' : '' ?> style="transform: scale(1.3); margin-right:10px;">
                            <label class="form-check-label fw-bold" for="is_active">Dosen Aktif (Matikan jika sedang Tugas Belajar/S3)</label>
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