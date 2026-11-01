<?php
// admin/pages/dosen.php - Konten Kelola Dosen (TANPA SIDEBAR)
require_once __DIR__ . '/../cek-login.php';
require_once __DIR__ . '/../../config/database.php';

try {
    $stmt = $pdo->query("SELECT * FROM lecturers ORDER BY sort_order ASC, name ASC");
    $dosen_list = $stmt->fetchAll();
} catch (PDOException $e) {
    $dosen_list = [];
}
?>

<style>
    .page-header { background: white; padding: 20px 25px; border-radius: 8px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 5px rgba(0,0,0,0.05); margin-bottom: 30px; }
    .page-header h1 { color: #1a2a3a; font-size: 24px; margin: 0; }
    .btn-primary { background: #1a2a3a; border-color: #1a2a3a; color: white; padding: 8px 16px; border-radius: 6px; text-decoration: none; font-size: 14px; font-weight: 500; border: none; cursor: pointer; }
    .btn-primary:hover { background: #2c3e50; color: white; }
    .btn-warning { background: #f39c12; border: none; color: white; padding: 5px 10px; border-radius: 4px; cursor: pointer; }
    .btn-warning:hover { background: #e67e22; color: white; }
    .btn-danger { background: #c0392b; border: none; color: white; padding: 5px 10px; border-radius: 4px; cursor: pointer; }
    .btn-danger:hover { background: #a93226; color: white; }
    .card { background: white; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); border: none; }
    .card-body { padding: 25px; }
    .table { width: 100%; border-collapse: collapse; }
    .table thead th { background: #f8f9fa; color: #1a2a3a; font-weight: 600; font-size: 14px; padding: 12px; border-bottom: 2px solid #dee2e6; text-align: left; }
    .table tbody td { padding: 12px; vertical-align: middle; font-size: 14px; color: #555; border-bottom: 1px solid #f0f0f0; }
    .table tbody tr:hover { background: #f8f9fa; }
    .photo-circle { width: 50px; height: 50px; border-radius: 50%; object-fit: cover; border: 2px solid #f0f0f0; }
    .photo-placeholder { width: 50px; height: 50px; border-radius: 50%; background: #e9ecef; display: flex; align-items: center; justify-content: center; color: #6c757d; font-size: 24px; }
    .empty-state { text-align: center; padding: 60px 20px; color: #95a5a6; }
    .empty-state .icon { font-size: 64px; margin-bottom: 15px; opacity: 0.3; }
    .alert { padding: 15px 20px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; }
    .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    .alert-danger { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
</style>

<div class="page-header">
    <h1>👨‍🏫 Kelola Dosen</h1>
    <button class="btn-primary" onclick="navigateTo('dosen-tambah')">
        <i class="bi bi-plus-lg"></i> Tambah Dosen
    </button>
</div>

<?php if (isset($_GET['msg'])): ?>
    <div class="alert alert-success"><i class="bi bi-check-circle"></i> <?= htmlspecialchars($_GET['msg']) ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <?php if (empty($dosen_list)): ?>
            <div class="empty-state">
                <div class="icon">👨‍🏫</div>
                <p>Belum ada data dosen</p>
                <button class="btn-primary" onclick="loadPage('dosen-tambah')">
    <i class="bi bi-plus-lg"></i> Tambah Dosen
</button>
                </button>
            </div>
        <?php else: ?>
            <div style="overflow-x: auto;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Foto</th>
                            <th>Nama</th>
                            <th>Jabatan</th>
                            <th>Email</th>
                            <th>Keahlian</th>
                            <th style="text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($dosen_list as $index => $dosen): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td>
                                    <?php if (!empty($dosen['photo'])): ?>
                                        <img src="../assets/uploads/<?= htmlspecialchars($dosen['photo']) ?>" class="photo-circle" alt="Foto">
                                    <?php else: ?>
                                        <div class="photo-placeholder"><i class="bi bi-person"></i></div>
                                    <?php endif; ?>
                                </td>
                                <td><strong style="color: #1a2a3a;"><?= htmlspecialchars($dosen['name']) ?></strong></td>
                                <td><?= htmlspecialchars($dosen['position'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($dosen['email'] ?? '-') ?></td>
                                <td><small><?= htmlspecialchars($dosen['specialization'] ?? '-') ?></small></td>
                                <td style="text-align: center;">
                                    <button class="btn-warning" onclick="navigateTo('dosen-edit&id=<?= $dosen['id'] ?>')" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn-danger" onclick="hapusDosen(<?= $dosen['id'] ?>, '<?= htmlspecialchars($dosen['name']) ?>')" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #f0f0f0;">
                <small style="color: #95a5a6;">Total: <strong><?= count($dosen_list) ?></strong> dosen</small>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function navigateTo(page) {
    const link = document.querySelector(`.menu a[data-page="${page}"]`);
    if (link) link.click();
}

function hapusDosen(id, nama) {
    if (confirm(`Yakin hapus dosen "${nama}"?`)) {
        fetch('api/hapus_dosen.php?id=' + id)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Dosen berhasil dihapus');
                    navigateTo('dosen');
                } else {
                    alert('Gagal menghapus: ' + data.message);
                }
            });
    }
}
</script>