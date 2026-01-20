<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="<?= base_url('/') ?>">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-laugh-wink"></i>
        </div>
        <div class="sidebar-brand-text mx-3">Welcome !</div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item active">
        <a class="nav-link" href="<?= base_url('/') ?>">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span></a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">Kelola</div>
    <!-- Menu Toko -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUtilities">
            <i class="fas fa-fw fa-store"></i>
            <span>Pemasukan</span>
        </a>
        <div id="collapseUtilities" class="collapse" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">platform:</h6>
                <a class="collapse-item" href="<?= base_url('shopee') ?>">
                    <img src="<?= base_url('img/shopee.png') ?>" alt="Shopee" width="18" style="margin-right:8px;">
                    Shopee
                </a>
                <a class="collapse-item" href="<?= base_url('tiktok') ?>">
                    <img src="<?= base_url('img/tiktok.png') ?>" alt="TikTok" width="18" style="margin-right:8px;">
                    TikTok
                </a>
                <a class="collapse-item" href="<?= base_url('zefatex') ?>">
                    <img src="<?= base_url('img/konveksi.png') ?>" alt="Zefatex" width="18" style="margin-right:8px;">
                    Zefatex
                </a>
            </div>
        </div>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Nav Item - Charts -->
    <li class="nav-item">
        <a class="nav-link" href="<?= base_url('pengeluaran') ?>">
            <i class="fas fa-fw fa-money-bill-wave"></i>
            <span>Pengeluaran</span></a>
    </li>

    <?php if (session()->get('is_master') || session()->get('role') === 'admin'): ?>
        <!-- Nav Item - Kelola Pengguna -->
        <li class="nav-item">
            <a class="nav-link" href="<?= base_url('dashboard/kelola_pengguna') ?>">
                <i class="fas fa-fw fa-user-cog"></i>
                <span>Kelola Pengguna</span></a>
        </li>
    <?php endif; ?>

    <!-- Nav Item - Tables -->
    <li class="nav-item">
        <a class="nav-link" href="<?= base_url('laporan') ?>">
            <i class="fas fa-fw fa-file-pdf"></i>
            <span>Rekapitulasi</span></a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>
</ul>