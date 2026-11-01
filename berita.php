<?php
// berita.php - Halaman Publik Portal Berita HIMASTI
require_once __DIR__ . '/config/database.php';

$page_title = 'Berita';

// =========================================================
// 1. LOGIKA PENCARIAN
// =========================================================
$search_query = $_GET['cari'] ?? '';
$where_clause = "posts.status = 'published'";
$params = [];

// Jika user mengetik di kotak pencarian
if (!empty($search_query)) {
    $where_clause .= " AND (posts.title LIKE ? OR posts.excerpt LIKE ?)";
    $params[] = "%$search_query%";
    $params[] = "%$search_query%";
}

// =========================================================
// 2. AMBIL DATA BERITA DARI DATABASE
// =========================================================
try {
    $sql = "
        SELECT posts.*, categories.name AS category_name, users.name AS author_name
        FROM posts
        LEFT JOIN categories ON posts.category_id = categories.id
        LEFT JOIN users ON posts.author_id = users.id
        WHERE $where_clause
        ORDER BY posts.published_at DESC
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $berita_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $berita_list = [];
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

        /* Kartu Berita Premium */
        .news-card { background: #fff; border-radius: 16px; overflow: hidden; border: 1px solid #e2e8f0; transition: all 0.3s ease; height: 100%; display: flex; flex-direction: column; text-decoration: none; color: inherit;}
        .news-card:hover { transform: translateY(-8px); box-shadow: 0 20px 40px -5px rgba(0,0,0,0.1); border-color:#cbd5e1; }
        
        .news-img-wrapper { position: relative; width: 100%; height: 220px; background: #f1f5f9; overflow: hidden; }
        .news-img { width: 100%; height: 100%; object-fit: cover; transition: 0.5s; }
        .news-card:hover .news-img { transform: scale(1.05); }
        
        /* Badge Kategori Melayang */
        .badge-kategori { position: absolute; top: 15px; left: 15px; background: rgba(15, 23, 42, 0.85); color: #38bdf8; padding: 6px 15px; border-radius: 8px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; backdrop-filter: blur(4px);}
        
        .news-body { padding: 25px; display: flex; flex-direction: column; flex-grow: 1; }
        .news-date { font-size: 13px; color: #64748b; margin-bottom: 10px; display: flex; align-items: center; font-weight: 500;}
        .news-title { font-size: 18px; font-weight: 800; color: #0f172a; margin-bottom: 12px; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;}
        .news-excerpt { font-size: 14px; color: #475569; line-height: 1.6; margin-bottom: 20px; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; flex-grow: 1;}
        
        /* Footer Kartu */
        .news-footer { display: flex; justify-content: space-between; align-items: center; margin-top: auto; padding-top: 15px; border-top: 1px dashed #e2e8f0; }
        .news-author { font-size: 13px; color: #64748b; font-weight: 600; display: flex; align-items: center; gap: 8px;}
        .news-author i { color: #38bdf8; }
        .btn-read { color: #0284c7; font-weight: 700; font-size: 14px; text-decoration: none; transition: 0.3s;}
        .news-card:hover .btn-read { color: #0f172a; }
    </style>
</head>
<body>

    <?php @include 'includes/navbar.php'; ?>

    <!-- HEADER HALAMAN -->
    <div class="page-header-custom">
        <div class="container pb-4">
            <h1>Berita</h1>
            <p>Dapatkan informasi terkini, liputan kegiatan, dan artikel seputar teknologi dari HIMASTI IT Del.</p>
        </div>
    </div>

    <!-- KOTAK PENCARIAN MINIMALIS (Melayang di antara Header dan Konten) -->
    <div class="search-container">
        <div class="filter-box">
            <form action="berita.php" method="GET" class="d-flex gap-2">
                <div class="input-group flex-grow-1">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-search"></i></span>
                    <input type="text" name="cari" class="form-control border-start-0 search-input" placeholder="Ketik kata kunci berita yang ingin Anda cari..." value="<?= htmlspecialchars($search_query) ?>">
                </div>
                <button type="submit" class="btn-search shadow-sm">Cari</button>
            </form>
        </div>
    </div>

    <div class="container mb-5 pb-5 mt-2">
        <!-- GRID KARTU BERITA -->
        <div class="row g-4">
            <?php if(empty($berita_list)): ?>
                <div class="col-12 text-center mt-2">
                    <div class="alert alert-light border p-5" style="border-radius: 15px;">
                        <i class="fas fa-search fa-3x text-muted mb-3 opacity-50"></i>
                        <h5 class="text-muted">Tidak ada berita yang ditemukan.</h5>
                        <p class="text-muted small">Coba ubah kata kunci pencarian Anda dengan kata yang lebih umum.</p>
                        <a href="berita.php" class="btn btn-outline-primary mt-2 rounded-pill px-4">Tampilkan Semua Berita</a>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach($berita_list as $berita): 
                    $image_path = 'assets/uploads/' . $berita['image'];
                    $has_image = !empty($berita['image']) && $berita['image'] != 'default.jpg' && file_exists($image_path);
                    $tanggal = date('d M Y', strtotime($berita['published_at']));
                ?>
                <div class="col-lg-4 col-md-6">
                    <a href="detail/detail-berita.php?slug=<?= htmlspecialchars($berita['slug']) ?>" class="news-card">
                        
                        <div class="news-img-wrapper">
                            <?php if($has_image): ?>
                                <img src="<?= htmlspecialchars($image_path) ?>" class="news-img" alt="<?= htmlspecialchars($berita['title']) ?>">
                            <?php else: ?>
                                <div class="w-100 h-100 d-flex align-items-center justify-content-center bg-light">
                                    <i class="fas fa-newspaper fa-4x" style="color: #cbd5e1;"></i>
                                </div>
                            <?php endif; ?>
                            
                            <?php if(!empty($berita['category_name'])): ?>
                                <span class="badge-kategori"><?= htmlspecialchars($berita['category_name']) ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="news-body">
                            <div class="news-date"><i class="far fa-calendar-alt me-2 text-primary"></i> <?= $tanggal ?></div>
                            <h3 class="news-title"><?= htmlspecialchars($berita['title']) ?></h3>
                            <p class="news-excerpt">
                                <?= htmlspecialchars(substr(strip_tags($berita['excerpt'] ?? $berita['content']), 0, 120)) ?>...
                            </p>
                            
                            <div class="news-footer">
                                <div class="news-author">
                                    <i class="fas fa-user-edit"></i> <?= htmlspecialchars($berita['author_name'] ?? 'Administrator') ?>
                                </div>
                                <div class="btn-read">Baca <i class="fas fa-arrow-right ms-1"></i></div>
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