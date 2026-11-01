<?php
// admin/pesan/baca.php
require_once __DIR__ . '/../cek-login.php';
require_once __DIR__ . '/../../config/database.php';

$id = $_GET['id'] ?? 0;
$action = $_GET['action'] ?? '';

// Jika tombol "Tandai Sudah Dibalas" diklik
if ($action === 'replied') {
    $pdo->prepare("UPDATE contacts SET status = 'replied' WHERE id = ?")->execute([$id]);
    header("Location: baca.php?id=$id&msg=Pesan+ditandai+telah+dibalas");
    exit;
}

// Ambil detail pesan
$stmt = $pdo->prepare("SELECT * FROM contacts WHERE id = ?");
$stmt->execute([$id]);
$pesan = $stmt->fetch();

if (!$pesan) {
    header("Location: index.php?msg=Pesan+tidak+ditemukan");
    exit;
}

// Ubah status jadi 'read' jika masih 'unread'
if ($pesan['status'] === 'unread') {
    $pdo->prepare("UPDATE contacts SET status = 'read' WHERE id = ?")->execute([$id]);
    $pesan['status'] = 'read'; // Update variabel lokal juga
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Baca Pesan - Admin HIMASTI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body style="background: #f4f6f8; padding: 30px;">
    
    <div class="container max-w-3xl mx-auto" style="max-width: 800px;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4><i class="fas fa-envelope-open-text text-primary me-2"></i> Detail Pesan</h4>
            <a href="index.php" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left me-1"></i> Kembali</a>
        </div>

        <?php if (isset($_GET['msg'])): ?>
            <div class="alert alert-success"><i class="fas fa-check-circle me-1"></i> <?= htmlspecialchars($_GET['msg']) ?></div>
        <?php endif; ?>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between border-bottom pb-3 mb-3">
                    <div>
                        <h5 class="fw-bold mb-1"><?= htmlspecialchars($pesan['name']) ?></h5>
                        <div class="text-muted small">
                            <i class="fas fa-envelope me-1"></i> <?= htmlspecialchars($pesan['email']) ?> 
                            <?php if(!empty($pesan['phone'])): ?>
                            | <i class="fas fa-phone me-1"></i> <?= htmlspecialchars($pesan['phone']) ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="text-end text-muted small">
                        <?= date('d F Y, H:i', strtotime($pesan['created_at'])) ?>
                        <div class="mt-1">
                            <?php if($pesan['status'] == 'read'): ?>
                                <span class="badge bg-info text-dark">Sudah Dibaca</span>
                            <?php elseif($pesan['status'] == 'replied'): ?>
                                <span class="badge bg-success">Sudah Dibalas</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <h6 class="fw-bold mb-3">Subjek: <?= htmlspecialchars($pesan['subject'] ?: '(Tanpa Subjek)') ?></h6>
                <div class="p-3 bg-light rounded" style="white-space: pre-wrap; font-size: 15px; line-height: 1.6;"><?= htmlspecialchars($pesan['message']) ?></div>
            </div>
            
            <div class="card-footer bg-white p-4 border-top text-end">
                <p class="text-muted small text-start mb-3">Balas pesan ini menggunakan Email atau WhatsApp (Jika nomor tersedia):</p>
                
                <a href="mailto:<?= htmlspecialchars($pesan['email']) ?>?subject=Balasan: <?= urlencode($pesan['subject']) ?>" class="btn btn-primary" target="_blank">
                    <i class="fas fa-reply me-1"></i> Balas via Email
                </a>
                
                <?php if(!empty($pesan['phone'])): ?>
                    <?php 
                    // Format nomor WA (ganti 0 di depan jadi 62)
                    $wa = preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $pesan['phone'])); 
                    ?>
                    <a href="https://wa.me/<?= $wa ?>" class="btn btn-success" target="_blank">
                        <i class="fab fa-whatsapp me-1"></i> Balas via WA
                    </a>
                <?php endif; ?>

                <?php if($pesan['status'] !== 'replied'): ?>
                    <a href="baca.php?id=<?= $pesan['id'] ?>&action=replied" class="btn btn-outline-success ms-2" onclick="return confirm('Tandai pesan ini sudah selesai dibalas?')">
                        <i class="fas fa-check me-1"></i> Tandai Sudah Dibalas
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

</body>
</html>