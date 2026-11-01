
# Website Prodi Informatika IT Del

Website portal akademik untuk Program Studi Informatika, Institut Teknologi Del.

## 📋 Fitur

### Frontend (Publik)
- ✅ Homepage dengan hero section, statistik, berita, dan agenda
- ✅ Navigasi responsif dengan top bar
- ✅ Tampilan berita dan agenda terbaru
- ✅ Footer lengkap dengan informasi kontak
- ✅ Desain modern dan profesional

### Admin Dashboard
- ✅ Login & Logout dengan autentikasi
- ✅ Dashboard dengan statistik
- ✅ CRUD Berita (Create, Read, Update, Delete)
- ✅ Rich Text Editor (TinyMCE) untuk konten berita
- ✅ Upload gambar untuk berita
- ✅ Manajemen kategori berita
- ✅ Status publikasi (Draft, Published, Archived)

## 🚀 Instalasi

### Prasyarat
- XAMPP (PHP 7.4 atau lebih baru)
- MySQL/MariaDB
- Browser modern (Chrome, Firefox, Edge)

### Langkah-langkah

1. **Clone atau Download Repository**
   ```bash
   cd /xampp/htdocs
   git clone <repository-url> web-informatika-itdel
   ```

2. **Import Database**
   - Buka phpMyAdmin (http://localhost/phpmyadmin)
   - Buat database baru bernama `informatika_itdel`
   - Import file SQL dari folder `database/` (jika ada)
   - Atau jalankan script SQL yang disediakan di deskripsi

3. **Konfigurasi Database**
   - File konfigurasi sudah ada di `config/database.php`
   - Sesuaikan jika perlu:
     ```php
     $host = 'localhost';
     $dbname = 'informatika_itdel';
     $username = 'root';
     $password = '';
     ```

4. **Setup User Admin (Opsional)**
   Jalankan query berikut di phpMyAdmin untuk membuat user admin default:
   ```sql
   INSERT INTO users (name, email, password, role, is_active) VALUES
   ('Administrator', 'admin@informatika.del.ac.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'super_admin', 1),
   ('Humas', 'humas@informatika.del.ac.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'editor', 1);
   ```

   **Password default**: `admin123` (untuk kedua user)

5. **Akses Website**
   - Frontend: http://localhost/web-informatika-itdel/
   - Admin: http://localhost/web-informatika-itdel/admin/login.php

## 📁 Struktur Folder

```
web-informatika-itdel/
│
├── index.php                 # Homepage
├── config/
│   ├── database.php          # Konfigurasi database
│   └── auth.php              # Fungsi autentikasi
│
├── includes/
│   ├── header.php            # Header HTML
│   ├── navbar.php            # Navigation bar
│   ├── footer.php            # Footer
│   └── functions.php         # Fungsi helper
│
├── admin/
│   ├── login.php             # Halaman login admin
│   ├── dashboard.php         # Dashboard admin
│   ├── cek-login.php         # Cek sesi login
│   └── berita/
│       ├── index.php         # Daftar berita
│       ├── tambah.php        # Tambah berita
│       └── edit.php          # Edit berita
│
├── assets/
│   ├── css/
│   │   └── style.css         # Custom CSS
│   ├── js/
│   │   └── main.js           # Custom JavaScript
│   ├── img/                  # Gambar statis
│   └── uploads/              # Gambar upload (berita, dll)
│
└── database/
    └── informatika_itdel.sql # Script SQL database
```

## 🎨 Tema & Desain

- **Warna Utama**: Biru tua (#1a2a3a) dengan aksen toska (#4a90a4)
- **Framework CSS**: Bootstrap 5.3
- **Icons**: Font Awesome 6
- **Font**: Poppins (Google Fonts)
- **Rich Text Editor**: TinyMCE 6

## 🔐 Keamanan

- Password di-hash menggunakan `password_hash()` PHP
- Prepared statements untuk mencegah SQL Injection
- Session management untuk autentikasi
- Validasi input pada form
- File upload validation (tipe dan ukuran)

## 📝 Pengembangan Selanjutnya

Berikut fitur yang dapat ditambahkan:

- [ ] Halaman Profil Prodi
- [ ] Halaman Dosen
- [ ] Halaman Kurikulum
- [ ] CRUD Agenda/Kegiatan
- [ ] CRUD Galeri Foto
- [ ] CRUD Alumni
- [ ] CRUD Dosen
- [ ] Halaman Kontak dengan form
- [ ] Search functionality
- [ ] Pagination untuk berita
- [ ] Export data ke Excel/PDF
- [ ] Multi-language support

## 👥 Kontributor

Silakan berkontribusi dengan membuat pull request atau melaporkan issue.

## 📄 License

Project ini dibuat untuk tujuan pembelajaran dan pengembangan website Prodi Informatika IT Del.

---

## Cara Menjalankan (Lokal)
1. Instal XAMPP, lalu start Apache dan MySQL.
2. Clone repo ke `C:\xampp\htdocs\` dengan nama folder `WEB-HIMASTI`.
3. Buka http://localhost/phpmyadmin, buat database `informatika_itdel`, lalu Import file `database/informatika_itdel.sql`.
4. Buka http://localhost/WEB-HIMASTI/
5. Login admin: http://localhost/WEB-HIMASTI/admin/login.php
