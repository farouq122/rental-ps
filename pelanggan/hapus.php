<?php
require_once __DIR__ . '/../includes/helper.php';
require_once __DIR__ . '/../config/db.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
    try {
        $stmt = $pdo->prepare("DELETE FROM pelanggan WHERE id_pelanggan = ?");
        $stmt->execute([$id]);
    } catch (\PDOException $e) {
        // cascade delete handled by MySQL
    }
}

header('Location: ' . BASE_URL . '/pelanggan/list.php');
exit;
?>
