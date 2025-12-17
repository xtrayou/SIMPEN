<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-3">
                <div class="card-header">Detail Ticket</div>
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <div><strong>Subjek:</strong> <?= esc($ticket['subject']) ?></div>
                            <div><strong>Tipe:</strong> <?= esc($ticket['type']) ?></div>
                            <div><strong>Prioritas:</strong> <?= esc($ticket['priority']) ?></div>
                        </div>
                        <div class="col-md-6">
                            <div><strong>Status:</strong> <span class="badge bg-secondary text-uppercase"><?= esc($ticket['status']) ?></span></div>
                            <div><strong>Pemohon:</strong> <?= esc($ticket['requester_name']) ?></div>
                            <div><strong>Kontak:</strong> <?= esc($ticket['requester_contact'] ?? '-') ?></div>
                        </div>
                    </div>
                    <div>
                        <strong>Deskripsi:</strong>
                        <div class="text-muted"><?= nl2br(esc($ticket['description'] ?? '-')) ?></div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">Komentar</div>
                <div class="card-body">
                    <div id="comments">
                        <?php foreach (($ticket['comments'] ?? []) as $c): ?>
                            <div class="border rounded p-2 mb-2">
                                <div class="d-flex justify-content-between">
                                    <div class="fw-bold"><?= esc($c['author_name']) ?></div>
                                    <div class="text-muted small"><?= esc($c['created_at']) ?></div>
                                </div>
                                <div><?= nl2br(esc($c['comment'])) ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <form id="comment-form" class="mt-3">
                        <?= csrf_field() ?>
                        <div class="row g-2">
                            <div class="col-md-3">
                                <input name="author_name" class="form-control" placeholder="Nama" required>
                            </div>
                            <div class="col-md-7">
                                <input name="comment" class="form-control" placeholder="Tulis komentar" required>
                            </div>
                            <div class="col-md-2 d-grid">
                                <button class="btn btn-outline-primary" type="submit">Kirim</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">Ubah Status</div>
                <div class="card-body">
                    <select id="status" class="form-select mb-2">
                        <?php foreach (['open','in_progress','resolved','closed'] as $s): ?>
                            <option value="<?= $s ?>" <?= $ticket['status']===$s?'selected':'' ?>><?= strtoupper($s) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button class="btn btn-primary w-100" id="btn-update-status">Update</button>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $('#comment-form').on('submit', function(e){
        e.preventDefault();
        showLoading('Mengirim komentar...');
        $.post('<?= base_url('tickets/comment/'.$ticket['id']) ?>', $(this).serialize())
            .done(function(){
                Swal.close();
                showSuccess('Terkirim', 'Komentar ditambahkan', function(){ location.reload(); });
            })
            .fail(function(xhr){
                Swal.close();
                showError('Gagal', xhr.responseJSON?.message || 'Tidak dapat mengirim');
            });
    });

    $('#btn-update-status').on('click', function(){
        const status = $('#status').val();
        showLoading('Memperbarui status...');
        $.post('<?= base_url('tickets/status/'.$ticket['id']) ?>', {status, <?= csrf_token() ?>: '<?= csrf_hash() ?>'})
            .done(function(){
                Swal.close();
                showSuccess('Berhasil', 'Status diperbarui', function(){ location.reload(); });
            })
            .fail(function(xhr){
                Swal.close();
                showError('Gagal', xhr.responseJSON?.message || 'Tidak dapat memperbarui');
            });
    });
</script>
<?= $this->endSection() ?>
