<?php
// fasilitas.php - Halaman Fasilitas Prodi Informatika IT Del
require_once 'config/database.php';
require_once 'config/auth.php';
require_once 'includes/functions.php';

// Set judul halaman
$page_title = 'Fasilitas - HIMASTI IT Del';

include 'includes/header.php';
include 'includes/navbar.php';
?>

<!-- Hero Section -->
<section class="hero-section" style="background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary-medium) 100%); padding: 100px 0;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8 mx-auto text-center text-white">
                <h1 class="display-4 fw-bold mb-3">Fasilitas Laboratorium</h1>
                <p class="lead mb-4">Laboratorium modern dan lengkap untuk mendukung pembelajaran praktis mahasiswa Informatika</p>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb justify-content-center mb-0">
                        <li class="breadcrumb-item"><a href="index.php" class="text-white-50 text-decoration-none">Beranda</a></li>
                        <li class="breadcrumb-item active text-white" aria-current="page">Fasilitas</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</section>

<!-- Fasilitas Section -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h6 class="text-uppercase fw-bold mb-2" style="color: var(--accent);">Fasilitas Laboratorium</h6>
            <h2 style="color: var(--primary-dark);">Laboratorium Terkini dan Modern</h2>
        </div>
        
        <!-- Laboratorium Komputer -->
        <div class="row g-4 mb-5">
            <div class="col-lg-6">
                <div class="card border-0 shadow-lg h-100 overflow-hidden">
                    <div class="position-relative">
                        <img src="assets/uploads/lab-komputer.jpg" 
                             alt="Laboratorium Komputer" 
                             class="img-fluid w-100" 
                             style="height: 400px; object-fit: cover;">
                        <div class="position-absolute bottom-0 start-0 w-100 p-4" 
                             style="background: rgba(0, 0, 0, 0.6);">
                            <h3 class="text-white mb-2">Laboratorium Komputer</h3>
                            <p class="text-white-50 mb-0">Ruang laboratorium dengan 30 komputer modern dan koneksi internet berkecepatan tinggi.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card border-0 shadow-lg h-100 p-4">
                    <h4 class="mb-4" style="color: var(--primary-dark);">Spesifikasi Laboratorium</h4>
                    
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-microchip text-primary me-3 fs-3"></i>
                                <div>
                                    <h6 class="mb-1">Prosesor</h6>
                                    <p class="text-muted mb-0">Intel Core i7 (terbaru)</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-memory text-primary me-3 fs-3"></i>
                                <div>
                                    <h6 class="mb-1">RAM</h6>
                                    <p class="text-muted mb-0">16GB DDR4</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-hdd text-primary me-3 fs-3"></i>
                                <div>
                                    <h6 class="mb-1">Storage</h6>
                                    <p class="text-muted mb-0">1TB SSD</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-network-wired text-primary me-3 fs-3"></i>
                                <div>
                                    <h6 class="mb-1">Jaringan</h6>
                                    <p class="text-muted mb-0">1 Gbps + WiFi 6</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <h4 class="mb-4" style="color: var(--primary-dark);">Jadwal Penggunaan</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Hari</th>
                                    <th>Waktu</th>
                                    <th>Prodi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Senin - Jumat</td>
                                    <td>08:00 - 17:00</td>
                                    <td>Prodi Informatika</td>
                                </tr>
                                <tr>
                                    <td>Senin - Jumat</td>
                                    <td>18:00 - 21:00</td>
                                    <td>Ekstrakurikuler HIMASTI</td>
                                </tr>
                                <tr>
                                    <td>Sabtu</td>
                                    <td>08:00 - 12:00</td>
                                    <td>Open Lab (Umum)</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Laboratorium Jaringan -->
        <div class="row g-4 mb-5">
            <div class="col-lg-6">
                <div class="card border-0 shadow-lg h-100 overflow-hidden">
                    <div class="position-relative">
                        <img src="assets/uploads/lab-jaringan.jpg" 
                             alt="Laboratorium Jaringan" 
                             class="img-fluid w-100" 
                             style="height: 400px; object-fit: cover;">
                        <div class="position-absolute bottom-0 start-0 w-100 p-4" 
                             style="background: rgba(0, 0, 0, 0.6);">
                            <h3 class="text-white mb-2">Laboratorium Jaringan</h3>
                            <p class="text-white-50 mb-0">Laboratorium khusus untuk praktikum jaringan komputer dan keamanan siber.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card border-0 shadow-lg h-100 p-4">
                    <h4 class="mb-4" style="color: var(--primary-dark);">Perangkat Khusus</h4>
                    
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-server text-primary me-3 fs-3"></i>
                                <div>
                                    <h6 class="mb-1">Server</h6>
                                    <p class="text-muted mb-0">2 Unit Cisco UCS</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-router text-primary me-3 fs-3"></i>
                                <div>
                                    <h6 class="mb-1">Router</h6>
                                    <p class="text-muted mb-0">5 Unit Cisco 2900</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-shield-alt text-primary me-3 fs-3"></i>
                                <div>
                                    <h6 class="mb-1">Firewall</h6>
                                    <p class="text-muted mb-0">Palo Alto Networks</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-vial text-primary me-3 fs-3"></i>
                                <div>
                                    <h6 class="mb-1">Peralatan</h6>
                                    <p class="text-muted mb-0">Switch, Access Point, dll.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <h4 class="mb-4" style="color: var(--primary-dark);">Kegiatan Terkait</h4>
                    <ul class="list-group">
                        <li class="list-group-item d-flex align-items-center">
                            <i class="fas fa-check-circle text-success me-3"></i>
                            <span>Praktikum Jaringan Komputer</span>
                        </li>
                        <li class="list-group-item d-flex align-items-center">
                            <i class="fas fa-check-circle text-success me-3"></i>
                            <span>Pelatihan Keamanan Siber</span>
                        </li>
                        <li class="list-group-item d-flex align-items-center">
                            <i class="fas fa-check-circle text-success me-3"></i>
                            <span>Kompetisi CTF (Capture The Flag)</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        
        <!-- Laboratorium IoT -->
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="card border-0 shadow-lg h-100 overflow-hidden">
                    <div class="position-relative">
                        <img src="assets/uploads/lab-iot.jpg" 
                             alt="Laboratorium IoT" 
                             class="img-fluid w-100" 
                             style="height: 400px; object-fit: cover;">
                        <div class="position-absolute bottom-0 start-0 w-100 p-4" 
                             style="background: rgba(0, 0, 0, 0.6);">
                            <h3 class="text-white mb-2">Laboratorium IoT</h3>
                            <p class="text-white-50 mb-0">Laboratorium khusus untuk pengembangan sistem Internet of Things.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card border-0 shadow-lg h-100 p-4">
                    <h4 class="mb-4" style="color: var(--primary-dark);">Komponen Utama</h4>
                    
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <i class="fab fa-arduino text-primary me-3 fs-3"></i>
                                <div>
                                    <h6 class="mb-1">Platform Pengembangan</h6>
                                    <p class="text-muted mb-0">Arduino, Raspberry Pi, ESP32</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-microchip text-primary me-3 fs-3"></i>
                                <div>
                                    <h6 class="mb-1">Sensor</h6>
                                    <p class="text-muted mb-0">15+ jenis sensor (suhu, gerak, cahaya, dll)</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-satellite text-primary me-3 fs-3"></i>
                                <div>
                                    <h6 class="mb-1">Komunikasi</h6>
                                    <p class="text-muted mb-0">Wi-Fi, Bluetooth, LoRa, 5G</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-cloud text-primary me-3 fs-3"></i>
                                <div>
                                    <h6 class="mb-1">Cloud</h6>
                                    <p class="text-muted mb-0">AWS IoT, Google Cloud IoT</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <h4 class="mb-4" style="color: var(--primary-dark);">Proyek Terkini</h4>
                    <div class="card border-0">
                        <div class="d-flex align-items-center mb-3">
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                <i class="fas fa-satellite fs-4"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">Smart Agriculture</h6>
                                <p class="text-muted mb-0">Sistem pertanian presisi berbasis IoT</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                <i class="fas fa-temperature-high fs-4"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">Smart Building</h6>
                                <p class="text-muted mb-0">Sistem pengendali bangunan cerdas</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-5" style="background: var(--gradient-primary); color: white;">
    <div class="container text-center">
        <h2 class="mb-4">Ingin Menggunakan Fasilitas?</h2>
        <p class="lead mb-4" style="opacity: 0.9;">Hubungi kami untuk informasi lebih lanjut tentang penggunaan fasilitas laboratorium.</p>
        <a href="kontak.php" class="btn btn-outline-light btn-lg">
            <i class="fas fa-envelope me-2"></i>Hubungi Kami
        </a>
    </div>
</section>

<?php include 'includes/footer.php'; ?>