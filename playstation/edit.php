<?php
require_once __DIR__ . '/../includes/header.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$stmt = $pdo->prepare("SELECT * FROM playstation WHERE id_ps = ?");
$stmt->execute([$id]);
$ps = $stmt->fetch();

if (!$ps) {
    header('Location: ' . BASE_URL . '/playstation/list.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_ps      = trim($_POST['nama_ps']);
    $tipe_ps      = trim($_POST['tipe_ps']);
    $tarif_per_jam = intval($_POST['tarif_per_jam']);

    if (empty($nama_ps) || empty($tipe_ps) || $tarif_per_jam <= 0) {
        $error = 'Semua field wajib diisi dengan benar!';
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE playstation SET nama_ps = ?, tipe_ps = ?, tarif_per_jam = ? WHERE id_ps = ?");
            $stmt->execute([$nama_ps, $tipe_ps, $tarif_per_jam, $id]);
            $success = 'Data PlayStation berhasil diperbarui!';
            $stmt = $pdo->prepare("SELECT * FROM playstation WHERE id_ps = ?");
            $stmt->execute([$id]);
            $ps = $stmt->fetch();
        } catch (\PDOException $e) {
            $error = 'Gagal memperbarui data: ' . $e->getMessage();
        }
    }
}
?>

<div class="row justify-content-center">
    <div class="col-12 col-md-7 col-lg-5">
        <div class="d-flex align-items-center mb-4">
            <a href="<?= BASE_URL ?>/playstation/list.php" class="btn btn-sm btn-outline-secondary me-3">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
            <h4 class="mb-0 fw-bold">Edit PlayStation</h4>
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
                    <label for="nama_ps" class="form-label fw-semibold">Nama Unit</label>
                    <input type="text" class="form-control" id="nama_ps" name="nama_ps"
                           value="<?= htmlspecialchars($ps['nama_ps']) ?>" required>
                </div>
                <div class="mb-3">
                    <label for="tipe_ps" class="form-label fw-semibold">Tipe PlayStation</label>
                    <select class="form-select" id="tipe_ps" name="tipe_ps" required>
                        <option value="PS3" <?= $ps['tipe_ps'] === 'PS3' ? 'selected' : '' ?>>PS3</option>
                        <option value="PS4" <?= $ps['tipe_ps'] === 'PS4' ? 'selected' : '' ?>>PS4</option>
                        <option value="PS5" <?= $ps['tipe_ps'] === 'PS5' ? 'selected' : '' ?>>PS5</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="tarif_per_jam" class="form-label fw-semibold">Tarif Per Jam (Rupiah)</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="number" class="form-control" id="tarif_per_jam" name="tarif_per_jam"
                               min="100" step="500" value="<?= htmlspecialchars($ps['tarif_per_jam']) ?>" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-save me-1"></i> Simpan Perubahan
                </button>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
