<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <form method="post" action="<?= base_url('tickets/store') ?>">
                <?= csrf_field() ?>
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label">Subjek</label>
                        <input type="text" name="subject" class="form-control" required value="<?= old('subject') ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Tipe</label>
                        <select name="type" class="form-select">
                            <option value="loan-request">Permintaan ATK</option>
                            <option value="restock">Permintaan Restock</option>
                            <option value="issue">Isu/Perbaikan</option>
                            <option value="other">Lainnya</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Prioritas</label>
                        <select name="priority" class="form-select">
                            <option value="low">Low</option>
                            <option value="medium" selected>Medium</option>
                            <option value="high">High</option>
                            <option value="urgent">Urgent</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Nama Pemohon</label>
                        <input type="text" name="requester_name" class="form-control" required value="<?= old('requester_name') ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Kontak</label>
                        <input type="text" name="requester_contact" class="form-control" value="<?= old('requester_contact') ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="description" class="form-control" rows="4"><?= old('description') ?></textarea>
                    </div>
                </div>
                <div class="mt-4 d-flex justify-content-between">
                    <a href="<?= base_url('tickets') ?>" class="btn btn-light">Batal</a>
                    <button class="btn btn-primary" type="submit">Kirim Ticket</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
