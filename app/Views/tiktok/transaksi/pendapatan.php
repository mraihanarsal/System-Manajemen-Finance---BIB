<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Pendapatan TikTok - <?= esc($toko['nama_toko']) ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- HEADER -->
<div class="row mb-4">
    <div class="col-md-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= base_url('/tiktok') ?>">Dashboard TikTok</a></li>
                <li class="breadcrumb-item active" aria-current="page">Upload Pendapatan</li>
            </ol>
        </nav>
        <div class="card shadow-sm p-3 d-flex flex-row align-items-center">
            <img src="<?= base_url('img/tiktoklogo.png') ?>"
                alt="TikTok Logo"
                style="height:50px;margin-right:15px;">
            <div>
                <h4 class="mb-0">Toko <?= esc($toko['nama_toko']) ?></h4>
            </div>
        </div>
    </div>
</div>

<!-- FORM UPLOAD FILE -->
<div class="card shadow-sm p-4 mb-4">
    <h5 class="mb-3">Upload file laporan dari TikTok Seller Center</h5>

    <form action="<?= base_url('/tiktok/transaksi/processPendapatan/' . $toko['id_toko']) ?>"
        method="post"
        enctype="multipart/form-data">
        <div class="mb-3">
            <input type="file" name="file" class="form-control"
                accept=".csv,.xls,.xlsx" required>
            <small class="text-muted">Format: Excel Max (10 MB)</small>
        </div>

        <div class="d-grid">
            <button class="btn btn-success btn-lg">
                <i class="fas fa-calculator"></i> Proses Hitung
            </button>
        </div>
    </form>
</div>

<!-- INFO SUKSES UPLOAD (EPHEMERAL) -->
<?php if (!empty($preview_info)): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <h5 class="alert-heading"><i class="fas fa-check-circle"></i> Berhasil Disimpan!</h5>
        <hr>
        <p class="mb-0">
            Laporan untuk periode <strong><?= esc($preview_info[0]['periode'] ?? '-') ?></strong>
            dengan total settlement <strong>Rp <?= number_format($preview_info[0]['settlement'] ?? 0) ?></strong>
            telah berhasil disimpan ke database.
        </p>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<!-- TABEL RIWAYAT UPLOAD TERAKHIR -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-gradient-primary text-white d-flex justify-content-between align-items-center" style="background: linear-gradient(45deg, #4e73df, #224abe);">
            <h6 class="m-0 font-weight-bold"><i class="fas fa-history me-2"></i>Riwayat Upload Pendapatan Terakhir</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="tablePendapatan" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">No</th>
                            <th>Periode Laporan</th>
                            <th>Settlement (Pendapatan)</th>
                            <th>Tanggal Upload</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($history as $i => $row): ?>
                            <tr>
                                <td class="text-center"><?= $i + 1 ?></td>
                                <td>
                                    <span class="badge bg-primary">
                                        <?= date('d M Y', strtotime($row['periode_start'])) ?> - <?= date('d M Y', strtotime($row['periode_end'])) ?>
                                    </span>
                                </td>
                                <td class="text-end fw-bold text-success">Rp <?= number_format($row['settlement']) ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($row['created_at'])) ?></td>
                                <td class="text-center">
                                    <a href="<?= base_url('/tiktok/transaksi/laba/' . $id_toko) ?>" class="btn btn-sm btn-info text-white rounded-pill px-2">
                                        <i class="fas fa-calculator me-1"></i> Hitung Laba
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- DataTables CDN -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<!-- Include Bootstrap Datepicker -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
<!-- Include SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.id.min.js"></script>

<script>
    $(document).ready(function() {
        // Init DataTable
        $('#tablePendapatan').DataTable({
            "pageLength": 5,
            "lengthMenu": [5, 10, 25, 50],
            "scrollX": true,
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json"
            }
        });

        // SweetAlert for reset buttons
        $(document).on('click', '.reset-btn', function(e) {
            e.preventDefault();
            var url = $(this).data('url');
            var type = $(this).data('type');

            Swal.fire({
                title: 'Reset Data Pendapatan?',
                text: 'Data pendapatan yang belum disimpan akan hilang.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Reset!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
        });

        // Validasi file sebelum upload
        $('input[type="file"]').on('change', function() {
            var file = this.files[0];
            var maxSize = 10 * 1024 * 1024; 

            if (file && file.size > maxSize) {
                Swal.fire({
                    title: 'File Terlalu Besar',
                    text: 'Ukuran file maksimal 10MB',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
                this.value = '';
            }
        });
    });
</script>

<?= $this->endSection() ?>