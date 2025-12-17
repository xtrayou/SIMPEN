<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="<?= base_url('tickets/create') ?>" class="btn btn-primary"><i class="bi bi-plus"></i> Buat Ticket</a>
        <form method="get" class="d-flex" action="<?= base_url('tickets') ?>">
            <select name="status" class="form-select me-2">
                <option value="">Semua Status</option>
                <?php $statuses = ['open'=>'Open','in_progress'=>'Proses','resolved'=>'Selesai','closed'=>'Closed'];
                foreach ($statuses as $key=>$label): ?>
                    <option value="<?= $key ?>" <?= ($status??'')===$key?'selected':'' ?>><?= $label ?></option>
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
                        <th>Subjek</th>
                        <th>Tipe</th>
                        <th>Prioritas</th>
                        <th>Status</th>
                        <th>Pemohon</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($tickets)): ?>
                        <tr><td colspan="7" class="text-center text-muted">Belum ada ticket</td></tr>
                    <?php else: foreach ($tickets as $t): ?>
                        <tr>
                            <td>#<?= $t['id'] ?></td>
                            <td><?= esc($t['subject']) ?></td>
                            <td><span class="badge bg-light text-dark text-uppercase"><?= esc($t['type']) ?></span></td>
                            <td><span class="badge bg-warning text-dark text-uppercase"><?= esc($t['priority']) ?></span></td>
                            <td><span class="badge bg-secondary text-uppercase"><?= esc($t['status']) ?></span></td>
                            <td><?= esc($t['requester_name']) ?></td>
                            <td>
                                <a href="<?= base_url('tickets/show/'.$t['id']) ?>" class="btn btn-sm btn-outline-primary">Detail</a>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
