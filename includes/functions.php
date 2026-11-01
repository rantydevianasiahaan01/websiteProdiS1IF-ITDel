
<?php
// includes/functions.php
// Fungsi-fungsi helper untuk website publik

// Format tanggal ke bahasa Indonesia
function format_tanggal_indonesia($tanggal) {
    $bulan = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];

    if (empty($tanggal)) return '';

    $tgl = date('j', strtotime($tanggal));
    $bln = $bulan[date('n', strtotime($tanggal))];
    $thn = date('Y', strtotime($tanggal));

    return "$tgl $bln $thn";
}

// Format datetime dengan jam
function format_datetime_indonesia($datetime) {
    if (empty($datetime)) return '';

    $tanggal = format_tanggal_indonesia($datetime);
    $jam = date('H:i', strtotime($datetime));

    return "$tanggal, $jam WIB";
}

// Potong teks dengan panjang tertentu
function potong_teks($teks, $panjang = 100) {
    if (strlen($teks) <= $panjang) return $teks;

    return substr($teks, 0, $panjang) . '...';
}

// Ambil waktu relatif (contoh: "2 hari yang lalu")
function waktu_relatif($waktu) {
    if (empty($waktu)) return '';

    $now = time();
    $time = strtotime($waktu);
    $diff = $now - $time;

    if ($diff < 60) return 'Baru saja';
    if ($diff < 3600) return floor($diff / 60) . ' menit yang lalu';
    if ($diff < 86400) return floor($diff / 3600) . ' jam yang lalu';
    if ($diff < 604800) return floor($diff / 86400) . ' hari yang lalu';

    return format_tanggal_indonesia($waktu);
}

