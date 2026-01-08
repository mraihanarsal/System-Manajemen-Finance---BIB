<?= $this->extend('layouts/main') ?>
<?= $this->section('title') ?>Detail TikTok - <?= esc($toko['nama_toko']) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="container-fluid">

    <!-- ============================ -->
    <!-- HEADER TOKO -->
    <!-- ============================ -->
    <div class="row mb-4">
        <div class="col-md-12">

            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb bg-white px-3 py-2 shadow-sm rounded">
                    <li class="breadcrumb-item">
                        <a href="<?= base_url('/tiktok') ?>" class="text-decoration-none">Dashboard TikTok</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        <?= esc($toko['nama_toko']) ?>
                    </li>
                </ol>
            </nav>

            <!-- Title & Actions -->
            <div class="d-flex justify-content-between align-items-center bg-white p-4 shadow-sm rounded">
                <div class="d-flex align-items-center">
                    <img src="<?= base_url('img/tiktoklogo.png') ?>" alt="Logo" style="height: 48px; margin-right: 15px;">
                    <div>
                        <h4 class="mb-0 font-weight-bold text-gray-800"><?= esc($toko['nama_toko']) ?></h4>
                        <small class="text-muted"><i class="fas fa-map-marker-alt"></i> <?= esc($toko['alamat'] ?? '-') ?></small>
                    </div>
                </div>
                <div>
                    <a href="<?= base_url('/tiktok/transaksi/pendapatan/' . $toko['id_toko']) ?>" class="btn btn-primary shadow-sm">
                        <i class="fas fa-upload fa-sm text-white-50"></i> Upload Pendapatan
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================ -->
    <!-- FILTER TANGGAL -->
    <!-- ============================ -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow-sm border-left-primary">
                <div class="card-body py-2">
                    <form action="" method="get" class="row align-items-center g-3" id="filterForm">
                        <div class="col-auto">
                            <label class="col-form-label fw-bold">Periode:</label>
                        </div>
                        <div class="col-auto">
                            <input type="date" name="start" class="form-control form-control-sm" value="<?= esc($start) ?>">
                        </div>
                        <div class="col-auto">
                            <span>s/d</span>
                        </div>
                        <div class="col-auto">
                            <input type="date" name="end" class="form-control form-control-sm" value="<?= esc($end) ?>">
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-sm btn-primary">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================ -->
    <!-- SUMMARY CARDS -->
    <!-- ============================ -->
    <div class="row mb-4">
        <!-- Earnings (Monthly) Card Example -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Settlement (Pendapatan)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rp <?= number_format($summary['settlement'] ?? 0) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Earnings (Monthly) Card Example -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Total Modal (Per-barang)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rp <?= number_format($summary['harga_modal'] ?? 0) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-box fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Profit Card Example -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Net Profit (Laba Bersih)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rp <?= number_format($summary['profit'] ?? 0) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================ -->
    <!-- TABEL TRANSAKSI -->
    <!-- ============================ -->
    <!-- ============================ -->
    <!-- TABEL 1: RIWAYAT UPLOAD PENDAPATAN -->
    <!-- ============================ -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-gradient-primary text-white d-flex justify-content-between align-items-center" style="background: linear-gradient(45deg, #4e73df, #224abe);">
            <h6 class="m-0 font-weight-bold"><i class="fas fa-file-invoice-dollar me-2"></i>Riwayat Upload Pendapatan</h6>
            <a href="<?= base_url('/tiktok/transaksi/deleteAllPendapatan/' . $toko['id_toko']) ?>" 
               class="btn btn-sm btn-danger btn-delete shadow"
               data-title="Hapus SEMUA Data Pendapatan?"
               data-text="PERINGATAN: Tindakan ini akan menghapus SELURUH riwayat upload pendapatan untuk toko ini. Data yang sudah dihapus tidak dapat dikembalikan.">
               <i class="fas fa-trash-alt me-1"></i> Hapus Semua
            </a>
        </div>
        <div class="card-body">
            <div class="alert alert-info py-2 mb-3">
                <small><i class="fas fa-info-circle me-1"></i> Data hasil upload file laporan TikTok. Angka ini digunakan sebagai dasar Settlement sebelum dialokasikan ke barang.</small>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="tablePendapatan" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">No</th>
                            <th>Tgl Upload</th>
                            <th>Periode Laporan</th>
                            <th>Total Settlement</th>
                            <th>Keterangan</th>
                            <th width="5%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($riwayatPendapatan as $i => $row): ?>
                            <tr>
                                <td class="text-center"><?= $i + 1 ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($row['created_at'])) ?></td>
                                <td>
                                    <span class="badge bg-primary">
                                        <?= date('d M Y', strtotime($row['periode_start'])) ?> - <?= date('d M Y', strtotime($row['periode_end'])) ?>
                                    </span>
                                </td>
                                <td class="text-end fw-bold">Rp <?= number_format($row['settlement']) ?></td>
                                <td>
                                    Pendapatan Bersih (Settlement) dari Laporan
                                </td>
                                <td class="text-center">
                                     <a href="<?= base_url('/tiktok/transaksi/delete/' . $row['id']) ?>" 
                                        class="btn btn-sm btn-outline-danger btn-delete"
                                        data-title="Hapus Data Pendapatan?"
                                        data-text="Menghapus ini tidak akan menghapus data Laba yang sudah dihitung. Lanjutkan?">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ============================ -->
    <!-- TABEL 2: RIWAYAT PERHITUNGAN LABA -->
    <!-- ============================ -->
    <div class="card shadow mb-5">
        <div class="card-header py-3 text-white d-flex justify-content-between align-items-center" style="background: linear-gradient(45deg, #1cc88a, #13855c);">
            <h6 class="m-0 font-weight-bold"><i class="fas fa-calculator me-2"></i>Riwayat Perhitungan Laba</h6>
            <a href="<?= base_url('/tiktok/transaksi/deleteAllLaba/' . $toko['id_toko']) ?>"
               class="btn btn-sm btn-danger btn-delete shadow" 
               data-title="Hapus SEMUA Data Laba?"
               data-text="PERINGATAN: Tindakan ini akan menghapus SELURUH riwayat perhitungan laba untuk toko ini. Data yang sudah dihapus tidak dapat dikembalikan.">
               <i class="fas fa-trash-alt me-1"></i> Hapus Semua
            </a>
        </div>
        <div class="card-body">
            <div class="alert alert-success py-2 mb-3">
                 <small><i class="fas fa-check-circle me-1"></i> Detail laba bersih per barang hasil alokasi settlement - modal.</small>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="tableLaba" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">No</th>
                            <th>Tgl Hitung</th>
                            <th>Nama Barang</th>
                            <th class="text-end">Modal</th>
                            <th class="text-end">Settlement Alokasi</th>
                            <th class="text-end">Laba Bersih</th>
                            <th>Keterangan</th>
                            <th width="5%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($riwayatLaba as $i => $row): ?>
                            <tr>
                                <td class="text-center"><?= $i + 1 ?></td>
                                <td><?= date('d/m/Y', strtotime($row['created_at'])) ?></td>
                                <td>
                                    <span class="fw-bold"><?= esc($row['nama_barang']) ?></span><br>
                                    <small class="text-muted"><?= esc($row['kategori']) ?></small>
                                </td>
                                <td class="text-end">Rp <?= number_format($row['harga_modal']) ?></td>
                                <td class="text-end">Rp <?= number_format($row['settlement']) ?></td>
                                <td class="text-end fw-bold <?= $row['profit'] >= 0 ? 'text-success' : 'text-danger' ?>">
                                    Rp <?= number_format($row['profit']) ?>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        Laba Bersih <?= esc($row['nama_barang']) ?>
                                        <?php if($row['harga_modal'] > 0): ?>
                                            (Margin: <?= round(($row['profit'] / $row['harga_modal']) * 100, 1) ?>%)
                                        <?php endif; ?>
                                    </small>
                                </td>
                                <td class="text-center">
                                     <a href="<?= base_url('/tiktok/transaksi/delete/' . $row['id']) ?>" 
                                        class="btn btn-sm btn-outline-danger btn-delete"
                                        data-title="Hapus Data Laba?"
                                        data-text="Data laba untuk barang ini akan dihapus.">
                                        <i class="fas fa-trash"></i>
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

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- DataTables CDN -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

<script>
    $(document).ready(function() {
        // Init DataTable Pendapatan
        $('#tablePendapatan').DataTable({
            "pageLength": 5,
            "lengthMenu": [5, 10, 25, 50],
            "scrollX": true,
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json"
            }
        });

        // Init DataTable Laba
        $('#tableLaba').DataTable({
            "pageLength": 5,
            "lengthMenu": [5, 10, 25, 50],
            "scrollX": true,
            "order": [[ 0, "asc" ]], // Urutkan berdasarkan No
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json"
            }
        });
    });

    document.getElementById('filterForm').addEventListener('submit', function(e) {
        var start = document.querySelector('input[name="start"]').value;
        var end = document.querySelector('input[name="end"]').value;

        if (start && end) {
            if (start > end) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Periode Tidak Valid',
                    text: 'Tanggal awal tidak boleh lebih besar dari tanggal akhir!',
                    confirmButtonColor: '#4e73df'
                });
            }
        }
    });

    // SweetAlert untuk Delete
    $(document).on('click', '.btn-delete', function(e) {
        e.preventDefault();
        var url = $(this).attr('href');
        var title = $(this).data('title') || 'Konfirmasi Hapus';
        var text = $(this).data('text') || 'Apakah Anda yakin ingin menghapus data ini?';

        Swal.fire({
            title: title,
            text: text,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
    });
</script>
<?= $this->endSection() ?>