<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Shopee
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row mb-4">
    <div class="col-md-12 d-flex align-items-center">
        <div class="card shadow-sm p-3 w-100 d-flex flex-row align-items-center">
            <img src="<?= base_url('img/shopeelogo.png') ?>"
                alt="Shopee Logo"
                style="height: 55px; margin-right: 15px;">
            <h4 class="m-0 font-weight-bold text-dark">Dashboard Shopee</h4>
        </div>
    </div>
</div>


<div class="row mb-4">
    <div class="col-md-6">
        <div class="alert alert-info d-inline-block mb-0">
            <i class="fas fa-calendar-alt"></i>
            <strong>Tanggal:</strong>
            <?= date('l, d F Y') ?>
        </div>
    </div>
    <div class="col-md-6 text-md-right mt-2 mt-md-0">
        <a href="/shopee/pendapatan" class="btn btn-success me-2">
             <i class="fas fa-chart-line"></i> Lihat Pendapatan
        </a>
        <a href="/shopee/toko" class="btn btn-info">
            <i class="fas fa-store"></i> Kelola Toko
        </a>
    </div>
</div>

<!-- Statistik Cards -->
<div class="row mb-4">
    <!-- Jumlah Toko -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Jumlah Toko</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?= $total_toko ?? 0 ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-store fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Toko Aktif -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Toko Aktif</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?= $total_toko_aktif ?? 0 ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Toko Nonaktif -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                            Toko Nonaktif</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?= $total_toko_nonaktif ?? 0 ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Daftar Toko -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Daftar Toko Shopee</h6>
                </div>
                <div class="card-body">

                    <?php if (empty($toko)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-store-slash fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Belum ada toko Shopee</h5>
                            <p class="text-muted">Tambahkan toko Shopee Anda untuk memulai</p>
                            <a href="/shopee/toko" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Tambah Toko Pertama
                            </a>
                        </div>
                    <?php else: ?>

                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead class="thead-dark">
                                    <tr>
                                        <th width="50">No</th>
                                        <th>Nama Toko</th>
                                        <th>Alamat</th>
                                        <th width="120">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    <?php
                                    if (!isset($pager)) {
                                        $no = 1;
                                    } else {
                                        $currentPage = $pager->getCurrentPage('toko_shopee') ?? 1;
                                        $perPage = $pager->getPerPage('toko_shopee') ?? 10;
                                        $no = 1 + (($currentPage - 1) * $perPage);
                                    }
                                    ?>

                                    <?php foreach ($toko as $t): ?>
                                        <tr>
                                            <td class="text-center"><?= $no++ ?></td>
                                            <td><?= esc($t['nama_toko'] ?? '-') ?></td>
                                            <td><?= esc($t['alamat'] ?? '-') ?></td>

                                            <td class="text-center">
                                                <a href="/shopee/detail/<?= $t['id_toko'] ?>"
                                                    class="btn btn-info btn-sm"
                                                    title="Lihat Detail"
                                                    data-toggle="tooltip">
                                                    <i class="fas fa-eye"></i>
                                                </a>

                                                <a href="/shopee/transaksi/<?= $t['id_toko'] ?>"
                                                    class="btn btn-success btn-sm"
                                                    title="Lihat Transaksi"
                                                    data-toggle="tooltip">
                                                    <i class="fas fa-rupiah-sign"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>

                                </tbody>
                            </table>
                        </div>


                        <!-- PAGINATION -->
                        <div class="mt-3">
                            <?= $pager->links('toko_shopee', 'bootstrap_pagination') ?>
                        </div>

                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>

    <!-- Laporan Ringkas Toko -->
    <?php if (!empty($transaksi)): ?>
        <div class="row">
            <div class="col-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Ringkasan Performa Toko (<?= date('F Y', mktime(0, 0, 0, $bulan, 1, $tahun)) ?>)</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Nama Toko</th>
                                        <th>Total Transaksi</th>
                                        <th>Pendapatan Bersih</th>
                                        <th>Rata-rata per Transaksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($transaksi as $trx): ?>
                                        <tr>
                                            <td><?= esc($trx['nama_toko'] ?? '-') ?></td>
                                            <td class="text-center"><?= $trx['total_transaksi'] ?? 0 ?></td>
                                            <td class="text-right">Rp <?= number_format($trx['pendapatan_bersih'] ?? 0, 0, ',', '.') ?></td>
                                            <td class="text-right">Rp <?= number_format(($trx['pendapatan_bersih'] ?? 0) / max(($trx['total_transaksi'] ?? 1), 1), 0, ',', '.') ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <script>
        // Initialize tooltips
        $(function() {
            $('[data-toggle="tooltip"]').tooltip()
        });

        // SweetAlert untuk notifikasi
        <?php if (session()->getFlashdata('success')): ?>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: '<?= session()->getFlashdata('success') ?>',
                timer: 3000,
                showConfirmButton: false
            });
        <?php endif; ?>
    </script>

    <?= $this->endSection() ?>