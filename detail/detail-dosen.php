<?php
// PERBAIKAN: Tambahkan ../ untuk mundur satu folder ke root
require_once '../config/database.php';
require_once '../includes/functions.php';

// Ambil ID dosen dari URL
$id = $_GET['id'] ?? 0;

// Ambil data dosen dari database
try {
    $stmt = $pdo->prepare("
        SELECT * FROM lecturers 
        WHERE id = ? AND is_active = 1
    ");
    $stmt->execute([$id]);
    $dosen = $stmt->fetch();
    
    if (!$dosen) {
        header("Location: ../dosen.php");
        exit;
    }
} catch (PDOException $e) {
    header("Location: ../dosen.php");
    exit;
}

$page_title = htmlspecialchars($dosen['name']) . " - HIMASTI IT Del";

// PERBAIKAN: Tambahkan ../ pada semua include
include '../includes/header.php';
include '../includes/navbar.php';
?>

<!-- Hero Section -->
<section class="hero-section hero-sm" style="background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary) 100%);">
    <div class="container">
        <div class="row align-items-center py-5">
            <div class="col-lg-8 mx-auto text-center text-white">
                <h1 class="display-5 fw-bold mb-3">Profil Dosen</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb justify-content-center mb-0">
                        <li class="breadcrumb-item"><a href="../index.php" class="text-white-50 text-decoration-none">Beranda</a></li>
                        <li class="breadcrumb-item"><a href="../dosen.php" class="text-white-50 text-decoration-none">Dosen</a></li>
                        <li class="breadcrumb-item active text-white" aria-current="page">Detail</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</section>

<!-- Profil Dosen -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <!-- Foto & Info Dasar -->
            <div class="col-lg-4 mb-4">
                <div class="card border-0 shadow-sm text-center">
                    <div class="card-body p-4">
                        <div class="rounded-circle mx-auto mb-4 d-flex align-items-center justify-content-center" 
                             style="width: 200px; height: 200px; background: #f4f6f8; overflow: hidden;">
                            <?php if (!empty($dosen['photo'])): ?>
                                <!-- PERBAIKAN: Tambahkan ../ pada path gambar -->
                                <img src="../assets/uploads/<?= htmlspecialchars($dosen['photo']) ?>"
                                     alt="<?= htmlspecialchars($dosen['name']) ?>"
                                     style="width: 100%; height: 100%; object-fit: cover;">
                            <?php else: ?>
                                <i class="bi bi-person-fill" style="font-size: 6rem; color: var(--primary-dark);"></i>
                            <?php endif; ?>
                        </div>

                        <h3 class="mb-2" style="color: var(--primary-dark);">
                            <?php if (!empty($dosen['title_front'])): ?>
                            <small><?= htmlspecialchars($dosen['title_front']) ?></small><br>
                            <?php endif; ?>
                            <?= htmlspecialchars($dosen['name']) ?>
                            <?php if (!empty($dosen['title_behind'])): ?>
                            <small>, <?= htmlspecialchars($dosen['title_behind']) ?></small>
                            <?php endif; ?>
                        </h3>

                        <?php if (!empty($dosen['nidn'])): ?>
                        <p class="text-muted mb-3">NIDN: <?= htmlspecialchars($dosen['nidn']) ?></p>
                        <?php endif; ?>

                        <?php if (!empty($dosen['position'])): ?>
                        <p class="mb-3" style="color: var(--accent); font-size: 1.1rem;">
                            <?= htmlspecialchars($dosen['position']) ?>
                        </p>
                        <?php endif; ?>

                        <div class="d-flex justify-content-center gap-2 mb-4 flex-wrap">
                            <?php if (!empty($dosen['email'])): ?>
                            <a href="mailto:<?= htmlspecialchars($dosen['email']) ?>" class="btn btn-primary btn-sm" title="Email">
                                <i class="bi bi-envelope"></i> Email
                            </a>
                            <?php endif; ?>

                            <?php if (!empty($dosen['google_scholar'])): ?>
                            <a href="<?= htmlspecialchars($dosen['google_scholar']) ?>" target="_blank" class="btn btn-outline-secondary btn-sm" title="Google Scholar">
                                <i class="bi bi-mortarboard"></i> Scholar
                            </a>
                            <?php endif; ?>

                            <?php if (!empty($dosen['orcid'])): ?>
                            <a href="<?= htmlspecialchars($dosen['orcid']) ?>" target="_blank" class="btn btn-outline-info btn-sm" title="ORCID">
                                <i class="bi bi-link-45deg"></i> ORCID
                            </a>
                            <?php endif; ?>

                            <?php if (!empty($dosen['website'])): ?>
                            <a href="<?= htmlspecialchars($dosen['website']) ?>" target="_blank" class="btn btn-outline-success btn-sm" title="Website">
                                <i class="bi bi-globe"></i> Website
                            </a>
                            <?php endif; ?>
                        </div>

                        <?php if (!empty($dosen['expertise'])): ?>
                        <div class="mb-3 text-start">
                            <h6 class="text-muted mb-2">Keahlian:</h6>
                            <?php
                            $expertise_list = explode(',', $dosen['expertise']);
                            foreach ($expertise_list as $exp):
                            ?>
                            <span class="badge bg-primary bg-opacity-10 text-primary me-1 mb-1"><?= htmlspecialchars(trim($exp)) ?></span>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Detail Informasi -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Informasi Pribadi</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong class="text-muted">Nama Lengkap:</strong>
                                <p><?= htmlspecialchars($dosen['name']) ?></p>
                            </div>
                            <?php if (!empty($dosen['education'])): ?>
                            <div class="col-md-6">
                                <strong class="text-muted">Pendidikan Terakhir:</strong>
                                <p><?= htmlspecialchars($dosen['education']) ?></p>
                            </div>
                            <?php endif; ?>
                        </div>
                        <div class="row">
                            <?php if (!empty($dosen['email'])): ?>
                            <div class="col-md-6">
                                <strong class="text-muted">Email:</strong>
                                <p><a href="mailto:<?= htmlspecialchars($dosen['email']) ?>"><?= htmlspecialchars($dosen['email']) ?></a></p>
                            </div>
                            <?php endif; ?>
                            <?php if (!empty($dosen['phone'])): ?>
                            <div class="col-md-6">
                                <strong class="text-muted">Telepon:</strong>
                                <p><?= htmlspecialchars($dosen['phone']) ?></p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <?php if (!empty($dosen['research_interest']) || !empty($dosen['expertise'])): ?>
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">🎓 Bidang Keahlian</h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($dosen['research_interest'])): ?>
                        <div class="mb-3">
                            <strong class="text-muted">Spesialisasi:</strong>
                            <p><?= htmlspecialchars($dosen['research_interest']) ?></p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>

                <?php if (!empty($dosen['profile'])): ?>
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">👤 Tentang Saya</h5>
                    </div>
                    <div class="card-body">
                        <p style="line-height: 1.8;"><?= nl2br(htmlspecialchars($dosen['profile'])) ?></p>
                    </div>
                </div>
                <?php endif; ?>

                <div class="text-center mt-4">
                    <a href="../dosen.php" class="btn btn-secondary btn-lg">
                        <i class="bi bi-arrow-left"></i> Kembali ke Daftar Dosen
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- PERBAIKAN: Tambahkan ../ pada footer -->
<?php include '../includes/footer.php'; ?>