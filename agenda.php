<?php
// agenda.php - Halaman Publik Portal Agenda HIMASTI
require_once __DIR__ . '/config/database.php';

$page_title = 'Agenda';

// =========================================================
// 1. LOGIKA PENCARIAN
// =========================================================
$search_query = $_GET['cari'] ?? '';
$where_clause = "1=1"; // Default true untuk mengambil semua
$params = [];

// Jika user mengetik di kotak pencarian
if (!empty($search_query)) {
    $where_clause .= " AND (title LIKE ? OR description LIKE ? OR location LIKE ?)";
    $params[] = "%$search_query%";
    $params[] = "%$search_query%";
    $params[] = "%$search_query%";
}

// =========================================================
// 2. AMBIL DATA AGENDA DARI DATABASE
// =========================================================
try {
    // Mengambil data agenda, diurutkan dari yang paling dekat/terbaru
    $sql = "SELECT * FROM events WHERE $where_clause ORDER BY event_date DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $agenda_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $agenda_list = [];
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
        
        /* Navbar Style Premium */
        .navbar-custom { background-color: #0f172a; padding: 15px 0; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
        .navbar-brand { color: #f8fafc !important; font-weight: 800; font-size: 24px; letter-spacing: 0.5px; }
        .nav-link { color: #cbd5e1 !important; font-weight: 600; font-size: 14px; margin: 0 8px; transition: 0.3s; padding: 8px 15px !important; border-radius: 8px;}
        .nav-link:hover, .nav-link.active { color: #fff !important; background: rgba(255,255,255,0.1); }
        .btn-portal { background-color: #38bdf8; color: #0f172a; font-weight: 700; border-radius: 8px; padding: 10px 20px; transition: 0.3s; border: none; font-size: 14px;}
        .btn-portal:hover { background-color: #0284c7; color: white; transform: translateY(-2px);}
        
        /* Header Halaman */
        .page-header-custom { background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: white; padding: 70px 0; text-align: center; margin-bottom: 40px;}
        .page-header-custom h1 { font-weight: 800; font-size: 38px; margin-bottom: 15px; }
        .page-header-custom p { color: #cbd5e1; font-size: 16px; max-width: 600px; margin: 0 auto;}

        /* Kotak Pencarian Minimalis */
        .search-container { max-width: 700px; margin: -70px auto 50px; position: relative; z-index: 10; padding: 0 15px;}
        .filter-box { background: #fff; padding: 15px; border-radius: 12px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1); border: 1px solid #e2e8f0; }
        .search-input { border-radius: 8px; border: 1px solid #cbd5e1; padding: 12px 20px; font-size: 15px; background: #f8fafc;}
        .search-input:focus { border-color: #38bdf8; box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.1); outline: none; background: #fff;}
        .btn-search { background: #0f172a; color: white; border-radius: 8px; padding: 12px 35px; font-weight: 600; transition: 0.3s; border: none; font-size: 15px;}
        .btn-search:hover { background: #1e293b; transform: translateY(-1px);}

        /* Kartu Agenda Premium */
        .agenda-card { background: #fff; border-radius: 16px; overflow: hidden; border: 1px solid #e2e8f0; transition: all 0.3s ease; height: 100%; display: flex; flex-direction: column; text-decoration: none; color: inherit;}
        .agenda-card:hover { transform: translateY(-8px); box-shadow: 0 20px 40px -5px rgba(0,0,0,0.1); border-color:#cbd5e1; }
        
        .agenda-img-wrapper { position: relative; width: 100%; height: 200px; background: linear-gradient(135deg, #818cf8 0%, #6366f1 100%); overflow: hidden; display: flex; align-items: center; justify-content: center;}
        .agenda-img { width: 100%; height: 100%; object-fit: cover; transition: 0.5s; }
        .agenda-card:hover .agenda-img { transform: scale(1.05); }
        
        /* Badge Kategori Melayang */
        .badge-kategori { position: absolute; top: 15px; left: 15px; background: rgba(255, 255, 255, 0.9); color: #4f46e5; padding: 6px 15px; border-radius: 8px; font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);}
        
        /* Tanggal Besar di Sudut Kanan */
        .date-badge { position: absolute; bottom: -20px; right: 25px; background: #fff; border: 2px solid #e2e8f0; border-radius: 12px; text-align: center; width: 65px; height: 70px; display: flex; flex-direction: column; justify-content: center; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); z-index: 2; transition: 0.3s;}
        .agenda-card:hover .date-badge { border-color: #38bdf8; transform: translateY(-5px);}
        .date-day { font-size: 22px; font-weight: 800; color: #0f172a; line-height: 1; margin-bottom: 2px;}
        .date-month { font-size: 12px; font-weight: 700; color: #38bdf8; text-transform: uppercase;}

        .agenda-body { padding: 35px 25px 25px; display: flex; flex-direction: column; flex-grow: 1; }
        .agenda-title { font-size: 19px; font-weight: 800; color: #0f172a; margin-bottom: 15px; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;}
        
        /* Detail Informasi (Lokasi & Waktu) */
        .agenda-meta { font-size: 13.5px; color: #64748b; margin-bottom: 20px; display: flex; flex-direction: column; gap: 8px; flex-grow: 1;}
        .meta-item { display: flex; align-items: flex-start; gap: 10px; }
        .meta-icon { color: #38bdf8; font-size: 15px; margin-top: 2px; width: 15px; text-align: center;}
        
        /* Footer Kartu */
        .agenda-footer { display: flex; justify-content: flex-end; align-items: center; margin-top: auto; padding-top: 20px; border-top: 1px dashed #e2e8f0; }
        .btn-read { color: #ffffff; background-color: #38bdf8; padding: 8px 20px; border-radius: 8px; font-weight: 700; font-size: 13px; text-decoration: none; transition: 0.3s;}
        .agenda-card:hover .btn-read { background-color: #0284c7; }
    </style>
</head>
<body>

    <!-- NAVBAR KONSISTEN -->
    <?php @include 'includes/navbar.php'; ?>

    <!-- HEADER HALAMAN -->
    <div class="page-header-custom">
        <div class="container pb-4">
            <h1>Agenda Kegiatan</h1>
            <p>Jadwal acara, kompetisi, seminar, dan aktivitas kemahasiswaan Program Studi Informatika IT Del.</p>
        </div>
    </div>

    <!-- KOTAK PENCARIAN MINIMALIS -->
    <div class="search-container">
        <div class="filter-box">
            <form action="agenda.php" method="GET" class="d-flex gap-2">
                <div class="input-group flex-grow-1">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-search"></i></span>
                    <input type="text" name="cari" class="form-control border-start-0 search-input" placeholder="Cari nama acara atau lokasi kegiatan..." value="<?= htmlspecialchars($search_query) ?>">
                </div>
                <button type="submit" class="btn-search shadow-sm">Cari</button>
            </form>
        </div>
    </div>

    <div class="container mb-5 pb-5 mt-2">
        <!-- GRID KARTU AGENDA -->
        <div class="row g-4">
            <?php if(empty($agenda_list)): ?>
                <div class="col-12 text-center mt-2">
                    <div class="alert alert-light border p-5" style="border-radius: 15px;">
                        <i class="fas fa-calendar-times fa-3x text-muted mb-3 opacity-50"></i>
                        <h5 class="text-muted">Tidak ada agenda yang ditemukan.</h5>
                        <p class="text-muted small">Mungkin kata kunci yang Anda masukkan salah, atau belum ada jadwal baru.</p>
                        <a href="agenda.php" class="btn btn-outline-primary mt-2 rounded-pill px-4">Tampilkan Semua Agenda</a>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach($agenda_list as $agenda): 
                    $image_path = 'assets/uploads/' . ($agenda['image'] ?? '');
                    $has_image = !empty($agenda['image']) && file_exists($image_path);
                    
                    // Memecah Tanggal untuk ditampilkan di Badge Kalender
                    $waktu_db = strtotime($agenda['event_date']);
                    $hari = date('d', $waktu_db);
                    $bulan = date('M', $waktu_db);
                    $tahun = date('Y', $waktu_db);
                    $tanggal_lengkap = date('d F Y', $waktu_db);
                    
                    // Fallback Kategori, Waktu dan Lokasi
                    $kategori = !empty($agenda['category']) ? $agenda['category'] : 'Kegiatan';
                    $lokasi = !empty($agenda['location']) ? $agenda['location'] : 'Lokasi Menyusul';
                    $waktu_mulai = !empty($agenda['start_time']) ? date('H:i', strtotime($agenda['start_time'])) : '08:00';
                    $waktu_selesai = !empty($agenda['end_time']) ? date('H:i', strtotime($agenda['end_time'])) : 'Selesai';
                ?>
                <div class="col-lg-4 col-md-6">
                    <a href="detail/detail-agenda.php?slug=<?= htmlspecialchars($agenda['slug'] ?? '') ?>" class="agenda-card">
                        
                        <!-- Gambar / Banner Agenda -->
                        <div class="agenda-img-wrapper">
                            <?php if($has_image): ?>
                                <img src="<?= htmlspecialchars($image_path) ?>" class="agenda-img" alt="<?= htmlspecialchars($agenda['title']) ?>">
                            <?php else: ?>
                                <!-- Fallback jika tidak ada gambar (Gradien Ungu Keren dengan Ikon Kalender) -->
                                <i class="far fa-calendar-check fa-5x" style="color: rgba(255,255,255,0.4);"></i>
                            <?php endif; ?>
                            
                            <!-- Label Kategori Melayang -->
                            <span class="badge-kategori"><?= htmlspecialchars($kategori) ?></span>
                            
                            <!-- Badge Tanggal Ikonik -->
                            <div class="date-badge">
                                <div class="date-day"><?= $hari ?></div>
                                <div class="date-month"><?= $bulan ?></div>
                            </div>
                        </div>
                        
                        <!-- Konten Agenda -->
                        <div class="agenda-body">
                            <h3 class="agenda-title"><?= htmlspecialchars($agenda['title']) ?></h3>
                            
                            <!-- Detail Waktu & Lokasi -->
                            <div class="agenda-meta">
                                <div class="meta-item">
                                    <i class="far fa-calendar-alt meta-icon"></i>
                                    <span><?= $tanggal_lengkap ?></span>
                                </div>
                                <div class="meta-item">
                                    <i class="far fa-clock meta-icon"></i>
                                    <span><?= $waktu_mulai ?> - <?= $waktu_selesai ?> WIB</span>
                                </div>
                                <div class="meta-item">
                                    <i class="fas fa-map-marker-alt meta-icon"></i>
                                    <span><?= htmlspecialchars($lokasi) ?></span>
                                </div>
                            </div>
                            
                            <!-- Footer Kartu -->
                            <div class="agenda-footer">
                                <div class="btn-read">Selengkapnya <i class="fas fa-arrow-right ms-1"></i></div>
                            </div>
                        </div>
                        
                    </a>
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