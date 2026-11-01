<?php
// includes/footer.php
// Footer untuk website publik - Prodi Informatika IT Del
?>

<!-- Footer -->
<footer class="footer">
    <div class="container">
        <div class="row">
            <!-- Kolom 1: Tentang Prodi -->
            <div class="col-lg-4 col-md-6 mb-4 mb-lg-0">
                <h5 class="footer-title">Prodi Informatika IT Del</h5>
                <p class="footer-text">
                    Program Studi Informatika Institut Teknologi Del berkomitmen menghasilkan lulusan yang unggul, profesional, inovatif, dan mampu berkontribusi melalui teknologi.
                </p>
                <div class="social-links mt-3">
                    <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-youtube"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>

            <!-- Kolom 2: Link Cepat -->
            <div class="col-lg-2 col-md-6 mb-4 mb-lg-0">
                <h5 class="footer-title">Link Cepat</h5>
                <ul class="footer-links">
                    <li><a href="<?= BASE_URL ?>index.php">Beranda</a></li>
                    <li><a href="<?= BASE_URL ?>profil.php">Profil</a></li>
                    <li><a href="<?= BASE_URL ?>akademik.php">Akademik</a></li>
                    <li><a href="<?= BASE_URL ?>dosen.php">Dosen</a></li>
                    <li><a href="<?= BASE_URL ?>berita.php">Berita</a></li>
                    <li><a href="<?= BASE_URL ?>kontak.php">Kontak</a></li>
                </ul>
            </div>
            
<!-- Akademik -->
<div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
    <h5 style="color: white; font-weight: 700; margin-bottom: 20px; font-size: 18px; position: relative; padding-bottom: 10px;">
        Akademik
        <span style="position: absolute; bottom: 0; left: 0; width: 40px; height: 3px; background: #4a90a4;"></span>
    </h5>
    <ul class="list-unstyled" style="line-height: 2;">
        <li class="mb-2">
            <a href="akademik.php#bagian-kurikulum" style="color: #b0bec5; text-decoration: none; transition: color 0.3s;" onmouseover="this.style.color='#ffffff'" onmouseout="this.style.color='#b0bec5'">
                <i class="fas fa-book me-2"></i>Kurikulum
            </a>
        </li>
        <li class="mb-2">
            <a href="akademik.php#bagian-panduan" style="color: #b0bec5; text-decoration: none; transition: color 0.3s;" onmouseover="this.style.color='#ffffff'" onmouseout="this.style.color='#b0bec5'">
                <i class="fas fa-file-alt me-2"></i>Panduan Akademik
            </a>
        </li>
        <li class="mb-2">
            <a href="akademik.php#bagian-kalender" style="color: #b0bec5; text-decoration: none; transition: color 0.3s;" onmouseover="this.style.color='#ffffff'" onmouseout="this.style.color='#b0bec5'">
                <i class="fas fa-calendar-alt me-2"></i>Kalender Akademik
            </a>
        </li>
    </ul>
</div>

            <!-- Kolom 4: Kontak -->
            <div class="col-lg-3 col-md-6">
                <h5 class="footer-title">Hubungi Kami</h5>
                <ul class="contact-info list-unstyled">
                    <li class="mb-3">
                        <i class="fas fa-map-marker-alt me-2"></i>
                        Jl. Sisingamangaraja, Porsea, Sumatera Utara
                    </li>
                    <li class="mb-3">
                        <i class="fas fa-phone me-2"></i>
                        (0632) 331234
                    </li>
                    <li class="mb-3">
                        <i class="fas fa-envelope me-2"></i>
                        informatika@del.ac.id
                    </li>
                    <li>
                        <i class="fas fa-clock me-2"></i>
                        Senin - Jumat: 08:00 - 17:00
                    </li>
                </ul>
            </div>
        </div>

        <hr class="footer-divider my-4">

        <div class="row align-items-center">
            <div class="col-md-6 text-center text-md-start">
                <p class="copyright mb-0">
                    &copy; <?= date('Y') ?> Program Studi Informatika IT Del. All rights reserved.
                </p>
                <!-- WATERMARK PEMBUAT WEB DITAMBAHKAN DI SINI -->
                <p class="mb-0 mt-1" style="font-size: 13px; color: #b0bec5;">
                    Designed & Developed with <i class="fas fa-heart text-danger mx-1" style="animation: pulse 1.5s infinite;"></i> by <a href="#" style="color: #4a90a4; text-decoration: none; font-weight: 700; transition: color 0.3s;" onmouseover="this.style.color='#ffffff'" onmouseout="this.style.color='#4a90a4'">Theresia Oktaviani Samosir</a>
                </p>
            </div>
            <div class="col-md-6 text-center text-md-end mt-3 mt-md-0">
                <a href="#" class="footer-link me-3">Privacy Policy</a>
                <a href="#" class="footer-link">Terms of Service</a>
            </div>
        </div>
    </div>
</footer>

<!-- Animasi detak jantung untuk ikon pembuat web -->
<style>
@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.2); }
    100% { transform: scale(1); }
}
</style>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Custom JS -->
<script src="<?= BASE_URL ?>assets/js/main.js"></script>

</body>
</html>