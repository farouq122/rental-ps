<?php
require_once __DIR__ . '/../includes/header.php';

$query = "
    SELECT 
        p.*,
        s.waktu_selesai
    FROM playstation p
    LEFT JOIN sewa s ON p.id_ps = s.id_ps AND NOW() < s.waktu_selesai
    ORDER BY p.id_ps ASC
";
$ps_list = $pdo->query($query)->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 fw-bold"><i class="bi bi-device-ssd me-2"></i>Daftar PlayStation</h4>
    <span class="text-muted small">Total: <strong>10 Unit Tetap</strong></span>
</div>

<div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead>
            <tr>
                <th style="width:50px">No</th>
                <th>Nama Unit</th>
                <th>Tipe</th>
                <th>Tarif / Jam</th>
                <th>Status Saat Ini</th>
                <th style="width:120px;text-align:center">Aksi</th>
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
                <td class="text-center">
                    <a href="<?= BASE_URL ?>/playstation/edit.php?id=<?= $ps['id_ps'] ?>" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-pencil-square"></i> Edit
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
