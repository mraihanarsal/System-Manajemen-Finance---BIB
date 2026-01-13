<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container-fluid py-4 animate-fade-in">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title mb-1 ml-2">Kelola Pengeluaran</h1>
        <p class="page-subtitle mb-0"></p>
    </div>
    <!-- Button moved to header for better UX -->
    <button class="btn btn-primary-premium" data-bs-toggle="modal" data-bs-target="#modalTambah">
        <i class="fas fa-plus-circle"></i> 
        <span>Buat Pengeluaran Baru</span>
    </button>
  </div>

  <div class="row g-4">
    <!-- Filter Section (Full Width) -->
    <div class="col-12">
        <?= $this->include('pengeluaran/_filter_card') ?>
    </div>

    <!-- Main Content: Table & Widget -->
    <div class="col-12">
        <div class="card card-premium h-100">
            <div class="card-header-premium d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold text-dark">Daftar Transaksi</h5>
                <!-- Total Widget Small Inline or distinct? Let's use the external Total Grid for now -->
            </div>
            
            <div class="card-body-premium p-0">
                <!-- Tabel -->
                <?= $this->include('pengeluaran/_tabel_pengeluaran') ?>
            </div>
            
            <div class="card-footer bg-white border-top-0 py-3">
                 <!-- Total dan Pagination -->
                <?= $this->include('pengeluaran/_total_dan_pagination') ?>
            </div>
        </div>
    </div>
    </div>
  </div>

</div>

<!-- Modal must be outside of animated container to avoid transform stacking context issues -->
<?= $this->include('pengeluaran/_modal_tambah') ?>
<?= $this->include('pengeluaran/_modal_edit') ?>
<?= $this->include('pengeluaran/_modal_hapus') ?>

<!-- Custom CSS -->
<?= $this->include('pengeluaran/_custom_style') ?>

<!-- Script -->
<?= $this->include('pengeluaran/_script') ?>

<?= $this->endSection() ?>
