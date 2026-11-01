<?php
require_once __DIR__ . '/../cek-login.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/auth.php';

cek_editor();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $event_date = $_POST['event_date'] ?? '';
    $start_time = !empty($_POST['start_time']) ? $_POST['start_time'] : null;
    $end_time = !empty($_POST['end_time']) ? $_POST['end_time'] : null;
    $location = trim($_POST['location'] ?? '');
    $location_detail = trim($_POST['location_detail'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $status = $_POST['status'] ?? 'upcoming';
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $registration_link = trim($_POST['registration_link'] ?? '');
    $created_by = $_SESSION['user_id'] ?? null;

    if (empty($title)) {
        $error = "Judul agenda harus diisi!";
    } elseif (empty($event_date)) {
        $error = "Tanggal acara harus diisi!";
    } else {
        // Generate slug
        $slug = generate_slug($title);
        $original_slug = $slug;
        $counter = 1;
        while (true) {
            $check_stmt = $pdo->prepare("SELECT id FROM events WHERE slug = ?");
            $check_stmt->execute([$slug]);
            if (!$check_stmt->fetch()) break;
            $slug = $original_slug . '-' . $counter;
            $counter++;
        }

        // Upload gambar
        $image_name = 'default-event.jpg';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            $max_size = 2 * 1024 * 1024;

            if (in_array($_FILES['image']['type'], $allowed_types) && $_FILES['image']['size'] <= $max_size) {
                $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $image_name = 'agenda-' . time() . '-' . uniqid() . '.' . $ext;
                $upload_path = __DIR__ . '/../../assets/uploads/' . $image_name;
                
                if (!move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                    $image_name = 'default-event.jpg';
                }
            }
        }

        // Konten default jika kosong
        if (empty($content)) {
            $content = '<p><em>Detail agenda belum tersedia.</em></p>';
        }

        try {
            $stmt = $pdo->prepare("
                INSERT INTO events (title, slug, description, content, event_date, start_time, end_time, 
                                   location, location_detail, image, category, registration_link, 
                                   status, is_featured, created_by)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $stmt->execute([
                $title, $slug, $description, $content, $event_date, $start_time, $end_time,
                $location, $location_detail, $image_name, $category, $registration_link,
                $status, $is_featured, $created_by
            ]);

            header("Location: index.php?msg=Agenda berhasil ditambahkan");
            exit;

        } catch (PDOException $e) {
            $error = "Gagal menyimpan: " . $e->getMessage();
        }
    }
}

$page_title = "Tambah Agenda";
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
        .header h1 { color: #1a2a3a; font-size: 24px; }
        .btn-secondary {
            background: #95a5a6;
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
        .btn-secondary:hover { background: #7f8c8d; transform: translateY(-2px); }
        
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
        .alert-info { background: #d1ecf1; color: #0c5460; border-left: 4px solid #17a2b8; }
        
        /* Form Card */
        .form-card {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            margin-bottom: 30px;
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
        
        /* Form Elements */
        .form-group { margin-bottom: 25px; }
        .form-label {
            display: block;
            color: #1a2a3a;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 8px;
        }
        .form-label .required { color: #dc3545; }
        .form-control {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            font-size: 14px;
            transition: all 0.3s;
            font-family: 'Segoe UI', Tahoma, sans-serif;
        }
        .form-control:focus {
            outline: none;
            border-color: #4a90a4;
            box-shadow: 0 0 0 3px rgba(74, 144, 164, 0.1);
        }
        textarea.form-control {
            resize: vertical;
            min-height: 100px;
        }
        .form-text {
            display: block;
            margin-top: 5px;
            font-size: 12px;
            color: #95a5a6;
        }
        
        /* Form Check */
        .form-check {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 20px 0;
        }
        .form-check-input {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }
        .form-check-label {
            color: #1a2a3a;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
        }
        
        /* Buttons */
        .btn-primary {
            background: #1a2a3a;
            color: white;
            padding: 12px 24px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border: none;
            cursor: pointer;
            transition: all 0.3s;
            width: 100%;
        }
        .btn-primary:hover { background: #2c3e50; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(26, 42, 58, 0.3); }
        
        /* Grid Layout */
        .row { display: flex; flex-wrap: wrap; margin: 0 -15px; }
        .col-6 { flex: 0 0 50%; max-width: 50%; padding: 0 15px; }
        .col-md-8 { flex: 0 0 66.666%; max-width: 66.666%; padding: 0 15px; }
        .col-md-4 { flex: 0 0 33.333%; max-width: 33.333%; padding: 0 15px; }
        
        /* Divider */
        hr {
            margin: 30px 0;
            border: 0;
            border-top: 1px solid #e2e8f0;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .sidebar { width: 100%; height: auto; position: relative; }
            .main { margin-left: 0; width: 100%; }
            body { flex-direction: column; }
            .header { flex-direction: column; gap: 15px; align-items: flex-start; }
            .col-md-8, .col-md-4, .col-6 { flex: 0 0 100%; max-width: 100%; }
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
            <li><a href="index.php" class="active"><i class="fas fa-calendar-alt"></i> Kelola Agenda</a></li>
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
            <h1><i class="fas fa-calendar-plus"></i> Tambah Agenda</h1>
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
        
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i>
            <div>
                <strong>Tips:</strong> Hanya <strong>Judul</strong> dan <strong>Tanggal Acara</strong> yang wajib diisi.
            </div>
        </div>
        
        <div class="form-card">
            <h3><i class="fas fa-edit"></i> Form Agenda</h3>
            
            <form method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label class="form-label">
                                Judul Agenda <span class="required">*</span>
                            </label>
                            <input type="text" name="title" class="form-control" required 
                                   value="<?= htmlspecialchars($_POST['title'] ?? '') ?>"
                                   placeholder="Contoh: Workshop Pemrograman Web">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Deskripsi Singkat</label>
                            <textarea name="description" class="form-control" rows="3" 
                                      placeholder="Deskripsi singkat agenda..."><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                            <small class="form-text">Opsional - Akan muncul di card agenda</small>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Konten Lengkap</label>
                            <textarea name="content" class="form-control" rows="10" 
                                      placeholder="Detail lengkap agenda..."><?= $_POST['content'] ?? '' ?></textarea>
                            <small class="form-text">Opsional - Bisa gunakan HTML</small>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label">Gambar</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                            <small class="form-text">Opsional - Max 2MB</small>
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                Tanggal Acara <span class="required">*</span>
                            </label>
                            <input type="date" name="event_date" class="form-control" required 
                                   value="<?= htmlspecialchars($_POST['event_date'] ?? '') ?>">
                        </div>

                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="form-label">Jam Mulai</label>
                                    <input type="time" name="start_time" class="form-control" 
                                           value="<?= htmlspecialchars($_POST['start_time'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="form-label">Jam Selesai</label>
                                    <input type="time" name="end_time" class="form-control" 
                                           value="<?= htmlspecialchars($_POST['end_time'] ?? '') ?>">
                                    <small class="form-text">Opsional</small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Lokasi</label>
                            <input type="text" name="location" class="form-control" 
                                   value="<?= htmlspecialchars($_POST['location'] ?? '') ?>"
                                   placeholder="Contoh: Aula IT Del">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Detail Lokasi</label>
                            <textarea name="location_detail" class="form-control" rows="2" 
                                      placeholder="Contoh: Lantai 2, Ruang 201"><?= htmlspecialchars($_POST['location_detail'] ?? '') ?></textarea>
                            <small class="form-text">Opsional - Keterangan tambahan lokasi</small>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Kategori</label>
                            <input type="text" name="category" class="form-control" 
                                   value="<?= htmlspecialchars($_POST['category'] ?? '') ?>"
                                   placeholder="Contoh: Workshop, Seminar, Lomba">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-control">
                                <option value="upcoming" <?= (($_POST['status'] ?? 'upcoming') == 'upcoming') ? 'selected' : '' ?>>Akan Datang</option>
                                <option value="ongoing" <?= (($_POST['status'] ?? '') == 'ongoing') ? 'selected' : '' ?>>Sedang Berlangsung</option>
                                <option value="done" <?= (($_POST['status'] ?? '') == 'done') ? 'selected' : '' ?>>Selesai</option>
                                <option value="cancelled" <?= (($_POST['status'] ?? '') == 'cancelled') ? 'selected' : '' ?>>Dibatalkan</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Link Pendaftaran</label>
                            <input type="url" name="registration_link" class="form-control" 
                                   value="<?= htmlspecialchars($_POST['registration_link'] ?? '') ?>"
                                   placeholder="https://...">
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="is_featured"
                                   <?= (isset($_POST['is_featured']) && $_POST['is_featured']) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="is_featured">
                                <i class="fas fa-star"></i> Jadikan Agenda Unggulan
                            </label>
                        </div>

                        <hr>
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-save"></i> Simpan Agenda
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</body>
</html>