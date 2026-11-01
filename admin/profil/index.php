<?php
// admin/profil/index.php
require_once __DIR__ . '/../cek-login.php';
require_once __DIR__ . '/../../config/database.php';

$page_title = "Edit Profil Organisasi";
$pesan_sukses = "";
$pesan_error = "";

// =========================================================================
// 1. AUTO-PATCH DATABASE (Menambahkan kolom kompleks secara otomatis)
// =========================================================================
$new_columns = [
    "short_name VARCHAR(100) NULL AFTER org_name",
    "founded_year VARCHAR(10) NULL AFTER tagline",
    "description TEXT NULL AFTER founded_year",
    "goals TEXT NULL AFTER mission",
    "logo_meaning TEXT NULL AFTER goals",
    "graduate_profiles TEXT NULL AFTER logo_meaning" // TAMBAHAN BARU UNTUK PROFIL LULUSAN
];
foreach ($new_columns as $col) {
    $col_name = explode(" ", trim($col))[0];
    try {
        $pdo->exec("ALTER TABLE profile_settings ADD COLUMN $col");
    } catch (PDOException $e) { /* Abaikan jika kolom sudah ada */ }
}

// =========================================================================
// 2. PROSES JIKA FORM DISIMPAN
// =========================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $org_name          = trim($_POST['org_name'] ?? '');
    $short_name        = trim($_POST['short_name'] ?? '');
    $tagline           = trim($_POST['tagline'] ?? '');
    $founded_year      = trim($_POST['founded_year'] ?? '');
    $description       = trim($_POST['description'] ?? '');
    $vision            = trim($_POST['vision'] ?? '');
    $mission           = trim($_POST['mission'] ?? '');
    $goals             = trim($_POST['goals'] ?? '');
    $history           = trim($_POST['history'] ?? '');
    $logo_meaning      = trim($_POST['logo_meaning'] ?? '');
    $graduate_profiles = trim($_POST['graduate_profiles'] ?? '');

    $cek = $pdo->query("SELECT COUNT(*) FROM profile_settings")->fetchColumn();
    if ($cek == 0) {
        $pdo->exec("INSERT INTO profile_settings (org_name) VALUES ('HIMASTI IT Del')");
    }

    try {
        $stmt = $pdo->prepare("UPDATE profile_settings SET 
            org_name = ?, short_name = ?, tagline = ?, founded_year = ?, 
            description = ?, vision = ?, mission = ?, goals = ?, 
            history = ?, logo_meaning = ?, graduate_profiles = ?");
        $stmt->execute([
            $org_name, $short_name, $tagline, $founded_year, 
            $description, $vision, $mission, $goals, 
            $history, $logo_meaning, $graduate_profiles
        ]);

        $upload_dir = __DIR__ . '/../../assets/uploads/';
        if (!file_exists($upload_dir)) mkdir($upload_dir, 0777, true);

        if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
            $logo_name = 'logo_' . time() . '_' . $_FILES['logo']['name'];
            if (move_uploaded_file($_FILES['logo']['tmp_name'], $upload_dir . $logo_name)) {
                $pdo->prepare("UPDATE profile_settings SET logo = ?")->execute([$logo_name]);
            }
        }

        if (isset($_FILES['banner']) && $_FILES['banner']['error'] === UPLOAD_ERR_OK) {
            $banner_name = 'banner_' . time() . '_' . $_FILES['banner']['name'];
            if (move_uploaded_file($_FILES['banner']['tmp_name'], $upload_dir . $banner_name)) {
                $pdo->prepare("UPDATE profile_settings SET banner = ?")->execute([$banner_name]);
            }
        }

        $pesan_sukses = "Profil organisasi dan data lulusan berhasil diperbarui!";
    } catch (PDOException $e) {
        $pesan_error = "Gagal menyimpan data: " . $e->getMessage();
    }
}

// =========================================================================
// 3. AMBIL DATA UNTUK DITAMPILKAN DI FORM
// =========================================================================
$stmt = $pdo->query("SELECT * FROM profile_settings LIMIT 1");
$profil = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$profil) {
    $profil = array_fill_keys(['org_name','short_name','tagline','founded_year','description','vision','mission','goals','history','logo_meaning','graduate_profiles','logo','banner'], '');
}
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
        * { font-family: 'Inter', 'Segoe UI', Tahoma, sans-serif; }
        body { background: #f8fafc; display: flex; min-height: 100vh; margin: 0; color: #334155; }
        
        .sidebar { width: 250px; background: #0f172a; color: white; position: fixed; height: 100vh; overflow-y: auto; z-index: 1000; }
        .sidebar-header { padding: 25px 20px; border-bottom: 1px solid rgba(255,255,255,0.05); background: #0b1120; }
        .sidebar-header h2 { color: #f8fafc; font-size: 22px; margin-bottom: 5px; font-weight: 700; letter-spacing: 1px;}
        .sidebar-header p { color: #94a3b8; font-size: 12px; margin:0;}
        .menu { list-style: none; padding: 20px 0; margin:0;}
        .menu li a { display: flex; align-items: center; gap: 12px; color: #cbd5e1; text-decoration: none; padding: 12px 20px; transition: all 0.3s; border-left: 3px solid transparent; font-size: 14px; }
        .menu li a i { width: 20px; text-align: center; }
        .menu li a:hover, .menu li a.active { background: #1e293b; color: #fff; border-left-color: #38bdf8; }
        .menu-divider { color: #64748b; font-size: 11px; padding: 15px 20px 5px; text-transform: uppercase; font-weight: 700; letter-spacing: 1px;}
        
        .main { margin-left: 250px; padding: 35px 40px; width: calc(100% - 250px); }
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .page-header h1 { font-size: 26px; font-weight: 700; color: #0f172a; margin: 0; }
        
        .card-custom { background: #ffffff; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05), 0 2px 4px -1px rgba(0,0,0,0.03); border: 1px solid #e2e8f0; margin-bottom: 30px; overflow: hidden; }
        .card-header-custom { background: #f8fafc; padding: 18px 25px; border-bottom: 1px solid #e2e8f0; font-weight: 600; color: #0f172a; font-size: 16px; display: flex; align-items: center; gap: 10px; }
        .card-body-custom { padding: 25px; }
        
        .form-label { font-weight: 600; color: #475569; font-size: 14px; margin-bottom: 8px; }
        .form-control { border-radius: 8px; border: 1px solid #cbd5e1; padding: 10px 15px; font-size: 14px; transition: 0.3s; }
        .form-control:focus { border-color: #38bdf8; box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.1); }
        .form-text { font-size: 12.5px; color: #64748b; margin-top: 6px; }
        
        .file-upload-wrapper { border: 2px dashed #cbd5e1; border-radius: 10px; padding: 20px; text-align: center; background: #f8fafc; transition: 0.3s; position: relative; }
        .file-upload-wrapper:hover { border-color: #38bdf8; background: #f0f9ff; }
        .img-preview { max-height: 100px; border-radius: 6px; margin-top: 15px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .banner-preview { max-height: 120px; width: 100%; object-fit: cover; border-radius: 6px; margin-top: 15px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        
        .btn-save { background: #0f172a; color: white; padding: 12px 30px; border-radius: 8px; font-weight: 600; border: none; transition: 0.3s; font-size: 15px; }
        .btn-save:hover { background: #1e293b; transform: translateY(-2px); box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
    
    <div class="sidebar">
        <div class="sidebar-header">
            <h2>HIMASTI</h2>
            <p>Admin Panel &bull; IT Del</p>
        </div>
        <ul class="menu">
            <li><a href="../dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
            <li class="menu-divider">Konten</li>
            <li><a href="../berita/index.php"><i class="fas fa-newspaper"></i> Kelola Berita</a></li>
            <li><a href="../agenda/index.php"><i class="fas fa-calendar-alt"></i> Kelola Agenda</a></li>
            <li><a href="../galeri/index.php"><i class="fas fa-images"></i> Kelola Galeri</a></li>
            <li><a href="../pesan/index.php"><i class="fas fa-envelope"></i> Pesan Masuk</a></li>
            <li class="menu-divider">Profil Organisasi</li>
            <li><a href="index.php" class="active"><i class="fas fa-university"></i> Edit Profil Institusi</a></li>
            <li><a href="../pengurus/index.php"><i class="fas fa-sitemap"></i> Struktur Pengurus</a></li>
            <li><a href="../program/index.php"><i class="fas fa-star"></i> Program Unggulan</a></li>
            <li class="menu-divider">Master Data</li>
            <li><a href="../dosen/index.php"><i class="fas fa-chalkboard-teacher"></i> Data Dosen</a></li>
            <li><a href="../alumni/index.php"><i class="fas fa-user-graduate"></i> Jejaring Alumni</a></li>
            <li><a href="../user/index.php"><i class="fas fa-users-cog"></i> Pengguna Sistem</a></li>
            <li><a href="../pengaturan/index.php"><i class="fas fa-cog"></i> Konfigurasi Web</a></li>
            <li class="menu-divider">Eksternal</li>
            <li><a href="../../index.php" target="_blank"><i class="fas fa-external-link-alt"></i> Buka Website Publik</a></li>
        </ul>
    </div>
    
    <div class="main">
        <div class="page-header">
            <h1>Profil Himpunan</h1>
            <a href="../../profil.php" target="_blank" class="btn btn-outline-primary shadow-sm" style="border-radius: 8px; font-weight: 600;">
                <i class="fas fa-eye me-2"></i>Pratinjau Publik
            </a>
        </div>
        
        <?php if ($pesan_sukses): ?>
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3"><i class="fas fa-check-circle me-2"></i> <?= $pesan_sukses ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        <?php endif; ?>
        <?php if ($pesan_error): ?>
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3"><i class="fas fa-exclamation-triangle me-2"></i> <?= $pesan_error ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        <?php endif; ?>
        
        <form method="POST" action="" enctype="multipart/form-data">
            
            <div class="card-custom">
                <div class="card-header-custom"><i class="fas fa-id-card text-primary"></i> Identitas Pokok & Aset Visual</div>
                <div class="card-body-custom">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label">Nama Resmi Organisasi <span class="text-danger">*</span></label>
                            <input type="text" name="org_name" class="form-control" value="<?= htmlspecialchars($profil['org_name']) ?>" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Singkatan (Opsional)</label>
                            <input type="text" name="short_name" class="form-control" value="<?= htmlspecialchars($profil['short_name'] ?? '') ?>" placeholder="Misal: HIMASTI">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Tahun Berdiri</label>
                            <input type="number" name="founded_year" class="form-control" value="<?= htmlspecialchars($profil['founded_year'] ?? '') ?>" placeholder="YYYY">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Slogan / Tagline</label>
                            <input type="text" name="tagline" class="form-control" value="<?= htmlspecialchars($profil['tagline']) ?>" placeholder="Moto atau semangat yang diusung...">
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label">Logo Himpunan</label>
                            <div class="file-upload-wrapper">
                                <input type="file" name="logo" class="form-control" accept="image/*">
                                <div class="form-text">Format PNG transparan disarankan. Maks 2MB.</div>
                                <?php if(!empty($profil['logo'])): ?>
                                    <img src="../../assets/uploads/<?= htmlspecialchars($profil['logo']) ?>" class="img-preview" alt="Logo">
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="col-md-8">
                            <label class="form-label">Banner Header Website (Resolusi Tinggi)</label>
                            <div class="file-upload-wrapper">
                                <input type="file" name="banner" class="form-control" accept="image/*">
                                <div class="form-text">Format JPG/PNG. Ukuran ideal 1920x600px.</div>
                                <?php if(!empty($profil['banner'])): ?>
                                    <img src="../../assets/uploads/<?= htmlspecialchars($profil['banner']) ?>" class="banner-preview" alt="Banner">
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-custom">
                <div class="card-header-custom"><i class="fas fa-book-open text-primary"></i> Profil & Histori</div>
                <div class="card-body-custom">
                    <div class="row g-4">
                        <div class="col-md-12">
                            <label class="form-label">Deskripsi Umum Singkat</label>
                            <textarea name="description" class="form-control" rows="3" placeholder="Paragraf pengantar singkat tentang HIMASTI yang akan muncul di beranda..."><?= htmlspecialchars($profil['description'] ?? '') ?></textarea>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Sejarah Pendirian & Latar Belakang</label>
                            <textarea name="history" class="form-control" rows="6"><?= htmlspecialchars($profil['history']) ?></textarea>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Filosofi & Makna Lambang/Logo</label>
                            <textarea name="logo_meaning" class="form-control" rows="5"><?= htmlspecialchars($profil['logo_meaning'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-custom">
                <div class="card-header-custom"><i class="fas fa-compass text-primary"></i> Arah Gerak Organisasi</div>
                <div class="card-body-custom">
                    <div class="row g-4">
                        <div class="col-md-12">
                            <label class="form-label">Visi Utama <span class="text-danger">*</span></label>
                            <textarea name="vision" class="form-control" rows="3" required><?= htmlspecialchars($profil['vision']) ?></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Misi (Langkah Strategis) <span class="text-danger">*</span></label>
                            <textarea name="mission" class="form-control" rows="7" required><?= htmlspecialchars($profil['mission']) ?></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tujuan & Sasaran (Opsional)</label>
                            <textarea name="goals" class="form-control" rows="7"><?= htmlspecialchars($profil['goals'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION BARU: PROFIL LULUSAN -->
            <div class="card-custom">
                <div class="card-header-custom"><i class="fas fa-user-graduate text-primary"></i> Profil Lulusan & Prospek Karir</div>
                <div class="card-body-custom">
                    <div class="row g-4">
                        <div class="col-md-12">
                            <label class="form-label">Daftar Profesi & Jalur Karir Lulusan</label>
                            <textarea name="graduate_profiles" class="form-control" rows="8" placeholder="Contoh Pengisian:&#10;&#10;1. Software Engineer (App Engineer, UX Designer, Software Analyst, QA Tester, Software Security Engineer)&#10;2. Artificial Intelligence (AI Engineer)&#10;3. Cyber Security (Cybersecurity Practitioner)"><?= htmlspecialchars($profil['graduate_profiles'] ?? '') ?></textarea>
                            <div class="form-text">Masukkan prospek lulusan sesuai dengan kurikulum program studi. Anda bisa menuliskannya dalam format daftar bersusun (bullet points).</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end mb-5">
                <button type="submit" class="btn-save shadow">
                    <i class="fas fa-cloud-upload-alt me-2"></i> Simpan Seluruh Perubahan Profil
                </button>
            </div>
        </form>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>