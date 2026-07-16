<?php
// ── Proses POST SEBELUM output HTML apapun ──
require_once __DIR__ . '/../includes/helper.php';
require_once __DIR__ . '/../config/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama   = trim($_POST['nama']   ?? '');
    $no_hp  = trim($_POST['no_hp']  ?? '');
    $alamat = trim($_POST['alamat'] ?? '');

    if (empty($nama) || empty($no_hp) || empty($alamat)) {
        $error = 'Semua field wajib diisi!';
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO pelanggan (nama, no_hp, alamat) VALUES (?, ?, ?)");
            $stmt->execute([$nama, $no_hp, $alamat]);
            // Redirect sebelum HTML dikirim
            header('Location: ' . BASE_URL . '/pelanggan/list.php');
            exit;
        } catch (\PDOException $e) {
            $error = 'Gagal menyimpan data: ' . $e->getMessage();
        }
    }
}

// ── Baru tampilkan HTML setelah logika selesai ──
require_once __DIR__ . '/../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-12 col-md-7 col-lg-5">
        <div class="d-flex align-items-center mb-4">
            <a href="<?= BASE_URL ?>/pelanggan/list.php" class="btn btn-sm btn-outline-secondary me-3">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
            <h4 class="mb-0 fw-bold">Tambah Pelanggan</h4>
        </div>

        <div class="card p-4">
            <?php if ($error): ?>
                <div class="alert alert-danger d-flex align-items-center gap-2">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <div><?= htmlspecialchars($error) ?></div>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="mb-3">
                    <label for="nama" class="form-label fw-semibold">Nama Lengkap</label>
                    <input type="text" class="form-control" id="nama" name="nama"
                           placeholder="Masukkan nama pelanggan" required
                           value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>">
                </div>
                <div class="mb-3">
                    <label for="no_hp" class="form-label fw-semibold">Nomor HP</label>
                    <input type="text" class="form-control" id="no_hp" name="no_hp"
                           placeholder="Contoh: 081234567890" required
                           value="<?= htmlspecialchars($_POST['no_hp'] ?? '') ?>">
                </div>
                <div class="mb-4">
                    <label for="alamat" class="form-label fw-semibold">Alamat</label>
                    <textarea class="form-control" id="alamat" name="alamat" rows="3"
                              placeholder="Masukkan alamat pelanggan" required><?= htmlspecialchars($_POST['alamat'] ?? '') ?></textarea>
                </div>
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-person-plus me-1"></i> Simpan Pelanggan
                </button>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
