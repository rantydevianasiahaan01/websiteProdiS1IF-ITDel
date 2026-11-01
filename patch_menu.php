<?php
// patch_menu.php - Skrip Sapu Jagat untuk mengganti Navbar otomatis
echo "<div style='font-family: sans-serif; padding: 20px;'>";
echo "<h2>🛠️ Memproses Pembaruan Menu Otomatis...</h2><hr><ul>";

$files = glob("*.php"); // Ambil semua file PHP di folder ini
$berhasil = 0;

foreach($files as $file) {
    $content = file_get_contents($file);
    
    // Lewati file ini sendiri, atau file yang memang sudah pakai include
    if($file === 'patch_menu.php' || strpos($content, "includes/navbar.php") !== false) {
        continue;
    }
    
    // Cari blok <nav class="navbar ... sampai </nav> lalu ganti dengan include
    $pattern = '/<nav class="navbar.*?<\/nav>/is';
    $replacement = "<?php @include 'includes/navbar.php'; ?>";
    
    $new_content = preg_replace($pattern, $replacement, $content);
    
    // Jika berhasil diganti, simpan file-nya
    if($new_content !== null && $new_content !== $content) {
        file_put_contents($file, $new_content);
        echo "<li style='color: green; margin-bottom: 5px;'>✅ File <b>$file</b> berhasil diperbarui!</li>";
        $berhasil++;
    }
}

echo "</ul>";
echo "<h3 style='color: blue;'>🎉 Selesai! $berhasil file telah sukses dipasangi sistem menu otomatis.</h3>";
echo "<p>Sekarang kamu bisa menghapus file patch_menu.php ini.</p>";
echo "</div>";
?>