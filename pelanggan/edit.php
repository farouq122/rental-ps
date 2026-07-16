<?php
require_once __DIR__ . '/../includes/header.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$stmt = $pdo->prepare("SELECT * FROM pelanggan WHERE id_pelanggan = ?");
$stmt->execute([$id]);
$cust = $stmt->fetch();

if (!$cust) {
    header('Location: ' . BASE_URL . '/pelanggan/list.php');
    exit;
}

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama   = trim($_POST['nama']);
    $no_hp  = trim($_POST['no_hp']);
    $alamat = trim($_POST['alamat']);

    if (empty($nama) || empty($no_hp) || empty($alamat)) {
        $error = 'Semua field wajib diisi!';
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE pelanggan SET nama = ?, no_hp = ?, alamat = ? WHERE id_pelanggan = ?");
            $stmt->execute([$nama, $no_hp, $alamat, $id]);
            $success = 'Data pelanggan berhasil diperbarui!';
            $stmt = $pdo->prepare("SELECT * FROM pelanggan WHERE id_pelanggan = ?");
            $stmt->execute([$id]);
            $cust = $stmt->fetch();
        } catch (\PDOException $e) {
            $error = 'Gagal memperbarui data: ' . $e->getMessage();
        }
    }
}
?>

<div class="row justify-content-center">
    <div class="col-12 col-md-7 col-lg-5">
        <div class="d-flex align-items-center mb-4">
            <a href="<?= BASE_URL ?>/pelanggan/list.php" class="btn btn-sm btn-outline-secondary me-3">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
            <h4 class="mb-0 fw-bold">Edit Data Pelanggan</h4>
        </div>

        <div class="card p-4">
            <?php if ($error): ?>
                <div class="alert alert-danger d-flex align-items-center gap-2">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <div><?= htmlspecialchars($error) ?></div>
                </div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success d-flex align-items-center gap-2">
                    <i class="bi bi-check-circle-fill"></i>
                    <div><?= htmlspecialchars($success) ?></div>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="mb-3">
                    <label for="nama" class="form-label fw-semibold">Nama Lengkap</label>
                    <input type="text" class="form-control" id="nama" name="nama"
                           value="<?= htmlspecialchars($cust['nama']) ?>" required>
                </div>
                <div class="mb-3">
                    <label for="no_hp" class="form-label fw-semibold">Nomor HP</label>
                    <input type="text" class="form-control" id="no_hp" name="no_hp"
                           value="<?= htmlspecialchars($cust['no_hp']) ?>" required>
                </div>
                <div class="mb-4">
                    <label for="alamat" class="form-label fw-semibold">Alamat</label>
                    <textarea class="form-control" id="alamat" name="alamat" rows="3" required><?= htmlspecialchars($cust['alamat']) ?></textarea>
                </div>
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-save me-1"></i> Simpan Perubahan
                </button>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
