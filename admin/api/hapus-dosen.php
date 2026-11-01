<?php
// admin/api/hapus_dosen.php
require_once __DIR__ . '/../cek-login.php';
require_once __DIR__ . '/../../config/database.php';

header('Content-Type: application/json');

$id = $_GET['id'] ?? 0;

try {
    $stmt = $pdo->prepare("SELECT photo FROM lecturers WHERE id = ?");
    $stmt->execute([$id]);
    $dosen = $stmt->fetch();
    
    if ($dosen) {
        if (!empty($dosen['photo']) && file_exists(__DIR__ . '/../../assets/uploads/' . $dosen['photo'])) {
            unlink(__DIR__ . '/../../assets/uploads/' . $dosen['photo']);
        }
        $pdo->prepare("DELETE FROM lecturers WHERE id = ?")->execute([$id]);
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Data tidak ditemukan']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>