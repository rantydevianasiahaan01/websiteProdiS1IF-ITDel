<?php
// admin/index.php - Container Utama Admin Panel
require_once '../cek-login.php';
require_once '../config/database.php';
require_once '../config/auth.php';

$page_title = "Dashboard Admin";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?> - HIMASTI IT Del</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
        body { background: #f4f6f8; display: flex; min-height: 100vh; }
        
        /* Sidebar */
        .sidebar {
            width: 250px;
            background: #1a2a3a;
            color: white;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            z-index: 1000;
        }
        .sidebar-header {
            padding: 25px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            background: rgba(0,0,0,0.2);
        }
        .sidebar-header h2 { color: #fff; font-size: 22px; margin-bottom: 5px; letter-spacing: 1px; font-weight: 700; }
        .sidebar-header p { color: #b0bec5; font-size: 12px; }
        
        .menu { list-style: none; padding: 20px 0; }
        .menu li a {
            display: block;
            color: white;
            text-decoration: none;
            padding: 12px 20px;
            transition: all 0.3s;
            border-left: 3px solid transparent;
            cursor: pointer;
        }
        .menu li a:hover { background: rgba(61,90,115,0.3); border-left-color: #4a90a4; }
        .menu li a.active { background: #2c3e50; border-left-color: #4a90a4; }
        .menu-divider {
            color: #7f8c8d;
            font-size: 11px;
            padding: 15px 20px 5px;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
        }
        
        /* Main Content */
        .main {
            margin-left: 250px;
            padding: 30px;
            width: calc(100% - 250px);
            min-height: 100vh;
        }
        
        /* Loading */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 250px;
            right: 0;
            bottom: 0;
            background: rgba(255,255,255,0.8);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 999;
        }
        .loading-overlay.show { display: flex; }
        .loading-spinner {
            width: 50px;
            height: 50px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #1a2a3a;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .sidebar { width: 100%; height: auto; position: relative; }
            .main { margin-left: 0; width: 100%; }
            .loading-overlay { left: 0; }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h2>HIMASTI</h2>
            <p>Himpunan Mahasiswa Sarjana Informatika</p>
        </div>
        <ul class="menu">
            <li><a href="#" data-page="dashboard" class="active">🏠 Dashboard</a></li>
            
            <li class="menu-divider">Konten</li>
            <li><a href="#" data-page="berita">📰 Kelola Berita</a></li>
            <li><a href="#" data-page="agenda">📅 Kelola Agenda</a></li>
            <li><a href="#" data-page="galeri">🖼️ Kelola Galeri</a></li>
            
            <li class="menu-divider">Profil Organisasi</li>
            <li><a href="#" data-page="profil">📝 Edit Profil</a></li>
            <li><a href="#" data-page="pengurus">👥 Kelola Pengurus</a></li>
            <li><a href="#" data-page="program">⭐ Program Unggulan</a></li>
            
            <?php if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'super_admin'): ?>
                <li class="menu-divider">Master Data (Admin Only)</li>
                <li><a href="#" data-page="dosen">👨‍ Kelola Dosen</a></li>
                <li><a href="#" data-page="alumni">🎓 Kelola Alumni</a></li>
                <li><a href="#" data-page="users">👤 Kelola User</a></li>
                <li><a href="#" data-page="settings">⚙️ Pengaturan</a></li>
            <?php endif; ?>
            
            <li class="menu-divider">Website</li>
            <li><a href="../index.php" target="_blank">🌐 Lihat Website</a></li>
            <li><a href="../profil.php" target="_blank">👁️ Halaman Profil</a></li>
            
            <li class="menu-divider">Akun</li>
            <li><a href="login.php?logout=1" style="color: #ff6b6b;">🚪 Logout</a></li>
        </ul>
    </div>
    
    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-spinner"></div>
    </div>
    
    <!-- Main Content -->
    <div class="main" id="mainContent">
        <!-- Konten akan di-load di sini via AJAX -->
    </div>
    
    <script>
        // Fungsi untuk load konten via AJAX
        function loadPage(pageName) {
            const mainContent = document.getElementById('mainContent');
            const loadingOverlay = document.getElementById('loadingOverlay');
            
            loadingOverlay.classList.add('show');
            
            fetch('pages/' + pageName + '.php')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Halaman tidak ditemukan');
                    }
                    return response.text();
                })
                .then(html => {
                    mainContent.innerHTML = html;
                    loadingOverlay.classList.remove('show');
                    
                    const newUrl = '?page=' + pageName;
                    window.history.pushState({page: pageName}, '', newUrl);
                })
                .catch(error => {
                    mainContent.innerHTML = `
                        <div style="background: white; padding: 40px; border-radius: 8px; text-align: center;">
                            <h3 style="color: #c0392b;">⚠️ Error</h3>
                            <p style="color: #666;">${error.message}</p>
                        </div>
                    `;
                    loadingOverlay.classList.remove('show');
                });
        }
        
        // Fungsi navigateTo untuk menu sidebar
        function navigateTo(pageName) {
            const link = document.querySelector(`.menu a[data-page="${pageName}"]`);
            if (link) {
                document.querySelectorAll('.menu a').forEach(a => a.classList.remove('active'));
                link.classList.add('active');
                loadPage(pageName);
            } else {
                loadPage(pageName);
            }
        }
        
        // Handle klik menu sidebar
        document.querySelectorAll('.menu a[data-page]').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                
                document.querySelectorAll('.menu a').forEach(a => a.classList.remove('active'));
                this.classList.add('active');
                
                const pageName = this.getAttribute('data-page');
                loadPage(pageName);
            });
        });
        
        // Load halaman default
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const pageFromUrl = urlParams.get('page');
            
            if (pageFromUrl) {
                const link = document.querySelector(`.menu a[data-page="${pageFromUrl}"]`);
                if (link) {
                    document.querySelectorAll('.menu a').forEach(a => a.classList.remove('active'));
                    link.classList.add('active');
                    loadPage(pageFromUrl);
                } else {
                    loadPage('dashboard');
                }
            } else {
                loadPage('dashboard');
            }
        });
        
        // Handle browser back/forward
        window.addEventListener('popstate', function(e) {
            if (e.state && e.state.page) {
                const link = document.querySelector(`.menu a[data-page="${e.state.page}"]`);
                if (link) {
                    document.querySelectorAll('.menu a').forEach(a => a.classList.remove('active'));
                    link.classList.add('active');
                    loadPage(e.state.page);
                }
            }
        });
    </script>
</body>
</html>