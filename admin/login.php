<?php
// admin/login.php
// Halaman login & logout untuk Admin dan Editor - Tema HIMASTI

// Mulai session
session_start();

// ==========================================
// LOGOUT LOGIC (Jika ada parameter ?logout=1)
// ==========================================
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit;
}

// Jika sudah login, langsung ke dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

// Load konfigurasi database
require_once '../config/database.php';

// INISIALISASI $error DI AWAL
$error = '';

// Proses login ketika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // UBAH: Ambil data dari input 'username' (bukan email)
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    // Validasi input
    if (empty($username) || empty($password)) {
        $error = "Username dan password harus diisi!";
    } else {
        try {
            // UBAH: Cari user berdasarkan kolom 'username' di database
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND is_active = 1");
            $stmt->execute([$username]);
            $user = $stmt->fetch();
            
            // Verifikasi password
            if ($user && password_verify($password, $user['password'])) {
                // Login berhasil - simpan data ke session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_username'] = $user['username']; // Simpan username, bukan email
                $_SESSION['role'] = $user['role'];
                $_SESSION['login_time'] = time();
                
                // Update last_login di database
                $update_stmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
                $update_stmt->execute([$user['id']]);
                
                // Redirect ke dashboard
                header("Location: dashboard.php");
                exit;
            } else {
                $error = "Username atau password salah!";
            }
        } catch (PDOException $e) {
            $error = "Terjadi kesalahan sistem: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - HIMASTI IT Del</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        :root {
            --himasti-navy-dark: #1a2a3a;
            --himasti-navy-medium: #2c3e50;
            --himasti-navy-light: #3d5a73;
            --himasti-accent: #4a90a4;
            --himasti-bg-light: #f4f6f8;
            --himasti-white: #ffffff;
            --himasti-text-light: #7f8c8d;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1a2a3a 0%, #2c3e50 50%, #3d5a73 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        .login-wrapper {
            display: flex;
            max-width: 1000px;
            width: 100%;
            background: var(--himasti-white);
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        
        /* Left Side - Logo */
        .login-branding {
            flex: 1;
            background: linear-gradient(135deg, var(--himasti-navy-dark) 0%, var(--himasti-navy-medium) 100%);
            padding: 60px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
            text-align: center;
        }
        
        .logo-container {
            margin-bottom: 30px;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        
        .logo-image {
            max-width: 250px;
            height: auto;
            display: block;
        }
        
        .branding-title {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 10px;
            letter-spacing: 2px;
        }
        
        .branding-subtitle {
            font-size: 14px;
            color: #b0bec5;
            line-height: 1.6;
        }
        
        /* Right Side - Form */
        .login-form-container {
            flex: 1;
            padding: 60px 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .login-header {
            margin-bottom: 30px;
        }
        
        .login-header h1 {
            font-size: 28px;
            color: var(--himasti-navy-dark);
            margin-bottom: 8px;
            font-weight: 700;
        }
        
        .login-header p {
            color: var(--himasti-text-light);
            font-size: 14px;
        }
        
        .info-box {
            background: var(--himasti-bg-light);
            border-left: 4px solid var(--himasti-navy-dark);
            padding: 15px;
            margin-bottom: 25px;
            border-radius: 6px;
        }
        
        .info-box p {
            font-size: 13px;
            color: #666;
            margin: 4px 0;
            font-family: 'Courier New', monospace;
        }
        
        .info-box strong {
            color: var(--himasti-navy-dark);
            font-family: 'Segoe UI', sans-serif;
            font-weight: 600;
        }
        
        .error-message {
            background: #fee;
            border-left: 4px solid #c0392b;
            color: #c0392b;
            padding: 12px 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: var(--himasti-navy-dark);
            font-weight: 600;
            font-size: 14px;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s;
            background: var(--himasti-bg-light);
        }
        
        .form-group input:focus {
            outline: none;
            border-color: var(--himasti-navy-dark);
            box-shadow: 0 0 0 4px rgba(26, 42, 58, 0.1);
            background: var(--himasti-white);
        }
        
        .btn-login {
            width: 100%;
            padding: 14px;
            background: var(--himasti-navy-dark);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 10px;
        }
        
        .btn-login:hover {
            background: var(--himasti-navy-medium);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(26, 42, 58, 0.3);
        }
        
        .btn-login:active {
            transform: translateY(0);
        }
        
        .login-footer {
            text-align: center;
            margin-top: 25px;
            padding-top: 25px;
            border-top: 1px solid #e0e0e0;
        }
        
        .login-footer a {
            color: var(--himasti-navy-dark);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: color 0.3s;
        }
        
        .login-footer a:hover {
            color: var(--himasti-accent);
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .login-wrapper {
                flex-direction: column;
            }
            
            .login-branding {
                padding: 40px 30px;
            }
            
            .logo-image {
                max-width: 200px;
            }
            
            .branding-title {
                font-size: 24px;
            }
            
            .login-form-container {
                padding: 40px 30px;
            }
        }
        
        @media (max-width: 480px) {
            body {
                padding: 10px;
            }
            
            .login-branding {
                padding: 30px 20px;
            }
            
            .logo-image {
                max-width: 150px;
            }
            
            .login-form-container {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <!-- Left Side - Branding -->
        <div class="login-branding">
            <div class="logo-container">
                <img src="../assets/logo-himasti.jpeg" alt="Logo HIMASTI" class="logo-image">
            </div>
            <h2 class="branding-title">HIMASTI</h2>
            <p class="branding-subtitle">Himpunan Mahasiswa Sarjana Informatika<br>Institut Teknologi Del</p>
        </div>
        
        <!-- Right Side - Login Form -->
        <div class="login-form-container">
            <div class="login-header">
                <h1>Selamat Datang</h1>
                <p>Silakan login untuk mengakses dashboard</p>
            </div>
            
            <div class="info-box">
                <!-- UBAH Teks Info jadi Username -->
                <p><strong>👤 Admin:</strong> admin / admin123</p>
                <p><strong>👤 Editor:</strong> humas / editor123</p>
            </div>
            
            <?php if (!empty($error)): ?>
                <div class="error-message">
                    ⚠️ <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <!-- UBAH Form Email jadi Username -->
                    <label for="username">Username Akun</label>
                    <input 
                        type="text" 
                        id="username" 
                        name="username" 
                        placeholder="Contoh: admin atau admin@himasti.del.ac.id" 
                        value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>"
                        required 
                        autofocus
                    >
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        placeholder="••••••••" 
                        required
                    >
                </div>
                
                <button type="submit" class="btn-login">
                    🔐 Masuk ke Dashboard
                </button>
            </form>
            
            <div class="login-footer">
                <a href="../index.php">← Kembali ke Website Publik</a>
            </div>
        </div>
    </div>
</body>
</html>