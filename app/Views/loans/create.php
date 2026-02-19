<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <form method="post" action="<?= base_url('loans/store') ?>">
                <?= csrf_field() ?>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Nama Pemohon</label>
                        <input type="text" name="borrower_name" class="form-control" required value="<?= old('borrower_name') ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">NIM/NIP</label>
                        <input type="text" name="borrower_identifier" class="form-control" value="<?= old('borrower_identifier') ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Unit/Prodi</label>
                        <input type="text" name="borrower_unit" class="form-control" value="<?= old('borrower_unit') ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Kontak</label>
                        <input type="text" name="contact" class="form-control" value="<?= old('contact') ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tanggal Permintaan</label>
                        <input type="date" name="loan_date" class="form-control" required value="<?= old('loan_date', date('Y-m-d')) ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Catatan</label>
                        <textarea name="notes" class="form-control" rows="2"><?= old('notes') ?></textarea>
                    </div>
                </div>

                <hr>
                <h6 class="mb-3">Barang yang Diminta</h6>
                <div id="items">
                    <div class="row g-2 align-items-end item-row">
                        <div class="col-md-6">
                            <label class="form-label">Produk</label>
                            <select name="product_id[]" class="form-select">
                                <option value="">- Pilih Produk -</option>
                                <?php foreach ($products as $p): ?>
                                    <option value="<?= $p['id'] ?>"><?= esc($p['name']) ?> (<?= esc($p['sku']) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Jumlah</label>
                            <input type="number" name="quantity[]" class="form-control" min="1" value="1">
                        </div>
                        <div class="col-md-3">
                            <button type="button" class="btn btn-outline-success add-row"><i class="bi bi-plus"></i> Tambah</button>
                        </div>
                    </div>
                </div>

                <div class="mt-4 d-flex justify-content-between">
                    <a href="<?= base_url('loans') ?>" class="btn btn-light">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).on('click', '.add-row', function(){
        const row = $(this).closest('.item-row');
        const clone = row.clone();
        clone.find('select').val('');
        clone.find('input[name="quantity[]"]').val(1);
        clone.find('.add-row').removeClass('add-row btn-outline-success').addClass('remove-row btn-outline-danger').html('<i class="bi bi-trash"></i> Hapus');
        $('#items').append(clone);
    });

    $(document).on('click', '.remove-row', function(){
        $(this).closest('.item-row').remove();
    });
</script>
<?= $this->endSection() ?>
