<?php
// admin/pages/dosen-tambah.php
require_once __DIR__ . '/../cek-login.php';
require_once __DIR__ . '/../../config/database.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $position = trim($_POST['position']);
    $education = trim($_POST['education']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $specialization = trim($_POST['specialization']);
    $description = trim($_POST['description']);
    $sort_order = $_POST['sort_order'] ?? 0;
    
    $photo_name = '';
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === 0) {
        $allowed = ['jpg', 'jpeg', 'png'];
        $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            $photo_name = 'dosen_' . time() . '.' . $ext;
            $upload_path = __DIR__ . '/../../assets/uploads/' . $photo_name;
            $uploads_dir = __DIR__ . '/../../assets/uploads';
            
            if (!file_exists($uploads_dir)) {
                mkdir($uploads_dir, 0777, true);
            }
            
            if (!move_uploaded_file($_FILES['photo']['tmp_name'], $upload_path)) {
                $error = "Gagal upload foto";
            }
        } else {
            $error = "Format foto tidak didukung";
        }
    }
    
    if (empty($error)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO lecturers (name, position, education, email, phone, photo, specialization, description, sort_order, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1)");
            $stmt->execute([$name, $position, $education, $email, $phone, $photo_name, $specialization, $description, $sort_order]);
            $success = "Dosen berhasil ditambahkan!";
        } catch (PDOException $e) {
            $error = "Gagal menyimpan: " . $e->getMessage();
        }
    }
}
?>

<style>
    .page-header { background: white; padding: 20px 25px; border-radius: 8px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 5px rgba(0,0,0,0.05); margin-bottom: 30px; }
    .page-header h1 { color: #1a2a3a; font-size: 24px; margin: 0; }
    .btn-primary { background: #1a2a3a; color: white; padding: 8px 16px; border-radius: 6px; border: none; cursor: pointer; font-size: 14px; font-weight: 500; }
    .btn-primary:hover { background: #2c3e50; color: white; }
    .btn-secondary { background: #6c757d; color: white; padding: 8px 16px; border-radius: 6px; border: none; cursor: pointer; font-size: 14px; text-decoration: none; }
    .btn-secondary:hover { background: #5a6268; color: white; }
    .card { background: white; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); border: none; }
    .card-body { padding: 25px; }
    .card-footer { padding: 15px 25px; background: #f8f9fa; border-top: 1px solid #e9ecef; }
    .form-label { color: #1a2a3a; font-weight: 500; font-size: 14px; margin-bottom: 8px; display: block; }
    .form-control { border: 1px solid #dee2e6; border-radius: 6px; padding: 10px 12px; font-size: 14px; width: 100%; }
    .form-control:focus { border-color: #4a90a4; outline: none; box-shadow: 0 0 0 0.2rem rgba(74, 144, 164, 0.25); }
    .alert { padding: 15px 20px; border-radius: 8px; margin-bottom: 20px; }
    .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    .alert-danger { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    .row { display: flex; gap: 15px; }
    .col-md-6 { flex: 1; }
    .mb-3 { margin-bottom: 15px; }
    @media (max-width: 768px) { .row { flex-direction: column; } }
</style>

<div class="page-header">
    <h1>➕ Tambah Dosen</h1>
    <button class="btn-secondary" onclick="loadPage('dosen')">← Kembali</button>
</div>

<?php if ($success): ?>
    <div class="alert alert-success">✅ <?= htmlspecialchars($success) ?></div>
    <script>setTimeout(() => navigateTo('dosen'), 1500);</script>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-danger">❌ <?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data" class="card">
    <div class="card-body">
        <div class="mb-3">
            <label class="form-label">Nama Lengkap <span style="color: red;">*</span></label>
            <input type="text" name="name" class="form-control" required placeholder="Contoh: Dr. John Doe, M.Kom">
        </div>
        
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Jabatan</label>
                <input type="text" name="position" class="form-control" placeholder="Contoh: Ketua Program Studi">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Pendidikan Terakhir</label>
                <input type="text" name="education" class="form-control" placeholder="Contoh: S3 Informatika">
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" placeholder="contoh@del.ac.id">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">No. Telepon</label>
                <input type="text" name="phone" class="form-control" placeholder="081234567890">
            </div>
        </div>
        
        <div class="mb-3">
            <label class="form-label">Bidang Keahlian</label>
            <input type="text" name="specialization" class="form-control" placeholder="Contoh: AI, Data Science">
        </div>
        
        <div class="mb-3">
            <label class="form-label">Deskripsi Singkat</label>
            <textarea name="description" class="form-control" rows="3"></textarea>
        </div>
        
        <div class="mb-3">
            <label class="form-label">Urutan</label>
            <input type="number" name="sort_order" class="form-control" value="0">
            <small style="color: #95a5a6;">Urutan tampilan (0 = paling atas)</small>
        </div>
        
        <div class="mb-3">
            <label class="form-label">Foto Dosen</label>
            <input type="file" name="photo" class="form-control" accept="image/*">
            <small style="color: #95a5a6;">Format: JPG, PNG (Max 2MB)</small>
        </div>
    </div>
    <div class="card-footer">
        <button type="submit" class="btn-primary"><i class="bi bi-save"></i> Simpan</button>
        <button type="button" class="btn-secondary" onclick="navigateTo('dosen')">Batal</button>
    </div>
</form>

<script>
function navigateTo(page) {
    const link = document.querySelector(`.menu a[data-page="${page}"]`);
    if (link) link.click();
}
</script>