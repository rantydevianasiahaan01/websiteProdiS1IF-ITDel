<?php
// admin/user/edit.php
require_once __DIR__ . '/../cek-login.php';
require_once __DIR__ . '/../../config/database.php';

$id = $_GET['id'] ?? 0;
$pesan_error = "";

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header("Location: index.php?err=" . urlencode("Pengguna tidak ditemukan."));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $role     = $_POST['role'] ?? 'editor';
    
    if (empty($name) || empty($username)) {
        $pesan_error = "Nama dan Username wajib diisi.";
    } else {
        try {
            // Jika kolom password diisi, berarti admin ingin reset password akun tersebut
            if (!empty($password)) {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt_update = $pdo->prepare("UPDATE users SET name=?, username=?, role=?, password=? WHERE id=?");
                $stmt_update->execute([$name, $username, $role, $hashed_password, $id]);
            } else {
                // Jika password dikosongkan, update data lainnya saja
                $stmt_update = $pdo->prepare("UPDATE users SET name=?, username=?, role=? WHERE id=?");
                $stmt_update->execute([$name, $username, $role, $id]);
            }
            
            header("Location: index.php?msg=" . urlencode("Profil pengguna {$name} berhasil diperbarui."));
            exit;
        } catch (PDOException $e) {
             $pesan_error = "Error Database (Username mungkin sudah dipakai): " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Pengguna - Admin HIMASTI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { font-family: 'Inter', 'Segoe UI', Tahoma, sans-serif; }
        body { background: #f8fafc; display: flex; min-height: 100vh; margin: 0; color: #334155; }
        .sidebar { width: 250px; background: #0f172a; color: white; position: fixed; height: 100vh; z-index: 1000; }
        .sidebar-header { padding: 25px 20px; border-bottom: 1px solid rgba(255,255,255,0.05); background: #0b1120; }
        .sidebar-header h2 { color: #f8fafc; font-size: 22px; font-weight: 700; margin-bottom: 5px;}
        .sidebar-header p { color: #94a3b8; font-size: 12px; margin:0;}
        .menu { list-style: none; padding: 20px 0; margin:0;}
        .menu li a { display: flex; align-items: center; gap: 12px; color: #cbd5e1; text-decoration: none; padding: 12px 20px; transition: 0.3s; font-size: 14px; border-left: 3px solid transparent;}
        .menu li a:hover, .menu li a.active { background: #1e293b; color: #fff; border-left-color: #38bdf8; }
        
        .main { margin-left: 250px; padding: 35px 40px; width: calc(100% - 250px); }
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; max-width: 800px; }
        .page-header h1 { font-size: 24px; font-weight: 700; color: #0f172a; margin: 0; }
        
        .card-custom { background: #ffffff; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; max-width: 800px; padding: 35px;}
        .form-label { font-weight: 600; color: #475569; font-size: 14px; margin-bottom: 8px; }
        .form-control, .form-select { border-radius: 8px; border: 1px solid #cbd5e1; padding: 12px 15px; font-size: 14px; }
        .form-control:focus, .form-select:focus { border-color: #38bdf8; box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.1); outline:none;}
        
        .btn-kembali { background: #e2e8f0; color: #475569; border: none; border-radius: 8px; font-weight: 600; padding: 10px 20px; text-decoration: none;}
        .btn-simpan { background: #0f172a; color: white; border: none; border-radius: 8px; font-weight: 600; padding: 12px 25px; transition: 0.3s; }
        .btn-simpan:hover { background: #1e293b; transform: translateY(-2px); }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header"><h2>HIMASTI</h2><p>Admin Panel &bull; IT Del</p></div>
        <ul class="menu">
            <li><a href="../dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="index.php" class="active"><i class="fas fa-users-cog"></i> Pengguna Sistem</a></li>
        </ul>
    </div>
    
    <div class="main">
        <div class="page-header">
            <h1><i class="fas fa-pen text-primary me-2"></i> Edit Pengguna</h1>
            <a href="index.php" class="btn-kembali shadow-sm"><i class="fas fa-arrow-left me-2"></i>Batal</a>
        </div>
        
        <?php if ($pesan_error): ?>
            <div class="alert alert-danger" style="max-width: 800px; border-radius:10px;"><i class="fas fa-exclamation-triangle me-2"></i> <?= $pesan_error ?></div>
        <?php endif; ?>

        <div class="card-custom">
            <form method="POST" action="">
                <div class="row g-4">
                    <div class="col-md-12">
                        <label class="form-label">Nama Asli Pemilik Akun <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required value="<?= htmlspecialchars($user['name']) ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Username (Untuk Login) <span class="text-danger">*</span></label>
                        <input type="text" name="username" class="form-control" required value="<?= htmlspecialchars($user['username']) ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tingkat Hak Akses <span class="text-danger">*</span></label>
                        <select name="role" class="form-select" required <?= ($user['username'] === 'admin') ? 'disabled' : '' ?>>
                            <option value="editor" <?= $user['role'] === 'editor' ? 'selected' : '' ?>>Editor (Hanya kelola konten)</option>
                            <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Administrator (Akses Penuh)</option>
                        </select>
                        <?php if($user['username'] === 'admin'): ?>
                            <!-- Pastikan data role 'admin' tetap terkirim meski select-nya di-disable -->
                            <input type="hidden" name="role" value="admin">
                        <?php endif; ?>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Kata Sandi Baru (Opsional)</label>
                        <input type="password" name="password" class="form-control" placeholder="Biarkan kosong jika tidak ingin mengganti password">
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