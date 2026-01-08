<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambah">
            <i class="fas fa-plus"></i> Buat Pengeluaran
        </button>
    </div>
    <div class="card-body">
        <form id="filterForm" class="row g-3 align-items-end">
             <div class="col-md-3">
                <label for="filterStart" class="form-label fw-bold small">Mulai Tanggal</label>
                <input type="date" class="form-control" id="filterStart" name="start" value="<?= date('Y-m-01') ?>">
            </div>
            <div class="col-md-3">
                <label for="filterEnd" class="form-label fw-bold small">Sampai Tanggal</label>
                <input type="date" class="form-control" id="filterEnd" name="end" value="<?= date('Y-m-t') ?>">
            </div>
            <div class="col-md-3">
                <label for="filterKategori" class="form-label fw-bold small">Kategori</label>
                <select class="form-control" id="filterKategori" name="kategori_id">
                    <option value="">Semua Kategori</option>
                    <?php foreach ($kategori as $k): ?>
                        <option value="<?= $k['id'] ?>"><?= esc($k['nama']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3 text-end">
                 <button type="button" id="btnFilter" class="btn btn-primary w-100">
                    <i class="fas fa-filter"></i> Tampilkan
                 </button>
            </div>
        </form>
    </div>
</div>