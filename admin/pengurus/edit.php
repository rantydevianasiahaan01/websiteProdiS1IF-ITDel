<?php
// admin/pengurus/edit.php
require_once __DIR__ . '/../cek-login.php';
require_once __DIR__ . '/../../config/database.php';

$type = $_GET['type'] ?? 'member';
$id = $_GET['id'] ?? null;

$data = null;
if ($id) {
    try {
        if ($type === 'division') {
            $stmt = $pdo->prepare("SELECT * FROM divisions WHERE id = ?");
            $stmt->execute([$id]);
            $data = $stmt->fetch();
        } elseif ($type === 'member') {
            $stmt = $pdo->prepare("SELECT * FROM members WHERE id = ?");
            $stmt->execute([$id]);
            $data = $stmt->fetch();
        }
        
        if (!$data) {
            header("Location: index.php?error=Data tidak ditemukan");
            exit;
        }
    } catch (PDOException $e) {
        header("Location: index.php?error=Gagal mengambil data");
        exit;
    }
}

// Get divisions for dropdown
try {
    $divisions = $pdo->query("SELECT * FROM divisions WHERE is_active = 1 ORDER BY name")->fetchAll();
} catch (PDOException $e) {
    $divisions = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $error = '';
    
    if ($type === 'division') {
        $name = trim($_POST['name']);
        $description = trim($_POST['description']);
        $color = $_POST['color'];
        $sort_order = (int)$_POST['sort_order'];
        
        if (empty($name)) {
            $error = "Nama divisi wajib diisi!";
        } else {
            try {
                if ($id) {
                    $stmt = $pdo->prepare("UPDATE divisions SET name=?, description=?, color=?, sort_order=? WHERE id=?");
                    $stmt->execute([$name, $description, $color, $sort_order, $id]);
                } else {
                    $stmt = $pdo->prepare("INSERT INTO divisions (name, description, color, sort_order, created_at) VALUES (?, ?, ?, ?, NOW())");
                    $stmt->execute([$name, $description, $color, $sort_order]);
                }
                header("Location: index.php?msg=Divisi berhasil disimpan");
                exit;
            } catch (PDOException $e) {
                $error = "Gagal menyimpan: " . $e->getMessage();
            }
        }
        
    } elseif ($type === 'member') {
        $division_id = $_POST['division_id'] ?: null;
        $name = trim($_POST['name']);
        $role = $_POST['role'];
        $angkatan = trim($_POST['angkatan']);
        $email = trim($_POST['email']);
        $sort_order = (int)$_POST['sort_order'];
        
        if (empty($name)) {
            $error = "Nama dan posisi wajib diisi!";
        } else {
            $photo = 'avatar-default.jpg';
            if ($id && isset($_POST['old_photo'])) {
                $photo = $_POST['old_photo'];
            }
            
            if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = __DIR__ . '/../../assets/uploads/';
                $file_ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
                
                if (in_array($file_ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                    $photo = 'member-' . time() . '-' . uniqid() . '.' . $file_ext;
                    move_uploaded_file($_FILES['photo']['tmp_name'], $upload_dir . $photo);
                }
            }
            
            try {
                if ($id) {
                    $stmt = $pdo->prepare("UPDATE members SET division_id=?, name=?, role=?, photo=?, angkatan=?, email=?, sort_order=? WHERE id=?");
                    $stmt->execute([$division_id, $name, $role, $photo, $angkatan, $email, $sort_order, $id]);
                } else {
                    $stmt = $pdo->prepare("INSERT INTO members (division_id, name, role, photo, angkatan, email, sort_order, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
                    $stmt->execute([$division_id, $name, $role, $photo, $angkatan, $email, $sort_order]);
                }
                header("Location: index.php?msg=Anggota berhasil disimpan");
                exit;
            } catch (PDOException $e) {
                $error = "Gagal menyimpan: " . $e->getMessage();
            }
        }
    }
}

$page_title = ($id ? "Edit" : "Tambah") . " " . ($type === 'division' ? "Divisi" : "Anggota");
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
        
        /* Sidebar - SAMA PERSIS */
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
        
        /* Main Content - SAMA PERSIS */
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
        .header h1 { color: #1a2a3a; font-size: 24px; }
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
        .btn-primary:hover { background: #2c3e50; transform: translateY(-2px); }
        .btn-secondary {
            background: #6c757d;
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
        .btn-secondary:hover { background: #5a6268; transform: translateY(-2px); }
        
        /* Alert - SAMA PERSIS */
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
        
        /* Form Card - SAMA PERSIS */
        .form-card {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .form-card h3 {
            color: #1a2a3a;
            font-size: 18px;
            margin-bottom: 25px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        /* Form Elements - SAMA PERSIS */
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #1a2a3a;
            font-size: 14px;
        }
        .form-group label .required {
            color: #dc3545;
        }
        .form-control {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            transition: all 0.3s;
        }
        .form-control:focus {
            outline: none;
            border-color: #1a2a3a;
            box-shadow: 0 0 0 3px rgba(26, 42, 58, 0.1);
        }
        textarea.form-control {
            resize: vertical;
            min-height: 100px;
        }
        select.form-control {
            cursor: pointer;
        }
        
        /* Form Row */
        .form-row {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }
        
        /* Photo Preview */
        .photo-preview {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #e2e8f0;
            margin-bottom: 10px;
        }
        .photo-placeholder {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: linear-gradient(135deg, #1a2a3a 0%, #2c3e50 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 40px;
            margin-bottom: 10px;
        }
        
        /* Button Group */
        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #f0f0f0;
        }
        
        /* Breadcrumb */
        .breadcrumb {
            display: flex;
            list-style: none;
            padding: 0;
            margin-bottom: 20px;
            font-size: 14px;
        }
        .breadcrumb li + li::before {
            content: "›";
            margin: 0 10px;
            color: #95a5a6;
        }
        .breadcrumb a {
            color: #4a90a4;
            text-decoration: none;
        }
        .breadcrumb a:hover {
            text-decoration: underline;
        }
        .breadcrumb .active {
            color: #95a5a6;   
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .sidebar { width: 100%; height: auto; position: relative; }
            .main { margin-left: 0; width: 100%; }
            body { flex-direction: column; }
            .header { flex-direction: column; gap: 15px; align-items: flex-start; }
            .form-row { grid-template-columns: 1fr; }
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
            <li><a href="../berita/index.php"><i class="fas fa-newspaper"></i> Kelola Berita</a></li>
            <li><a href="../agenda/index.php"><i class="fas fa-calendar-alt"></i> Kelola Agenda</a></li>
            <li><a href="../galeri/index.php"><i class="fas fa-images"></i> Kelola Galeri</a></li>
            
            <li class="menu-divider">Profil Organisasi</li>
            <li><a href="../profil/index.php"><i class="fas fa-edit"></i> Edit Profil</a></li>
            <li><a href="index.php" class="active"><i class="fas fa-users"></i> Kelola Pengurus</a></li>
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
            <h1>
                <?php if ($type === 'division'): ?>
                    <i class="fas fa-sitemap"></i> <?= $id ? "Edit" : "Tambah" ?> Divisi
                <?php else: ?>
                    <i class="fas fa-user"></i> <?= $id ? "Edit" : "Tambah" ?> Anggota
                <?php endif; ?>
            </h1>
            <a href="index.php" class="btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
        
        <!-- Breadcrumb -->
        <ul class="breadcrumb">
            <li><a href="index.php">Kelola Pengurus</a></li>
            <li class="active"><?= $page_title ?></li>
        </ul>
        
        <?php if (isset($error) && $error): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                <span><?= htmlspecialchars($error) ?></span>
            </div>
        <?php endif; ?>
        
        <div class="form-card">
            <?php if ($type === 'division'): ?>
            <!-- FORM DIVISI -->
            <h3><i class="fas fa-sitemap"></i> Informasi Divisi</h3>
            <form method="POST">
                <div class="form-group">
                    <label>Nama Divisi <span class="required">*</span></label>
                    <input type="text" name="name" class="form-control" 
                           value="<?= htmlspecialchars($data['name'] ?? '') ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Deskripsi</label>
                    <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($data['description'] ?? '') ?></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Warna Badge</label>
                        <select name="color" class="form-control">
                            <option value="primary" <?= ($data['color'] ?? '') === 'primary' ? 'selected' : '' ?>>Biru</option>
                            <option value="success" <?= ($data['color'] ?? '') === 'success' ? 'selected' : '' ?>>Hijau</option>
                            <option value="info" <?= ($data['color'] ?? '') === 'info' ? 'selected' : '' ?>>Biru Muda</option>
                            <option value="warning" <?= ($data['color'] ?? '') === 'warning' ? 'selected' : '' ?>>Kuning</option>
                            <option value="danger" <?= ($data['color'] ?? '') === 'danger' ? 'selected' : '' ?>>Merah</option>
                            <option value="secondary" <?= ($data['color'] ?? '') === 'secondary' ? 'selected' : '' ?>>Abu-abu</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Urutan</label>
                        <input type="number" name="sort_order" class="form-control" 
                               value="<?= $data['sort_order'] ?? 0 ?>" min="0">
                        <small style="color: #95a5a6; font-size: 12px;">Angka lebih kecil = tampil lebih awal</small>
                    </div>
                </div>

                <div class="button-group">
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                    <a href="index.php" class="btn-secondary">
                        <i class="fas fa-times"></i> Batal
                    </a>
                </div>
            </form>

            <?php else: ?><!-- FORM ANGGOTA -->
<h3><i class="fas fa-user"></i> Informasi Anggota</h3>
<form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="old_photo" value="<?= htmlspecialchars($data['photo'] ?? '') ?>">
    
    <div class="form-row">
        <div class="form-group">
            <label>Nama Lengkap <span class="required">*</span></label>
            <input type="text" name="name" class="form-control" 
                   value="<?= htmlspecialchars($data['name'] ?? '') ?>" required>
        </div>
        <div class="form-group">
            <label>Role</label>
            <select name="role" class="form-control">
                <option value="">-- Tanpa Role --</option>
                <option value="member" <?= ($data['role'] ?? '') === 'member' ? 'selected' : '' ?>>Anggota</option>
                <option value="head" <?= ($data['role'] ?? '') === 'head' ? 'selected' : '' ?>>Ketua</option>
            </select>
            <small style="color: #95a5a6; font-size: 12px;">Opsional - pilih jika ingin menandai sebagai ketua/anggota</small>
        </div>
    </div>
    
    <div class="form-row">
        <div class="form-group">
            <label>Divisi</label>
            <select name="division_id" class="form-control">
                <option value="">-- Tanpa Divisi --</option>
                <?php foreach ($divisions as $div): ?>
                <option value="<?= $div['id'] ?>" 
                        <?= ($data['division_id'] ?? '') == $div['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($div['name']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Angkatan</label>
            <input type="text" name="angkatan" class="form-control" 
                   value="<?= htmlspecialchars($data['angkatan'] ?? '') ?>" 
                   placeholder="Contoh: 2021, 2022">
        </div>
    </div>
    
    <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" class="form-control" 
               value="<?= htmlspecialchars($data['email'] ?? '') ?>">
    </div>
    
    <div class="form-group">
        <label>Urutan</label>
        <input type="number" name="sort_order" class="form-control" 
               value="<?= $data['sort_order'] ?? 0 ?>" min="0">
    </div>
    
    <div class="form-group">
        <label>Foto Profil</label>
        <?php if (!empty($data['photo']) && $data['photo'] !== 'avatar-default.jpg'): ?>
        <div>
            <img src="../../assets/uploads/<?= htmlspecialchars($data['photo']) ?>" 
                 alt="Foto saat ini" class="photo-preview">
        </div>
        <?php else: ?>
        <div class="photo-placeholder">
            <i class="fas fa-user"></i>
        </div>
        <?php endif; ?>
        <input type="file" name="photo" class="form-control" accept="image/*">
        <small style="color: #95a5a6; font-size: 12px;">Kosongkan jika tidak ingin mengubah foto</small>
    </div>

    <div class="button-group">
        <button type="submit" class="btn-primary">
            <i class="fas fa-save"></i> Simpan
        </button>
        <a href="index.php" class="btn-secondary">
            <i class="fas fa-times"></i> Batal
        </a>
    </div>
</form>
<?php endif; ?>