<?php
// alumni.php - Halaman Publik Direktori Alumni HIMASTI
require_once __DIR__ . '/config/database.php';

$page_title = 'Jejaring Alumni';

// =========================================================
// 1. LOGIKA PENCARIAN & FILTER ANGKATAN
// =========================================================
$search_query = $_GET['cari'] ?? '';
$angkatan_filter = $_GET['angkatan'] ?? '';

$where_clause = "1=1"; // Default true untuk mengambil semua
$params = [];

// Jika user mengetik di kotak pencarian
if (!empty($search_query)) {
    $where_clause .= " AND (name LIKE ? OR current_job LIKE ? OR company LIKE ?)";
    $params[] = "%$search_query%";
    $params[] = "%$search_query%";
    $params[] = "%$search_query%";
}

// Jika user memilih filter angkatan
if (!empty($angkatan_filter) && $angkatan_filter !== 'Semua') {
    $where_clause .= " AND graduation_year = ?";
    $params[] = $angkatan_filter;
}

// =========================================================
// 2. AMBIL DATA DARI DATABASE
// =========================================================
// Ambil Daftar Alumni
try {
    $sql = "SELECT * FROM alumni WHERE $where_clause ORDER BY graduation_year DESC, name ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $alumni_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $alumni_list = [];
}

// Ambil Daftar Angkatan (Tahun Lulus) untuk Pilihan Dropdown
try {
    $angkatan_list = $pdo->query("SELECT DISTINCT graduation_year FROM alumni WHERE graduation_year IS NOT NULL AND graduation_year != '' ORDER BY graduation_year DESC")->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    $angkatan_list = [];
}

// Ambil Data Statistik (Menghitung Total)
try {
    $total_alumni = $pdo->query("SELECT COUNT(*) FROM alumni")->fetchColumn() ?: 0;
    $total_company = $pdo->query("SELECT COUNT(DISTINCT company) FROM alumni WHERE company IS NOT NULL AND company != ''")->fetchColumn() ?: 0;
    $total_angkatan = count($angkatan_list);
} catch (PDOException $e) {
    $total_alumni = 0; $total_company = 0; $total_angkatan = 0;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?> - HIMASTI IT Del</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { font-family: 'Inter', 'Segoe UI', Tahoma, sans-serif; }
        body { background-color: #f8fafc; }
        
        /* Navbar Style Premium (Konsisten) */
        .navbar-custom { background-color: #0f172a; padding: 15px 0; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
        .navbar-brand { color: #f8fafc !important; font-weight: 800; font-size: 24px; letter-spacing: 0.5px; }
        .nav-link { color: #cbd5e1 !important; font-weight: 600; font-size: 14px; margin: 0 8px; transition: 0.3s; padding: 8px 15px !important; border-radius: 8px;}
        .nav-link:hover, .nav-link.active { color: #fff !important; background: rgba(255,255,255,0.1); }
        .btn-portal { background-color: #38bdf8; color: #0f172a; font-weight: 700; border-radius: 8px; padding: 10px 20px; transition: 0.3s; border: none; font-size: 14px;}
        .btn-portal:hover { background-color: #0284c7; color: white; transform: translateY(-2px);}
        
        /* Header Halaman */
        .page-header-custom { background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: white; padding: 80px 0 100px; text-align: center;}
        .page-header-custom h1 { font-weight: 800; font-size: 38px; margin-bottom: 15px; }
        .page-header-custom p { color: #cbd5e1; font-size: 16px; max-width: 600px; margin: 0 auto;}

        /* Statistik Melayang */
        .stats-wrapper { margin-top: -50px; position: relative; z-index: 10; margin-bottom: 30px; }
        .stat-box { background: #fff; border-radius: 12px; padding: 25px 20px; text-align: center; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.08); border: 1px solid #e2e8f0; height: 100%; transition: 0.3s;}
        .stat-box:hover { transform: translateY(-5px); border-color: #38bdf8;}
        .stat-icon { font-size: 28px; color: #38bdf8; margin-bottom: 10px;}
        .stat-number { font-size: 36px; font-weight: 800; color: #0f172a; line-height: 1; margin-bottom: 5px; }
        .stat-label { font-size: 13px; color: #64748b; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;}

        /* Kotak Pencarian Minimalis */
        .filter-box { background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); border: 1px solid #e2e8f0; margin-bottom: 50px; }
        .search-input, .form-select-custom { border-radius: 8px; border: 1px solid #cbd5e1; padding: 12px 20px; font-size: 15px; background: #f8fafc; color: #475569;}
        .search-input:focus, .form-select-custom:focus { border-color: #38bdf8; box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.1); outline: none; background: #fff;}
        .btn-search { background: #0f172a; color: white; border-radius: 8px; padding: 12px 35px; font-weight: 600; transition: 0.3s; border: none; font-size: 15px;}
        .btn-search:hover { background: #1e293b; transform: translateY(-1px);}

        /* Kartu Alumni Premium */
        .alumni-card { background: #fff; border-radius: 16px; padding: 30px 25px; text-align: center; border: 1px solid #e2e8f0; transition: all 0.3s ease; height: 100%; display: flex; flex-direction: column; align-items: center; position: relative;}
        .alumni-card:hover { box-shadow: 0 20px 40px -5px rgba(0,0,0,0.1); transform: translateY(-8px); border-color:#cbd5e1;}
        
        .alumni-avatar { width: 100px; height: 100px; border-radius: 50%; margin-bottom: 15px; object-fit: cover; border: 4px solid #f8fafc; box-shadow: 0 4px 10px rgba(0,0,0,0.08);}
        .alumni-name { font-size: 17px; font-weight: 800; color: #0f172a; margin-bottom: 5px; line-height: 1.4; width: 100%;}
        
        /* Label Angkatan */
        .alumni-batch { position: absolute; top: 15px; right: 15px; background: #f0f9ff; color: #0284c7; padding: 5px 12px; border-radius: 8px; font-size: 12px; font-weight: 700; border: 1px solid #bae6fd;}
        
        .alumni-job { font-size: 14px; font-weight: 600; color: #475569; margin-bottom: 5px;}
        .alumni-company { font-size: 13px; color: #64748b; margin-bottom: 15px; display: flex; align-items: center; justify-content: center; gap: 5px;}
        .alumni-company i { color: #38bdf8; }
        
        /* Tombol Profil (LinkedIn / Email) */
        .alumni-actions { display: flex; justify-content: center; gap: 10px; margin-top: auto; padding-top: 20px; border-top: 1px dashed #e2e8f0; width: 100%;}
        .btn-social { width: 35px; height: 35px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; text-decoration: none; transition: 0.3s; font-size: 14px; }
        .btn-linkedin { background: #f0f9ff; color: #0077b5; }
        .btn-linkedin:hover { background: #e0f2fe; transform: translateY(-3px);}
        .btn-email { background: #f1f5f9; color: #475569; }
        .btn-email:hover { background: #e2e8f0; transform: translateY(-3px);}
    </style>
</head>
<body>

    <!-- NAVBAR KONSISTEN -->
    <?php @include 'includes/navbar.php'; ?>

    <!-- HEADER HALAMAN -->
    <div class="page-header-custom">
        <div class="container">
            <h1><i class="fas fa-user-graduate me-3" style="color:#38bdf8;"></i>Jejaring Alumni</h1>
            <p>Terhubung dengan lulusan hebat HIMASTI IT Del yang kini berkarya di berbagai perusahaan dan industri teknologi di seluruh dunia.</p>
        </div>
    </div>

    <!-- KOTAK STATISTIK MELAYANG -->
    <div class="container stats-wrapper">
        <div class="row justify-content-center g-4">
            <div class="col-md-4 col-sm-6">
                <div class="stat-box">
                    <div class="stat-icon"><i class="fas fa-users"></i></div>
                    <div class="stat-number"><?= $total_alumni ?></div>
                    <div class="stat-label">Total Alumni Terdata</div>
                </div>
            </div>
            <div class="col-md-4 col-sm-6">
                <div class="stat-box">
                    <div class="stat-icon"><i class="fas fa-building"></i></div>
                    <div class="stat-number"><?= $total_company ?></div>
                    <div class="stat-label">Perusahaan / Institusi</div>
                </div>
            </div>
            <div class="col-md-4 col-sm-6">
                <div class="stat-box">
                    <div class="stat-icon"><i class="fas fa-calendar-check"></i></div>
                    <div class="stat-number"><?= $total_angkatan ?></div>
                    <div class="stat-label">Generasi Angkatan</div>
                </div>
            </div>
        </div>
    </div>

    <div class="container mb-5 pb-5 mt-4">
        
        <!-- PENCARIAN & FILTER SEJAJAR -->
        <div class="filter-box">
            <form action="alumni.php" method="GET" class="row g-3 align-items-center">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-search"></i></span>
                        <input type="text" name="cari" class="form-control border-start-0 search-input" placeholder="Cari nama, posisi, atau nama perusahaan..." value="<?= htmlspecialchars($search_query) ?>">
                    </div>
                </div>
                <div class="col-md-4">
                    <select name="angkatan" class="form-select form-select-custom w-100">
                        <option value="Semua">Semua Angkatan</option>
                        <?php foreach($angkatan_list as $angkatan): ?>
                            <option value="<?= htmlspecialchars($angkatan) ?>" <?= $angkatan_filter == $angkatan ? 'selected' : '' ?>>
                                Angkatan <?= htmlspecialchars($angkatan) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn-search w-100 shadow-sm">Cari</button>
                </div>
            </form>
        </div>

        <!-- JUDUL DIREKTORI -->
        <div class="d-flex justify-content-between align-items-end mb-4 border-bottom pb-3">
            <div>
                <h3 class="fw-bold mb-1" style="color: #0f172a;">Direktori Alumni</h3>
                <p class="text-muted mb-0" style="font-size: 14px;">Menampilkan profil alumni berdasarkan pencarian Anda.</p>
            </div>
        </div>

        <!-- GRID KARTU ALUMNI -->
        <div class="row g-4 justify-content-center">
            <?php if(empty($alumni_list)): ?>
                <div class="col-12 text-center mt-2">
                    <div class="alert alert-light border p-5" style="border-radius: 15px;">
                        <i class="fas fa-user-times fa-4x text-muted mb-3 opacity-50"></i>
                        <h5 class="fw-bold text-muted">Tidak Ada Alumni Ditemukan</h5>
                        <p class="text-muted small mb-4">Coba ubah kata kunci pencarian atau ubah filter angkatan Anda.</p>
                        <!-- Tombol Reset yang Diperbaiki -->
                        <a href="alumni.php" class="btn btn-outline-primary rounded-pill px-4" style="font-weight: 600;">
                            <i class="fas fa-sync-alt me-2"></i> Reset Pencarian
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach($alumni_list as $alumni): 
                    // Membuat nama tanpa gelar untuk avatar otomatis jika foto kosong
                    $nama_bersih = trim(preg_replace('/[^a-zA-Z\s]/', '', explode(',', $alumni['name'])[0]));
                    $avatar_url = !empty($alumni['photo']) && file_exists('assets/uploads/' . $alumni['photo']) 
                                  ? 'assets/uploads/' . htmlspecialchars($alumni['photo']) 
                                  : "https://ui-avatars.com/api/?name=" . urlencode($nama_bersih) . "&background=e2e8f0&color=0f172a&size=150";
                ?>
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="alumni-card">
                        
                        <!-- Label Angkatan di Sudut Kanan -->
                        <?php if(!empty($alumni['graduation_year'])): ?>
                            <div class="alumni-batch"><?= htmlspecialchars($alumni['graduation_year']) ?></div>
                        <?php endif; ?>
                        
                        <img src="<?= $avatar_url ?>" class="alumni-avatar" alt="<?= htmlspecialchars($alumni['name']) ?>">
                        
                        <h4 class="alumni-name"><?= htmlspecialchars($alumni['name']) ?></h4>
                        
                        <div class="alumni-job"><?= htmlspecialchars($alumni['current_job'] ?: 'Alumni') ?></div>
                        
                        <?php if(!empty($alumni['company'])): ?>
                            <div class="alumni-company">
                                <i class="fas fa-building"></i> <?= htmlspecialchars($alumni['company']) ?>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Tombol Sosial Media -->
                        <div class="alumni-actions">
                            <?php if(!empty($alumni['linkedin_url'])): ?>
                                <a href="<?= htmlspecialchars($alumni['linkedin_url']) ?>" target="_blank" class="btn-social btn-linkedin" title="Profil LinkedIn">
                                    <i class="fab fa-linkedin-in"></i>
                                </a>
                            <?php endif; ?>
                            <a href="mailto:info@del.ac.id" class="btn-social btn-email" title="Hubungi Alumni">
                                <i class="fas fa-envelope"></i>
                            </a>
                        </div>
                        
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

    </div>

    <!-- FOOTER SEDERHANA -->
    <footer style="background: #0b1120; padding: 30px 0; color: #94a3b8; text-align: center; font-size: 14px;">
        <div class="container">
            © 2026 HIMASTI IT Del. All Rights Reserved.
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>