<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Pendapatan & Laba TikTok
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="d-flex align-items-center mb-4">
    <a href="<?= base_url('tiktok') ?>" class="btn btn-secondary btn-sm me-3">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
    <h1 class="h3 mb-0 text-gray-800 fw-bold ml-3">Pendapatan & Laba Keseluruhan TikTok</h1>
</div>

<!-- TOTAL PER TAHUN (HIGHLIGHT + TABLE) -->
<div class="row mb-3">
    <div class="col-md-12">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h6 class="m-0 font-weight-bold">Ringkasan Tahunan</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Bagian Kiri: Highlight Tahun Terbaru -->
                    <div class="col-md-5 d-flex flex-column align-items-center justify-content-center border-right">
                        <?php 
                            $currentYear = date('Y');
                            $latest = [
                                'periode_tahun' => $currentYear,
                                'total_pendapatan' => 0,
                                'total_laba' => 0
                            ];

                            // Cek apakah data terbaru dari DB adalah tahun ini
                            if (!empty($history_tahunan) && $history_tahunan[0]['periode_tahun'] == $currentYear) {
                                $latest = $history_tahunan[0];
                            }
                        ?>
                            
                        <div class="text-center py-3 border-bottom w-100">
                            <h5 class="font-weight-bold text-gray-600 mb-1">Total Pendapatan Tahun <?= esc($latest['periode_tahun']) ?></h5>
                            <h2 class="font-weight-bold text-success mb-0">
                                Rp <?= number_format($latest['total_pendapatan'], 0, ',', '.') ?>
                            </h2>
                        </div>

                        <div class="text-center py-3 w-100">
                            <h5 class="font-weight-bold text-gray-600 mb-1">Total Laba Bersih Tahun <?= esc($latest['periode_tahun']) ?></h5>
                            <h2 class="font-weight-bold text-info mb-0">
                                Rp <?= number_format($latest['total_laba'], 0, ',', '.') ?>
                            </h2>
                            <small class="text-muted mt-2 d-block"><i class="fas fa-check-circle text-info"></i> Otomatis Update</small>
                        </div>
                    </div>

                    <!-- Bagian Kanan: Tabel Riwayat Tahunan -->
                    <div class="col-md-7">
                        <h6 class="font-weight-bold text-gray-800 mb-3">Riwayat Per Tahun</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm table-hover" id="tblTahunan" width="100%" cellspacing="0">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="text-center" width="10%">No</th>
                                        <th class="text-center">Tahun</th>
                                        <th class="text-right">Pendapatan</th>
                                        <th class="text-right">Laba Bersih</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($history_tahunan)): ?>
                                        <?php $no = 1; foreach ($history_tahunan as $yr): ?>
                                            <tr class="<?= $no == 1 ? 'table-primary font-weight-bold' : '' ?>">
                                                <td class="text-center"><?= $no++ ?></td>
                                                <td class="text-center"><?= esc($yr['periode_tahun']) ?></td>
                                                <td class="text-right text-success">Rp <?= number_format($yr['total_pendapatan'], 0, ',', '.') ?></td>
                                                <td class="text-right text-info">Rp <?= number_format($yr['total_laba'], 0, ',', '.') ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- TOTAL PER BULAN (TABLE) -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
         <h6 class="m-0 font-weight-bold text-primary">Rincian Per Bulan</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="tblPendapatan" width="100%" cellspacing="0">
                <thead class="bg-primary text-white">
                    <tr>
                        <th width="5%">No</th>
                        <th>Bulan</th>
                        <th>Tahun</th>
                        <th>Pendapatan</th>
                        <th>Laba Bersih</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($history_bulanan)): ?>
                        <?php $no=1; foreach($history_bulanan as $row): ?>
                        <tr>
                            <td class="text-center"><?= $no++ ?></td>
                            <td data-order="<?= $row['periode_bulan'] ?>"><?= date('F', mktime(0, 0, 0, $row['periode_bulan'], 10)) ?></td>
                            <td data-order="<?= $row['periode_tahun'] ?>"><?= $row['periode_tahun'] ?></td>
                            <td class="text-end fw-bold text-success">Rp <?= number_format($row['total_pendapatan'], 0, ',', '.') ?></td>
                            <td class="text-end fw-bold text-info">Rp <?= number_format($row['total_laba'], 0, ',', '.') ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        $('#tblPendapatan').DataTable({
            "order": [[ 2, "desc" ], [ 1, "desc" ]] // Sort by Year DESC, then Month DESC
        });

        $('#tblTahunan').DataTable({
            "order": [[ 1, "desc" ]], // Sort by Year DESC
            "pageLength": 5, // Default 5 items per page for smaller table
            "lengthMenu": [5, 10, 25]
        });
    });
</script>
<?= $this->endSection() ?>
