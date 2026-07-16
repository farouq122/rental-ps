<?php
require_once __DIR__ . '/includes/header.php';

// 1. Total PlayStation units is fixed at 10
$total_ps = 10;

// 2. Fetch total customers
$stmt = $pdo->query("SELECT COUNT(*) as total FROM pelanggan");
$total_pelanggan = $stmt->fetch()['total'];

// 3. Count active rentals
$stmt = $pdo->query("SELECT COUNT(*) as total FROM sewa WHERE NOW() < waktu_selesai");
$sewa_aktif = $stmt->fetch()['total'];

// 4. Count available PS units
$ps_tersedia = $total_ps - $sewa_aktif;

// 5. Fetch all PlayStation units with real-time status
$query = "
    SELECT 
        p.*,
        s.waktu_selesai,
        s.id_sewa,
        pl.nama as nama_pelanggan
    FROM playstation p
    LEFT JOIN sewa s ON p.id_ps = s.id_ps AND NOW() < s.waktu_selesai
    LEFT JOIN pelanggan pl ON s.id_pelanggan = pl.id_pelanggan
    ORDER BY p.id_ps ASC
";
$ps_list = $pdo->query($query)->fetchAll();
?>

<!-- Summary Cards -->
<div class="row g-4 mb-5">
    <div class="col-6 col-xl-3">
        <div class="card h-100 border-0">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 p-3 flex-shrink-0" style="background:rgba(79,70,229,.1)">
                    <i class="bi bi-controller fs-3 text-primary"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold text-uppercase" style="font-size:.72rem;letter-spacing:.05em">Total Unit</div>
                    <div class="fw-bold fs-3 lh-1 mt-1"><?= $total_ps ?></div>
                    <div class="text-muted" style="font-size:.78rem">PlayStation</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="card h-100 border-0">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 p-3 flex-shrink-0" style="background:rgba(16,185,129,.1)">
                    <i class="bi bi-check-circle fs-3 text-success"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold text-uppercase" style="font-size:.72rem;letter-spacing:.05em">Tersedia</div>
                    <div class="fw-bold fs-3 lh-1 mt-1 text-success"><?= $ps_tersedia ?></div>
                    <div class="text-muted" style="font-size:.78rem">Unit siap disewa</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="card h-100 border-0">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 p-3 flex-shrink-0" style="background:rgba(239,68,68,.1)">
                    <i class="bi bi-hourglass-split fs-3 text-danger"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold text-uppercase" style="font-size:.72rem;letter-spacing:.05em">Sedang Disewa</div>
                    <div class="fw-bold fs-3 lh-1 mt-1 text-danger"><?= $sewa_aktif ?></div>
                    <div class="text-muted" style="font-size:.78rem">Unit aktif</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="card h-100 border-0">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 p-3 flex-shrink-0" style="background:rgba(124,58,237,.1)">
                    <i class="bi bi-people fs-3" style="color:#7c3aed"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold text-uppercase" style="font-size:.72rem;letter-spacing:.05em">Pelanggan</div>
                    <div class="fw-bold fs-3 lh-1 mt-1" style="color:#7c3aed"><?= $total_pelanggan ?></div>
                    <div class="text-muted" style="font-size:.78rem">Terdaftar</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- PlayStation Real-time Status -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0 fw-bold"><i class="bi bi-display me-2"></i>Status Real-time PlayStation</h5>
    <a href="<?= BASE_URL ?>/sewa/tambah.php" class="btn btn-primary btn-sm px-3">
        <i class="bi bi-plus-lg me-1"></i> Sewa Baru
    </a>
</div>

<div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead>
            <tr>
                <th style="width:50px">No</th>
                <th>Unit</th>
                <th>Tipe</th>
                <th>Tarif / Jam</th>
                <th>Status</th>
                <th>Penyewa</th>
                <th style="width:140px;text-align:center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; foreach ($ps_list as $ps):
                $status = get_sewa_status($ps['waktu_selesai']);
            ?>
            <tr>
                <td><strong><?= $no++ ?></strong></td>
                <td class="fw-semibold"><?= htmlspecialchars($ps['nama_ps']) ?></td>
                <td><span class="badge bg-secondary"><?= $ps['tipe_ps'] ?></span></td>
                <td class="fw-semibold text-primary"><?= format_rupiah($ps['tarif_per_jam']) ?></td>
                <td><?= get_status_badge($status) ?></td>
                <td>
                    <?php if ($status === 'Sedang Disewa'): ?>
                        <span class="text-danger fw-semibold"><i class="bi bi-person-fill me-1"></i><?= htmlspecialchars($ps['nama_pelanggan']) ?></span>
                    <?php else: ?>
                        <span class="text-muted">—</span>
                    <?php endif; ?>
                </td>
                <td class="text-center">
                    <?php if ($status === 'Sedang Disewa'): ?>
                        <a href="<?= BASE_URL ?>/sewa/list.php" class="btn btn-outline-warning btn-sm">
                            <i class="bi bi-eye"></i> Detail
                        </a>
                    <?php else: ?>
                        <a href="<?= BASE_URL ?>/sewa/tambah.php?id_ps=<?= $ps['id_ps'] ?>" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-play-fill"></i> Sewa
                        </a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
