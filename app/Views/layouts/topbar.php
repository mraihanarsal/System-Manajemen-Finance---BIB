<!-- Topbar -->
<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 shadow">

    <!-- Sidebar Toggle (Topbar) -->
    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
        <i class="fa fa-bars"></i>
    </button>
     <div class="sidebar-brand-text mx-3" style="margin-top: 0,8cm; margin-left: 1cm;">
        <h4 class="font-weight-bold text-gray-800">PT BEX INDO BERKAT</h4>
    </div>
    <!-- Topbar Navbar -->
    <ul class="navbar-nav ml-auto">
        <div class="topbar-divider d-none d-sm-block"></div>

        <!-- User Info -->
        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown">
                <div class="d-flex flex-column text-right mr-3">
                    <span class="text-gray-600 small font-weight-bold">
                        <?= session()->get('nama') ?? 'User' ?>
                    </span>
                    <span class="text-xs text-gray-500">
                        <?= ucfirst(session()->get('role') ?? 'Guest') ?>
                    </span>
                </div>
                <?php 
                    $foto = session()->get('foto');
                    $imgSrc = ($foto && $foto !== 'default.png' && file_exists('uploads/profiles/' . $foto)) 
                        ? base_url('uploads/profiles/' . $foto) 
                        : base_url('img/undraw_profile_2.svg');
                ?>
                <img class="img-profile rounded-circle" src="<?= $imgSrc ?>" style="object-fit: cover;">
            </a>
            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in">
                <a class="dropdown-item" href="<?= base_url('dashboard/profile') ?>">
                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>Profile
                </a>
                <?php if (session()->get('is_master') || session()->get('role') === 'admin'): ?>
                <a class="dropdown-item" href="<?= base_url('dashboard/kelola_pengguna') ?>">
                    <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>Pengguna
                </a>
                <?php endif; ?>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>Logout
                </a>
            </div>
        </li>
    </ul>
</nav>

<!-- Logout Modal-->
<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Logout</h5>
                <button class="close" type="button" data-dismiss="modal">
                    <span>×</span>
                </button>
            </div>
            <div class="modal-body">Apakah Anda yakin ingin logout?</div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                <a class="btn btn-primary" href="<?= base_url('auth/logout') ?>">Logout</a>
            </div>
        </div>
    </div>
</div>