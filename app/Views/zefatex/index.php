<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Dashboard Zefatex
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- HEADER -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card shadow border-0 overflow-hidden" style="border-radius: 15px;">
            <div class="card-body p-4 d-flex align-items-center" style="background: linear-gradient(135deg, #ffffff 0%, #f3f6f9 100%); position: relative;">
                <!-- Decorative Accent -->
                <div style="position: absolute; left: 0; top: 0; bottom: 0; width: 5px; background: linear-gradient(to bottom, #4e73df, #224abe);"></div>
                
                <div class="d-flex align-items-center flex-grow-1">
                    <div class="bg-white p-2 shadow-sm rounded-circle d-flex justify-content-center align-items-center me-3" style="width: 70px; height: 70px;">
                        <img src="<?= base_url('img/konveksi.png') ?>" alt="Logo" style="height: 45px; object-fit: contain;">
                    </div>
                    <div>
                        <h4 class="m-0 font-weight-bold text-dark" style="letter-spacing: -0.5px;">Dashboard Zefatex</h4>
                        <p class="text-muted m-0 small">Pemasukan & Manajemen Invoice Konveksi</p>
                    </div>
                </div>
                
                <a href="<?= base_url('zefatex/create') ?>" class="btn btn-primary btn-lg shadow-lg px-4" style="border-radius: 50px; background: linear-gradient(to right, #4e73df, #224abe); border: none;">
                    <i class="fas fa-plus-circle me-2"></i> Input Transaksi Baru
                </a>
            </div>
        </div>
    </div>
</div>

<!-- TOTAL REVENUE -->
<div class="row mb-4">
    <div class="col-xl-12 col-md-12 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Total Pemasukan Zefatex (Semua Data)
                        </div>
                        <div class="h3 mb-0 font-weight-bold text-gray-800">
                            Rp <?= number_format($totalRevenue) ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- TABLE -->
<div class="card shadow mb-4">
    <div class="card-header py-3 bg-white d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Daftar Transaksi Invoice</h6>
    </div>
    
    <div class="card-body">
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover" id="dataTable" width="100%" cellspacing="0">
                <thead class="bg-light">
                    <tr>
                        <th width="5%">No</th>
                        <th>Tanggal Dibuat</th>
                        <th>No. Invoice</th>
                        <th>Bill To (Customer)</th>
                        <th>Total Amount</th>
                        <th width="10%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($invoices)): ?>
                        <!-- DataTables handles empty state gracefully, but we can leave this blank or simple -->
                    <?php else: ?>
                        <?php foreach ($invoices as $i => $row): ?>
                            <tr>
                                <td class="text-center"><?= $i + 1 ?></td>
                                <td data-sort="<?= strtotime($row['transaction_date']) ?>">
                                    <?= date('d M Y', strtotime($row['transaction_date'])) ?>
                                </td>
                                <td class="fw-bold text-primary"><?= esc($row['invoice_number']) ?></td>
                                <td><?= esc($row['customer_name']) ?></td>
                                <td class="text-end fw-bold text-success" data-sort="<?= $row['total_amount'] ?>">
                                    Rp <?= number_format($row['total_amount']) ?>
                                </td>
                                <td class="text-center">
                                    <a href="<?= base_url('zefatex/edit/' . $row['id']) ?>" 
                                        class="btn btn-sm btn-warning btn-circle me-1"
                                        title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="<?= base_url('zefatex/delete/' . $row['id']) ?>" 
                                       class="btn btn-sm btn-danger btn-circle btn-delete"
                                       title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
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
<!-- DataTables & SweetAlert -->
<link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {
        $('#dataTable').DataTable({
            "language": {
                "search": "Search:",
                "lengthMenu": "Tampilkan _MENU_ data per halaman",
                "zeroRecords": "Tidak ada data yang ditemukan",
                "info": "Menampilkan halaman _PAGE_ dari _PAGES_",
                "infoEmpty": "Tidak ada data tersedia",
                "infoFiltered": "(difilter dari _MAX_ total data)",
                "paginate": {
                    "first": "Awal",
                    "last": "Akhir",
                    "next": "Selanjutnya",
                    "previous": "Sebelumnya"
                }
            },
            "order": [[ 1, "desc" ]], // Default sort by Date (Column index 1)
            "pageLength": 10
        });

        // Delete Confirmation
        $(document).on('click', '.btn-delete', function(e) {
            e.preventDefault();
            var url = $(this).attr('href');
            Swal.fire({
                title: 'Hapus Transaksi?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
        });
    });
</script>
<?= $this->endSection() ?>
