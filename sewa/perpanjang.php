<?php
require_once __DIR__ . '/../includes/helper.php';
require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_sewa         = isset($_POST['id_sewa'])         ? intval($_POST['id_sewa'])         : 0;
    $durasi_tambahan = isset($_POST['durasi_tambahan']) ? intval($_POST['durasi_tambahan']) : 0;

    if ($id_sewa > 0 && $durasi_tambahan > 0) {
        try {
            // Ambil rental aktif
            $stmt = $pdo->prepare("SELECT durasi_jam FROM sewa WHERE id_sewa = ? AND NOW() < waktu_selesai");
            $stmt->execute([$id_sewa]);
            $rental = $stmt->fetch();

            if ($rental) {
                $durasi_baru = $rental['durasi_jam'] + $durasi_tambahan;
                $stmt = $pdo->prepare("
                    UPDATE sewa
                    SET durasi_jam = ?,
                        waktu_selesai = DATE_ADD(waktu_selesai, INTERVAL ? HOUR)
                    WHERE id_sewa = ?
                ");
                $stmt->execute([$durasi_baru, $durasi_tambahan, $id_sewa]);
            }
        } catch (\PDOException $e) {
            // Redirect tetap berjalan
        }
    }
}

header('Location: ' . BASE_URL . '/sewa/list.php');
exit;
?>
