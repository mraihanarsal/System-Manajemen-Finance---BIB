<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>PT BEX INDO BERKAT</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom fonts -->
    <link href="<?= base_url('vendor/fontawesome-free/css/all.min.css') ?>" rel="stylesheet" type="text/css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">

    <!-- Custom styles -->
    <link href="<?= base_url('css/sb-admin-2.min.css') ?>" rel="stylesheet">

    <?= $this->renderSection('styles') ?>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        const BASE_URL = "<?= base_url() ?>";
        const SITE_URL = "<?= site_url() ?>";
    </script>
</head>

<body id="page-top">
    <div id="wrapper">

        <!-- Sidebar -->
        <?= $this->include('layouts/sidebar') ?>
        <!-- End Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">

                <!-- Topbar -->
                <?= $this->include('layouts/topbar') ?>
                <!-- End Topbar -->

                <!-- Main Content -->
                <div class="container-fluid">
                    <?= $this->renderSection('content') ?>
                </div>
                <!-- End Main Content -->
            </div>
        </div>
    </div>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Flash Message (Success / Error)
        <?php if (session()->getFlashdata('success')): ?>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: '<?= session()->getFlashdata('success') ?>',
                timer: 2000,
                showConfirmButton: false
            });
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: '<?= session()->getFlashdata('error') ?>',
                timer: 2500,
                showConfirmButton: true
            });
        <?php endif; ?>
    </script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Scripts -->
    <?= $this->include('layouts/scripts') ?>

    <?= $this->renderSection('scripts') ?>
</body>

</html>