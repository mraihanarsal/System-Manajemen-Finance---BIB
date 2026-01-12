<!-- Area Chart -->
<div class="col-xl-8 col-lg-7">
    <div class="card shadow mb-4">
        <!-- Card Header - Dropdown -->
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Grafik Tahunan</h6>
            <div class="dropdown no-arrow">
                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                    aria-labelledby="dropdownMenuLink" style="max-height: 200px; overflow-y: auto;">
                    <div class="dropdown-header">Pilih Tahun:</div>
                    <div class="dropdown-divider"></div>
                    <?php foreach ($availableYears as $year): ?>
                        <a class="dropdown-item chart-year-select" href="#" data-year="<?= $year ?>"><?= $year ?></a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <!-- Card Body -->
        <div class="card-body">
            <div class="chart-area">
                <canvas id="myAreaChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Pie Chart -->
<div class="col-xl-4 col-lg-5">
    <div class="card shadow mb-4">
        <!-- Card Header - Dropdown -->
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Sumber Pendapatan</h6>
            <div class="dropdown no-arrow">
                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                    aria-labelledby="dropdownMenuLink" style="max-height: 200px; overflow-y: auto;">
                    <div class="dropdown-header">Pilih Tahun:</div>
                    <div class="dropdown-divider"></div>
                    <?php foreach ($availableYears as $year): ?>
                        <a class="dropdown-item chart-year-select" href="#" data-year="<?= $year ?>"><?= $year ?></a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <!-- Card Body -->
        <div class="card-body">
            <div class="chart-pie pt-4 pb-2">
                <canvas id="myPieChart"></canvas>
            </div>
            <div class="mt-4 text-center small">
                <span class="mr-2">
                    <i class="fas fa-circle text-primary"></i> Shopee
                </span>
                <span class="mr-2">
                    <i class="fas fa-circle text-success"></i> Tiktok
                </span>
                <span class="mr-2">
                    <i class="fas fa-circle text-info"></i> Zefatex
                </span>
            </div>
        </div>
    </div>
</div>
</div>