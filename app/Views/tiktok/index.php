<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Tiktok
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- ===================== -->
<!-- HEADER BRANDING TIKTOK -->
<!-- ===================== -->
<div class="row mb-4">
    <div class="col-md-12 d-flex align-items-center">
        <div class="card shadow-sm p-3 w-100 d-flex flex-row align-items-center">
            <img src="<?= base_url('img/tiktoklogo.png') ?>"
                alt="TikTok Logo"
                style="height: 55px; margin-right: 15px;">
            <h4 class="m-0 font-weight-bold text-dark">Dashboard TikTok</h4>
        </div>
    </div>
</div>

<!-- Informasi tanggal + button toko -->
<div class="row mb-2">
    <div class="col-md-6">
        <div class="alert alert-info d-inline-block">
            <i class="fas fa-calendar-alt"></i>
            <strong>Tanggal:</strong>
            <?= date('l, d F Y') ?>
        </div>
    </div>

    <div class="col-md-6 text-right">
        <a href="/tiktok/pendapatan" class="btn btn-success mr-2">
            <i class="fas fa-chart-line"></i> Lihat Pendapatan
        </a>
        <a href="/tiktok/toko" class="btn btn-info mr-2">
            <i class="fas fa-store"></i> Kelola Toko
        </a>
    </div>
</div>

<!-- ========================== -->
<!-- CARD STATISTIK TIKTOK -->
<!-- ========================== -->
<div class="row">

    <!-- Total Toko -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Jumlah Toko
                        </div>
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
                            Toko Aktif
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?= $active_toko ?? 0 ?>
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
                            Toko Tidak Aktif
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?= $inactive_toko ?? 0 ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ===================== -->
<!-- DAFTAR TOKO -->
<!-- ===================== -->
<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Daftar Toko Tiktok</h6>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th width="50">No</th>
                                <th>Nama Toko</th>
                                <th>Alamat</th>
                                <th width="190">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>

                            <?php
                            $no = isset($pager)
                                ? 1 + ($pager->getCurrentPage('default') - 1) * $pager->getPerPage('default')
                                : 1;
                            ?>

                            <?php foreach ($toko as $t): ?>
                                <tr>
                                    <td class="text-center"><?= $no++ ?></td>
                                    <td><?= esc($t['nama_toko']) ?></td>
                                    <td><?= esc($t['alamat']) ?></td>
                                    <td>
                                        <a href="/tiktok/detail/<?= esc($t['id_toko']) ?>" class="btn btn-info">
                                            <i title="Lihat Detail" class="fas fa-file-alt"></i>
                                        </a>
                                        <a href="<?= base_url('tiktok/transaksi/pendapatan/' . $t['id_toko']) ?>"
                                            class="btn btn-primary">
                                            <i title="Upload Transaksi" class="fas fa-upload"></i>
                                        </a>

                                    </td>
                                </tr>
                            <?php endforeach; ?>

                        </tbody>
                    </table>
                </div>

                <!-- PAGINATION -->
                <div class="mt-3">
                    <?php if (isset($pager)): ?>
                        <?= $pager->links('default', 'bootstrap_pagination') ?>
                    <?php endif; ?>
                </div>

            </div>

        </div>
    </div>
</div>

<?= $this->endSection() ?>