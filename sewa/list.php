<?php
require_once __DIR__ . '/../includes/header.php';

$query = "
    SELECT 
        s.*,
        p.nama_ps, p.tipe_ps, p.tarif_per_jam,
        pl.nama as nama_pelanggan, pl.no_hp
    FROM sewa s
    JOIN playstation p ON s.id_ps = p.id_ps
    JOIN pelanggan pl ON s.id_pelanggan = pl.id_pelanggan
    WHERE NOW() < s.waktu_selesai
    ORDER BY s.waktu_selesai ASC
";
$active_rentals = $pdo->query($query)->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 fw-bold"><i class="bi bi-hourglass-split me-2"></i>Transaksi Sewa Aktif</h4>
    <a href="<?= BASE_URL ?>/sewa/tambah.php" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Transaksi Baru
    </a>
</div>

<?php if (count($active_rentals) > 0): ?>
    <div class="row g-4">
        <?php foreach ($active_rentals as $r):
            $total_biaya = hitung_total_biaya($r['tarif_per_jam'], $r['durasi_jam']);
        ?>
        <div class="col-12 col-md-6 col-xl-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <!-- Header unit -->
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h5 class="fw-bold mb-1 text-primary"><?= htmlspecialchars($r['nama_ps']) ?></h5>
                            <span class="badge bg-secondary"><?= $r['tipe_ps'] ?></span>
                        </div>
                        <span class="fw-semibold text-muted"><?= format_rupiah($r['tarif_per_jam']) ?>/jam</span>
                    </div>

                    <!-- Pelanggan -->
                    <p class="mb-3 text-muted">
                        <i class="bi bi-person-fill me-1 text-primary"></i>
                        <strong><?= htmlspecialchars($r['nama_pelanggan']) ?></strong>
                        <span class="ms-2 small"><?= htmlspecialchars($r['no_hp']) ?></span>
                    </p>

                    <!-- Waktu -->
                    <div class="bg-light rounded p-3 mb-3">
                        <div class="row text-center">
                            <div class="col-6 border-end">
                                <div class="text-muted small">MULAI</div>
                                <div class="fw-semibold"><?= date('H:i', strtotime($r['waktu_mulai'])) ?></div>
                                <div class="text-muted" style="font-size:.75rem"><?= date('d/m/Y', strtotime($r['waktu_mulai'])) ?></div>
                            </div>
                            <div class="col-6">
                                <div class="text-muted small">SELESAI</div>
                                <div class="fw-semibold"><?= date('H:i', strtotime($r['waktu_selesai'])) ?></div>
                                <div class="text-muted" style="font-size:.75rem"><?= date('d/m/Y', strtotime($r['waktu_selesai'])) ?></div>
                            </div>
                        </div>
                    </div>

                    <!-- Countdown -->
                    <div class="d-flex justify-content-between align-items-center rounded p-2 mb-3"
                         style="background:rgba(239,68,68,.08)">
                        <span class="small fw-semibold text-danger"><i class="bi bi-alarm-fill me-1"></i>SISA WAKTU</span>
                        <span class="fw-bold text-danger countdown-timer"
                              data-endtime="<?= $r['waktu_selesai'] ?>">--:--:--</span>
                    </div>

                    <!-- Total & Durasi -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <span class="text-muted small"><?= $r['durasi_jam'] ?> Jam × <?= format_rupiah($r['tarif_per_jam']) ?></span>
                        <span class="fw-bold text-success fs-5"><?= format_rupiah($total_biaya) ?></span>
                    </div>

                    <!-- Aksi -->
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-warning btn-sm flex-fill"
                                data-bs-toggle="modal" data-bs-target="#extendModal-<?= $r['id_sewa'] ?>">
                            <i class="bi bi-plus-circle me-1"></i> Perpanjang
                        </button>
                        <a href="<?= BASE_URL ?>/sewa/akhiri.php?id=<?= $r['id_sewa'] ?>"
                           class="btn btn-danger btn-sm flex-fill"
                           onclick="return confirm('Akhiri sewa <?= htmlspecialchars($r['nama_ps']) ?> sekarang?');">
                            <i class="bi bi-stop-circle me-1"></i> Akhiri
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Perpanjang -->
        <div class="modal fade" id="extendModal-<?= $r['id_sewa'] ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold">Perpanjang Sewa — <?= htmlspecialchars($r['nama_ps']) ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="<?= BASE_URL ?>/sewa/perpanjang.php" method="POST">
                        <input type="hidden" name="id_sewa" value="<?= $r['id_sewa'] ?>">
                        <div class="modal-body pt-2">
                            <p class="text-muted mb-3">
                                Pelanggan: <strong><?= htmlspecialchars($r['nama_pelanggan']) ?></strong><br>
                                Waktu selesai saat ini: <strong><?= date('H:i, d/m/Y', strtotime($r['waktu_selesai'])) ?></strong>
                            </p>
                            <label for="durasi_tambahan_<?= $r['id_sewa'] ?>" class="form-label fw-semibold">Tambah Durasi (Jam)</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="durasi_tambahan_<?= $r['id_sewa'] ?>"
                                       name="durasi_tambahan" min="1" max="24" value="1" required>
                                <span class="input-group-text">Jam</span>
                            </div>
                        </div>
                        <div class="modal-footer border-0 pt-0">
                            <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary btn-sm px-4">Simpan Perpanjangan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="text-center py-5 bg-white rounded-4 shadow-sm">
        <div class="display-1 text-muted"><i class="bi bi-hourglass-bottom"></i></div>
        <p class="text-muted mt-3 mb-3">Tidak ada transaksi sewa yang aktif saat ini.</p>
        <a href="<?= BASE_URL ?>/sewa/tambah.php" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Mulai Sewa Baru
        </a>
    </div>
<?php endif; ?>

<!-- Countdown Timer Script -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    function updateTimers() {
        document.querySelectorAll(".countdown-timer").forEach(function (el) {
            const end = new Date(el.dataset.endtime.replace(/-/g, "/")).getTime();
            const diff = end - Date.now();
            if (diff <= 0) {
                el.textContent = "Selesai";
                el.closest("[style]").style.background = "rgba(16,185,129,.08)";
                el.classList.replace("text-danger", "text-success");
            } else {
                const h = String(Math.floor(diff / 3600000)).padStart(2, "0");
                const m = String(Math.floor((diff % 3600000) / 60000)).padStart(2, "0");
                const s = String(Math.floor((diff % 60000) / 1000)).padStart(2, "0");
                el.textContent = h + ":" + m + ":" + s;
            }
        });
    }
    updateTimers();
    setInterval(updateTimers, 1000);
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
