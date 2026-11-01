<?php
// admin/berita/edit.php - Edit Berita (CRUD Update)
require_once '../cek-login.php';
require_once '../../config/database.php';
require_once '../../config/auth.php';

cek_editor();

$error = '';
$success = '';

// Cek apakah ada parameter id
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = (int)$_GET['id'];

// Ambil data berita yang akan diedit
try {
    $stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
    $stmt->execute([$id]);
    $berita = $stmt->fetch();

    if (!$berita) {
        $_SESSION['error'] = "Berita tidak ditemukan!";
        header("Location: index.php");
        exit;
    }
} catch (PDOException $e) {
    $_SESSION['error'] = "Gagal mengambil data berita: " . $e->getMessage();
    header("Location: index.php");
    exit;
}

// Ambil kategori untuk dropdown
try {
    $stmt = $pdo->query("SELECT * FROM categories WHERE is_active = 1 ORDER BY name ASC");
    $categories = $stmt->fetchAll();
} catch (PDOException $e) {
    $categories = [];
}

// Proses form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $category_id = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null;
    $excerpt = trim($_POST['excerpt']);
    $content = trim($_POST['content']);
    $status = $_POST['status'];
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;

    // Validasi - HANYA JUDUL WAJIB
    if (empty($title)) {
        $error = "Judul berita harus diisi!";
    } else {
        // Generate slug baru jika judul berubah
        if ($title != $berita['title']) {
            $slug = generate_slug($title);
            $original_slug = $slug;
            $counter = 1;
            while (true) {
                $check_stmt = $pdo->prepare("SELECT id FROM posts WHERE slug = ? AND id != ?");
                $check_stmt->execute([$slug, $id]);
                if (!$check_stmt->fetch()) break;
                $slug = $original_slug . '-' . $counter;
                $counter++;
            }
        } else {
            $slug = $berita['slug'];
        }

        // Handle upload gambar (opsional, jika user upload gambar baru)
        $image_name = $berita['image'];
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            $max_size = 2 * 1024 * 1024;

            if (!in_array($_FILES['image']['type'], $allowed_types)) {
                $error = "Tipe file harus JPG, JPEG, PNG, atau GIF!";
            } elseif ($_FILES['image']['size'] > $max_size) {
                $error = "Ukuran file maksimal 2MB!";
            } else {
                // Hapus gambar lama jika bukan default
                if ($berita['image'] != 'default.jpg' && file_exists('../../assets/uploads/' . $berita['image'])) {
                    unlink('../../assets/uploads/' . $berita['image']);
                }

                $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $image_name = 'berita-' . time() . '-' . uniqid() . '.' . $ext;

                $upload_path = '../../assets/uploads/' . $image_name;

                if (!move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                    $error = "Gagal mengupload gambar!";
                    $image_name = $berita['image'];
                }
            }
        }

        if (empty($error)) {
            try {
                $published_at = $berita['published_at'];
                if ($status == 'published' && empty($published_at)) {
                    $published_at = date('Y-m-d H:i:s');
                } elseif ($status != 'published') {
                    $published_at = null;
                }
                
                // Jika konten kosong, buat konten default
                if (empty($content)) {
                    $content = '<p><em>Konten berita belum tersedia. Silakan hubungi admin untuk informasi lebih lanjut.</em></p>';
                }

                $stmt = $pdo->prepare("
                    UPDATE posts SET
                        category_id = ?, title = ?, slug = ?, excerpt = ?, content = ?,
                        image = ?, status = ?, is_featured = ?, published_at = ?
                    WHERE id = ?
                ");

                $stmt->execute([
                    $category_id, $title, $slug, $excerpt, $content,
                    $image_name, $status, $is_featured, $published_at, $id
                ]);

                $_SESSION['success'] = "Berita berhasil diupdate!";
                header("Location: index.php");
                exit;

            } catch (PDOException $e) {
                $error = "Gagal mengupdate berita: " . $e->getMessage();
            }
        }
    }
}

$page_title = "Edit Berita";
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
        body { background: #f4f6f8; font-family: 'Segoe UI', sans-serif; }
        .sidebar { width: 250px; background: #1a2a3a; color: white; position: fixed; height: 100vh; overflow-y: auto; }
        .sidebar-header { padding: 25px 20px; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .sidebar-header h2 { color: #ffffff; font-size: 22px; margin-bottom: 5px; font-weight: 700; }
        .sidebar-header p { color: #b0bec5; font-size: 12px; }
        .menu { list-style: none; padding: 20px 0; }
        .menu li a { display: block; color: white; text-decoration: none; padding: 12px 20px; transition: all 0.3s; border-left: 3px solid transparent; }
        .menu li a:hover { background: rgba(61, 90, 115, 0.3); border-left-color: #4a90a4; }
        .menu li a.active { background: #2c3e50; border-left-color: #4a90a4; }
        .menu-divider { color: #7f8c8d; font-size: 11px; padding: 15px 20px 5px; text-transform: uppercase; letter-spacing: 1px; font-weight: 600; }
        .main { margin-left: 250px; padding: 30px; }
        .content-card { background: white; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); padding: 25px; }
        .placeholder-image { 
            width: 100%; 
            height: 200px; 
            background: #f0f0f0; 
            border: 2px dashed #ddd;
            border-radius: 8px;
            display: flex; 
            align-items: center; 
            justify-content: center; 
            color: #999;
            margin-bottom: 10px;
        }
        @media (max-width: 768px) { .sidebar { width: 100%; height: auto; position: relative; } .main { margin-left: 0; } }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header"><h2>HIMASTI</h2><p>Panel Admin</p></div>
        <ul class="menu">
            <li><a href="../dashboard.php"> Dashboard</a></li>
            <li class="menu-divider">Konten</li>
            <li><a href="index.php" class="active">📰 Kelola Berita</a></li>
            <li><a href="../agenda/index.php">📅 Kelola Agenda</a></li>
            <li><a href="../galeri/index.php">🖼️ Kelola Galeri</a></li>
            <?php if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'super_admin'): ?>
                <li class="menu-divider">Master Data</li>
                <li><a href="../dosen/index.php">👨‍🏫 Kelola Dosen</a></li>
                <li><a href="../alumni/index.php">🎓 Kelola Alumni</a></li>
                <li><a href="../user/index.php">👥 Kelola User</a></li>
            <?php endif; ?>
            <li class="menu-divider">Lainnya</li>
            <li><a href="../login.php?logout=1">🚪 Logout</a></li>
        </ul>
    </div>

    <div class="main">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-edit me-2"></i><?= $page_title ?></h2>
            <a href="index.php" class="btn btn-secondary"><i class="fas fa-arrow-left me-2"></i>Kembali</a>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($error) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Tips:</strong> Hanya <strong>Judul</strong> yang wajib diisi. Field lainnya opsional.
        </div>

        <div class="content-card">
            <form method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="mb-3">
                            <label for="title" class="form-label">Judul Berita <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="title" name="title" value="<?= htmlspecialchars($berita['title']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="excerpt" class="form-label">Ringkasan (Excerpt)</label>
                            <textarea class="form-control" id="excerpt" name="excerpt" rows="3"><?= htmlspecialchars($berita['excerpt'] ?? '') ?></textarea>
                            <small class="text-muted">Opsional</small>
                        </div>
                        <div class="mb-3">
                            <label for="content" class="form-label">Konten Berita</label>
                            <textarea class="form-control" id="content" name="content" rows="15" style="font-family: monospace;"><?= htmlspecialchars($berita['content']) ?></textarea>
                            <small class="text-muted">Opsional - Jika kosong, akan tampil pesan default. Bisa gunakan HTML: &lt;b&gt;tebal&lt;/b&gt;, &lt;i&gt;miring&lt;/i&gt;, &lt;p&gt;paragraf&lt;/p&gt;</small>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="mb-3">
                            <label for="image" class="form-label">Gambar Utama</label>
                            <?php if ($berita['image'] != 'default.jpg' && file_exists('../../assets/uploads/' . $berita['image'])): ?>
                                <img src="../../assets/uploads/<?= htmlspecialchars($berita['image']) ?>" alt="Gambar saat ini" class="img-thumbnail mb-2" style="max-width: 100%;">
                            <?php else: ?>
                                <div class="placeholder-image">
                                    <div class="text-center">
                                        <i class="fas fa-image fa-3x mb-2"></i>
                                        <p class="mb-0">Tidak ada gambar</p>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <input type="file" class="form-control" id="image" name="image" accept="image/*">
                            <small class="text-muted">Upload gambar baru untuk mengganti. (Opsional)</small>
                        </div>
                        <div class="mb-3">
                            <label for="category_id" class="form-label">Kategori</label>
                            <select class="form-select" id="category_id" name="category_id">
                                <option value="">-- Pilih Kategori (Opsional) --</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>" <?= $berita['category_id'] == $cat['id'] ? 'selected' : '' ?>><?= htmlspecialchars($cat['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="draft" <?= $berita['status'] == 'draft' ? 'selected' : '' ?>>Draft</option>
                                <option value="published" <?= $berita['status'] == 'published' ? 'selected' : '' ?>>Published (Langsung Tayang)</option>
                                <option value="archived" <?= $berita['status'] == 'archived' ? 'selected' : '' ?>>Archived</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" value="1" <?= $berita['is_featured'] ? 'checked' : '' ?>>
                                <label class="form-check-label" for="is_featured">Jadikan Berita Unggulan</label>
                            </div>
                        </div>
                        <hr>
                        <button type="submit" class="btn btn-primary w-100"><i class="fas fa-save me-2"></i>Update Berita</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>