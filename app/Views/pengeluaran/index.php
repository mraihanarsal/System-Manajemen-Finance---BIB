<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container-fluid px-3 py-4">
  <div class="container mt-1">
    <h3>Data Pengeluaran</h3>

    <!-- Filter -->
    <?= $this->include('pengeluaran/_filter_card') ?>

    <!-- Tabel -->
    <?= $this->include('pengeluaran/_tabel_pengeluaran') ?>

    <!-- Total dan Pagination -->
    <?= $this->include('pengeluaran/_total_dan_pagination') ?>

    <!-- Custom CSS -->
    <?= $this->include('pengeluaran/_custom_style') ?>

    <!-- Modal -->
    <?= $this->include('pengeluaran/_modal_tambah') ?>
    <?= $this->include('pengeluaran/_modal_edit') ?>
    <?= $this->include('pengeluaran/_modal_hapus') ?>

    <!-- Script -->
    <?= $this->include('pengeluaran/_script') ?>
  </div>
</div>

<?= $this->endSection() ?>
