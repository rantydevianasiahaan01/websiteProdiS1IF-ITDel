<?php
// akademik.php - Halaman Kurikulum Prodi Informatika IT Del
require_once __DIR__ . '/config/database.php';

$page_title = 'Akademik & Kurikulum';

// Data kurikulum IT Del Tahun 2024 (Data dipertahankan persis sesuai aslinya)
$kurikulum_data = [
    1 => [
        ['kode' => 'MAS1101', 'nama' => 'Matematika Dasar I', 'sks' => 4, 'jenis' => 'Wajib'],
        ['kode' => 'KUS1101', 'nama' => 'Bahasa Inggris I', 'sks' => 2, 'jenis' => 'Wajib'],
        ['kode' => 'KUS1102', 'nama' => 'Pembentukan Karakter Del', 'sks' => 2, 'jenis' => 'Wajib'],
        ['kode' => 'KUS1001', 'nama' => 'Bahasa Indonesia', 'sks' => 2, 'jenis' => 'Wajib'],
        ['kode' => '10S1001', 'nama' => 'Matematika Diskrit', 'sks' => 3, 'jenis' => 'Wajib'],
        ['kode' => '11S1111', 'nama' => 'Pemograman Fungsional (+P)', 'sks' => 3, 'jenis' => 'Wajib'],
        ['kode' => 'KUS0101', 'nama' => 'Penyelesaian Masalah di Dunia Digital', 'sks' => 2, 'jenis' => 'Wajib'],
    ],
    2 => [
        ['kode' => 'MAS1201', 'nama' => 'Matematika Dasar II', 'sks' => 4, 'jenis' => 'Wajib'],
        ['kode' => 'KUS1202', 'nama' => 'Pengantar Rekayasa dan Desain', 'sks' => 2, 'jenis' => 'Wajib'],
        ['kode' => '11S1221', 'nama' => 'Pemodelan dan Simulasi (+P)', 'sks' => 3, 'jenis' => 'Wajib'],
        ['kode' => '11S1231', 'nama' => 'Rekayasa Perangkat Lunak', 'sks' => 3, 'jenis' => 'Wajib'],
        ['kode' => '10S1201', 'nama' => 'Pemograman Prosedural (+P)', 'sks' => 3, 'jenis' => 'Wajib'],
        ['kode' => 'KUS1201', 'nama' => 'Bahasa Inggris II', 'sks' => 2, 'jenis' => 'Wajib'],
        ['kode' => 'KUS0001', 'nama' => 'Pancasila', 'sks' => 2, 'jenis' => 'Wajib'],
    ],
    3 => [
        ['kode' => 'KUS0202', 'nama' => 'Agama', 'sks' => 2, 'jenis' => 'Wajib'],
        ['kode' => '11S2126', 'nama' => 'Teori Bahasa Formal dan Automata', 'sks' => 3, 'jenis' => 'Wajib'],
        ['kode' => '10S2001', 'nama' => 'Jaringan Komputer (+P)', 'sks' => 3, 'jenis' => 'Wajib'],
        ['kode' => '11S2113', 'nama' => 'Pemrograman Berorientasi Objek (+P)', 'sks' => 4, 'jenis' => 'Wajib'],
        ['kode' => '10S1003', 'nama' => 'Arsitektur dan Organisasi Komputer (+P)', 'sks' => 3, 'jenis' => 'Wajib'],
        ['kode' => '10S1003', 'nama' => 'Basis Data (+P)', 'sks' => 3, 'jenis' => 'Wajib'],
        ['kode' => 'KUS0002', 'nama' => 'Agama Kristen', 'sks' => 2, 'jenis' => 'Wajib'],
        ['kode' => 'KUS0003', 'nama' => 'Agama Katolik', 'sks' => 2, 'jenis' => 'Wajib'],
        ['kode' => 'KUS0004', 'nama' => 'Agama Islam', 'sks' => 2, 'jenis' => 'Wajib'],
    ],
    4 => [
        ['kode' => 'MAS2101', 'nama' => 'Probabilitas dan Statistika', 'sks' => 3, 'jenis' => 'Wajib'],
        ['kode' => '10S2201', 'nama' => 'Aljabar Linier', 'sks' => 3, 'jenis' => 'Wajib'],
        ['kode' => '10S2002', 'nama' => 'Sistem Operasi (+P)', 'sks' => 3, 'jenis' => 'Wajib'],
        ['kode' => '11S2215', 'nama' => 'Algoritma dan Struktur Data (+P)', 'sks' => 4, 'jenis' => 'Wajib'],
        ['kode' => '11S2223', 'nama' => 'Logika Informatika', 'sks' => 3, 'jenis' => 'Wajib'],
        ['kode' => '11S2224', 'nama' => 'Interaksi Manusia Komputer', 'sks' => 3, 'jenis' => 'Wajib'],
    ],
    5 => [
        ['kode' => '11S3133', 'nama' => 'Pengembangan Aplikasi Berbasis Web (+P)', 'sks' => 4, 'jenis' => 'Wajib'],
        ['kode' => '11S3132', 'nama' => 'Analisis dan Perancangan Perangkat Lunak', 'sks' => 3, 'jenis' => 'Wajib'],
        ['kode' => '10S3001', 'nama' => 'Kecerdasan Buatan (+P)', 'sks' => 3, 'jenis' => 'Wajib'],
        ['kode' => '11S3126', 'nama' => 'Manajemen Proyek Teknologi Informasi', 'sks' => 3, 'jenis' => 'Wajib'],
        ['kode' => '11S3135', 'nama' => 'Pengujian dan Penjaminan Mutu Perangkat Lunak (+P)', 'sks' => 3, 'jenis' => 'Wajib'],
        ['kode' => '11S3143', 'nama' => 'Kriptografi dan Keamanan Informasi', 'sks' => 3, 'jenis' => 'Wajib'],
    ],
    6 => [
        ['kode' => 'KUS0201', 'nama' => 'Bahasa Inggris III', 'sks' => 2, 'jenis' => 'Wajib'],
        ['kode' => '11S3244', 'nama' => 'Keamanan Perangkat Lunak', 'sks' => 3, 'jenis' => 'Wajib'],
        ['kode' => '11S3237', 'nama' => 'Pengembangan Aplikasi Mobile (+P)', 'sks' => 4, 'jenis' => 'Wajib'],
        ['kode' => '11S3242', 'nama' => 'Pembelajaran Mesin (+P)', 'sks' => 3, 'jenis' => 'Wajib'],
        ['kode' => '11S3228', 'nama' => 'Proyek Perangkat Lunak (+P)', 'sks' => 4, 'jenis' => 'Wajib'],
        ['kode' => '11S3246', 'nama' => 'Sistem Paralel dan Terdistribusi (+P)', 'sks' => 3, 'jenis' => 'Wajib'],
    ],
    7 => [
        ['kode' => '11S4190', 'nama' => 'Kerja Praktik', 'sks' => 3, 'jenis' => 'Wajib'],
        ['kode' => '11S4093', 'nama' => 'Tugas Akhir 1', 'sks' => 4, 'jenis' => 'Wajib'],
        ['kode' => '11S4051', 'nama' => 'Pemrograman Berbasis Blok', 'sks' => 3, 'jenis' => 'Pilihan'],
        ['kode' => '11S4053', 'nama' => 'Pengembangan Aplikasi Platform Khusus', 'sks' => 3, 'jenis' => 'Pilihan'],
        ['kode' => '11S4055', 'nama' => 'Pengujian Otomasi', 'sks' => 3, 'jenis' => 'Pilihan'],
        ['kode' => '11S4056', 'nama' => 'Ethical Hacking dan Penetration Testing', 'sks' => 3, 'jenis' => 'Pilihan'],
        ['kode' => '11S4062', 'nama' => 'Pembelajaran Mesin Lanjut', 'sks' => 3, 'jenis' => 'Pilihan'],
        ['kode' => 'KUS3061', 'nama' => 'Magang 1', 'sks' => 1, 'jenis' => 'Pilihan'],
        ['kode' => 'KUS3062', 'nama' => 'Magang 2', 'sks' => 2, 'jenis' => 'Pilihan'],
        ['kode' => 'KUS3063', 'nama' => 'Magang 3', 'sks' => 3, 'jenis' => 'Pilihan'],
        ['kode' => 'KUS3064', 'nama' => 'Magang 4', 'sks' => 4, 'jenis' => 'Pilihan'],
        ['kode' => 'KUS3065', 'nama' => 'Magang 5', 'sks' => 5, 'jenis' => 'Pilihan'],
        ['kode' => 'KUS3066', 'nama' => 'Magang 6', 'sks' => 6, 'jenis' => 'Pilihan'],
        ['kode' => 'KUS3071', 'nama' => 'Penelitian 1', 'sks' => 1, 'jenis' => 'Pilihan'],
        ['kode' => 'KUS3072', 'nama' => 'Penelitian 2', 'sks' => 2, 'jenis' => 'Pilihan'],
        ['kode' => 'KUS3073', 'nama' => 'Penelitian 3', 'sks' => 3, 'jenis' => 'Pilihan'],
        ['kode' => 'KUS3074', 'nama' => 'Penelitian 4', 'sks' => 4, 'jenis' => 'Pilihan'],
        ['kode' => 'KUS3075', 'nama' => 'Penelitian 5', 'sks' => 5, 'jenis' => 'Pilihan'],
        ['kode' => 'KUS3076', 'nama' => 'Penelitian 6', 'sks' => 6, 'jenis' => 'Pilihan'],
    ],
    8 => [
        ['kode' => 'TIS0001', 'nama' => 'Keteknowiraan', 'sks' => 3, 'jenis' => 'Wajib'],
        ['kode' => '11S4094', 'nama' => 'Tugas Akhir 2', 'sks' => 4, 'jenis' => 'Wajib'],
        ['kode' => '11S4195', 'nama' => 'Kapita Selekta', 'sks' => 3, 'jenis' => 'Wajib'],
        ['kode' => '11S4052', 'nama' => 'Pembangunan Aplikasi Berbasis Service', 'sks' => 3, 'jenis' => 'Pilihan'],
        ['kode' => '11S4054', 'nama' => 'UI/UX Design', 'sks' => 3, 'jenis' => 'Pilihan'],
        ['kode' => '11S4057', 'nama' => 'Keamanan Jaringan Siber', 'sks' => 3, 'jenis' => 'Pilihan'],
        ['kode' => '11S4063', 'nama' => 'Kecerdasan Komputasional', 'sks' => 3, 'jenis' => 'Pilihan'],
        ['kode' => '11S4064', 'nama' => 'Visi Komputer', 'sks' => 3, 'jenis' => 'Pilihan'],
        ['kode' => 'KUS3051', 'nama' => 'Studi/Proyek Independen 1', 'sks' => 1, 'jenis' => 'Pilihan'],
        ['kode' => 'KUS3052', 'nama' => 'Studi/Proyek Independen 2', 'sks' => 2, 'jenis' => 'Pilihan'],
        ['kode' => 'KUS3053', 'nama' => 'Studi/Proyek Independen 3', 'sks' => 3, 'jenis' => 'Pilihan'],
        ['kode' => 'KUS3054', 'nama' => 'Studi/Proyek Independen 4', 'sks' => 4, 'jenis' => 'Pilihan'],
        ['kode' => 'KUS3055', 'nama' => 'Studi/Proyek Independen 5', 'sks' => 5, 'jenis' => 'Pilihan'],
        ['kode' => 'KUS3056', 'nama' => 'Studi/Proyek Independen 6', 'sks' => 6, 'jenis' => 'Pilihan'],
        ['kode' => 'KUS0203', 'nama' => 'Kewarganegaraan', 'sks' => 2, 'jenis' => 'Wajib'],
    ],
];

// Hitung total SKS (Hanya menghitung yang Wajib agar akurat sebagai syarat lulus, atau total semua)
$total_sks = 0;
$total_mk = 0;
foreach ($kurikulum_data as $semester => $mks) {
    foreach ($mks as $mk) {
        if ($mk['jenis'] === 'Wajib') {
            $total_sks += $mk['sks'];
        }
        $total_mk++;
    }
}
// Tambahkan asumsi SKS pilihan wajib (misal 14 SKS pilihan) untuk total 144 SKS standar Sarjana
$total_sks_lulus = $total_sks + 14; 
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
        .page-header-custom { background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: white; padding: 80px 0 100px; text-align: center;}
        .page-header-custom h1 { font-weight: 800; font-size: 38px; margin-bottom: 15px; }
        .page-header-custom p { color: #cbd5e1; font-size: 16px; max-width: 600px; margin: 0 auto;}

        /* Statistik Melayang */
        .stats-wrapper { margin-top: -50px; position: relative; z-index: 10; margin-bottom: 50px; }
        .stat-box { background: #fff; border-radius: 16px; padding: 30px 20px; text-align: center; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.08); border: 1px solid #e2e8f0; height: 100%; transition: 0.3s;}
        .stat-box:hover { transform: translateY(-5px); border-color: #38bdf8;}
        .stat-icon { width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 24px; margin: 0 auto 15px; color: white;}
        .stat-number { font-size: 32px; font-weight: 800; color: #0f172a; line-height: 1; margin-bottom: 5px; }
        .stat-label { font-size: 13px; color: #64748b; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;}

        /* Section Title */
        .section-title { text-align: center; margin-bottom: 40px; }
        .section-subtitle { color: #38bdf8; font-weight: 700; font-size: 13px; letter-spacing: 2px; text-transform: uppercase; margin-bottom: 10px; }
        .section-title h2 { color: #0f172a; font-weight: 800; font-size: 30px; }

        /* Card Capaian Pembelajaran */
        .outcome-card { background: #fff; border-radius: 12px; padding: 30px 25px; border: 1px solid #e2e8f0; height: 100%; transition: 0.3s;}
        .outcome-card:hover { box-shadow: 0 10px 20px rgba(0,0,0,0.05); transform: translateY(-3px);}
        .outcome-header { display: flex; align-items: center; gap: 15px; margin-bottom: 15px;}
        .outcome-icon { width: 45px; height: 45px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 20px; color: white;}
        .outcome-title { font-weight: 800; color: #0f172a; font-size: 18px; margin: 0;}
        .outcome-desc { color: #64748b; font-size: 14px; line-height: 1.7; margin: 0;}

        /* Modern Tabs / Pills */
        .nav-pills-custom { gap: 10px; margin-bottom: 30px; justify-content: center; }
        .nav-pills-custom .nav-link { background: #f1f5f9; color: #475569; border-radius: 30px; padding: 10px 25px; font-weight: 600; font-size: 14px; transition: all 0.3s; border: 1px solid #e2e8f0;}
        .nav-pills-custom .nav-link:hover { background: #e2e8f0; color: #0f172a;}
        .nav-pills-custom .nav-link.active { background: #0f172a; color: #ffffff; border-color: #0f172a; box-shadow: 0 4px 10px rgba(15, 23, 42, 0.2); transform: scale(1.05);}

        /* Table Kurikulum Premium */
        .table-wrapper { background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); border: 1px solid #e2e8f0;}
        .table-header-custom { background: #f8fafc; padding: 20px 25px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;}
        .table-header-custom h4 { margin: 0; font-weight: 800; font-size: 18px; color: #0f172a;}
        
        .table-custom { margin-bottom: 0; }
        .table-custom th { background: white; color: #64748b; font-weight: 600; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid #e2e8f0; padding: 15px 25px;}
        .table-custom td { padding: 15px 25px; vertical-align: middle; border-bottom: 1px solid #f1f5f9; color: #334155; font-size: 15px;}
        .table-custom tbody tr:hover { background-color: #f8fafc; }
        
        .mk-kode { background: #f0f9ff; color: #0284c7; padding: 5px 10px; border-radius: 6px; font-family: monospace; font-weight: 700; font-size: 13px; border: 1px solid #bae6fd;}
        .mk-nama { font-weight: 700; color: #0f172a; }
        .mk-sks { font-weight: 800; color: #475569; }
        
        .table-footer-custom { background: #f8fafc; border-top: 2px solid #e2e8f0; padding: 15px 25px; text-align: right; font-weight: 700; color: #0f172a; font-size: 15px;}
        .total-sks-highlight { color: #0284c7; font-size: 18px; margin-left: 15px; background: #e0f2fe; padding: 5px 15px; border-radius: 8px;}
    </style>
</head>
<body>

    <!-- NAVBAR KONSISTEN -->
    <?php @include 'includes/navbar.php'; ?>

    <!-- HEADER HALAMAN -->
    <div class="page-header-custom">
        <div class="container">
            <h1><i class="fas fa-book-open me-3" style="color:#38bdf8;"></i>Kurikulum Akademik</h1>
            <p>Panduan lengkap mata kuliah dan capaian pembelajaran Program Studi Informatika IT Del Tahun 2024.</p>
        </div>
    </div>

    <!-- STATISTIK MELAYANG -->
    <div class="container stats-wrapper">
        <div class="row justify-content-center g-4">
            <div class="col-lg-3 col-md-6 col-6">
                <div class="stat-box">
                    <div class="stat-icon" style="background: #3b82f6;"><i class="fas fa-graduation-cap"></i></div>
                    <div class="stat-number">146</div>
                    <div class="stat-label">SKS Lulus Minimum</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-6">
                <div class="stat-box">
                    <div class="stat-icon" style="background: #10b981;"><i class="fas fa-clock"></i></div>
                    <div class="stat-number">8</div>
                    <div class="stat-label">Semester Normal</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-6">
                <div class="stat-box">
                    <div class="stat-icon" style="background: #f59e0b;"><i class="fas fa-book"></i></div>
                    <div class="stat-number"><?= $total_mk ?></div>
                    <div class="stat-label">Total Mata Kuliah</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-6">
                <div class="stat-box">
                    <div class="stat-icon" style="background: #0ea5e9;"><i class="fas fa-certificate"></i></div>
                    <div class="stat-number">2024</div>
                    <div class="stat-label">Tahun Kurikulum</div>
                </div>
            </div>
        </div>
    </div>

    <div class="container mb-5 pb-5">
        
        <!-- CAPAIAN PEMBELAJARAN (LEARNING OUTCOMES) -->
        <div class="section-title">
            <div class="section-subtitle">LEARNING OUTCOMES</div>
            <h2>Capaian Pembelajaran Lulusan</h2>
        </div>
        
        <div class="row g-4 mb-5 pb-4">
            <div class="col-lg-3 col-md-6">
                <div class="outcome-card">
                    <div class="outcome-header">
                        <div class="outcome-icon" style="background: #3b82f6;"><i class="fas fa-brain"></i></div>
                        <h3 class="outcome-title">Sikap</h3>
                    </div>
                    <p class="outcome-desc">Bertakwa kepada Tuhan YME, beretika profesional, dan memiliki jiwa kepemimpinan.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="outcome-card">
                    <div class="outcome-header">
                        <div class="outcome-icon" style="background: #10b981;"><i class="fas fa-lightbulb"></i></div>
                        <h3 class="outcome-title">Pengetahuan</h3>
                    </div>
                    <p class="outcome-desc">Menguasai konsep teoritis informatika, algoritma, dan teknologi terkini secara mendalam.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="outcome-card">
                    <div class="outcome-header">
                        <div class="outcome-icon" style="background: #f59e0b;"><i class="fas fa-tools"></i></div>
                        <h3 class="outcome-title">Umum</h3>
                    </div>
                    <p class="outcome-desc">Mampu berpikir kritis, berkomunikasi efektif, dan bekerja dalam tim multidisiplin.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="outcome-card">
                    <div class="outcome-header">
                        <div class="outcome-icon" style="background: #0ea5e9;"><i class="fas fa-code"></i></div>
                        <h3 class="outcome-title">Khusus</h3>
                    </div>
                    <p class="outcome-desc">Mampu merancang, mengembangkan, dan mengimplementasikan sistem informatika.</p>
                </div>
            </div>
        </div>

        <!-- STRUKTUR KURIKULUM (TABS & TABEL) -->
        <div class="section-title">
            <div class="section-subtitle">STRUKTUR KURIKULUM 2024</div>
            <h2>Mata Kuliah Per Semester</h2>
        </div>
        
        <!-- Tab Navigation (Pills) -->
        <ul class="nav nav-pills nav-pills-custom" id="semesterTabs" role="tablist">
            <?php foreach ($kurikulum_data as $semester => $mks): ?>
            <li class="nav-item" role="presentation">
                <button class="nav-link <?= $semester === 1 ? 'active' : '' ?>" 
                        id="tab-sem-<?= $semester ?>" 
                        data-bs-toggle="pill" 
                        data-bs-target="#content-sem-<?= $semester ?>" 
                        type="button" role="tab">
                    Semester <?= $semester ?>
                </button>
            </li>
            <?php endforeach; ?>
        </ul>
        
        <!-- Tab Content (Tabel Bersih Tanpa Kolom Jenis) -->
        <div class="tab-content" id="semesterTabsContent">
            <?php foreach ($kurikulum_data as $semester => $mks): 
                // Hitung total SKS di semester ini (Gabungan wajib & pilihan yang ada di array)
                $total_sks_semester = array_sum(array_column($mks, 'sks'));
            ?>
            <div class="tab-pane fade <?= $semester === 1 ? 'show active' : '' ?>" id="content-sem-<?= $semester ?>" role="tabpanel">
                <div class="table-wrapper">
                    
                    <div class="table-header-custom">
                        <h4>Mata Kuliah Semester <?= $semester ?></h4>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-custom">
                            <thead>
                                <tr>
                                    <th width="15%">Kode MK</th>
                                    <th>Nama Mata Kuliah</th>
                                    <th width="15%" class="text-center">SKS</th>
                                    <!-- Kolom 'Jenis' DIHAPUS sesuai permintaan -->
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($mks as $mk): ?>
                                <tr>
                                    <td><span class="mk-kode"><?= $mk['kode'] ?></span></td>
                                    <td class="mk-nama"><?= $mk['nama'] ?></td>
                                    <td class="text-center mk-sks"><?= $mk['sks'] ?></td>
                                    <!-- Kolom badge 'Wajib/Pilihan' DIHAPUS -->
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Footer Tabel (Penjumlahan) -->
                    <div class="table-footer-custom">
                        Total Beban Studi: <span class="total-sks-highlight"><?= $total_sks_semester ?> SKS</span>
                    </div>
                    
                </div>
            </div>
            <?php endforeach; ?>
        </div>

    </div>

    <!-- CTA DOWNLOAD KURIKULUM -->
    <section class="py-5" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: white;">
        <div class="container text-center py-4">
            <h2 class="mb-4 fw-bold">Download Dokumen Kurikulum Lengkap</h2>
            <p class="lead mb-4" style="opacity: 0.9; max-width: 700px; margin: 0 auto;">Dapatkan dokumen resmi kurikulum Program Studi Informatika dalam format PDF untuk referensi penyusunan rencana studi Anda.</p>
            <div class="mt-4">
                <a href="#" class="btn btn-light btn-lg fw-bold rounded-pill px-5 shadow text-dark">
                    <i class="fas fa-file-pdf me-2 text-danger"></i> Download PDF
                </a>
            </div>
        </div>
    </section>

    <!-- FOOTER SEDERHANA -->
    <footer style="background: #0b1120; padding: 30px 0; color: #94a3b8; text-align: center; font-size: 14px;">
        <div class="container">
            © 2026 HIMASTI IT Del. All Rights Reserved.
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>