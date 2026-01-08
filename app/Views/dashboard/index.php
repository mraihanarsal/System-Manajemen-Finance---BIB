<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Dashboard
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    
    <!-- Include Cards Earning -->
    <?= $this->include('dashboard/_cards_earning') ?>

    <!-- Content Row -->
    <div class="row">
    <!-- Include Graph Area Chart and Pie Chart -->
    <?= $this->include('dashboard/_graph_pie_chart') ?>


<?= $this->endSection() ?>