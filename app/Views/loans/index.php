<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="<?= base_url('loans/create') ?>" class="btn btn-primary"><i class="bi bi-plus"></i> Buat Permintaan ATK</a>
        <form method="get" class="d-flex" action="<?= base_url('loans') ?>">
            <select name="status" class="form-select me-2">
                <option value="">Semua Status</option>
                <?php $statuses = ['requested' => 'Diajukan', 'approved' => 'Disetujui', 'distributed' => 'Didistribusikan', 'cancelled' => 'Dibatalkan'];
                foreach ($statuses as $key => $label): ?>
                    <option value="<?= $key ?>" <?= ($status ?? '') === $key ? 'selected' : '' ?>><?= $label ?></option>
                <?php endforeach; ?>
            </select>
            <button class="btn btn-outline-secondary" type="submit">Filter</button>
        </form>
    </div>

    <div class="card">
        <div class="card-body table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Pemohon</th>
                        <th>Tanggal Permintaan</th>
                        <th>Keperluan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($loans)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted">Belum ada data</td>
                        </tr>
                        <?php else: foreach ($loans as $l): ?>
                            <tr>
                                <td>#<?= $l['id'] ?></td>
                                <td>
                                    <div class="fw-bold"><?= esc($l['borrower_name']) ?></div>
                                    <div class="text-muted small"><?= esc($l['borrower_identifier'] ?? '') ?></div>
                                </td>
                                <td><?= esc($l['loan_date']) ?></td>
                                <td><?= esc($l['notes'] ?? '-') ?></td>
                                <td><span class="badge bg-secondary text-uppercase"><?= esc($l['status']) ?></span></td>
                                <td>
                                    <a href="<?= base_url('loans/show/' . $l['id']) ?>" class="btn btn-sm btn-outline-primary">Detail</a>
                                </td>
                            </tr>
                    <?php endforeach;
                    endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>