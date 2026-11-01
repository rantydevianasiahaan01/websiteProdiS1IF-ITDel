<?php
require_once '../config/database.php';

$slug = $_GET['slug'] ?? '';

try {
    $stmt = $pdo->prepare("
        SELECT e.*, u.name as creator_name 
        FROM events e
        LEFT JOIN users u ON e.created_by = u.id
        WHERE e.slug = ?
    ");
    $stmt->execute([$slug]);
    $agenda = $stmt->fetch();
    
    if (!$agenda) {
        header("Location: ../agenda.php");
        exit;
    }
    
} catch (PDOException $e) {
    header("Location: ../agenda.php");
    exit;
}

$page_title = $agenda['title'] . " - HIMASTI IT Del";

// Ambil agenda terkait (kategori sama)
try {
    $stmt = $pdo->prepare("
        SELECT * FROM events 
        WHERE category = ? 
          AND id != ? 
          AND status IN ('upcoming', 'ongoing')
        ORDER BY event_date ASC 
        LIMIT 3
    ");
    $stmt->execute([$agenda['category'], $agenda['id']]);
    $agenda_terkait = $stmt->fetchAll();
} catch (PDOException $e) {
    $agenda_terkait = [];
}

// Ambil agenda terbaru
try {
    $stmt = $pdo->query("
        SELECT * FROM events 
        WHERE status IN ('upcoming', 'ongoing')
        ORDER BY event_date ASC 
        LIMIT 5
    ");
    $agenda_terbaru = $stmt->fetchAll();
} catch (PDOException $e) {
    $agenda_terbaru = [];
}

include '../includes/header.php';
include '../includes/navbar.php';
?>

<!-- Hero Section -->
<?php 
$hero_image = '../assets/img/default-hero.jpg';
if (!empty($agenda['image']) && $agenda['image'] != 'default-event.jpg' && file_exists('../assets/uploads/' . $agenda['image'])) {
    $hero_image = '../assets/uploads/' . htmlspecialchars($agenda['image']);
}
?>
<section class="hero-section" style="background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.7)), url('<?= $hero_image ?>'); background-size: cover; background-position: center; padding: 120px 0 60px;">
    <div class="container">
        <div class="row">
            <div class="col-lg-10 mx-auto text-white">
                <?php if (!empty($agenda['category'])): ?>
                    <span class="badge bg-primary mb-3 px-3 py-2">
                        <?= htmlspecialchars($agenda['category']) ?>
                    </span>
                <?php endif; ?>
                
                <?php
                $status_badge = [
                    'upcoming' => ['bg-success', 'Akan Datang'],
                    'ongoing' => ['bg-warning', 'Sedang Berlangsung'],
                    'done' => ['bg-secondary', 'Selesai'],
                    'cancelled' => ['bg-danger', 'Dibatalkan']
                ];
                $badge = $status_badge[$agenda['status']] ?? ['bg-secondary', $agenda['status']];
                ?>
                <span class="badge <?= $badge[0] ?> mb-3 px-3 py-2 ms-2">
                    <?= $badge[1] ?>
                </span>
                
                <h1 class="display-4 fw-bold mb-3"><?= htmlspecialchars($agenda['title']) ?></h1>
                <div class="d-flex align-items-center flex-wrap gap-3">
                    <span>
                        <i class="fas fa-calendar me-1"></i> 
                        <?= date('d F Y', strtotime($agenda['event_date'])) ?>
                    </span>
                    <?php if (!empty($agenda['start_time'])): ?>
                    <span>
                        <i class="fas fa-clock me-1"></i> 
                        <?= date('H:i', strtotime($agenda['start_time'])) ?>
                        <?php if (!empty($agenda['end_time'])): ?>
                            - <?= date('H:i', strtotime($agenda['end_time'])) ?> WIB
                        <?php endif; ?>
                    </span>
                    <?php endif; ?>
                    <?php if (!empty($agenda['location'])): ?>
                    <span>
                        <i class="fas fa-map-marker-alt me-1"></i> 
                        <?= htmlspecialchars($agenda['location']) ?>
                    </span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Konten Utama -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <!-- Info Card -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                        <i class="fas fa-calendar-alt text-primary"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block">Tanggal Acara</small>
                                        <strong><?= date('d F Y', strtotime($agenda['event_date'])) ?></strong>
                                    </div>
                                </div>
                            </div>
                            
                            <?php if (!empty($agenda['start_time'])): ?>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-success bg-opacity-10 d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                        <i class="fas fa-clock text-success"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block">Waktu</small>
                                        <strong><?= date('H:i', strtotime($agenda['start_time'])) ?> WIB</strong>
                                        <?php if (!empty($agenda['end_time'])): ?>
                                            <br><small class="text-muted">s/d <?= date('H:i', strtotime($agenda['end_time'])) ?> WIB</small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($agenda['location'])): ?>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-danger bg-opacity-10 d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                        <i class="fas fa-map-marker-alt text-danger"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block">Lokasi</small>
                                        <strong><?= htmlspecialchars($agenda['location']) ?></strong>
                                        <?php if (!empty($agenda['location_detail'])): ?>
                                            <br><small class="text-muted"><?= htmlspecialchars($agenda['location_detail']) ?></small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($agenda['category'])): ?>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-info bg-opacity-10 d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                        <i class="fas fa-tag text-info"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block">Kategori</small>
                                        <strong><?= htmlspecialchars($agenda['category']) ?></strong>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Deskripsi -->
                <?php if (!empty($agenda['description'])): ?>
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h5 class="mb-3"><i class="fas fa-info-circle me-2 text-primary"></i>Deskripsi</h5>
                        <p class="lead mb-0"><?= htmlspecialchars($agenda['description']) ?></p>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Konten Lengkap -->
                <?php if (!empty($agenda['content'])): ?>
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4 p-md-5">
                        <h5 class="mb-3"><i class="fas fa-file-alt me-2 text-primary"></i>Detail Acara</h5>
                        <div class="article-content" style="line-height: 1.8; font-size: 1.05rem;">
                            <?= $agenda['content'] ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Tombol Pendaftaran -->
                <?php if (!empty($agenda['registration_link']) && $agenda['status'] != 'done' && $agenda['status'] != 'cancelled'): ?>
                <div class="card border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary) 100%); color: white;">
                    <div class="card-body p-4 text-center">
                        <h4 class="mb-3"><i class="fas fa-ticket-alt me-2"></i>Pendaftaran</h4>
                        <p class="mb-3">Klik tombol di bawah untuk mendaftar acara ini</p>
                        <a href="<?= htmlspecialchars($agenda['registration_link']) ?>" 
                           target="_blank" 
                           class="btn btn-light btn-lg">
                            <i class="fas fa-external-link-alt me-2"></i>Daftar Sekarang
                        </a>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Share Buttons -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h6 class="mb-3"><i class="fas fa-share-alt me-2"></i>Bagikan acara ini:</h6>
                        <div class="d-flex gap-2 flex-wrap">
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) ?>" 
                               target="_blank" class="btn btn-outline-primary">
                                <i class="fab fa-facebook me-1"></i> Facebook
                            </a>
                            <a href="https://twitter.com/intent/tweet?url=<?= urlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) ?>&text=<?= urlencode($agenda['title']) ?>" 
                               target="_blank" class="btn btn-outline-info">
                                <i class="fab fa-twitter me-1"></i> Twitter
                            </a>
                            <a href="https://wa.me/?text=<?= urlencode($agenda['title'] . ' - http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) ?>" 
                               target="_blank" class="btn btn-outline-success">
                                <i class="fab fa-whatsapp me-1"></i> WhatsApp
                            </a>
                            <button onclick="copyToClipboard()" class="btn btn-outline-secondary">
                                <i class="fas fa-link me-1"></i> Copy Link
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Agenda Terkait -->
                <?php if (!empty($agenda_terkait)): ?>
                <div class="mt-5">
                    <h4 class="mb-4">Agenda Terkait</h4>
                    <div class="row g-3">
                        <?php foreach ($agenda_terkait as $related): ?>
                        <div class="col-md-4">
                            <div class="card border-0 shadow-sm h-100 agenda-card" 
                                 style="cursor: pointer; transition: all 0.3s;"
                                 onclick="window.location.href='detail-agenda.php?slug=<?= htmlspecialchars($related['slug']) ?>';">
                                <?php 
                                $related_image = '../assets/uploads/' . $related['image'];
                                $has_related_image = !empty($related['image']) && $related['image'] != 'default-event.jpg' && file_exists($related_image);
                                ?>
                                <?php if ($has_related_image): ?>
                                    <img src="<?= htmlspecialchars($related_image) ?>" 
                                         class="card-img-top" 
                                         alt="<?= htmlspecialchars($related['title']) ?>"
                                         style="height: 150px; object-fit: cover;">
                                <?php else: ?>
                                    <div style="height: 150px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-calendar-alt text-white" style="font-size: 2rem; opacity: 0.5;"></i>
                                    </div>
                                <?php endif; ?>
                                <div class="card-body p-3">
                                    <h6 class="card-title mb-2">
                                        <a href="detail-agenda.php?slug=<?= htmlspecialchars($related['slug']) ?>" 
                                           class="text-decoration-none text-dark">
                                            <?= htmlspecialchars($related['title']) ?>
                                        </a>
                                    </h6>
                                    <small class="text-muted">
                                        <i class="fas fa-calendar me-1"></i>
                                        <?= date('d M Y', strtotime($related['event_date'])) ?>
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
                    <a href="../agenda.php" class="btn btn-secondary btn-lg">
                        <i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar Agenda
                    </a>
                </div>
            </div>
            
            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Agenda Terbaru -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-clock me-2"></i>Agenda Mendatang</h5>
                    </div>
                    <div class="card-body p-0">
                        <?php if (empty($agenda_terbaru)): ?>
                            <div class="p-3 text-center text-muted">
                                <i class="fas fa-calendar fa-2x mb-2"></i>
                                <p class="mb-0">Belum ada agenda</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($agenda_terbaru as $latest): ?>
                            <a href="detail-agenda.php?slug=<?= htmlspecialchars($latest['slug']) ?>" 
                               class="d-flex p-3 text-decoration-none text-dark border-bottom"
                               style="transition: background 0.2s;"
                               onmouseover="this.style.background='#f8fafc'"
                               onmouseout="this.style.background='transparent'">
                                <?php 
                                $latest_image = '../assets/uploads/' . $latest['image'];
                                $has_latest_image = !empty($latest['image']) && $latest['image'] != 'default-event.jpg' && file_exists($latest_image);
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
                                        <i class="fas fa-calendar me-1"></i>
                                        <?= date('d M Y', strtotime($latest['event_date'])) ?>
                                    </small>
                                </div>
                            </a>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Status Acara -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Status Acara</h5>
                    </div>
                    <div class="card-body">
                        <?php
                        $now = time();
                        $event_date = strtotime($agenda['event_date']);
                        $start_time = !empty($agenda['start_time']) ? strtotime($agenda['start_time']) : null;
                        
                        if ($agenda['status'] == 'cancelled') {
                            $status_text = 'Acara ini telah dibatalkan';
                            $status_color = 'danger';
                        } elseif ($agenda['status'] == 'done') {
                            $status_text = 'Acara ini telah selesai';
                            $status_color = 'secondary';
                        } elseif ($now < $event_date) {
                            $days_left = ceil(($event_date - $now) / 86400);
                            if ($days_left == 0) {
                                $status_text = 'Acara dimulai hari ini!';
                            } else {
                                $status_text = "Acara dimulai dalam {$days_left} hari lagi";
                            }
                            $status_color = 'primary';
                        } elseif ($now >= $event_date) {
                            $status_text = 'Acara sedang berlangsung!';
                            $status_color = 'success';
                        } else {
                            $status_text = 'Acara telah selesai';
                            $status_color = 'secondary';
                        }
                        ?>
                        <div class="alert alert-<?= $status_color ?> mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong><?= $status_text ?></strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.article-content img { max-width: 100%; height: auto; border-radius: 8px; margin: 20px 0; }
.article-content p { margin-bottom: 1rem; }
.agenda-card:hover { transform: translateY(-3px); box-shadow: 0 5px 15px rgba(0,0,0,0.1) !important; }
</style>

<script>
function copyToClipboard() {
    navigator.clipboard.writeText(window.location.href).then(() => {
        alert('Link berhasil disalin!');
    });
}
</script>

<?php include '../includes/footer.php'; ?>