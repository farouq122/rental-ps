<?php
require_once __DIR__ . '/../includes/helper.php';
require_once __DIR__ . '/../config/db.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
    try {
        $stmt = $pdo->prepare("UPDATE sewa SET waktu_selesai = NOW() WHERE id_sewa = ?");
        $stmt->execute([$id]);
    } catch (\PDOException $e) {
        // Redirect tetap berjalan walau error
    }
}

header('Location: ' . BASE_URL . '/sewa/list.php');
exit;
?>
