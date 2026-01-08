<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Hitung Laba Barang - <?= esc($toko['nama_toko']) ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- HEADER -->
 <div class="row mb-4">
    <div class="col-md-12">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-transparent p-0 mb-0 ml-4 small fw-medium">
            <li class="breadcrumb-item">
                <a href="<?= base_url('/tiktok') ?>" class="text-decoration-none text-secondary">
                    <i class="bi bi-tiktok color-primary me-1"></i> TikTok
                </a>
            </li>
            <li class="breadcrumb-item">
                <a href="<?= base_url('/tiktok/transaksi/pendapatan/' . $toko['id_toko']) ?>" class="text-decoration-none text-secondary">
                    Pendapatan
                </a>
            </li>
            <li class="breadcrumb-item active text-primary" aria-current="page">Hitung Laba</li>
        </ol>
    </nav>
    </div>
 </div>
<div class="container-fluid mb-5">
    
    <!-- CARD 1: FORM INPUT -->
    <div class="card card-form overflow-hidden mb-4">
        <div class="card-header-custom d-flex align-items-center gap-3">
            <!-- Logo -->
            <div class="bg-white rounded-3 p-2 d-flex align-items-center justify-content-center"
                style="width:56px;height:56px;">
                <img src="<?= base_url('img/tiktoklogo.png') ?>" alt="TikTok Logo" style="max-height:36px;">
            </div>

            <!-- Title -->
            <div class="flex-grow-1">
                <h4 class="mb-1 fw-semibold">Hitung Laba Barang</h4>
                <div class="small opacity-90">
                    <i class="bi bi-shop me-1"></i> <?= esc($toko['nama_toko']) ?>
                </div>
            </div>

            <!-- Badge -->
            <span class="badge bg-light text-primary fw-semibold px-3 py-2 d-none d-md-inline">
                TikTok Analytics
            </span>
        </div>
        
        <div class="card-body p-4">
            <form action="<?= base_url('/tiktok/transaksi/addBarang/' . $toko['id_toko']) ?>" method="post" id="formItem">
                <div class="row g-4">
                    <div class="col-12 col-md-6 col-xl-3">
                        <label class="form-label">Nama Barang *</label>
                        <input type="text" name="nama_barang" class="form-control" placeholder="Contoh: Kaos Polos" required>
                        <small class="form-text">Nama produk di toko</small>
                    </div>
                    <div class="col-12 col-md-6 col-xl-2">
                        <label class="form-label">Deskripsi *</label>
                        <input type="text" name="kategori" class="form-control" placeholder="Pakaian">
                        <small class="form-text">Jenis/Kategori produk</small>
                    </div>
                    <div class="col-12 col-md-6 col-xl-2">
                        <label class="form-label">Harga Modal *</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" name="harga_modal" class="form-control" min="0" value="0">
                        </div>
                        <small class="form-text">Modal per pcs</small>
                    </div>
                    <div class="col-12 col-md-6 col-xl-2">
                        <label class="form-label">Bobot (%) *</label>
                        <div class="input-group">
                            <input type="number" name="bobot" class="form-control" step="0.1" min="0" max="100" required>
                            <span class="input-group-text">%</span>
                        </div>
                        <small class="form-text">Persentase kontribusi</small>
                    </div>
                    <div class="col-12 col-md-6 col-xl-2">
                        <label class="form-label">Modal Periode *</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" name="modal_periode" class="form-control" min="0" required>
                        </div>
                        <small class="form-text">Total modal sebulan</small>
                    </div>
                    <div class="col-12 col-md-6 col-xl-1">
                        <label class="form-label d-block text-transparent user-select-none">Tambah</label>
                        <button class="btn btn-primary-custom text-white w-100">
                            <i class="bi bi-plus-lg"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php
    $totalBobot = array_sum(array_column($items ?? [], 'bobot'));
    $bobotValid = (round($totalBobot, 2) == 100.00);
    ?>

    <!-- CARD 2: DAFTAR BARANG -->
    <?php if (!empty($items)): ?>
        <div class="card card-form overflow-hidden mb-4">
            <div class="card-header-custom py-3 d-flex justify-content-between align-items-center" style="background:linear-gradient(135deg, #4e73df, #224abe);">
                <div class="text-white">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-list-check me-2"></i>Daftar Barang</h5>
                </div>
                <div>
                    <span class="badge <?= $bobotValid ? 'bg-success' : 'bg-warning text-dark' ?> fs-6 fw-normal">
                         Total Bobot: <?= number_format($totalBobot, 1) ?>%
                    </span>
                </div>
            </div>

            <div class="card-body p-4">
                <?php if (!$bobotValid): ?>
                    <div class="alert alert-warning d-flex align-items-center mb-4" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                        <div>
                            <strong>Perhatian:</strong> Total bobot belum 100%. Harap sesuaikan bobot barang agar totalnya pas 100% untuk memulai perhitungan.
                        </div>
                    </div>
                <?php endif; ?>

                <div class="table-responsive rounded-3 border">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="py-3 ps-3">Nama Barang</th>
                                <th class="py-3">Deskripsi</th>
                                <th class="py-3">Modal Satuan</th>
                                <th class="py-3 text-center">Bobot (%)</th>
                                <th class="py-3">Modal Periode</th>
                                <th class="py-3 text-end pe-3" width="100">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $item): ?>
                                <tr>
                                    <td class="ps-3 fw-medium"><?= esc($item['nama_barang']) ?></td>
                                    <td class="text-muted small"><?= esc($item['kategori']) ?></td>
                                    <td>Rp <?= number_format($item['harga_modal'] ?? 0) ?></td>
                                    <td class="text-center">
                                        <span class="badge <?= ($item['bobot'] ?? 0) > 0 ? 'bg-info bg-opacity-10 text-info border border-info' : 'bg-secondary' ?>">
                                            <?= number_format($item['bobot'] ?? 0, 1) ?>%
                                        </span>
                                    </td>
                                    <td>Rp <?= number_format($item['modal_periode'] ?? 0) ?></td>
                                    <td class="text-end pe-3">
                                        <a href="<?= base_url('/tiktok/transaksi/deleteBarang/' . $toko['id_toko'] . '/' . $item['id']) ?>"
                                            class="btn btn-sm btn-outline-danger border-0 btn-delete"
                                            title="Hapus"
                                            data-title="Hapus Barang?"
                                            data-text="Apakah Anda yakin ingin menghapus barang ini?">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-4 pt-2 border-top">
                    <a href="<?= base_url('/tiktok/transaksi/clearBarang/' . $toko['id_toko']) ?>"
                        class="btn btn-outline-danger btn-sm px-3 btn-delete"
                        data-title="Hapus Semua Barang?"
                        data-text="Semua barang dalam daftar akan dihapus. Lanjutkan?">
                        <i class="bi bi-trash3 me-1"></i> Hapus Semua
                    </a>

                    <?php if ($bobotValid): ?>
                        <form method="post" action="<?= base_url('/tiktok/transaksi/processLaba/' . $toko['id_toko']) ?>" class="d-inline">
                            <button class="btn btn-primary-custom text-white px-4 shadow-sm">
                                <i class="bi bi-calculator me-2"></i> Hitung Laba
                            </button>
                        </form>
                    <?php else: ?>
                        <button class="btn btn-secondary px-4" disabled>
                            <i class="bi bi-calculator me-2"></i> Hitung Laba
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- CARD 3: PREVIEW HASIL -->
    <?php if (!empty($preview)): ?>
        <div class="card card-form overflow-hidden mb-5">
            <div class="card-header-custom py-3 d-flex justify-content-between align-items-center" style="background:linear-gradient(135deg, #1cc88a, #13855c);">
                <div class="text-white">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-file-earmark-bar-graph me-2"></i>Preview Hasil Perhitungan</h5>
                    <small class="opacity-75">Periode: <?= esc($periode ?? "$start s/d $end") ?></small>
                </div>
                 <a href="<?= base_url('/tiktok/transaksi/resetLaba/' . $toko['id_toko']) ?>"
                    class="btn btn-light btn-sm text-success fw-bold reset-btn"
                    data-url="<?= base_url('/tiktok/transaksi/resetLaba/' . $toko['id_toko']) ?>"
                    data-type="laba">
                    <i class="bi bi-arrow-counterclockwise"></i> Reset
                </a>
            </div>

            <div class="card-body p-4">
                <!-- SUMMARY METRICS -->
                <div class="row g-3 mb-4 align-items-stretch">
                    <!-- Total Settlement -->
                    <div class="col-md-3 col-xl-3">
                        <div class="p-3 rounded-3 border border-light bg-light h-100 d-flex flex-column justify-content-center">
                            <small class="text-muted text-uppercase fw-bold" style="font-size:0.75rem;">Total Settlement</small>
                            <h4 class="mb-0 fw-bold text-gray-800 text-break">Rp <?= number_format(array_sum(array_column($preview, 'settlement_barang')) ?? 0) ?></h4>
                            <small class="text-muted d-block mt-1">(Pendapatan Bersih)</small>
                        </div>
                    </div>

                    <!-- Operator (-) -->
                    <div class="col-md-1 col-xl-1 d-flex align-items-center justify-content-center">
                        <div class="bg-light rounded-circle text-muted fw-bold d-flex align-items-center justify-content-center border" style="width: 40px; height: 40px; font-size: 1.5rem;">
                            -
                        </div>
                    </div>

                    <!-- Total Modal -->
                    <div class="col-md-3 col-xl-3">
                        <div class="p-3 rounded-3 border border-light bg-light h-100 d-flex flex-column justify-content-center">
                            <small class="text-muted text-uppercase fw-bold" style="font-size:0.75rem;">Total Modal</small>
                            <h4 class="mb-0 fw-bold text-gray-800 text-break">Rp <?= number_format(array_sum(array_column($preview, 'modal')) ?? 0) ?></h4>
                            <small class="text-muted d-block mt-1">(Total Asset)</small>
                        </div>
                    </div>
                    
                    <!-- Operator (=) -->
                    <div class="col-md-1 col-xl-1 d-flex align-items-center justify-content-center">
                        <div class="bg-primary text-white rounded-circle fw-bold d-flex align-items-center justify-content-center shadow-sm" style="width: 40px; height: 40px; font-size: 1.5rem;">
                            =
                        </div>
                    </div>

                    <!-- Total Laba Bersih -->
                    <div class="col-md-4 col-xl-4">
                         <div class="p-3 rounded-3 text-white shadow-sm h-100 d-flex flex-column justify-content-center" style="background:linear-gradient(to right, #4e73df, #224abe);">
                            <small class="text-uppercase fw-bold text-white-50" style="font-size:0.75rem;">Total Laba Bersih</small>
                            <h4 class="mb-0 fw-bold text-break">Rp <?= number_format($totalLaba ?? 0) ?></h4>
                        </div>
                    </div>
                </div>

                <!-- TABLE -->
                <div class="table-responsive rounded-3 border mb-4">
                    <table class="table table-bordered table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="py-3 ps-3 text-center" width="50">No</th>
                                <th class="py-3">Nama Barang</th>
                                <th class="py-3 text-center">Bobot</th>
                                <th class="py-3 text-end">Settlement Alokasi</th>
                                <th class="py-3 text-end">Modal Periode</th>
                                <th class="py-3 text-end pe-3">Laba Alokasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($preview as $i => $row): ?>
                                <tr>
                                    <td class="text-center"><?= $i + 1 ?></td>
                                    <td class="fw-medium text-break"><?= esc($row['nama']) ?></td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary rounded-pill"><?= number_format($row['bobot'], 1) ?>%</span>
                                    </td>
                                    <td class="text-end">Rp <?= number_format($row['settlement_barang'] ?? 0) ?></td>
                                    <td class="text-end">Rp <?= number_format($row['modal'] ?? 0) ?></td>
                                    <td class="text-end pe-3 fw-bold <?= ($row['laba'] ?? 0) >= 0 ? 'text-success' : 'text-danger' ?>">
                                        Rp <?= number_format($row['laba'] ?? 0) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="alert alert-light border d-flex gap-3 align-items-start mb-4">
                     <i class="bi bi-info-circle text-primary mt-1"></i>
                     <small class="text-muted">
                        Angka di atas adalah estimasi. Total Settlement dialokasikan ke setiap barang berdasarkan persentase bobot yang Anda input. Laba = Settlement Alokasi - Modal Periode.
                     </small>
                </div>

                <form action="<?= base_url('/tiktok/transaksi/saveLaba/' . $toko['id_toko']) ?>" method="post">
                    <button class="btn btn-primary-custom w-100 py-3 fs-6 shadow-sm">
                         <i class="bi bi-save me-2"></i> Simpan Hasil Perhitungan
                    </button>
                </form>
            </div>
        </div>
    <?php endif; ?>



    <!-- CARD 4: RIWAYAT LABA -->
    <div class="card shadow mb-5">
        <div class="card-header py-3 text-white d-flex justify-content-between align-items-center" style="background: linear-gradient(45deg, #1cc88a, #13855c);">
            <h6 class="m-0 font-weight-bold"><i class="bi bi-clock-history me-2"></i>Riwayat Perhitungan Laba</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="tableLabaHistory" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">No</th>
                            <th>Dibuat Pada</th>
                            <th>Periode Data</th>
                            <th class="text-end">Total Pendapatan</th>
                            <th class="text-end">Total Laba Bersih</th>
                            <th width="10%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($historyLaba as $i => $row): ?>
                            <tr>
                                <td class="text-center"><?= $i + 1 ?></td>
                                <td>
                                    <div class="fw-medium text-dark"><?= date('d M Y', strtotime($row['created_at'])) ?></div>
                                    <small class="text-muted"><?= date('H:i', strtotime($row['created_at'])) ?> WIB</small>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">
                                        <?= date('d M', strtotime($row['periode_start'])) ?> - <?= date('d M Y', strtotime($row['periode_end'])) ?>
                                    </span>
                                </td>
                                <td class="text-end">Rp <?= number_format($row['total_revenue']) ?></td>
                                <td class="text-end fw-bold <?= $row['total_laba'] >= 0 ? 'text-success' : 'text-danger' ?>">
                                    Rp <?= number_format($row['total_laba']) ?>
                                </td>
                                <td class="text-center">
                                    <a href="<?= base_url('/tiktok/transaksi/deleteLabaHistory/' . $toko['id_toko'] . '?date=' . urlencode($row['created_at'])) ?>"
                                       class="btn btn-sm btn-outline-danger btn-delete"
                                       data-title="Hapus Riwayat?"
                                       data-text="Hapus riwayat perhitungan ini beserta detail itemnya?">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<!-- DataTables CDN -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

<style>
    :root {
        --primary: #4361ee;
        --primary-soft: rgba(67, 97, 238, .12);
        --radius: 14px;
        --shadow: 0 10px 30px rgba(0, 0, 0, .08);
        --transition: .25s ease;
    }

    body {
        background: #f4f6fb;
        font-family: 'Inter', 'Segoe UI', sans-serif;
        color: #2b2f38;
    }

    /* ===== CARD ===== */
    .card-form {
        max-width: 1400px;
        margin: auto;
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        border: none;
    }

    /* ===== HEADER ===== */
    .card-header-custom {
        background: linear-gradient(135deg, var(--primary), #5f7cff);
        color: white;
        border-radius: var(--radius) var(--radius) 0 0;
        padding: 24px 28px;
    }

    .card-header-custom h4 {
        margin: 0;
        font-weight: 600;
    }

    .card-header-custom p {
        margin: 6px 0 0;
        opacity: .9;
        font-size: .9rem;
    }

    /* ===== FORM ===== */
    .form-label {
        font-weight: 600;
        font-size: .85rem;
        margin-bottom: 6px;
    }

    .form-control {
        border-radius: 10px;
        padding: 10px 14px;
        font-size: .9rem;
    }

    .form-control:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 .15rem var(--primary-soft);
    }

    .input-group-text {
        background: #f1f3f9;
        border: none;
        font-weight: 600;
    }

    /* ===== HELP TEXT ===== */
    .form-text {
        font-size: .75rem;
        color: #6c757d;
    }

    /* ===== BUTTON ===== */
    .btn-primary-custom {
        background: var(--primary);
        border: none;
        border-radius: 12px;
        font-weight: 600;
        padding: 12px;
        transition: var(--transition);
    }

    .btn-primary-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(67, 97, 238, .35);
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 768px) {
        .card-header-custom {
            padding: 20px;
        }
    }
</style>


<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- Include Bootstrap Datepicker -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
<!-- Include SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.id.min.js"></script>

<script>
    $(document).ready(function() {
        // Init DataTable Laba History
        $('#tableLabaHistory').DataTable({
            "pageLength": 5,
            "lengthMenu": [5, 10, 25, 50],
            "scrollX": true,
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json"
            }
        });

        // Initialize datepicker
        $('.input-group.date').datepicker({
            format: "dd-mm-yyyy",
            language: "id",
            autoclose: true,
            todayHighlight: true,
            orientation: "bottom auto"
        });

        // --- VALIDASI INPUT FORM ---
        $('#formItem').on('submit', function(e) {
            let valid = true;
            let errorMsg = '';

            // Validasi Harga & Modal Periode tidak boleh astronomical
            const maxVal = 100000000000; // 100 Milyar limit
            const inputs = ['harga_modal', 'modal_periode'];

            inputs.forEach(function(name) {
                let val = $('input[name="' + name + '"]').val();
                if (val > maxVal) {
                    valid = false;
                    errorMsg = 'Nilai ' + name.replace('_', ' ') + ' terlalu besar (Max 100 Milyar). Harap cek kembali input Anda.';
                }
            });

            // Validasi Panjang Teks
            if ($('input[name="nama_barang"]').val().length > 100) {
                 valid = false;
                 errorMsg = 'Nama barang terlalu panjang (Max 100 karakter).';
            }

             // Validasi Bobot
            let bobot = parseFloat($('input[name="bobot"]').val());
            if (bobot < 0 || bobot > 100) {
                valid = false;
                errorMsg = 'Bobot harus di antara 0 - 100%.';
            }

            if (!valid) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Input Tidak Valid',
                    text: errorMsg,
                    confirmButtonColor: '#4361ee'
                });
            }
        });

        // --- SWEETALERT UNTUK DELETE / ACTION ---
        $(document).on('click', '.btn-delete, .reset-btn', function(e) {
            e.preventDefault();
            var url = $(this).attr('href') || $(this).data('url'); // Support href or data-url
            var title = $(this).data('title') || 'Konfirmasi';
            var text = $(this).data('text') || 'Apakah Anda yakin?';
            
            // Khusus reset button punya logic text sendiri sebelumnya, kita override jika ada data attributes
            // Tapi untuk menjaga kompatibilitas script lama reset-btn:
            if ($(this).hasClass('reset-btn')) {
                 var type = $(this).data('type');
                 var typeText = type === 'laba' ? 'laba' : 'pendapatan';
                 title = 'Reset Data Preview?';
                 text = 'Data preview ' + typeText + ' yang belum disimpan akan hilang.';
            }

            Swal.fire({
                title: title,
                text: text,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Lanjutkan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
        });
    });
</script>

<?= $this->endSection() ?>