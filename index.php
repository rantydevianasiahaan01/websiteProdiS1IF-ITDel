<?php
// index.php - Homepage Website Prodi Informatika IT Del

// Load konfigurasi
require_once 'config/database.php';
require_once 'config/auth.php';

// Set judul halaman
$page_title = 'Beranda';

// 1. Ambil Data Profil Organisasi (Logo, Deskripsi, Nama)
try {
    $profil = $pdo->query("SELECT * FROM profile_settings LIMIT 1")->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) { $profil = []; }

// 2. Ambil statistik
try {
    $stats = [
        'mahasiswa' => 250, 
        'dosen' => $pdo->query("SELECT COUNT(*) FROM lecturers WHERE is_active=1")->fetchColumn() ?: 15,
        'alumni' => $pdo->query("SELECT COUNT(*) FROM alumni")->fetchColumn() ?: 100,
        'prestasi' => 45
    ];
} catch (PDOException $e) {
    $stats = ['mahasiswa' => 250, 'dosen' => 15, 'alumni' => 100, 'prestasi' => 45];
}

// 3. Ambil Program Unggulan
try {
    $programs = $pdo->query("SELECT * FROM programs ORDER BY id ASC")->fetchAll();
} catch (PDOException $e) { $programs = []; }

// Include header dan navbar (Pastikan file ini benar-benar ada di foldermu)
@include 'includes/header.php';
@include 'includes/navbar.php';
?>

<!-- Hero Section dengan Slider -->
<section class="hero-section position-relative" style="min-height: 600px;">
    <!-- Bootstrap Carousel -->
    <div id="heroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="5000">
        <div class="carousel-inner" style="height: 600px;">
            
            <!-- Slide 1: Dinamis dari Profil -->
            <div class="carousel-item active">
                <div class="position-absolute w-100 h-100" style="background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.7)), url('assets/uploads/<?= htmlspecialchars($profil['banner'] ?? 'lab-komputer.jpg') ?>'); background-size: cover; background-position: center;"></div>
                <div class="container position-relative h-100">
                    <div class="row h-100 align-items-center">
                        <div class="col-lg-8 text-white">
                            <h1 class="display-3 fw-bold mb-4"><?= htmlspecialchars($profil['org_name'] ?? 'Program Studi Informatika') ?><br><?= htmlspecialchars($profil['short_name'] ?? 'Institut Teknologi Del') ?></h1>
                            <p class="lead mb-4"><?= htmlspecialchars($profil['tagline'] ?? 'Membentuk lulusan informatika yang unggul, profesional, inovatif, dan mampu berkontribusi melalui teknologi untuk kemajuan bangsa.') ?></p>
                            <div class="d-flex gap-3">
                                <a href="profil.php" class="btn btn-primary btn-lg">
                                    <i class="fas fa-info-circle me-2"></i>Lihat Profil
                                </a>
                                <a href="#keunggulan" class="btn btn-outline-light btn-lg">
                                    <i class="fas fa-star me-2"></i>Program Unggulan
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Slide 2: Kegiatan Belajar (Statis) -->
            <div class="carousel-item">
                <div class="position-absolute w-100 h-100" style="background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.7)), url('assets/uploads/kegiatan-belajar.jpg'); background-size: cover; background-position: center;"></div>
                <div class="container position-relative h-100">
                    <div class="row h-100 align-items-center">
                        <div class="col-lg-8 text-white">
                            <h1 class="display-3 fw-bold mb-4">Pembelajaran Berbasis Teknologi</h1>
                            <p class="lead mb-4">Kurikulum modern yang disesuaikan dengan kebutuhan industri teknologi informasi terkini.</p>
                            <div class="d-flex gap-3">
                                <a href="dosen.php" class="btn btn-primary btn-lg">
                                    <i class="fas fa-chalkboard-teacher me-2"></i>Pakar Akademik
                                </a>
                                <a href="https://spmbdel.del.ac.id/login" target="_blank" class="btn btn-success btn-lg">
                                    <i class="fas fa-user-plus me-2"></i>Daftar Sekarang
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        
        <!-- Carousel Controls -->
        <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>
</section>

<!-- Stats Section -->
<section class="stats-section py-5 bg-white">
    <div class="container">
        <div class="row g-4 text-center">
            <div class="col-md-3 col-6">
                <div class="stat-card p-3 shadow-sm rounded-3 border">
                    <div class="icon fs-1 text-primary mb-2"><i class="fas fa-users"></i></div>
                    <h2 class="number fw-bold"><?= $stats['mahasiswa'] ?>+</h2>
                    <div class="label text-muted">Mahasiswa Aktif</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-card p-3 shadow-sm rounded-3 border">
                    <div class="icon fs-1 text-success mb-2"><i class="fas fa-chalkboard-teacher"></i></div>
                    <h2 class="number fw-bold"><?= $stats['dosen'] ?></h2>
                    <div class="label text-muted">Dosen Pakar</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-card p-3 shadow-sm rounded-3 border">
                    <div class="icon fs-1 text-warning mb-2"><i class="fas fa-user-graduate"></i></div>
                    <h2 class="number fw-bold"><?= $stats['alumni'] ?>+</h2>
                    <div class="label text-muted">Jejaring Alumni</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-card p-3 shadow-sm rounded-3 border">
                    <div class="icon fs-1 text-danger mb-2"><i class="fas fa-trophy"></i></div>
                    <h2 class="number fw-bold"><?= $stats['prestasi'] ?>+</h2>
                    <div class="label text-muted">Prestasi Diraih</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Tentang Prodi Section (Dinamis - Tanpa Visi Misi) -->
<section id="tentang" class="section bg-light py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-5 mb-4 mb-lg-0 text-center">
                <img src="assets/uploads/<?= htmlspecialchars($profil['logo'] ?? 'logo-himasti.jpeg') ?>" 
                     alt="Logo HIMASTI" 
                     class="img-fluid rounded shadow-sm" 
                     style="max-width: 300px; background: white; padding: 20px;"
                     onerror="this.src='https://ui-avatars.com/api/?name=HIMASTI&background=0f172a&color=fff&size=300'">
            </div>
            <div class="col-lg-7">
                <h2 class="section-title text-start fw-bold">Tentang <?= htmlspecialchars($profil['short_name'] ?? 'Prodi Informatika') ?></h2>
                <p class="lead mt-3 text-muted" style="line-height: 1.8;">
                    <?= htmlspecialchars($profil['description'] ?? 'Program Studi Informatika IT Del berkomitmen untuk menghasilkan lulusan yang menguasai teknologi dan memiliki karakter kuat.') ?>
                </p>
                <div class="mt-4">
                    <a href="profil.php" class="btn btn-primary px-4 py-2 rounded-pill shadow-sm" style="font-weight: 600;">
                        Mengenal Lebih Dekat <i class="fas fa-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Keunggulan / Program Unggulan Section (Dinamis dari Tabel Programs) -->
<section id="keunggulan" class="section py-5">
    <div class="container">
        <div class="section-header text-center mb-5">
            <h2 class="section-title fw-bold">Program Unggulan</h2>
            <p class="section-subtitle text-muted">Aktivitas dan kurikulum andalan Program Studi Informatika</p>
        </div>
        <div class="row g-4">
            <?php if(empty($programs)): ?>
                <div class="col-12 text-center text-muted">Belum ada data program unggulan.</div>
            <?php else: ?>
                <?php foreach($programs as $prog): ?>
                <div class="col-md-4">
                    <div class="card-custom p-4 text-center h-100 shadow-sm border rounded-3">
                        <div class="icon mb-3" style="font-size: 50px; color: #0284c7;">
                            <i class="<?= htmlspecialchars($prog['icon']) ?>"></i>
                        </div>
                        <h4 class="mb-3 fw-bold" style="font-size: 18px;"><?= htmlspecialchars($prog['title']) ?></h4>
                        <p class="text-muted" style="font-size: 14px; line-height: 1.6;"><?= htmlspecialchars($prog['description']) ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="section py-5" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: white;">
    <div class="container text-center py-4">
        <h2 class="mb-4 fw-bold">Siap Menjadi Bagian dari Kami?</h2>
        <p class="lead mb-4" style="opacity: 0.9;">Bergabunglah dengan <?= htmlspecialchars($profil['short_name'] ?? 'Informatika IT Del') ?> dan raih masa depan gemilang di bidang teknologi.</p>
        <div class="mt-4">
            <a href="https://spmbdel.del.ac.id" target="_blank" class="btn btn-success btn-lg me-3 fw-bold rounded-pill px-4 shadow">
                <i class="fas fa-user-plus me-2"></i>Daftar Sekarang
            </a>
            <a href="kontak.php" class="btn btn-outline-light btn-lg fw-bold rounded-pill px-4">
                <i class="fas fa-envelope me-2"></i>Hubungi Kami
            </a>
        </div>
    </div>
</section>

<!-- Custom CSS untuk hover effect -->
<style>
.card-custom:hover { transform: translateY(-5px); box-shadow: 0 10px 25px rgba(0,0,0,0.08) !important; border-color: #cbd5e1 !important;}
.card-custom { transition: 0.3s; }
.carousel-item { height: 600px; }
.stat-card { transition: 0.3s; background: white;}
.stat-card:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(0,0,0,0.05) !important; border-color: #cbd5e1 !important;}
</style>

<?php @include 'includes/footer.php'; ?>