<!-- Cards Section -->
<!-- Cards Section -->
<style>
    .card-premium {
        border: none;
        border-radius: 15px;
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        overflow: hidden;
        position: relative;
    }
    .card-premium:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.12), 0 6px 6px rgba(0,0,0,0.15) !important;
    }
    .card-premium .card-body {
        padding: 1.5rem;
        z-index: 1;
        position: relative;
    }
    .card-premium .bg-shape {
        position: absolute;
        top: -20px;
        right: -20px;
        width: 100px;
        height: 100px;
        border-radius: 50%;
        opacity: 0.1;
        z-index: 0;
    }
    .card-gradient-primary {
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        color: white;
    }
    .card-gradient-danger {
        background: linear-gradient(135deg, #e74a3b 0%, #be2617 100%);
        color: white;
    }
    .card-gradient-success {
        background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%);
        color: white;
    }
    .card-gradient-info {
        background: linear-gradient(135deg, #36b9cc 0%, #258391 100%);
        color: white;
    }
    .card-gradient-warning {
        background: linear-gradient(135deg, #f6c23e 0%, #dda20a 100%);
        color: white;
    }
    .text-white-80 {
        color: rgba(255, 255, 255, 0.8);
    }
    .icon-white-50 {
        color: rgba(255, 255, 255, 0.5);
    }
</style>

<div class="row">
    <!-- Total Pemasukan Pertahun Card -->
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card card-premium card-gradient-primary shadow h-100">
            <div class="bg-shape bg-white"></div>
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-white-80 text-uppercase mb-1" id="card-income-label">
                            Total Pemasukan (<?= $currentYear ?>)
                        </div>
                        <div class="h4 mb-0 font-weight-bold" id="card-income-value">
                            Rp <?= number_format($income['yearly'], 0, ',', '.') ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-arrow-up fa-2x icon-white-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Pengeluaran Pertahun Card -->
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card card-premium card-gradient-danger shadow h-100">
            <div class="bg-shape bg-white"></div>
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-white-80 text-uppercase mb-1" id="card-expense-label">
                            Total Pengeluaran (<?= $currentYear ?>)
                        </div>
                        <div class="h4 mb-0 font-weight-bold" id="card-expense-value">
                            Rp <?= number_format($expense['yearly'], 0, ',', '.') ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-arrow-down fa-2x icon-white-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- PENDAPATAN BERSIH (NET INCOME) -->
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card card-premium card-gradient-success shadow h-100">
            <div class="bg-shape bg-white"></div>
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-white-80 text-uppercase mb-1" id="card-net-income-label">
                            Pendapatan Bersih (<?= $currentYear ?>)
                        </div>
                        <div class="h4 mb-0 font-weight-bold" id="card-net-income-value">
                            Rp <?= number_format($net_income['yearly'], 0, ',', '.') ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-wallet fa-2x icon-white-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Pengguna Card -->
    <div class="col-xl-6 col-md-6 mb-4">
        <div class="card card-premium card-gradient-info shadow h-100">
            <div class="bg-shape bg-white"></div>
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-white-80 text-uppercase mb-1">Pengguna Terdaftar</div>
                        <div class="h5 mb-0 font-weight-bold"><?= $users_count ?> User</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x icon-white-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Toko Saya Card -->
    <div class="col-xl-6 col-md-6 mb-4">
        <div class="card card-premium card-gradient-warning shadow h-100">
            <div class="bg-shape bg-white"></div>
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-white-80 text-uppercase mb-1">Total Toko Saya</div>
                        <div class="h5 mb-0 font-weight-bold"><?= $stores['count'] ?> Toko</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-store fa-2x icon-white-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
