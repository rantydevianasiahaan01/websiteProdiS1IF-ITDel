<?php
require_once '../config/database.php';

$slug = $_GET['slug'] ?? '';

// Ambil detail berita
try {
    $stmt = $pdo->prepare("
        SELECT p.*, c.name as category_name, u.name as author_name 
        FROM posts p
        LEFT JOIN categories c ON p.category_id = c.id
        LEFT JOIN users u ON p.author_id = u.id
        WHERE p.slug = ? 
          AND p.status = 'published' 
          AND p.deleted_at IS NULL
    ");
    $stmt->execute([$slug]);
    $berita = $stmt->fetch();
    
    if (!$berita) {
        header("Location: ../berita.php");
        exit;
    }
    
    // Update view count
    $pdo->prepare("UPDATE posts SET view_count = view_count + 1 WHERE id = ?")->execute([$berita['id']]);
    
} catch (PDOException $e) {
    header("Location: ../berita.php");
    exit;
}

$page_title = $berita['title'] . " - HIMASTI IT Del";

// Ambil berita terkait (kategori sama, kecuali berita ini)
try {
    $stmt = $pdo->prepare("
        SELECT * FROM posts 
        WHERE category_id = ? 
          AND id != ? 
          AND status = 'published' 
          AND deleted_at IS NULL
        ORDER BY published_at DESC 
        LIMIT 3
    ");
    $stmt->execute([$berita['category_id'], $berita['id']]);
    $berita_terkait = $stmt->fetchAll();
} catch (PDOException $e) {
    $berita_terkait = [];
}

// Ambil berita terbaru
try {
    $stmt = $pdo->query("
        SELECT * FROM posts 
        WHERE status = 'published' AND deleted_at IS NULL
        ORDER BY published_at DESC 
        LIMIT 5
    ");
    $berita_terbaru = $stmt->fetchAll();
} catch (PDOException $e) {
    $berita_terbaru = [];
}

// Ambil kategori untuk sidebar
try {
    $stmt = $pdo->query("
        SELECT c.id, c.name, COUNT(p.id) as post_count 
        FROM categories c
        LEFT JOIN posts p ON c.id = p.category_id 
            AND p.status = 'published' 
            AND p.deleted_at IS NULL
        WHERE c.is_active = 1
        GROUP BY c.id, c.name
        HAVING post_count > 0
        ORDER BY c.name ASC
    ");
    $categories = $stmt->fetchAll();
} catch (PDOException $e) {
    $categories = [];
}

include '../includes/header.php';
include '../includes/navbar.php';
?>

<!-- Hero Section dengan Gambar -->
<?php 
$hero_image = 'assets/img/default-hero.jpg';
if (!empty($berita['image']) && $berita['image'] != 'default.jpg' && file_exists('../assets/uploads/' . $berita['image'])) {
    $hero_image = '../assets/uploads/' . htmlspecialchars($berita['image']);
}
?>
<section class="hero-section" style="background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.7)), url('<?= $hero_image ?>'); background-size: cover; background-position: center; padding: 120px 0 60px;">
    <div class="container">
        <div class="row">
            <div class="col-lg-10 mx-auto text-white">
                <?php if (!empty($berita['category_name'])): ?>
                    <span class="badge bg-primary mb-3 px-3 py-2">
                        <?= htmlspecialchars($berita['category_name']) ?>
                    </span>
                <?php endif; ?>
                <h1 class="display-4 fw-bold mb-3"><?= htmlspecialchars($berita['title']) ?></h1>
                <div class="d-flex align-items-center flex-wrap gap-3">
                    <span>
                        <i class="fas fa-user me-1"></i> 
                        <?= htmlspecialchars($berita['author_name'] ?? 'Admin') ?>
                    </span>
                    <span>
                        <i class="fas fa-calendar me-1"></i> 
                        <?= date('d F Y', strtotime($berita['published_at'] ?? $berita['created_at'])) ?>
                    </span>
                    <span>
                        <i class="fas fa-eye me-1"></i> 
                        <?= $berita['view_count'] ?> views
                    </span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Konten Utama -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <!-- Konten Artikel -->
            <div class="col-lg-8">
                <article class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4 p-md-5">
                        <!-- Excerpt (jika ada) -->
                        <?php if (!empty($berita['excerpt'])): ?>
                        <div class="lead mb-4 p-3" style="background: #f8fafc; border-left: 4px solid var(--primary); border-radius: 4px;">
                            <?= htmlspecialchars($berita['excerpt']) ?>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Konten (HTML dari TinyMCE) -->
                        <div class="article-content" style="line-height: 1.8; font-size: 1.05rem;">
                            <?= $berita['content'] ?>
                        </div>
                    </div>
                </article>
                
                <!-- Share Buttons -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h6 class="mb-3"><i class="fas fa-share-alt me-2"></i>Bagikan artikel ini:</h6>
                        <div class="d-flex gap-2 flex-wrap">
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) ?>" 
                               target="_blank" class="btn btn-outline-primary">
                                <i class="fab fa-facebook me-1"></i> Facebook
                            </a>
                            <a href="https://twitter.com/intent/tweet?url=<?= urlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) ?>&text=<?= urlencode($berita['title']) ?>" 
                               target="_blank" class="btn btn-outline-info">
                                <i class="fab fa-twitter me-1"></i> Twitter
                            </a>
                            <a href="https://wa.me/?text=<?= urlencode($berita['title'] . ' - http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) ?>" 
                               target="_blank" class="btn btn-outline-success">
                                <i class="fab fa-whatsapp me-1"></i> WhatsApp
                            </a>
                            <button onclick="copyToClipboard()" class="btn btn-outline-secondary">
                                <i class="fas fa-link me-1"></i> Copy Link
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Author Info -->
                <?php if (!empty($berita['author_name'])): ?>
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h6 class="mb-3">Ditulis oleh:</h6>
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3"
                                 style="width: 60px; height: 60px;">
                                <i class="fas fa-user text-white fs-4"></i>
                            </div>
                            <div>
                                <strong class="fs-5"><?= htmlspecialchars($berita['author_name']) ?></strong>
                                <br><small class="text-muted">Penulis HIMASTI IT Del</small>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Berita Terkait -->
                <?php if (!empty($berita_terkait)): ?>
                <div class="mt-5">
                    <h4 class="mb-4">Berita Terkait</h4>
                    <div class="row g-3">
                        <?php foreach ($berita_terkait as $related): ?>
                        <div class="col-md-4">
                            <div class="card border-0 shadow-sm h-100 berita-card" 
                                 style="cursor: pointer; transition: all 0.3s;"
                                 onclick="window.location.href='detail-berita.php?slug=<?= htmlspecialchars($related['slug']) ?>';">
                                <?php 
                                $related_image = '../assets/uploads/' . $related['image'];
                                $has_related_image = !empty($related['image']) && $related['image'] != 'default.jpg' && file_exists($related_image);
                                ?>
                                <?php if ($has_related_image): ?>
                                    <img src="<?= htmlspecialchars($related_image) ?>" 
                                         class="card-img-top" 
                                         alt="<?= htmlspecialchars($related['title']) ?>"
                                         style="height: 150px; object-fit: cover;">
                                <?php else: ?>
                                    <div style="height: 150px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-newspaper text-white" style="font-size: 2rem; opacity: 0.5;"></i>
                                    </div>
                                <?php endif; ?>
                                <div class="card-body p-3">
                                    <h6 class="card-title mb-2">
                                        <a href="detail-berita.php?slug=<?= htmlspecialchars($related['slug']) ?>" 
                                           class="text-decoration-none text-dark">
                                            <?= htmlspecialchars($related['title']) ?>
                                        </a>
                                    </h6>
                                    <small class="text-muted">
                                        <i class="fas fa-calendar me-1"></i>
                                        <?= date('d M Y', strtotime($related['published_at'] ?? $related['created_at'])) ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Back Button -->
                <div class="text-center mt-5">
                    <a href="../berita.php" class="btn btn-secondary btn-lg">
                        <i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar Berita
                    </a>
                </div>
            </div>
            
            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Berita Terbaru -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-clock me-2"></i>Berita Terbaru</h5>
                    </div>
                    <div class="card-body p-0">
                        <?php foreach ($berita_terbaru as $latest): ?>
                        <a href="detail-berita.php?slug=<?= htmlspecialchars($latest['slug']) ?>" 
                           class="d-flex p-3 text-decoration-none text-dark border-bottom"
                           style="transition: background 0.2s;"
                           onmouseover="this.style.background='#f8fafc'"
                           onmouseout="this.style.background='transparent'">
                            <?php 
                            $latest_image = '../assets/uploads/' . $latest['image'];
                            $has_latest_image = !empty($latest['image']) && $latest['image'] != 'default.jpg' && file_exists($latest_image);
                            ?>
                            <?php if ($has_latest_image): ?>
                                <img src="<?= htmlspecialchars($latest_image) ?>" 
                                     alt="<?= htmlspecialchars($latest['title']) ?>"
                                     style="width: 80px; height: 60px; object-fit: cover; border-radius: 4px; flex-shrink: 0;">
                            <?php endif; ?>
                            <div class="<?= $has_latest_image ? 'ms-3' : '' ?>">
                                <h6 class="mb-1" style="font-size: 14px; line-height: 1.3;">
                                    <?= htmlspecialchars($latest['title']) ?>
                                </h6>
                                <small class="text-muted">
                                    <?= date('d M Y', strtotime($latest['published_at'] ?? $latest['created_at'])) ?>
                                </small>
                            </div>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <!-- Kategori -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-folder me-2"></i>Kategori</h5>
                    </div>
                    <div class="card-body">
                        <?php foreach ($categories as $cat): ?>
                        <a href="../berita.php?category=<?= urlencode($cat['name']) ?>" 
                           class="d-flex justify-content-between align-items-center py-2 text-decoration-none text-dark border-bottom">
                            <span><?= htmlspecialchars($cat['name']) ?></span>
                            <span class="badge bg-primary"><?= $cat['post_count'] ?></span>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Style untuk konten artikel -->
<style>
.article-content img {
    max-width: 100%;
    height: auto;
    border-radius: 8px;
    margin: 20px 0;
}

.article-content h1, .article-content h2, .article-content h3, 
.article-content h4, .article-content h5, .article-content h6 {
    margin-top: 1.5rem;
    margin-bottom: 1rem;
    color: var(--primary-dark);
}

.article-content p {
    margin-bottom: 1rem;
}

.article-content blockquote {
    border-left: 4px solid var(--primary);
    padding-left: 1rem;
    margin: 1.5rem 0;
    font-style: italic;
    color: #64748b;
}

.article-content ul, .article-content ol {
    margin-bottom: 1rem;
    padding-left: 2rem;
}

.article-content a {
    color: var(--primary);
    text-decoration: underline;
}

.berita-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1) !important;
}
</style>

<script>
function copyToClipboard() {
    const url = window.location.href;
    navigator.clipboard.writeText(url).then(() => {
        alert('Link berhasil disalin!');
    });
}
</script>

<?php include '../includes/footer.php'; ?>