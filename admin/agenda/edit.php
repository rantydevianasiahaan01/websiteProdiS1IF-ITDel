<?php
require_once '../cek-login.php';
require_once '../../config/database.php';
require_once '../../config/auth.php';

cek_editor();

$error = '';
$id = $_GET['id'] ?? 0;

// Ambil data agenda
try {
    $stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
    $stmt->execute([$id]);
    $agenda = $stmt->fetch();

    if (!$agenda) {
        $_SESSION['error'] = "Agenda tidak ditemukan!";
        header("Location: index.php");
        exit;
    }
} catch (PDOException $e) {
    $_SESSION['error'] = "Gagal mengambil data: " . $e->getMessage();
    header("Location: index.php");
    exit;
}

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

    if (empty($title)) {
        $error = "Judul agenda harus diisi!";
    } elseif (empty($event_date)) {
        $error = "Tanggal acara harus diisi!";
    } else {
        // Generate slug baru jika judul berubah
        if ($title != $agenda['title']) {
            $slug = generate_slug($title);
            $original_slug = $slug;
            $counter = 1;
            while (true) {
                $check_stmt = $pdo->prepare("SELECT id FROM events WHERE slug = ? AND id != ?");
                $check_stmt->execute([$slug, $id]);
                if (!$check_stmt->fetch()) break;
                $slug = $original_slug . '-' . $counter;
                $counter++;
            }
        } else {
            $slug = $agenda['slug'];
        }

        // Upload gambar baru
        $image_name = $agenda['image'];
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            $max_size = 2 * 1024 * 1024;

            if (in_array($_FILES['image']['type'], $allowed_types) && $_FILES['image']['size'] <= $max_size) {
                if ($agenda['image'] != 'default-event.jpg' && file_exists('../../assets/uploads/' . $agenda['image'])) {
                    unlink('../../assets/uploads/' . $agenda['image']);
                }

                $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $image_name = 'agenda-' . time() . '-' . uniqid() . '.' . $ext;
                $upload_path = '../../assets/uploads/' . $image_name;

                if (!move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                    $image_name = $agenda['image'];
                }
            }
        }

        if (empty($content)) {
            $content = '<p><em>Detail agenda belum tersedia.</em></p>';
        }

        try {
            $stmt = $pdo->prepare("
                UPDATE events SET
                    title = ?, slug = ?, description = ?, content = ?, event_date = ?,
                    start_time = ?, end_time = ?, location = ?, location_detail = ?,
                    image = ?, category = ?, registration_link = ?, status = ?, is_featured = ?
                WHERE id = ?
            ");

            $stmt->execute([
                $title, $slug, $description, $content, $event_date,
                $start_time, $end_time, $location, $location_detail,
                $image_name, $category, $registration_link, $status, $is_featured, $id
            ]);

            $_SESSION['success'] = "Agenda berhasil diupdate!";
            header("Location: index.php");
            exit;

        } catch (PDOException $e) {
            $error = "Gagal mengupdate: " . $e->getMessage();
        }
    }
}

$page_title = "Edit Agenda";
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
        .placeholder-image { width: 100%; height: 200px; background: #f0f0f0; border: 2px dashed #ddd; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #999; margin-bottom: 10px; }
        @media (max-width: 768px) { .sidebar { width: 100%; height: auto; position: relative; } .main { margin-left: 0; } }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header"><h2>HIMASTI</h2><p>Panel Admin</p></div>
        <ul class="menu">
            <li><a href="../dashboard.php"> Dashboard</a></li>
            <li class="menu-divider">Konten</li>
            <li><a href="../berita/index.php">📰 Kelola Berita</a></li>
            <li><a href="index.php" class="active">📅 Kelola Agenda</a></li>
            <li><a href="../galeri/index.php">🖼️ Kelola Galeri</a></li>
            <?php if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'super_admin'): ?>
                <li class="menu-divider">Master Data</li>
                <li><a href="../dosen/index.php">‍🏫 Kelola Dosen</a></li>
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

        <div class="content-card">
            <form method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="mb-3">
                            <label class="form-label">Judul Agenda <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" required value="<?= htmlspecialchars($agenda['title']) ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Deskripsi Singkat</label>
                            <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($agenda['description'] ?? '') ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Konten Lengkap</label>
                            <textarea name="content" class="form-control" rows="10"><?= $agenda['content'] ?></textarea>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="mb-3">
                            <label class="form-label">Gambar Saat Ini</label>
                            <?php if ($agenda['image'] != 'default-event.jpg' && file_exists('../../assets/uploads/' . $agenda['image'])): ?>
                                <img src="../../assets/uploads/<?= htmlspecialchars($agenda['image']) ?>" class="img-thumbnail mb-2" style="max-width: 100%;">
                            <?php else: ?>
                                <div class="placeholder-image">
                                    <div class="text-center">
                                        <i class="fas fa-calendar fa-3x mb-2"></i>
                                        <p class="mb-0">Tidak ada gambar</p>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <input type="file" name="image" class="form-control" accept="image/*">
                            <small class="text-muted">Upload gambar baru untuk mengganti (opsional)</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tanggal Acara <span class="text-danger">*</span></label>
                            <input type="date" name="event_date" class="form-control" required value="<?= $agenda['event_date'] ?>">
                        </div>

                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="form-label">Jam Mulai</label>
                                <input type="time" name="start_time" class="form-control" value="<?= $agenda['start_time'] ?? '' ?>">
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label">Jam Selesai</label>
                                <input type="time" name="end_time" class="form-control" value="<?= $agenda['end_time'] ?? '' ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Lokasi</label>
                            <input type="text" name="location" class="form-control" value="<?= htmlspecialchars($agenda['location'] ?? '') ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Detail Lokasi</label>
                            <textarea name="location_detail" class="form-control" rows="2"><?= htmlspecialchars($agenda['location_detail'] ?? '') ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Kategori</label>
                            <input type="text" name="category" class="form-control" value="<?= htmlspecialchars($agenda['category'] ?? '') ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="upcoming" <?= $agenda['status'] == 'upcoming' ? 'selected' : '' ?>>Akan Datang</option>
                                <option value="ongoing" <?= $agenda['status'] == 'ongoing' ? 'selected' : '' ?>>Sedang Berlangsung</option>
                                <option value="done" <?= $agenda['status'] == 'done' ? 'selected' : '' ?>>Selesai</option>
                                <option value="cancelled" <?= $agenda['status'] == 'cancelled' ? 'selected' : '' ?>>Dibatalkan</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Link Pendaftaran</label>
                            <input type="url" name="registration_link" class="form-control" value="<?= htmlspecialchars($agenda['registration_link'] ?? '') ?>">
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_featured" value="1" <?= $agenda['is_featured'] ? 'checked' : '' ?>>
                                <label class="form-check-label">Jadikan Agenda Unggulan</label>
                            </div>
                        </div>

                        <hr>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-save me-2"></i>Update Agenda
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>