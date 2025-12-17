<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-3">
                <div class="card-header">Detail Peminjaman</div>
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <div><strong>Nama:</strong> <?= esc($loan['borrower_name']) ?></div>
                            <div><strong>ID:</strong> <?= esc($loan['borrower_identifier'] ?? '-') ?></div>
                            <div><strong>Unit:</strong> <?= esc($loan['borrower_unit'] ?? '-') ?></div>
                        </div>
                        <div class="col-md-6">
                            <div><strong>Tgl Pinjam:</strong> <?= esc($loan['loan_date']) ?></div>
                            <div><strong>Jatuh Tempo:</strong> <?= esc($loan['due_date'] ?? '-') ?></div>
                            <div><strong>Status:</strong> <span class="badge bg-secondary text-uppercase"><?= esc($loan['status']) ?></span></div>
                        </div>
                    </div>
                    <div>
                        <strong>Catatan:</strong>
                        <div class="text-muted"><?= nl2br(esc($loan['notes'] ?? '-')) ?></div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">Barang</div>
                <div class="card-body table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th>SKU</th>
                                <th class="text-end">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (($loan['items'] ?? []) as $it): ?>
                                <tr>
                                    <td><?= esc($it['product_name']) ?></td>
                                    <td><?= esc($it['sku']) ?></td>
                                    <td class="text-end"><?= (int)$it['quantity'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">Aksi</div>
                <div class="card-body d-grid gap-2">
                    <?php if (in_array($loan['status'], ['requested'])): ?>
                        <button class="btn btn-success" id="btn-approve">Setujui</button>
                    <?php endif; ?>
                    <?php if (in_array($loan['status'], ['requested','approved'])): ?>
                        <button class="btn btn-primary" id="btn-borrow">Pinjamkan</button>
                        <button class="btn btn-outline-danger" id="btn-cancel">Batalkan</button>
                    <?php endif; ?>
                    <?php if ($loan['status'] === 'borrowed'): ?>
                        <button class="btn btn-info" id="btn-return">Terima Pengembalian</button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    function postAction(url){
        showLoading('Memproses...');
        $.post(url, {<?= csrf_token() ?>: '<?= csrf_hash() ?>'})
            .done(function(resp){
                Swal.close();
                showSuccess('Berhasil', resp.message || 'Tindakan berhasil', function(){ location.reload(); });
            })
            .fail(function(xhr){
                Swal.close();
                showError('Gagal', xhr.responseJSON?.message || 'Terjadi kesalahan');
            });
    }

    $('#btn-approve').on('click', function(){
        showConfirm('Setujui?', 'Setujui peminjaman ini', function(){
            postAction('<?= base_url('loans/approve/'.$loan['id']) ?>');
        });
    });
    $('#btn-borrow').on('click', function(){
        showConfirm('Pinjamkan?', 'Kurangi stok sesuai item', function(){
            postAction('<?= base_url('loans/borrow/'.$loan['id']) ?>');
        });
    });
    $('#btn-return').on('click', function(){
        showConfirm('Terima Pengembalian?', 'Tambah stok kembali', function(){
            postAction('<?= base_url('loans/return/'.$loan['id']) ?>');
        });
    });
    $('#btn-cancel').on('click', function(){
        showConfirm('Batalkan?', 'Batalkan pengajuan ini', function(){
            postAction('<?= base_url('loans/cancel/'.$loan['id']) ?>');
        });
    });
</script>
<?= $this->endSection() ?>
