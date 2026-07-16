<?php
// ── Load config only (no HTML output yet) so we can redirect if needed ──
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/helper.php';

$preselected_ps = isset($_GET['id_ps']) ? intval($_GET['id_ps']) : 0;

// Hanya PS yang tersedia (tidak sedang disewa)
$ps_query = "
    SELECT p.* FROM playstation p
    WHERE p.id_ps NOT IN (
        SELECT s.id_ps FROM sewa s WHERE NOW() < s.waktu_selesai
    )
    ORDER BY p.id_ps ASC
";
$available_ps = $pdo->query($ps_query)->fetchAll();

// Semua pelanggan
$customers = $pdo->query("SELECT * FROM pelanggan ORDER BY nama ASC")->fetchAll();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_ps        = intval($_POST['id_ps']);
    $id_pelanggan = intval($_POST['id_pelanggan']);
    $durasi_jam   = intval($_POST['durasi_jam']);

    // Cek apakah PS sudah disewa (double booking)
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM sewa WHERE id_ps = ? AND NOW() < waktu_selesai");
    $stmt->execute([$id_ps]);
    $is_rented = $stmt->fetch()['total'] > 0;

    if ($id_ps <= 0 || $id_pelanggan <= 0 || $durasi_jam <= 0) {
        $error = 'Semua field wajib diisi dengan benar!';
    } elseif ($is_rented) {
        $error = 'PlayStation yang dipilih sedang disewa oleh pelanggan lain!';
    } else {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO sewa (id_ps, id_pelanggan, waktu_mulai, durasi_jam, waktu_selesai)
                VALUES (?, ?, NOW(), ?, DATE_ADD(NOW(), INTERVAL ? HOUR))
            ");
            $stmt->execute([$id_ps, $id_pelanggan, $durasi_jam, $durasi_jam]);
            // Redirect BEFORE any HTML is sent
            header('Location: ' . BASE_URL . '/sewa/list.php');
            exit;
        } catch (\PDOException $e) {
            $error = 'Gagal menyimpan transaksi: ' . $e->getMessage();
        }
    }
}

// ── Now it is safe to output HTML ──
require_once __DIR__ . '/../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-6">
        <div class="d-flex align-items-center mb-4">
            <a href="<?= BASE_URL ?>/sewa/list.php" class="btn btn-sm btn-outline-secondary me-3">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
            <h4 class="mb-0 fw-bold">Mulai Sewa Baru</h4>
        </div>

        <?php if (count($available_ps) === 0): ?>
            <div class="alert alert-warning d-flex align-items-center gap-2">
                <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                <div><strong>Semua unit PlayStation sedang disewa!</strong> Tidak ada unit yang tersedia saat ini.</div>
            </div>
        <?php endif; ?>

        <div class="card p-4">
            <?php if ($error): ?>
                <div class="alert alert-danger d-flex align-items-center gap-2">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <div><?= htmlspecialchars($error) ?></div>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <!-- Pilih Pelanggan -->
                <div class="mb-3">
                    <label for="id_pelanggan" class="form-label fw-semibold">Pilih Pelanggan</label>
                    <select class="form-select" id="id_pelanggan" name="id_pelanggan" required>
                        <option value="">-- Pilih Pelanggan --</option>
                        <?php foreach ($customers as $cust): ?>
                            <option value="<?= $cust['id_pelanggan'] ?>">
                                <?= htmlspecialchars($cust['nama']) ?> (<?= htmlspecialchars($cust['no_hp']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="form-text">
                        Belum terdaftar? <a href="<?= BASE_URL ?>/pelanggan/tambah.php" class="text-decoration-none">Tambah Pelanggan Baru</a>
                    </div>
                </div>

                <!-- Pilih PlayStation -->
                <div class="mb-3">
                    <label for="id_ps" class="form-label fw-semibold">Pilih PlayStation <span class="text-success small">(Hanya yang Tersedia)</span></label>
                    <select class="form-select" id="id_ps" name="id_ps" required <?= count($available_ps) === 0 ? 'disabled' : '' ?>>
                        <option value="">-- Pilih Unit PlayStation --</option>
                        <?php foreach ($available_ps as $ps): ?>
                            <option value="<?= $ps['id_ps'] ?>" <?= ($preselected_ps === $ps['id_ps']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($ps['nama_ps']) ?> [<?= $ps['tipe_ps'] ?>] — <?= format_rupiah($ps['tarif_per_jam']) ?>/jam
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Durasi -->
                <div class="mb-4">
                    <label for="durasi_jam" class="form-label fw-semibold">Durasi Sewa (Jam)</label>
                    <div class="input-group">
                        <input type="number" class="form-control" id="durasi_jam" name="durasi_jam"
                               min="1" max="24" value="1" required>
                        <span class="input-group-text">Jam</span>
                    </div>
                    <div class="form-text">Masukkan jumlah jam (bilangan bulat, min 1).</div>
                </div>

                <button type="submit" class="btn btn-primary w-100" <?= count($available_ps) === 0 ? 'disabled' : '' ?>>
                    <i class="bi bi-play-circle me-1"></i> Mulai Rental
                </button>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
