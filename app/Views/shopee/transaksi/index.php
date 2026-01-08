<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<h1 class="h3 mb-4 text-gray-800"></h1>

<div class="container mt-4">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="card-title">Upload PDF Shopee (Max 2MB)</h4>

            <form action="/shopee/transaksi/upload/<?= $id_toko ?>" method="POST" enctype="multipart/form-data" class="d-flex gap-2">
                <input type="file" name="pdf_file" accept="application/pdf" required class="form-control">
                <button type="submit" class="btn btn-primary">Upload</button>
            </form>
            <span class="badge badge-info"><?= esc($nama_toko) ?></span>
        </div>

        <div class="card-body">
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success">
                    <?= session()->getFlashdata('success') ?>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger">
                    <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>

            <a href="/shopee">
                <h6 class="m-0 font-weight-bold text-primary">Kembali ke Shopee</h6>
            </a>

            <table class="table table-bordered table-striped mt-3">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Nama Toko</th>
                        <th>Periode</th>
                        <th>Total Penghasilan</th>
                        <th>Tanggal Upload</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($reports)): ?>
                        <?php $no = 1 + (($pager->getCurrentPage() - 1) * $pager->getPerPage()); ?>
                        <?php foreach ($reports as $r): ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= esc($r['username']) ?></td>
                                <td>
                                    <?= esc($r['periode_awal']) ?>
                                    s/d
                                    <?= esc($r['periode_akhir']) ?>
                                </td>
                                <td>Rp <?= number_format($r['total_penghasilan'], 0, ',', '.') ?></td>
                                <td><?= esc($r['tanggal_upload']) ?></td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-danger"
                                        onclick="hapusTransaksi('<?= $r['id'] ?>')">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">Belum ada data.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <!-- PAGINATION -->
            <?php if (isset($pager) && $pager->getPageCount() > 1): ?>
                <div class="mt-3">
                    <?= $pager->links('default', 'bootstrap_pagination') ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<script>
    function hapusTransaksi(id) {
        Swal.fire({
            title: "Hapus Laporan PDF?",
            text: "Data transaksi & file PDF akan dihapus permanen!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#6c757d",
            confirmButtonText: "Ya, Hapus",
            cancelButtonText: "Batal"
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "/shopee/transaksi/delete/" + id;
            }
        });
    }
</script>
<?= $this->endSection() ?>