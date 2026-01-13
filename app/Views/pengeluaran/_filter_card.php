<div class="card card-premium mb-0">
    <div class="card-body p-4">
        <form id="filterForm" class="row g-3 align-items-end">
            <!-- Year Filter -->
            <div class="col-md-2">
                <label for="filterTahun" class="form-label-premium">
                    <i class="fas fa-history text-primary me-1"></i> Riwayat
                </label>
                <input type="number" class="form-control form-control-premium" id="filterTahun" placeholder="Tahun" min="2000" max="2100" value="<?= date('Y') ?>">
            </div>
            
             <div class="col-md-2">
                <label for="filterStart" class="form-label-premium">
                    <i class="far fa-calendar-alt me-1 text-primary"></i> Mulai
                </label>
                <input type="date" class="form-control form-control-premium" id="filterStart" name="start" value="<?= date('Y-01-01') ?>">
            </div>
            <div class="col-md-2">
                <label for="filterEnd" class="form-label-premium">
                    <i class="far fa-calendar-alt me-1 text-primary"></i> Sampai
                </label>
                <input type="date" class="form-control form-control-premium" id="filterEnd" name="end" value="<?= date('Y-12-31') ?>">
            </div>
            <div class="col-md-4">
                <label for="filterKategori" class="form-label-premium">
                    <i class="fas fa-tags me-1 text-primary"></i> Kategori
                </label>
                <select class="form-control form-control-premium" id="filterKategori" name="kategori_id">
                    <option value="">Semua Kategori</option>
                    <?php foreach ($kategori as $k): ?>
                        <option value="<?= $k['id'] ?>"><?= esc($k['nama']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                 <button type="button" id="btnFilter" class="btn btn-primary-premium w-100 justify-content-center">
                    <i class="fas fa-filter"></i> Terapkan
                 </button>
            </div>
        </form>
    </div>
</div>