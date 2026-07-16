<?php
require_once __DIR__ . '/../includes/header.php';

$keyword = isset($_GET['search']) ? trim($_GET['search']) : '';

if ($keyword !== '') {
    $stmt = $pdo->prepare("SELECT * FROM pelanggan WHERE nama LIKE ? OR no_hp LIKE ? OR alamat LIKE ? ORDER BY id_pelanggan DESC");
    $stmt->execute(["%$keyword%", "%$keyword%", "%$keyword%"]);
} else {
    $stmt = $pdo->query("SELECT * FROM pelanggan ORDER BY id_pelanggan DESC");
}
$customers = $stmt->fetchAll();
?>

<div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3 mb-4">
    <h4 class="mb-0 fw-bold"><i class="bi bi-people me-2"></i>Data Pelanggan</h4>
    <a href="<?= BASE_URL ?>/pelanggan/tambah.php" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Tambah Pelanggan
    </a>
</div>

<!-- Search -->
<div class="card p-3 mb-4">
    <form method="GET" action="" class="row g-2">
        <div class="col-12 col-md-10">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="search" class="form-control border-start-0"
                       placeholder="Cari nama, no HP, atau alamat..."
                       value="<?= htmlspecialchars($keyword) ?>">
            </div>
        </div>
        <div class="col-12 col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-outline-primary flex-fill"><i class="bi bi-search"></i> Cari</button>
            <?php if ($keyword): ?>
                <a href="<?= BASE_URL ?>/pelanggan/list.php" class="btn btn-outline-secondary" title="Reset"><i class="bi bi-x-lg"></i></a>
            <?php endif; ?>
        </div>
    </form>
</div>

<div class="table-responsive">
    <?php if (count($customers) > 0): ?>
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th style="width:50px">No</th>
                    <th>Nama Lengkap</th>
                    <th>No. HP</th>
                    <th>Alamat</th>
                    <th style="width:120px;text-align:center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; foreach ($customers as $cust): ?>
                <tr>
                    <td><strong><?= $no++ ?></strong></td>
                    <td class="fw-semibold"><?= htmlspecialchars($cust['nama']) ?></td>
                    <td>
                        <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $cust['no_hp']) ?>"
                           target="_blank" class="text-decoration-none text-success fw-medium">
                            <i class="bi bi-whatsapp me-1"></i><?= htmlspecialchars($cust['no_hp']) ?>
                        </a>
                    </td>
                    <td class="text-muted"><?= htmlspecialchars($cust['alamat']) ?></td>
                    <td class="text-center">
                        <div class="btn-group">
                            <a href="<?= BASE_URL ?>/pelanggan/edit.php?id=<?= $cust['id_pelanggan'] ?>"
                               class="btn btn-sm btn-outline-primary" title="Edit">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                            <a href="<?= BASE_URL ?>/pelanggan/hapus.php?id=<?= $cust['id_pelanggan'] ?>"
                               class="btn btn-sm btn-outline-danger" title="Hapus"
                               onclick="return confirm('Yakin hapus pelanggan ini?');">
                                <i class="bi bi-trash"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="text-center py-5">
            <div class="display-1 text-muted"><i class="bi bi-people"></i></div>
            <p class="text-muted mt-3">
                <?= $keyword ? 'Pelanggan tidak ditemukan.' : 'Belum ada data pelanggan.' ?>
            </p>
            <a href="<?= BASE_URL ?>/pelanggan/tambah.php" class="btn btn-primary btn-sm mt-2">
                <i class="bi bi-plus-lg me-1"></i> Tambah Pelanggan
            </a>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
