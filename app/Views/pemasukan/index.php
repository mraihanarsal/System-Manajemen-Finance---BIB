<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container-fluid px-3 py-4">
  <div class="container mt-1">
    <h3>Data Pemasukan</h3>

    <!-- Filter -->
    <?= $this->include('pemasukan/_filter_card') ?>

    <!-- Tabel -->
    <?= $this->include('pemasukan/_tabel_pemasukan') ?>

    <!-- Total dan Pagination -->
    <?= $this->include('pemasukan/_total_dan_pagination') ?>

    <!-- Custom CSS -->
    <?= $this->include('pemasukan/_custom_style') ?>

    <!-- Modal -->
    <?= $this->include('pemasukan/_modal_tambah') ?>
    <?= $this->include('pemasukan/_modal_edit') ?>
    <?= $this->include('pemasukan/_modal_hapus') ?>

    <!-- Script -->
    <?= $this->include('pemasukan/_script') ?>
  </div>
</div>

<?= $this->endSection() ?>
