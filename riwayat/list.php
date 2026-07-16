<?php
require_once __DIR__ . '/../includes/header.php';

$filter_date     = isset($_GET['tanggal'])   ? trim($_GET['tanggal'])   : '';
$filter_customer = isset($_GET['pelanggan']) ? trim($_GET['pelanggan']) : '';

$query = "
    SELECT 
        s.*,
        p.nama_ps, p.tipe_ps, p.tarif_per_jam,
        pl.nama as nama_pelanggan
    FROM sewa s
    JOIN playstation p  ON s.id_ps = p.id_ps
    JOIN pelanggan pl   ON s.id_pelanggan = pl.id_pelanggan
    WHERE NOW() >= s.waktu_selesai
";

$params = [];

if ($filter_date !== '') {
    $query .= " AND DATE(s.waktu_mulai) = ?";
    $params[] = $filter_date;
}
if ($filter_customer !== '') {
    $query .= " AND pl.nama LIKE ?";
    $params[] = "%$filter_customer%";
}

$query .= " ORDER BY s.waktu_selesai DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$history = $stmt->fetchAll();

// Total pendapatan
$total_pendapatan = 0;
foreach ($history as $row) {
    $total_pendapatan += $row['tarif_per_jam'] * $row['durasi_jam'];
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 fw-bold"><i class="bi bi-journal-text me-2"></i>Riwayat Penyewaan</h4>
    <span class="badge bg-primary fs-6"><?= count($history) ?> Transaksi</span>
</div>

<!-- Filter -->
<div class="card p-3 mb-4">
    <form method="GET" action="" class="row g-3">
        <div class="col-12 col-md-4">
            <label class="form-label text-muted small fw-semibold">Tanggal</label>
            <input type="date" name="tanggal" class="form-control" value="<?= htmlspecialchars($filter_date) ?>">
        </div>
        <div class="col-12 col-md-5">
            <label class="form-label text-muted small fw-semibold">Nama Pelanggan</label>
            <input type="text" name="pelanggan" class="form-control"
                   placeholder="Cari nama pelanggan..."
                   value="<?= htmlspecialchars($filter_customer) ?>">
        </div>
        <div class="col-12 col-md-3 d-flex align-items-end gap-2">
            <button type="submit" class="btn btn-primary flex-fill"><i class="bi bi-filter me-1"></i>Filter</button>
            <?php if ($filter_date !== '' || $filter_customer !== ''): ?>
                <a href="<?= BASE_URL ?>/riwayat/list.php" class="btn btn-outline-secondary" title="Reset">
                    <i class="bi bi-x-lg"></i>
                </a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Total Pendapatan (saat filter aktif) -->
<?php if (count($history) > 0): ?>
<div class="alert alert-success d-flex justify-content-between align-items-center mb-4 py-2">
    <span><i class="bi bi-cash-stack me-2"></i>Total Pendapatan <?= $filter_date ? 'Tgl. ' . date('d M Y', strtotime($filter_date)) : '' ?>:</span>
    <strong class="fs-5"><?= format_rupiah($total_pendapatan) ?></strong>
</div>
<?php endif; ?>

<!-- Tabel -->
<div class="table-responsive">
    <?php if (count($history) > 0): ?>
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th style="width:50px">No</th>
                    <th>Pelanggan</th>
                    <th>PlayStation</th>
                    <th>Tipe</th>
                    <th>Waktu Mulai</th>
                    <th>Waktu Selesai</th>
                    <th>Durasi</th>
                    <th>Total Biaya</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; foreach ($history as $row):
                    $total = hitung_total_biaya($row['tarif_per_jam'], $row['durasi_jam']);
                ?>
                <tr>
                    <td><strong><?= $no++ ?></strong></td>
                    <td class="fw-semibold"><?= htmlspecialchars($row['nama_pelanggan']) ?></td>
                    <td><?= htmlspecialchars($row['nama_ps']) ?></td>
                    <td><span class="badge bg-secondary"><?= $row['tipe_ps'] ?></span></td>
                    <td class="small text-muted"><?= date('d M Y, H:i', strtotime($row['waktu_mulai'])) ?></td>
                    <td class="small text-muted"><?= date('d M Y, H:i', strtotime($row['waktu_selesai'])) ?></td>
                    <td><strong><?= $row['durasi_jam'] ?> Jam</strong></td>
                    <td class="fw-bold text-success"><?= format_rupiah($total) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="text-center py-5">
            <div class="display-1 text-muted"><i class="bi bi-journal-x"></i></div>
            <p class="text-muted mt-3">Tidak ada riwayat transaksi ditemukan.</p>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
