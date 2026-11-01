<?php
// admin/galeri/tambah.php
require_once __DIR__ . '/../cek-login.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/auth.php';

cek_editor();

$page_title = "Tambah Foto Galeri";
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $uploaded_by = $_SESSION['user_id'] ?? null;

    if (empty($title)) {
        $error = "Judul foto harus diisi!";
    } elseif (!isset($_FILES['image']) || $_FILES['image']['error'] !== 0) {
        $error = "Silakan pilih foto untuk diunggah!";
    } else {
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        $max_size = 3 * 1024 * 1024; // Maksimal 3MB

        if (!in_array($_FILES['image']['type'], $allowed_types)) {
            $error = "Format file tidak didukung! Gunakan JPG, PNG, atau GIF.";
        } elseif ($_FILES['image']['size'] > $max_size) {
            $error = "Ukuran file terlalu besar! Maksimal 3MB.";
        } else {
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $image_name = 'galeri-' . time() . '-' . uniqid() . '.' . $ext;
            $upload_path = __DIR__ . '/../../assets/uploads/' . $image_name;

            // Pastikan folder uploads ada
            if (!file_exists(dirname($upload_path))) {
                mkdir(dirname($upload_path), 0777, true);
            }

            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                try {
                    $stmt = $pdo->prepare("
                        INSERT INTO galleries (title, image, description, uploaded_by)
                        VALUES (?, ?, ?, ?)
                    ");
                    $stmt->execute([$title, $image_name, $description, $uploaded_by]);

                    header("Location: index.php?msg=Foto berhasil ditambahkan ke galeri");
                    exit;
                } catch (PDOException $e) {
                    $error = "Gagal menyimpan data ke database: " . $e->getMessage();
                    // Hapus file jika database gagal
                    if (file_exists($upload_path)) unlink($upload_path);
                }
            } else {
                $error = "Gagal mengunggah file. Periksa hak akses folder.";
            }
        }
    }
}
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
        .sidebar { width: 250px; background: #1a2a3a; color: white; position: fixed; height: 100vh; overflow-y: auto; }
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
        .header { background: white; padding: 20px 25px; border-radius: 8px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 5px rgba(0,0,0,0.05); margin-bottom: 30px; }
        .header h1 { color: #1a2a3a; font-size: 24px; }
        
        .btn-secondary { background: #95a5a6; color: white; padding: 10px 20px; border-radius: 6px; text-decoration: none; font-size: 14px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; border: none; cursor: pointer; transition: all 0.3s; }
        .btn-secondary:hover { background: #7f8c8d; }
        .btn-primary { background: #1a2a3a; color: white; padding: 12px 24px; border-radius: 6px; font-size: 14px; font-weight: 600; display: inline-flex; align-items: center; justify-content: center; gap: 8px; border: none; cursor: pointer; transition: all 0.3s; width: 100%; margin-top: 15px; }
        .btn-primary:hover { background: #2c3e50; transform: translateY(-2px); }
        
        .alert { padding: 15px 20px; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; font-size: 14px; }
        .alert-danger { background: #f8d7da; color: #721c24; border-left: 4px solid #dc3545; }
        
        .form-card { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); max-width: 800px; }
        .form-group { margin-bottom: 20px; }
        .form-label { display: block; color: #1a2a3a; font-size: 14px; font-weight: 600; margin-bottom: 8px; }
        .form-label .required { color: #dc3545; }
        .form-control { width: 100%; padding: 12px 16px; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 14px; transition: all 0.3s; }
        .form-control:focus { outline: none; border-color: #4a90a4; box-shadow: 0 0 0 3px rgba(74, 144, 164, 0.1); }
        
        @media (max-width: 768px) { .sidebar { width: 100%; height: auto; position: relative; } .main { margin-left: 0; width: 100%; } body { flex-direction: column; } .header { flex-direction: column; gap: 15px; align-items: flex-start; } }
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
            <li class="menu-divider">Lainnya</li>
            <li><a href="../../index.php" target="_blank"><i class="fas fa-globe"></i> Lihat Website</a></li>
            <li><a href="../login.php?logout=1" style="color: #e74c3c;"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>
    
    <div class="main">
        <div class="header">
            <h1><i class="fas fa-upload"></i> Tambah Foto Galeri</h1>
            <a href="index.php" class="btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                <span><?= htmlspecialchars($error) ?></span>
            </div>
        <?php endif; ?>
        
        <div class="form-card">
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label class="form-label">Judul Foto <span class="required">*</span></label>
                    <input type="text" name="title" class="form-control" required 
                           value="<?= htmlspecialchars($_POST['title'] ?? '') ?>"
                           placeholder="Contoh: Dokumentasi HIMASTI Cup 2026">
                </div>
                
                <div class="form-group">
                    <label class="form-label">File Foto <span class="required">*</span></label>
                    <input type="file" name="image" class="form-control" required accept="image/*">
                    <small style="color: #95a5a6; font-size: 12px; display: block; margin-top: 5px;">Maksimal 3MB (JPG, PNG, GIF)</small>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Deskripsi Singkat</label>
                    <textarea name="description" class="form-control" rows="4" 
                              placeholder="Ceritakan sedikit tentang momen ini..."><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                </div>
                
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save"></i> Unggah Foto
                </button>
            </form>
        </div>
    </div>
</body>
</html>