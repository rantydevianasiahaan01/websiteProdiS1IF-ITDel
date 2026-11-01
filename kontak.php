<?php
// kontak.php - Halaman Publik Hubungi Kami HIMASTI
require_once __DIR__ . '/config/database.php';

$page_title = 'Hubungi Kami';

// =========================================================
// 1. AMBIL PENGATURAN WEB (Untuk Info Kontak)
// =========================================================
try {
    $set = $pdo->query("SELECT * FROM web_settings LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    // Fallback jika data kosong
    if (!$set) {
        $set = [
            'address' => 'Jl. Sisingamangaraja, Sitoluama, Laguboti, Toba, Sumatera Utara',
            'email' => 'informatika@del.ac.id',
            'phone' => '+62 632 331234',
            'instagram' => '#',
            'youtube' => '#',
            'linkedin' => '#'
        ];
    }
} catch (PDOException $e) {
    $set = [
        'address' => 'Jl. Sisingamangaraja, Sitoluama, Laguboti, Toba, Sumatera Utara',
        'email' => 'informatika@del.ac.id',
        'phone' => '+62 632 331234'
    ];
}

// =========================================================
// 2. PROSES FORM PESAN (Jika ada yang submit)
// =========================================================
$pesan_status = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telepon = trim($_POST['telepon'] ?? '');
    $subjek = trim($_POST['subjek'] ?? '');
    $pesan = trim($_POST['pesan'] ?? '');
    
    if(!empty($nama) && !empty($email) && !empty($pesan)) {
        try {
            // Asumsi nama tabel adalah 'messages' atau 'pesan' sesuai standar admin
            $stmt = $pdo->prepare("INSERT INTO messages (name, email, phone, subject, message, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
            $stmt->execute([$nama, $email, $telepon, $subjek, $pesan]);
            $pesan_status = 'success';
        } catch (PDOException $e) {
            // Jika tabelnya bernama 'pesan' berbahasa indonesia
            try {
                $stmt = $pdo->prepare("INSERT INTO pesan (nama, email, telepon, subjek, isi_pesan, tanggal) VALUES (?, ?, ?, ?, ?, NOW())");
                $stmt->execute([$nama, $email, $telepon, $subjek, $pesan]);
                $pesan_status = 'success';
            } catch (PDOException $e2) {
                $pesan_status = 'error';
            }
        }
    } else {
        $pesan_status = 'empty';
    }
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
        .page-header-custom { background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: white; padding: 70px 0; text-align: center; margin-bottom: 60px;}
        .page-header-custom h1 { font-weight: 800; font-size: 38px; margin-bottom: 15px; }
        .page-header-custom p { color: #cbd5e1; font-size: 16px; max-width: 600px; margin: 0 auto;}

        /* Section Title */
        .section-title { text-align: center; margin-bottom: 40px; margin-top: 60px; }
        .section-subtitle { color: #38bdf8; font-weight: 700; font-size: 13px; letter-spacing: 2px; text-transform: uppercase; margin-bottom: 10px; }
        .section-title h2 { color: #0f172a; font-weight: 800; font-size: 32px; }

        /* Informasi Kontak & Form */
        .info-title { font-size: 28px; font-weight: 800; color: #0f172a; margin-bottom: 15px;}
        .info-desc { color: #64748b; line-height: 1.6; margin-bottom: 30px;}
        
        .contact-item { display: flex; align-items: flex-start; gap: 15px; margin-bottom: 25px; }
        .contact-icon { width: 50px; height: 50px; background: #e0f2fe; color: #0284c7; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0;}
        .contact-text h5 { font-size: 16px; font-weight: 700; color: #0f172a; margin-bottom: 5px; }
        .contact-text p { font-size: 15px; color: #475569; margin: 0; line-height: 1.5; }

        /* Ikon Media Sosial */
        .social-links { display: flex; gap: 10px; margin-top: 30px;}
        .btn-social-outline { width: 40px; height: 40px; border: 2px solid #e2e8f0; color: #64748b; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; text-decoration: none; transition: 0.3s; font-size: 16px; }
        .btn-social-outline:hover { background: #38bdf8; border-color: #38bdf8; color: white; transform: translateY(-3px);}

        /* Kartu Form */
        .form-card { background: #fff; border-radius: 16px; padding: 40px; box-shadow: 0 10px 30px -10px rgba(0,0,0,0.08); border: 1px solid #e2e8f0;}
        .form-card h3 { font-size: 24px; font-weight: 800; color: #0f172a; margin-bottom: 25px;}
        .form-label { font-weight: 600; color: #475569; font-size: 14px; margin-bottom: 8px;}
        .form-control-custom { border-radius: 8px; border: 1px solid #cbd5e1; padding: 12px 15px; font-size: 14px; background: #f8fafc; transition: 0.3s;}
        .form-control-custom:focus { border-color: #38bdf8; box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.1); background: #fff; outline: none;}
        .btn-submit { background: #0f172a; color: white; font-weight: 600; padding: 12px 30px; border-radius: 8px; border: none; transition: 0.3s; font-size: 15px; display: inline-flex; align-items: center; gap: 8px;}
        .btn-submit:hover { background: #1e293b; transform: translateY(-2px); box-shadow: 0 4px 10px rgba(0,0,0,0.15);}

        /* Peta Kampus */
        .map-wrapper { border-radius: 16px; overflow: hidden; box-shadow: 0 10px 30px -10px rgba(0,0,0,0.08); border: 1px solid #e2e8f0; height: 450px; background: #e2e8f0;}
        
        /* Pertanyaan Umum (FAQ Accordion) */
        .accordion-custom { border: none; }
        .accordion-custom .accordion-item { border: 1px solid #e2e8f0; border-radius: 12px !important; margin-bottom: 15px; overflow: hidden; background: #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.02);}
        .accordion-custom .accordion-button { font-weight: 600; color: #0f172a; padding: 20px 25px; box-shadow: none; background: #fff;}
        .accordion-custom .accordion-button:not(.collapsed) { background: #f0f9ff; color: #0284c7;}
        .accordion-custom .accordion-button::after { background-size: 1rem; }
        .accordion-custom .accordion-body { padding: 5px 25px 25px; color: #475569; line-height: 1.7;}

        /* Bantuan Section */
        .help-box { text-align: center; margin-top: 40px; padding-top: 40px; border-top: 1px dashed #cbd5e1;}
    </style>
</head>
<body>

    <!-- NAVBAR KONSISTEN -->
    <?php @include 'includes/navbar.php'; ?>

    <!-- HEADER HALAMAN -->
    <div class="page-header-custom">
        <div class="container">
            <h1><i class="fas fa-envelope-open-text me-3" style="color:#38bdf8;"></i>Hubungi Kami</h1>
            <p>Kami siap mendengar dan membantu Anda. Jangan ragu untuk mengirimkan pertanyaan, saran, atau peluang kerja sama.</p>
        </div>
    </div>

    <div class="container mb-5 pb-5">
        
        <!-- BAGIAN 1: KONTAK & FORM -->
        <div class="row g-5 mb-5 pb-5">
            <!-- Kolom Info Kiri -->
            <div class="col-lg-5">
                <div class="section-subtitle" style="margin-top:0;">INFORMASI KONTAK</div>
                <h2 class="info-title">Mari Terhubung</h2>
                <p class="info-desc">Silakan hubungi kami untuk pertanyaan seputar program studi, pendaftaran mahasiswa baru, kerjasama institusi, atau informasi lainnya.</p>
                
                <div class="contact-item">
                    <div class="contact-icon"><i class="fas fa-map-marker-alt"></i></div>
                    <div class="contact-text">
                        <h5>Alamat Kampus</h5>
                        <p><?= htmlspecialchars($set['address'] ?? '') ?></p>
                    </div>
                </div>
                
                <div class="contact-item">
                    <div class="contact-icon"><i class="fas fa-envelope"></i></div>
                    <div class="contact-text">
                        <h5>Email Resmi</h5>
                        <p><?= htmlspecialchars($set['email'] ?? '') ?></p>
                    </div>
                </div>
                
                <div class="contact-item">
                    <div class="contact-icon"><i class="fas fa-phone-alt"></i></div>
                    <div class="contact-text">
                        <h5>Telepon / WhatsApp</h5>
                        <p><?= htmlspecialchars($set['phone'] ?? '') ?></p>
                    </div>
                </div>
                
                <div class="mt-4 pt-2">
                    <h5 style="font-size:14px; font-weight:700; color:#0f172a; margin-bottom:15px;">Ikuti Media Sosial Kami</h5>
                    <div class="social-links">
                        <a href="#" class="btn-social-outline"><i class="fab fa-facebook-f"></i></a>
                        <a href="<?= htmlspecialchars($set['instagram'] ?? '#') ?>" class="btn-social-outline"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="btn-social-outline"><i class="fab fa-twitter"></i></a>
                        <a href="<?= htmlspecialchars($set['youtube'] ?? '#') ?>" class="btn-social-outline"><i class="fab fa-youtube"></i></a>
                        <a href="<?= htmlspecialchars($set['linkedin'] ?? '#') ?>" class="btn-social-outline"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
            </div>
            
            <!-- Kolom Form Kanan -->
            <div class="col-lg-7">
                <div class="form-card">
                    <h3>Kirim Pesan Langsung</h3>
                    
                    <?php if($pesan_status === 'success'): ?>
                        <div class="alert alert-success alert-dismissible fade show rounded-3 border-0 shadow-sm">
                            <i class="fas fa-check-circle me-2"></i> <strong>Berhasil!</strong> Pesan Anda telah kami terima dan akan segera kami balas.
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php elseif($pesan_status === 'error'): ?>
                        <div class="alert alert-danger rounded-3 border-0">
                            <i class="fas fa-exclamation-triangle me-2"></i> Terjadi kesalahan sistem saat mengirim pesan.
                        </div>
                    <?php endif; ?>

                    <form action="" method="POST">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" name="nama" class="form-control form-control-custom" required placeholder="Masukkan nama Anda">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Alamat Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control form-control-custom" required placeholder="nama@email.com">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Telepon / WhatsApp</label>
                                <input type="text" name="telepon" class="form-control form-control-custom" placeholder="0812xxxxxx">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Subjek Pesan</label>
                                <input type="text" name="subjek" class="form-control form-control-custom" placeholder="Topik pertanyaan">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Isi Pesan <span class="text-danger">*</span></label>
                                <textarea name="pesan" class="form-control form-control-custom" rows="5" required placeholder="Tuliskan detail pertanyaan atau pesan Anda di sini..."></textarea>
                            </div>
                            <div class="col-12 mt-4">
                                <button type="submit" class="btn-submit">
                                    <i class="fas fa-paper-plane"></i> Kirim Pesan Sekarang
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- BAGIAN 2: PETA LOKASI -->
        <div class="section-title">
            <div class="section-subtitle">LOKASI KAMPUS</div>
            <h2>Peta Navigasi</h2>
        </div>
        <div class="map-wrapper mb-5 pb-4">
            <!-- Peta Google Maps Asli Institut Teknologi Del -->
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3986.3664472833076!2d99.1461996749683!3d2.383525497591866!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x302e00fdad2d7341%3A0xf59ef99ce7e9fdce!2sInstitut%20Teknologi%20Del!5e0!3m2!1sen!2sid!4v1714000000000!5m2!1sen!2sid" 
                    width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>

        <!-- BAGIAN 3: FAQ (PERTANYAAN UMUM) -->
        <div class="section-title">
            <div class="section-subtitle">BANTUAN INFORMASI</div>
            <h2>Pertanyaan Umum (FAQ)</h2>
        </div>
        
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="accordion accordion-custom" id="faqAccordion">
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingOne">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true">
                                Bagaimana cara mendaftar di Prodi Informatika IT Del?
                            </button>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Pendaftaran dapat dilakukan melalui website resmi Penerimaan Mahasiswa Baru (PMB) IT Del di <a href="https://spmbdel.del.ac.id" target="_blank" class="text-decoration-none fw-bold">spmbdel.del.ac.id</a>. Informasi lebih lanjut mengenai persyaratan berkas dan jadwal seleksi ujian masuk dapat dilihat secara lengkap di website tersebut.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingTwo">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo">
                                Apakah tersedia program beasiswa untuk mahasiswa Informatika?
                            </button>
                        </h2>
                        <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Ya, IT Del menyediakan berbagai program beasiswa bagi mahasiswa berprestasi dan juga mahasiswa yang membutuhkan bantuan finansial. Beasiswa tersebut bersumber dari yayasan, pemerintah (seperti KIP Kuliah), dan berbagai perusahaan mitra yang bekerja sama dengan IT Del.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingThree">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree">
                                Bagaimana prospek kerja lulusan Informatika IT Del?
                            </button>
                        </h2>
                        <div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Lulusan Prodi Informatika IT Del memiliki prospek karir yang sangat luas dan menjanjikan, mulai dari Software Engineer, Data Scientist, Ahli Keamanan Siber (Cyber Security), UI/UX Designer, hingga IT Consultant di berbagai perusahaan teknologi nasional maupun multinasional terkemuka.
                            </div>
                        </div>
                    </div>
                    
                </div>
                
                <!-- Bantuan Tambahan -->
                <div class="help-box">
                    <p class="text-muted mb-3">Tidak menemukan jawaban yang Anda cari?</p>
                    <button class="btn btn-outline-primary rounded-pill px-4" onclick="window.scrollTo({top: 0, behavior: 'smooth'})">
                        <i class="fas fa-envelope me-2"></i> Tulis Pesan Kepada Kami
                    </button>
                </div>
            </div>
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