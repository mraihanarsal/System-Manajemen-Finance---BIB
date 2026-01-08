<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Detail Toko Saya
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-body">
                <!-- Breadcrumb -->
                <nav aria-label="breadcrumb" class="mb-3">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="/shopee" class="text-decoration-none">
                                <i class="fas fa-store me-1"></i>Shopee
                            </a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            <?= esc($nama_toko) ?>
                        </li>
                    </ol>
                </nav>

                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h3 class="mb-1"><?= esc($nama_toko) ?></h3>
                        <p class="text-muted mb-0">
                            <i class="fas fa-calendar me-1"></i>Periode aktif: <?= esc($periode_aktif) ?>
                        </p>
                    </div>

                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        <div class="bg-light rounded p-3">
                            <div class="h4 mb-1 text-success">Rp <?= number_format($total_pendapatan, 0, ',', '.') ?></div>
                            <small class="text-muted">Total Semua Waktu</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistik Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Pendapatan</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">Rp <?= number_format($total_pendapatan, 0, ',', '.') ?></div>
                    </div>
                    <div><i class="fas fa-dollar-sign fa-2x text-gray-300"></i></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Rata-rata Per Laporan</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">Rp <?= number_format($rata_rata_bulanan, 0, ',', '.') ?></div>
                    </div>
                    <div><i class="fas fa-chart-line fa-2x text-gray-300"></i></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Jumlah Laporan</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $jumlah_laporan ?></div>
                    </div>
                    <div><i class="fas fa-file-pdf fa-2x text-gray-300"></i></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Performa</div>
                        <div class="h6 mb-0 font-weight-bold text-gray-800"><?= esc($performance_status) ?></div>
                        <small class="text-muted">Fluktuasi: <?= esc($performance_score) ?>%</small>
                    </div>
                    <div><i class="fas fa-tachometer-alt fa-2x text-gray-300"></i></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- FILTER -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Filter Rentang Tanggal</h6>
            </div>

            <div class="card-body">
                <form id="filterForm" action="/shopee/detail/<?= $id_toko ?>" method="get">

                    <div class="row align-items-end">
                        <div class="col-lg-4 mb-3">
                            <label class="form-label">Tanggal Dari</label>
                            <div class="input-group">
                                <input type="date" class="form-control" id="tanggal_dari" name="tanggal_dari"
                                    value="<?= esc($tanggal_dari ?? '') ?>">
                            </div>
                        </div>

                        <div class="col-lg-4 mb-3">
                            <label class="form-label">Tanggal Sampai</label>
                            <div class="input-group">
                                <input type="date" class="form-control" id="tanggal_sampai" name="tanggal_sampai"
                                    value="<?= esc($tanggal_sampai ?? '') ?>">
                            </div>
                        </div>

                        <div class="col-lg-4 mb-3">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter"></i> Filter
                                </button>
                                <button type="button" id="btnClear" class="btn btn-secondary">
                                    <i class="fas fa-redo"></i> Reset
                                </button>
                            </div>

                            <?php if (!empty($tanggal_dari) && !empty($tanggal_sampai)): ?>
                                <div class="mt-2">
                                    <span class="badge bg-info">
                                        Periode: <?= $tanggal_dari ?> s/d <?= $tanggal_sampai ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- TOTAL PER BULAN (CARD LIST) -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header">
                <strong>Total Pendapatan per Bulan</strong>
                <small class="text-muted ml-2"> (menampilkan 12 bulan terakhir atau sesuai rentang)</small>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php if (!empty($per_bulan)): ?>
                        <?php foreach ($per_bulan as $ym => $val): ?>
                            <div class="col-md-3 mb-2">
                                <div class="card border-secondary">
                                    <div class="card-body p-2">
                                        <div class="small text-muted"><?= date('M Y', strtotime($ym . '-01')) ?></div>
                                        <div class="font-weight-bold">Rp <?= number_format($val, 0, ',', '.') ?></div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12">
                            <p class="text-muted mb-0">Belum ada data per bulan untuk periode ini.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- TOTAL PER TAHUN (CARD LIST) -->
<div class="row mb-3">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header">
                <strong>Total Pendapatan per Tahun</strong>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php if (!empty($per_tahun)): ?>
                        <?php foreach ($per_tahun as $yr => $val): ?>
                            <div class="col-md-3 mb-2">
                                <div class="card border-secondary">
                                    <div class="card-body p-2">
                                        <div class="small text-muted"><?= esc($yr) ?></div>
                                        <div class="font-weight-bold">Rp <?= number_format($val, 0, ',', '.') ?></div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12">
                            <p class="text-muted mb-0">Belum ada data per tahun untuk periode ini.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- DATA LAPORAN DETAIL -->
<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <strong>Data Laporan Detail</strong>
                <span class="badge badge-primary">Total Data: <?= isset($jumlah_laporan) ? $jumlah_laporan : '0' ?></span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead class="table-dark">
                            <tr>
                                <th width="50">No</th>
                                <th>Periode Laporan</th>
                                <th class="text-right">Total Penghasilan</th>
                                <th>Nama Toko</th>
                                <th>Tanggal Upload</th>
                                <th>Report Code</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($reports)): ?>
                                <?php $no = 1 + (($pager->getCurrentPage() - 1) * $pager->getPerPage()); ?>
                                <?php foreach ($reports as $r): ?>
                                    <tr>
                                        <td class="text-center"><?= $no++ ?></td>
                                        <td><?= date('d M Y', strtotime($r['periode_awal'])) ?> - <?= date('d M Y', strtotime($r['periode_akhir'])) ?></td>
                                        <td class="text-right"><strong>Rp <?= number_format($r['total_penghasilan'], 0, ',', '.') ?></strong></td>
                                        <td><?= esc($r['username'] ?? '-') ?></td>
                                        <td><?= date('d M Y', strtotime($r['tanggal_upload'])) ?></td>
                                        <td><small><?= esc($r['report_code'] ?? '-') ?></small></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center">Tidak ada data laporan untuk periode yang dipilih.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if (isset($pager) && $pager->getPageCount() > 1): ?>
                    <div class="mt-3">
                        <?= $pager->links('default', 'bootstrap_pagination') ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Reset filter
        document.getElementById('btnClear').addEventListener('click', function() {
            document.getElementById('tanggal_dari').value = '';
            document.getElementById('tanggal_sampai').value = '';
            window.location.href = '/shopee/detail/<?= $id_toko ?>';
        });

        // Validasi tanggal
        document.getElementById('filterForm').addEventListener('submit', function(e) {
            const dari = document.getElementById('tanggal_dari').value;
            const sampai = document.getElementById('tanggal_sampai').value;

            if (dari && sampai && new Date(dari) > new Date(sampai)) {
                e.preventDefault();
                alert('Tanggal "Dari" tidak boleh lebih besar dari tanggal "Sampai"');
                return false;
            }
        });
    });
</script>
<?= $this->endSection() ?>